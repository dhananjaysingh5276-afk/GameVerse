<?php
/**
 * header.php - Common header + navbar for all user pages
 * Include this at the top of every user page
 */

// If page_title is not set, use default
$page_title = isset($page_title) ? $page_title : 'GamerVerse';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
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
    </style>
</head>
<body>

<?php
// Get current page name for active state
$current_page = basename($_SERVER['PHP_SELF']);
$current_folder = basename(dirname($_SERVER['PHP_SELF']));

// Determine which links are active
$is_dashboard = ($current_page == 'dashboard.php' && $current_folder == 'user');
$is_marketplace = ($current_page == 'browse.php' && $current_folder == 'user');
$is_inventory = ($current_page == 'inventory.php' && $current_folder == 'user');
$is_history = ($current_page == 'history.php' && $current_folder == 'transactions');

// Get user coins (if logged in)
$user_coins = 0;
if (isset($_SESSION['user_id'])) {
    $user_query = "SELECT coins FROM users WHERE id = ?";
    $stmt = $conn->prepare($user_query);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $user_coins = $row['coins'];
    }
}
?>

<nav class="navbar">
    <h1 onclick="window.location.href='../user/dashboard.php'">🎮 GamerVerse</h1>
    <div class="nav-links">
        <a href="../user/dashboard.php" class="<?= $is_dashboard ? 'active' : '' ?>">🏠 Dashboard</a>
        <a href="../user/browse.php" class="<?= $is_marketplace ? 'active' : '' ?>">🛒 Marketplace</a>
        <a href="../user/inventory.php" class="<?= $is_inventory ? 'active' : '' ?>">🎒 Collection</a>
        <a href="../transactions/history.php" class="<?= $is_history ? 'active' : '' ?>">📜 History</a>
        <span class="coins">🪙 <?= number_format($user_coins) ?></span>
        <a href="../auth/logout.php" class="logout">Logout</a>
    </div>
</nav>