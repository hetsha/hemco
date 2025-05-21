<?php
header('Content-Type: application/json');
include('../include/db_connect.php');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Accept both JSON and multipart/form-data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['prescription_image'])) {
    // Multipart/form-data (with file upload)
    $data = $_POST;
    // Parse prescription JSON if present
    if (isset($data['prescription'])) {
        $data['prescription'] = json_decode($data['prescription'], true);
    }
} else {
    // JSON body (frame only)
    $data = json_decode(file_get_contents('php://input'), true);
}

if (!isset($data['frame_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

// If lens_id is not set, treat as frame-only
$frame_only = !isset($data['lens_id']);

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

    $lens_price = 0;
    $prescription_id = null;
    if (!$frame_only) {
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

        // Save prescription if provided and at least one field is not empty
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
        // Prescription image upload logic
        $prescription_image_path = null;
        if (isset($_FILES['prescription_image']) && $_FILES['prescription_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = dirname(__DIR__) . '/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $ext = pathinfo($_FILES['prescription_image']['name'], PATHINFO_EXTENSION);
            $unique_name = time() . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
            $target_path = $upload_dir . $unique_name;
            if (move_uploaded_file($_FILES['prescription_image']['tmp_name'], $target_path)) {
                $prescription_image_path = 'uploads/' . $unique_name;
            }
        }
        if ($has_presc || $prescription_image_path) {
            // Assign each value to a variable to avoid bind_param by-reference error
            $left_eye_sph = $presc['left_eye_sph'] ?? null;
            $right_eye_sph = $presc['right_eye_sph'] ?? null;
            $left_eye_cyl = $presc['left_eye_cyl'] ?? null;
            $right_eye_cyl = $presc['right_eye_cyl'] ?? null;
            $axis = $presc['axis'] ?? null;
            $addition = $presc['addition'] ?? null;
            $save_prescription = "INSERT INTO prescription (user_id, left_eye_sph, right_eye_sph, left_eye_cyl, right_eye_cyl, axis, addition, prescription_image)
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($save_prescription);
            $stmt->bind_param("isssssss",
                $user_id,
                $left_eye_sph,
                $right_eye_sph,
                $left_eye_cyl,
                $right_eye_cyl,
                $axis,
                $addition,
                $prescription_image_path
            );
            if (!$stmt->execute()) {
                throw new Exception("Error saving prescription: " . $stmt->error);
            }
            $prescription_id = $conn->insert_id;
        }
    }

    // Calculate total price
    $total_price = $frame_price + $lens_price;

    // Add item to cart
    $add_item = "INSERT INTO cart_items (cart_id, frame_id, lens_id, prescription_id, quantity, price) VALUES (?, ?, ?, ?, 1, ?)";
    $stmt = $conn->prepare($add_item);
    $lens_id = $frame_only ? null : $data['lens_id'];
    $stmt->bind_param("iiiid", $cart_id, $data['frame_id'], $lens_id, $prescription_id, $total_price);

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
