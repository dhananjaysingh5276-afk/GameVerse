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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = trim($data['username'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    
    if (empty($username) || empty($email) || empty($password)) {
        $response['message'] = 'All fields are required';
        echo json_encode($response);
        exit();
    }
    
    if (strlen($password) < 6) {
        $response['message'] = 'Password must be at least 6 characters';
        echo json_encode($response);
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid email address';
        echo json_encode($response);
        exit();
    }
    
    // Check if user exists
    $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        $response['message'] = 'Username or email already exists';
        echo json_encode($response);
        exit();
    }
    
    // Insert user
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $insert = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    $insert->bind_param("sss", $username, $email, $password_hash);
    
    if ($insert->execute()) {
        $response['success'] = true;
        $response['message'] = 'Registration successful';
    } else {
        $response['message'] = 'Registration failed: ' . $conn->error;
    }
}

echo json_encode($response);
?>