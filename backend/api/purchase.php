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

if (!isLoggedIn()) {
    $response['message'] = 'Please login first';
    echo json_encode($response);
    exit();
}

if (isAdmin()) {
    $response['message'] = 'Admins cannot purchase items';
    echo json_encode($response);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$item_id = intval($data['item_id'] ?? 0);

if ($item_id <= 0) {
    $response['message'] = 'Invalid item';
    echo json_encode($response);
    exit();
}

$user_id = $_SESSION['user_id'];

mysqli_begin_transaction($conn);

try {
    // Get user balance
    $user_query = "SELECT coins FROM users WHERE id = ? FOR UPDATE";
    $stmt = $conn->prepare($user_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    if (!$user) {
        throw new Exception("User not found");
    }
    
    // Get item
    $item_query = "SELECT id, name, price, stock FROM items WHERE id = ? FOR UPDATE";
    $stmt = $conn->prepare($item_query);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();
    
    if (!$item) {
        throw new Exception("Item not found");
    }
    
    // Check if already owned
    $check = $conn->prepare("SELECT id FROM inventory WHERE user_id = ? AND item_id = ?");
    $check->bind_param("ii", $user_id, $item_id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        throw new Exception("You already own this item");
    }
    
    if ($item['stock'] <= 0) {
        throw new Exception("Item out of stock");
    }
    
    if ($user['coins'] < $item['price']) {
        throw new Exception("Insufficient coins");
    }
    
    // Deduct coins
    $new_balance = $user['coins'] - $item['price'];
    $update_balance = $conn->prepare("UPDATE users SET coins = ? WHERE id = ?");
    $update_balance->bind_param("ii", $new_balance, $user_id);
    $update_balance->execute();
    
    // Add to inventory
    $add_inventory = $conn->prepare("INSERT INTO inventory (user_id, item_id) VALUES (?, ?)");
    $add_inventory->bind_param("ii", $user_id, $item_id);
    $add_inventory->execute();
    
    // Update stock
    $new_stock = $item['stock'] - 1;
    $update_stock = $conn->prepare("UPDATE items SET stock = ? WHERE id = ?");
    $update_stock->bind_param("ii", $new_stock, $item_id);
    $update_stock->execute();
    
    // Log transaction
    $log = $conn->prepare("INSERT INTO transactions (user_id, item_id, amount, type, description, status) VALUES (?, ?, ?, 'purchase', ?, 'completed')");
    $description = "Purchased: " . $item['name'];
    $log->bind_param("iiis", $user_id, $item_id, $item['price'], $description);
    $log->execute();
    
    mysqli_commit($conn);
    
    $response['success'] = true;
    $response['message'] = "Successfully purchased " . $item['name'];
    $response['new_balance'] = $new_balance;
    
} catch (Exception $e) {
    mysqli_rollback($conn);
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>