<?php
// Include CORS handler
require_once __DIR__ . '/cors.php';
// CORS Headers
$allowed_origins = ['http://localhost:3000', 'http://localhost:3001'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowed_origins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../bootstrap.php';

// ============================================
// GET ALL GAMES
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT * FROM games ORDER BY created_at DESC";
    $result = $conn->query($query);
    $games = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($games);
    exit();
}

// ============================================
// ADD GAME (POST)
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Only admin can add
    if (!isAdmin()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }

    $data = json_decode(file_get_contents('php://input'), true);
    
    $name = $data['name'] ?? '';
    $developer = $data['developer'] ?? '';
    $genre = $data['genre'] ?? '';
    $description = $data['description'] ?? '';
    $release_date = $data['release_date'] ?? null;
    
    if (empty($name)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Game name is required']);
        exit();
    }

    $query = "INSERT INTO games (name, developer, genre, description, release_date) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $name, $developer, $genre, $description, $release_date);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Game added successfully', 'id' => $conn->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to add game']);
    }
    exit();
}

// ============================================
// UPDATE GAME (PUT)
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Only admin can update
    if (!isAdmin()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid game ID']);
        exit();
    }

    $data = json_decode(file_get_contents('php://input'), true);
    
    $name = $data['name'] ?? '';
    $developer = $data['developer'] ?? '';
    $genre = $data['genre'] ?? '';
    $description = $data['description'] ?? '';
    $release_date = $data['release_date'] ?? null;
    $is_active = isset($data['is_active']) ? intval($data['is_active']) : 1;
    
    if (empty($name)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Game name is required']);
        exit();
    }

    $query = "UPDATE games SET name=?, developer=?, genre=?, description=?, release_date=?, is_active=? WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssii", $name, $developer, $genre, $description, $release_date, $is_active, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Game updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update game']);
    }
    exit();
}

// ============================================
// DELETE GAME (DELETE)
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Only admin can delete
    if (!isAdmin()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid game ID']);
        exit();
    }

    $query = "DELETE FROM games WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Game deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete game']);
    }
    exit();
}
?>