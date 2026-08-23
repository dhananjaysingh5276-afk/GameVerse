<?php
require_once __DIR__ . '/../bootstrap.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

// Get purchase details from URL
$item_name = isset($_GET['item']) ? urldecode($_GET['item']) : 'Item';
$price = isset($_GET['price']) ? intval($_GET['price']) : 0;
$balance = isset($_GET['balance']) ? intval($_GET['balance']) : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Purchase Successful - GamerVerse</title>
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
            padding: 50px;
            border-radius: 12px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            border: 1px solid #2a2a4e;
            box-shadow: 0 0 40px rgba(68,255,136,0.1);
        }
        .success-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
        h1 {
            color: #44ff88;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #888;
            margin-bottom: 25px;
        }
        .details {
            background: #0a0a1a;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #2a2a4e;
            margin-bottom: 25px;
        }
        .details .row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }
        .details .label {
            color: #888;
        }
        .details .value {
            font-weight: 600;
        }
        .details .value.highlight {
            color: #ffd700;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            text-decoration: none;
            transition: 0.3s;
            margin: 5px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #00aaff, #0088cc);
            color: #fff;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,170,255,0.3);
        }
        .btn-secondary {
            background: #555;
            color: #fff;
        }
        .btn-secondary:hover {
            background: #444;
        }
        .btn-success {
            background: linear-gradient(135deg, #44ff88, #33cc66);
            color: #000;
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(68,255,136,0.3);
        }
        .confetti {
            font-size: 40px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">🎉</div>
        <h1>Purchase Successful!</h1>
        <p class="subtitle">Your item has been added to your collection</p>

        <div class="confetti">🎊 ✨ 🎈 🎉</div>

        <div class="details">
            <div class="row">
                <span class="label">Item Purchased</span>
                <span class="value highlight"><?= htmlspecialchars($item_name) ?></span>
            </div>
            <div class="row">
                <span class="label">Price Paid</span>
                <span class="value">🪙 <?= number_format($price) ?></span>
            </div>
            <div class="row" style="border-top:1px solid #2a2a4e;padding-top:10px;margin-top:5px;">
                <span class="label"><strong>New Balance</strong></span>
                <span class="value" style="font-size:18px;color:#44ff88;">
                    🪙 <?= number_format($balance) ?>
                </span>
            </div>
        </div>

        <div>
            <a href="../user/inventory.php" class="btn btn-success">🎒 View My Collection</a>
            <a href="../user/browse.php" class="btn btn-primary">🛒 Continue Shopping</a>
        </div>
        <br>
        <a href="../user/dashboard.php" class="btn btn-secondary">🏠 Go to Dashboard</a>
    </div>
</body>
</html>
