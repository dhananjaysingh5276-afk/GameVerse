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

$user_id = $_SESSION['user_id'];

// Get user info
$user_query = "SELECT username, coins FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Get all transactions with item details
$query = "SELECT t.*, i.name as item_name, i.image_url, i.category
          FROM transactions t
          LEFT JOIN items i ON t.item_id = i.id
          WHERE t.user_id = ?
          ORDER BY t.created_at DESC
          LIMIT 100";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$transactions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Calculate stats
$total_spent = 0;
$total_purchases = 0;
foreach ($transactions as $txn) {
    if ($txn['type'] == 'purchase') {
        $total_spent += $txn['amount'];
        $total_purchases++;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transaction History - GamerVerse</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            background: #0a0a0a; 
            color: #fff;
            min-height: 100vh;
        }

        .navbar {
            background: #1a1a2e;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #2a2a4e;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .navbar h1 { 
            color: #00aaff; 
            cursor: pointer; 
            font-size: 22px;
        }
        .navbar h1:hover { color: #33bbff; }
        .navbar .nav-links {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .navbar .nav-links a {
            color: #ccc;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 6px;
            transition: 0.3s;
            font-size: 14px;
        }
        .navbar .nav-links a:hover { background: #2a2a4e; color: #fff; }
        .navbar .nav-links a.active { background: #2a2a4e; color: #00aaff; }
        .navbar .nav-links .logout {
            color: #ff4444;
            border: 1px solid #ff4444;
        }
        .navbar .nav-links .logout:hover {
            background: #ff4444;
            color: #fff;
        }
        .navbar .coins {
            background: linear-gradient(135deg, #ffd700, #ffaa00);
            color: #000;
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }
        .header h2 {
            font-size: 28px;
            color: #00aaff;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #1a1a2e;
            padding: 15px 20px;
            border-radius: 10px;
            border: 1px solid #2a2a4e;
            text-align: center;
        }
        .stat-card .number {
            font-size: 24px;
            font-weight: 700;
            color: #ffd700;
        }
        .stat-card .label {
            color: #888;
            font-size: 13px;
            margin-top: 3px;
        }

        .table-container {
            background: #1a1a2e;
            border-radius: 10px;
            border: 1px solid #2a2a4e;
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #2a2a4e;
        }
        th {
            background: #0a0a1a;
            color: #00aaff;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        td {
            color: #ccc;
            font-size: 14px;
        }
        tr:hover td {
            background: #0a0a1a;
        }

        .item-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .item-cell img {
            width: 30px;
            height: 30px;
            border-radius: 4px;
            object-fit: cover;
            background: #0a0a1a;
        }
        .item-cell .item-name {
            font-weight: 500;
            color: #fff;
        }

        .badge {
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
        }
        .badge-purchase { background: #00aaff; color: #fff; }
        .badge-bonus { background: #44ff88; color: #000; }
        .badge-adjustment { background: #ffaa00; color: #000; }
        .badge-completed { background: #44ff88; color: #000; }
        .badge-pending { background: #ffaa00; color: #000; }
        .badge-failed { background: #ff4444; color: #fff; }

        .amount {
            font-weight: 600;
        }
        .amount.negative { color: #ff4444; }
        .amount.positive { color: #44ff88; }

        .empty {
            text-align: center;
            padding: 60px;
            color: #666;
        }
        .empty .icon { font-size: 60px; margin-bottom: 20px; }

        @media (max-width: 768px) {
            .navbar { flex-wrap: wrap; gap: 10px; }
            .navbar .nav-links { flex-wrap: wrap; justify-content: center; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            table { font-size: 12px; }
            th, td { padding: 8px 10px; }
        }
    </style>
</head>
<body>
   <?php require_once __DIR__ . '/../includes/navbar.php'; ?>
    <div class="container">
        <div class="header">
            <h2>📜 Transaction History</h2>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="number"><?= $total_purchases ?></div>
                <div class="label">Total Purchases</div>
            </div>
            <div class="stat-card">
                <div class="number">🪙 <?= number_format($total_spent) ?></div>
                <div class="label">Total Spent</div>
            </div>
            <div class="stat-card">
                <div class="number">🪙 <?= number_format($user['coins']) ?></div>
                <div class="label">Current Balance</div>
            </div>
            <div class="stat-card">
                <div class="number"><?= count($transactions) ?></div>
                <div class="label">Total Transactions</div>
            </div>
        </div>

        <?php if (empty($transactions)): ?>
            <div class="empty">
                <div class="icon">📜</div>
                <h3>No transactions yet</h3>
                <p>Your transaction history will appear here once you make a purchase.</p>
                <br>
                <a href="../user/browse.php" style="color:#00aaff;text-decoration:none;">🛒 Start Shopping →</a>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Item</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $txn): ?>
                            <tr>
                                <td><?= date('M d, Y H:i', strtotime($txn['created_at'])) ?></td>
                                <td>
                                    <div class="item-cell">
                                        <?php if ($txn['item_name']): ?>
                                            <img src="<?= $txn['image_url'] ?? 'default.png' ?>" alt="<?= $txn['item_name'] ?>">
                                            <span class="item-name"><?= htmlspecialchars($txn['item_name']) ?></span>
                                        <?php else: ?>
                                            <span style="color:#666;">—</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $txn['type'] ?>">
                                        <?= ucfirst($txn['type']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="amount <?= $txn['type'] == 'purchase' ? 'negative' : 'positive' ?>">
                                        <?= $txn['type'] == 'purchase' ? '-' : '+' ?>
                                        🪙 <?= number_format($txn['amount']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $txn['status'] ?>">
                                        <?= ucfirst($txn['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>