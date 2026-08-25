<?php
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

$game_filter = isset($_GET['game']) ? intval($_GET['game']) : 0;
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;

$query = "SELECT i.*, g.name as game_name 
          FROM items i 
          JOIN games g ON i.game_id = g.id 
          WHERE i.stock > 0";

$params = [];
$types = "";

if ($game_filter > 0) {
    $query .= " AND i.game_id = ?";
    $params[] = $game_filter;
    $types .= "i";
}

if ($category_filter) {
    $query .= " AND i.category = ?";
    $params[] = $category_filter;
    $types .= "s";
}

if ($search) {
    $query .= " AND (i.name LIKE ? OR i.description LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "ss";
}

switch ($sort) {
    case 'price_low':
        $query .= " ORDER BY i.price ASC";
        break;
    case 'price_high':
        $query .= " ORDER BY i.price DESC";
        break;
    default:
        $query .= " ORDER BY i.created_at DESC";
}

if ($limit > 0) {
    $query .= " LIMIT $limit";
}

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$response = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode($response);
?>