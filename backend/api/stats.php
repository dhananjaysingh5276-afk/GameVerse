<?php
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

if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$counts = [
    'users'         => 'SELECT COUNT(*) FROM users',
    'games'         => 'SELECT COUNT(*) FROM games',
    'items'         => 'SELECT COUNT(*) FROM items',
    'transactions'  => 'SELECT COUNT(*) FROM transactions',
];

$stats = [];
foreach ($counts as $key => $sql) {
    $stats[$key] = intval($conn->query($sql)->fetch_row()[0]);
}

echo json_encode($stats);
?>
