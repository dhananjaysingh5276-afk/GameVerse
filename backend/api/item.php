<?php
require_once __DIR__ . '/cors.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'PUT' || $method === 'DELETE') {
    if (!isAdmin()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $data = is_array($data) ? $data : [];

    $id = isset($_GET['id']) ? intval($_GET['id']) : (isset($data['id']) ? intval($data['id']) : 0);
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
        exit();
    }

    if ($method === 'DELETE') {
        $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to delete item']);
        }
        exit();
    }

    // PUT: merge submitted fields with the existing row so untouched
    // fields (e.g. stock) are preserved instead of being reset.
    $stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();

    if (!$existing) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Item not found']);
        exit();
    }

    $merged = array_merge($existing, $data);

    $name = $merged['name'] ?? '';
    $game_id = isset($merged['game_id']) ? intval($merged['game_id']) : 0;
    $category = $merged['category'] ?? '';
    $price = isset($merged['price']) ? floatval($merged['price']) : 0;
    $image_url = $merged['image_url'] ?? null;
    $skill_value = $merged['skill_value'] ?? null;
    $attribute = $merged['attribute'] ?? null;
    $special_effect = $merged['special_effect'] ?? null;
    $description = $merged['description'] ?? null;
    $stock = isset($merged['stock']) ? intval($merged['stock']) : intval($existing['stock']);

    if (empty($name)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Item name is required']);
        exit();
    }

    if ($game_id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Please select a valid game']);
        exit();
    }

    $query = "UPDATE items SET game_id=?, name=?, category=?, price=?, image_url=?, skill_value=?, attribute=?, special_effect=?, description=?, stock=? WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issdsssssii", $game_id, $name, $category, $price, $image_url, $skill_value, $attribute, $special_effect, $description, $stock, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update item']);
    }
    exit();
}

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid item id']);
    exit();
}

$conn->set_charset('utf8mb4');

$query = "SELECT i.*, g.name AS game_name, g.cover_image AS game_cover
          FROM items i
          JOIN games g ON i.game_id = g.id
          WHERE i.id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if (!$item) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Item not found']);
    exit();
}

echo json_encode(['success' => true, 'item' => $item]);
?>
