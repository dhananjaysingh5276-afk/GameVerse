<?php
// Include CORS handler
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

function readItemsBody() {
    return json_decode(file_get_contents('php://input'), true);
}

// ============================================
// ADD ITEM (POST) - admin only
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isAdmin()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }

    $data = readItemsBody();

    $name = $data['name'] ?? '';
    $game_id = isset($data['game_id']) ? intval($data['game_id']) : 0;
    $category = $data['category'] ?? '';
    $price = isset($data['price']) ? floatval($data['price']) : 0;
    $image_url = $data['image_url'] ?? null;
    $skill_value = isset($data['skill_value']) ? $data['skill_value'] : null;
    $attribute = $data['attribute'] ?? null;
    $special_effect = $data['special_effect'] ?? null;
    $description = $data['description'] ?? null;
    $stock = isset($data['stock']) ? intval($data['stock']) : 0;

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

    $query = "INSERT INTO items (game_id, name, category, price, image_url, skill_value, attribute, special_effect, description, stock)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issdsssssi", $game_id, $name, $category, $price, $image_url, $skill_value, $attribute, $special_effect, $description, $stock);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item added successfully', 'id' => $conn->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to add item']);
    }
    exit();
}

// ============================================
// UPDATE ITEM (PUT) - admin only
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (!isAdmin()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
        exit();
    }

    $data = readItemsBody();

    $name = $data['name'] ?? '';
    $game_id = isset($data['game_id']) ? intval($data['game_id']) : 0;
    $category = $data['category'] ?? '';
    $price = isset($data['price']) ? floatval($data['price']) : 0;
    $image_url = $data['image_url'] ?? null;
    $skill_value = isset($data['skill_value']) ? $data['skill_value'] : null;
    $attribute = $data['attribute'] ?? null;
    $special_effect = $data['special_effect'] ?? null;
    $description = $data['description'] ?? null;
    $stock = isset($data['stock']) ? intval($data['stock']) : 0;

    if (empty($name)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Item name is required']);
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

// ============================================
// DELETE ITEM (DELETE) - admin only
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!isAdmin()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
        exit();
    }

    $query = "DELETE FROM items WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete item']);
    }
    exit();
}

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