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

    // Get or create cart (ALWAYS use only one cart per user)
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    // Find existing cart for user
    $cart_id = null;
    $cart_query = $conn->prepare("SELECT cart_id FROM cart WHERE user_id = ? LIMIT 1");
    $cart_query->bind_param("i", $user_id);
    $cart_query->execute();
    $cart_result = $cart_query->get_result();
    if ($cart_row = $cart_result->fetch_assoc()) {
        $cart_id = $cart_row['cart_id'];
    } else {
        // Create new cart if none exists
        $create_cart = "INSERT INTO cart (user_id) VALUES (?)";
        $stmt = $conn->prepare($create_cart);
        $stmt->bind_param("i", $user_id);
        if (!$stmt->execute()) {
            throw new Exception("Error creating cart: " . $stmt->error);
        }
        $cart_id = $conn->insert_id;
    }
    $_SESSION['cart_id'] = $cart_id;

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

    // Save prescription if provided and at least one field is not empty
    $prescription_id = null;
    $presc = $data['prescription'] ?? [];
    $has_presc = false;
    foreach ([
        'left_eye_sph', 'right_eye_sph', 'left_eye_cyl', 'right_eye_cyl', 'axis', 'addition'
    ] as $field) {
        if (!empty($presc[$field])) {
            $has_presc = true;
            break;
        }
    }
    if ($has_presc) {
        $save_prescription = "INSERT INTO prescription (user_id, left_eye_sph, right_eye_sph, left_eye_cyl, right_eye_cyl, axis, addition)
                             VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($save_prescription);
        $stmt->bind_param("issssss",
            $user_id,
            $presc['left_eye_sph'],
            $presc['right_eye_sph'],
            $presc['left_eye_cyl'],
            $presc['right_eye_cyl'],
            $presc['axis'],
            $presc['addition']
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
    if (method_exists($conn, 'rollback')) {
        $conn->rollback();
    }
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
