<?php
require_once __DIR__ . '/../bootstrap.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

// Redirect admin to admin dashboard
if (isAdmin()) {
    redirect('../admin/dashboard.php');
}

$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($item_id <= 0) {
    die("Invalid item ID");
}

// Get item details
$item_query = "SELECT i.*, g.name as game_name 
               FROM items i 
               JOIN games g ON i.game_id = g.id 
               WHERE i.id = ?";
$stmt = $conn->prepare($item_query);
$stmt->bind_param("i", $item_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if (!$item) {
    die("Item not found");
}

// Check if user already owns this item
$check_query = "SELECT id FROM inventory WHERE user_id = ? AND item_id = ?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("ii", $_SESSION['user_id'], $item_id);
$stmt->execute();
$owns_item = $stmt->get_result()->num_rows > 0;

if ($owns_item) {
    die("You already own this item!");
}

// Get user's balance
$user_query = "SELECT username, coins FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$error = '';
$success = '';

// Process purchase
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm'])) {
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Check balance again (in case it changed)
        $user_query = "SELECT coins FROM users WHERE id = ? FOR UPDATE";
        $stmt = $conn->prepare($user_query);
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $current_balance = $stmt->get_result()->fetch_assoc()['coins'];
        
        if ($current_balance < $item['price']) {
            throw new Exception("Insufficient coins!");
        }
        
        // Check stock again
        $stock_query = "SELECT stock FROM items WHERE id = ? FOR UPDATE";
        $stmt = $conn->prepare($stock_query);
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $stock = $stmt->get_result()->fetch_assoc()['stock'];
        
        if ($stock <= 0) {
            throw new Exception("Item out of stock!");
        }
        
        // Deduct coins
        $new_balance = $current_balance - $item['price'];
        $update_balance = "UPDATE users SET coins = ? WHERE id = ?";
        $stmt = $conn->prepare($update_balance);
        $stmt->bind_param("ii", $new_balance, $_SESSION['user_id']);
        $stmt->execute();
        
        // Add to inventory
        $add_inventory = "INSERT INTO inventory (user_id, item_id) VALUES (?, ?)";
        $stmt = $conn->prepare($add_inventory);
        $stmt->bind_param("ii", $_SESSION['user_id'], $item_id);
        $stmt->execute();
        
        // Update stock
        $new_stock = $stock - 1;
        $update_stock = "UPDATE items SET stock = ? WHERE id = ?";
        $stmt = $conn->prepare($update_stock);
        $stmt->bind_param("ii", $new_stock, $item_id);
        $stmt->execute();
        
        // Log transaction
        $log_query = "INSERT INTO transactions (user_id, item_id, amount, type, description, status) 
                      VALUES (?, ?, ?, 'purchase', ?, 'completed')";
        $description = "Purchased: " . $item['name'] . " (" . $item['game_name'] . ")";
        $stmt = $conn->prepare($log_query);
        $stmt->bind_param("iiis", $_SESSION['user_id'], $item_id, $item['price'], $description);
        $stmt->execute();
        
        // Commit transaction
        mysqli_commit($conn);
        
        // Redirect to success page
        header('Location: success.php?item=' . urlencode($item['name']) . '&price=' . $item['price'] . '&balance=' . $new_balance);
        exit();
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Confirm Purchase - GamerVerse</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            background: #0a0a0a; 
            color: #fff;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background: #1a1a2e;
            padding: 40px;
            border-radius: 12px;
            max-width: 500px;
            width: 100%;
            border: 1px solid #2a2a4e;
            box-shadow: 0 0 40px rgba(0,150,255,0.1);
        }
        h1 {
            color: #00aaff;
            text-align: center;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #888;
            text-align: center;
            margin-bottom: 30px;
        }
        .item-preview {
            background: #0a0a1a;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #2a2a4e;
            margin-bottom: 25px;
            text-align: center;
        }
        .item-preview .name {
            font-size: 22px;
            font-weight: 700;
        }
        .item-preview .game {
            color: #888;
            font-size: 14px;
            margin: 5px 0 10px;
        }
        .item-preview .price {
            font-size: 24px;
            color: #ffd700;
            font-weight: 700;
        }
        .item-preview .stats {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 10px;
            font-size: 14px;
            color: #00aaff;
        }
        .balance-info {
            background: #0a0a1a;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #2a2a4e;
        }
        .balance-info .row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .balance-info .label {
            color: #888;
        }
        .balance-info .value {
            font-weight: 600;
        }
        .balance-info .value.positive { color: #44ff88; }
        .balance-info .value.negative { color: #ff4444; }
        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: 0.3s;
            display: block;
            text-align: center;
            text-decoration: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #00aaff, #0088cc);
            color: #fff;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,170,255,0.3);
        }
        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .btn-secondary {
            background: #555;
            color: #fff;
            margin-top: 10px;
        }
        .btn-secondary:hover {
            background: #444;
        }
        .error {
            color: #ff4444;
            background: rgba(255,68,68,0.1);
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
            border-left: 4px solid #ff4444;
            text-align: center;
        }
        .back-link {
            display: inline-block;
            color: #888;
            text-decoration: none;
            margin-top: 15px;
            font-size: 14px;
        }
        .back-link:hover {
            color: #00aaff;
        }
        .warning {
            color: #ffaa00;
            font-size: 13px;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛒 Confirm Purchase</h1>
        <p class="subtitle">Review your purchase before confirming</p>

        <?php if ($error): ?>
            <div class="error">❌ <?= $error ?></div>
        <?php endif; ?>

        <div class="item-preview">
            <div class="name"><?= htmlspecialchars($item['name']) ?></div>
            <div class="game">🎮 <?= htmlspecialchars($item['game_name']) ?></div>
            <?php if ($item['skill_value'] || $item['attribute']): ?>
                <div class="stats">
                    <?php if ($item['skill_value']): ?>
                        <span>⚡ <?= htmlspecialchars($item['skill_value']) ?></span>
                    <?php endif; ?>
                    <?php if ($item['attribute']): ?>
                        <span>🔥 <?= htmlspecialchars($item['attribute']) ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php if ($item['special_effect']): ?>
                <div style="color:#aa88ff;font-size:14px;margin-top:5px;">
                    ✨ <?= htmlspecialchars($item['special_effect']) ?>
                </div>
            <?php endif; ?>
            <div class="price">🪙 <?= number_format($item['price']) ?></div>
        </div>

        <div class="balance-info">
            <div class="row">
                <span class="label">Your Balance</span>
                <span class="value positive">🪙 <?= number_format($user['coins']) ?></span>
            </div>
            <div class="row">
                <span class="label">Item Price</span>
                <span class="value negative">- 🪙 <?= number_format($item['price']) ?></span>
            </div>
            <div class="row" style="border-top:1px solid #2a2a4e;padding-top:10px;margin-top:5px;">
                <span class="label"><strong>Remaining Balance</strong></span>
                <span class="value positive" style="font-size:18px;">
                    🪙 <?= number_format($user['coins'] - $item['price']) ?>
                </span>
            </div>
        </div>

        <?php if ($user['coins'] < $item['price']): ?>
            <div class="error">❌ Insufficient coins! You need <?= number_format($item['price'] - $user['coins']) ?> more coins.</div>
            <a href="../user/browse.php" class="btn btn-secondary">← Back to Marketplace</a>
        <?php elseif ($item['stock'] <= 0): ?>
            <div class="error">❌ Item is out of stock!</div>
            <a href="../user/browse.php" class="btn btn-secondary">← Back to Marketplace</a>
        <?php else: ?>
            <form method="POST">
                <button type="submit" name="confirm" class="btn btn-primary">
                    ✅ Confirm Purchase (<?= number_format($item['price']) ?> Coins)
                </button>
            </form>
            <a href="../user/item_detail.php?id=<?= $item['id'] ?>" class="back-link">← Cancel and go back</a>
        <?php endif; ?>
        
        <div class="warning">⚠️ This purchase is final and cannot be refunded.</div>
    </div>
</body>
</html>