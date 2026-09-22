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

$showAll = isset($_GET['all']) && intval($_GET['all']) === 1 && isAdmin();

if ($showAll) {
    $query = "SELECT t.*, i.name as item_name, i.image_url, i.category, u.username
              FROM transactions t
              LEFT JOIN items i ON t.item_id = i.id
              LEFT JOIN users u ON t.user_id = u.id
              ORDER BY t.created_at DESC
              LIMIT 200";

    $response = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
    echo json_encode($response);
    exit();
}

$query = "SELECT t.*, i.name as item_name, i.image_url, i.category 
          FROM transactions t 
          LEFT JOIN items i ON t.item_id = i.id 
          WHERE t.user_id = ? 
          ORDER BY t.created_at DESC 
          LIMIT 100";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$response = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode($response);
?>