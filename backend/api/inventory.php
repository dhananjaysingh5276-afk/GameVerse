<?php
// Include CORS handler
require_once __DIR__ . '/cors.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../bootstrap.php';

$response = [];

if (!isLoggedIn()) {
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT i.*, g.name as game_name, inv.purchase_date, inv.is_equipped 
          FROM inventory inv 
          JOIN items i ON inv.item_id = i.id 
          JOIN games g ON i.game_id = g.id 
          WHERE inv.user_id = ? 
          ORDER BY inv.purchase_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$response = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode($response);
?>
