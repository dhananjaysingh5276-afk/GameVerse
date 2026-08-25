<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CORS Headers - Allow multiple origins
$allowed_origins = ['http://localhost:3000', 'http://localhost:3001'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowed_origins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../bootstrap.php';

$response = ['success' => false, 'message' => ''];

// Get raw POST data
$raw_input = file_get_contents('php://input');
$data = json_decode($raw_input, true);

if (!$data) {
    $data = $_POST;
}

$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

if (empty($username) || empty($password)) {
    $response['message'] = 'Username and password required';
    echo json_encode($response);
    exit();
}

// Check user
$query = "SELECT id, username, password_hash, role, coins, avatar, bio, created_at FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    if (password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        $update = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $update->bind_param("i", $user['id']);
        $update->execute();
        
        unset($user['password_hash']);
        $response['success'] = true;
        $response['user'] = $user;
        $response['message'] = 'Login successful';
    } else {
        $response['message'] = 'Invalid password';
    }
} else {
    $response['message'] = 'User not found';
}

echo json_encode($response);
?>