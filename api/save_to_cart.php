<?php
header('Content-Type: application/json');
include('../include/db_connect.php');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['frame_id']) || !isset($data['lens_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

try {
    // Begin transaction
    $conn->begin_transaction();

    // Get or create cart
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $cart_id = isset($_SESSION['cart_id']) ? $_SESSION['cart_id'] : null;

    // If no cart_id in session, create a new cart
    if (!$cart_id) {
        // Create new cart
        $create_cart = "INSERT INTO cart (user_id) VALUES (?)";
        $stmt = $conn->prepare($create_cart);
        $stmt->bind_param("i", $user_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Error creating cart: " . $stmt->error);
        }
        
        $cart_id = $conn->insert_id;
        $_SESSION['cart_id'] = $cart_id;
    }

    // Get frame price
    $frame_query = "SELECT price FROM frames WHERE frame_id = ?";
    $stmt = $conn->prepare($frame_query);
    $stmt->bind_param("i", $data['frame_id']);
    $stmt->execute();
    $frame_result = $stmt->get_result();
    
    if ($frame_result->num_rows === 0) {
        throw new Exception("Frame not found");
    }
    
    $frame_price = $frame_result->fetch_assoc()['price'];

    // Get lens price
    $lens_query = "SELECT price FROM lens WHERE lens_id = ?";
    $stmt = $conn->prepare($lens_query);
    $stmt->bind_param("i", $data['lens_id']);
    $stmt->execute();
    $lens_result = $stmt->get_result();
    
    if ($lens_result->num_rows === 0) {
        throw new Exception("Lens not found");
    }
    
    $lens_price = $lens_result->fetch_assoc()['price'];
    
    // Calculate total price
    $total_price = $frame_price + $lens_price;

    // Save prescription if provided
    $prescription_id = null;
    if (isset($data['prescription'])) {
        $save_prescription = "INSERT INTO prescription (user_id, left_eye_sph, right_eye_sph, left_eye_cyl, right_eye_cyl, axis, addition) 
                             VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($save_prescription);
        $stmt->bind_param("issssss", 
            $user_id,
            $data['prescription']['left_eye_sph'],
            $data['prescription']['right_eye_sph'],
            $data['prescription']['left_eye_cyl'],
            $data['prescription']['right_eye_cyl'],
            $data['prescription']['axis'],
            $data['prescription']['addition']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error saving prescription: " . $stmt->error);
        }
        $prescription_id = $conn->insert_id;
    }

    // Add item to cart
    $add_item = "INSERT INTO cart_items (cart_id, frame_id, lens_id, prescription_id, quantity, price) VALUES (?, ?, ?, ?, 1, ?)";
    $stmt = $conn->prepare($add_item);
    $stmt->bind_param("iiiid", $cart_id, $data['frame_id'], $data['lens_id'], $prescription_id, $total_price);
    
    if (!$stmt->execute()) {
        throw new Exception("Error adding item to cart: " . $stmt->error);
    }

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'cart_id' => $cart_id,
        'message' => 'Item added to cart successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn->inTransaction()) {
        $conn->rollback();
    }
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}
?>
