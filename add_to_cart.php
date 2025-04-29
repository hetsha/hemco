<?php
session_start();
include('include/db_connect.php');

header('Content-Type: application/json');

// Get user ID from session
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Get or create cart
$cartStmt = $conn->prepare("SELECT cart_id FROM cart WHERE user_id = ?");
$cartStmt->bind_param('i', $user_id);
$cartStmt->execute();
$cartResult = $cartStmt->get_result();

if ($cartResult->num_rows > 0) {
    $cart = $cartResult->fetch_assoc();
    $cart_id = $cart['cart_id'];
} else {
    $insertCartStmt = $conn->prepare("INSERT INTO cart (user_id, created_at) VALUES (?, NOW())");
    $insertCartStmt->bind_param('i', $user_id);
    $insertCartStmt->execute();
    $cart_id = $conn->insert_id;
}

// Check what type of update we're doing
$action = $data['action'] ?? '';

switch ($action) {
    case 'add_frame':
        // Add just the frame
        if (!isset($data['frame_id']) || !isset($data['quantity'])) {
            echo json_encode(['success' => false, 'message' => 'Missing frame data']);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO cart_items (cart_id, frame_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $cart_id, $data['frame_id'], $data['quantity']);
        break;

    case 'update_lens':
        // Update existing cart item with lens
        if (!isset($data['cart_item_id']) || !isset($data['lens_id'])) {
            echo json_encode(['success' => false, 'message' => 'Missing lens data']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE cart_items SET lens_id = ? WHERE item_id = ? AND cart_id = ?");
        $stmt->bind_param("iii", $data['lens_id'], $data['cart_item_id'], $cart_id);
        break;

    case 'update_prescription':
        // Update existing cart item with prescription
        if (!isset($data['cart_item_id']) || !isset($data['prescription_id'])) {
            echo json_encode(['success' => false, 'message' => 'Missing prescription data']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE cart_items SET prescription_id = ? WHERE item_id = ? AND cart_id = ?");
        $stmt->bind_param("iii", $data['prescription_id'], $data['cart_item_id'], $cart_id);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
}

if ($stmt->execute()) {
    $item_id = $action === 'add_frame' ? $stmt->insert_id : $data['cart_item_id'];
    echo json_encode([
        'success' => true, 
        'message' => 'Cart updated successfully',
        'cart_item_id' => $item_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
}

$stmt->close();
$cartStmt->close();
if (isset($insertCartStmt)) $insertCartStmt->close();
$conn->close();
?>
