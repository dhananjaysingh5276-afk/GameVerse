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

// Get user's coins
$user_query = "SELECT username, coins FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Get user's inventory with item details
$query = "SELECT i.*, g.name as game_name, inv.purchase_date, inv.is_equipped
          FROM inventory inv
          JOIN items i ON inv.item_id = i.id
          JOIN games g ON i.game_id = g.id
          WHERE inv.user_id = ?
          ORDER BY inv.purchase_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$inventory = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Count categories
$weapons = array_filter($inventory, function($item) { return $item['category'] == 'weapon'; });
$costumes = array_filter($inventory, function($item) { return $item['category'] == 'costume'; });
$gems = array_filter($inventory, function($item) { return $item['category'] == 'gem'; });
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Collection - GamerVerse</title>
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
            max-width: 1400px;
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
        .header .stats {
            display: flex;
            gap: 20px;
            color: #888;
        }
        .header .stats span {
            background: #1a1a2e;
            padding: 8px 18px;
            border-radius: 20px;
            border: 1px solid #2a2a4e;
        }
        .header .stats strong {
            color: #fff;
        }

        .category-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }
        .category-tabs a {
            padding: 8px 20px;
            border-radius: 20px;
            text-decoration: none;
            color: #888;
            border: 1px solid #2a2a4e;
            transition: 0.3s;
            font-size: 14px;
        }
        .category-tabs a:hover {
            border-color: #00aaff;
            color: #fff;
        }
        .category-tabs a.active {
            background: #00aaff;
            color: #fff;
            border-color: #00aaff;
        }

        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .item-card {
            background: #1a1a2e;
            border-radius: 10px;
            border: 1px solid #2a2a4e;
            overflow: hidden;
            transition: 0.3s;
            position: relative;
        }
        .item-card:hover {
            transform: translateY(-5px);
            border-color: #00aaff;
            box-shadow: 0 10px 30px rgba(0,170,255,0.1);
        }
        .item-card .equipped-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #44ff88;
            color: #000;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 700;
        }
        .item-card .item-image {
            width: 100%;
            height: 150px;
            background: #0a0a1a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
        }
        .item-card .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .item-card .item-body {
            padding: 15px;
        }
        .item-card .item-body h3 {
            font-size: 15px;
            margin-bottom: 4px;
        }
        .item-card .item-body .game-name {
            color: #888;
            font-size: 12px;
            margin-bottom: 8px;
        }
        .item-card .item-body .stats {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin: 6px 0;
        }
        .item-card .item-body .stats span {
            background: #0a0a1a;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            color: #00aaff;
            border: 1px solid #2a2a4e;
        }
        .item-card .item-body .purchase-date {
            font-size: 11px;
            color: #666;
            margin-top: 8px;
        }

        .badge {
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            display: inline-block;
        }
        .badge-weapon { background: #ff4444; color: #fff; }
        .badge-costume { background: #44aaff; color: #fff; }
        .badge-gem { background: #ffaa00; color: #000; }

        .empty {
            text-align: center;
            padding: 60px;
            color: #666;
            background: #1a1a2e;
            border-radius: 10px;
            border: 1px dashed #2a2a4e;
        }
        .empty .icon { font-size: 60px; margin-bottom: 20px; }
        .empty .btn {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            background: #00aaff;
            color: #fff;
            margin-top: 15px;
            transition: 0.3s;
        }
        .empty .btn:hover {
            background: #0088cc;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .navbar { flex-wrap: wrap; gap: 10px; }
            .navbar .nav-links { flex-wrap: wrap; justify-content: center; }
            .header { flex-direction: column; text-align: center; }
            .items-grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); }
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>

    <div class="container">
        <div class="header">
            <h2>🎒 My Collection</h2>
            <div class="stats">
                <span>Total: <strong><?= count($inventory) ?></strong></span>
                <span>⚔️ Weapons: <strong><?= count($weapons) ?></strong></span>
                <span>👔 Costumes: <strong><?= count($costumes) ?></strong></span>
                <span>💎 Gems: <strong><?= count($gems) ?></strong></span>
            </div>
        </div>

        <?php if (empty($inventory)): ?>
            <div class="empty">
                <div class="icon">🎒</div>
                <h3>Your collection is empty</h3>
                <p>You haven't purchased any items yet.</p>
                <a href="../user/browse.php" class="btn">🛒 Browse Marketplace</a>
            </div>
        <?php else: ?>
            <div class="items-grid">
                <?php foreach ($inventory as $item): ?>
                    <div class="item-card">
                        <?php if ($item['is_equipped']): ?>
                            <span class="equipped-badge">⚡ EQUIPPED</span>
                        <?php endif; ?>
                        <div class="item-image">
                            <img src="<?= $item['image_url'] ?? 'default.png' ?>" alt="<?= $item['name'] ?>"
                                 onerror="this.parentElement.innerHTML='🎮'">
                        </div>
                        <div class="item-body">
                            <h3><?= htmlspecialchars($item['name']) ?></h3>
                            <div class="game-name">🎮 <?= htmlspecialchars($item['game_name']) ?></div>
                            
                            <span class="badge badge-<?= $item['category'] ?>">
                                <?= $item['category'] ?>
                            </span>

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

                            <div class="purchase-date">
                                📅 Purchased: <?= date('M d, Y', strtotime($item['purchase_date'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>