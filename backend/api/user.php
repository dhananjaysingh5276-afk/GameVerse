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

$response = ['success' => false, 'message' => ''];

if (isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    
    $query = "SELECT id, username, email, role, coins, avatar, bio, created_at FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    if ($user) {
        $response['success'] = true;
        $response['user'] = $user;
    } else {
        $response['message'] = 'User not found';
    }
} else {
    $response['message'] = 'Not logged in';
}

echo json_encode($response);
?>