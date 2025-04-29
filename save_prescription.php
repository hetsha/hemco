<?php
include('include/db_connect.php');
session_start();

$user_id = $_SESSION['user_id']; // ensure session is set
$frame_id = $_POST['frame_id']; // The selected frame ID
$lens_id = $_POST['lens_id'];   // The selected lens ID
$quantity = $_POST['quantity']; // Quantity selected
$prescription_id = $_POST['prescription_id']; // Prescription ID (from the previous insert)

if ($user_id && $frame_id && $lens_id && $quantity > 0) {
    // Step 1: Check if the user already has a cart
    $cartQuery = "SELECT * FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($cartQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cartResult = $stmt->get_result();

    if ($cartResult->num_rows > 0) {
        // User already has a cart, get the cart_id
        $cart = $cartResult->fetch_assoc();
        $cart_id = $cart['cart_id'];
    } else {
        // No cart, create a new cart
        $cartInsertQuery = "INSERT INTO cart (user_id) VALUES (?)";
        $stmt = $conn->prepare($cartInsertQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $cart_id = $stmt->insert_id; // Get the new cart_id
    }

    // Step 2: Add the item to the cart_items table
    $priceQuery = "SELECT price FROM frames WHERE frame_id = ?";
    $stmt = $conn->prepare($priceQuery);
    $stmt->bind_param("i", $frame_id);
    $stmt->execute();
    $priceResult = $stmt->get_result();
    $priceRow = $priceResult->fetch_assoc();
    $frame_price = $priceRow['price'];

    $lensPriceQuery = "SELECT price FROM lens WHERE lens_id = ?";
    $stmt = $conn->prepare($lensPriceQuery);
    $stmt->bind_param("i", $lens_id);
    $stmt->execute();
    $lensResult = $stmt->get_result();
    $lensRow = $lensResult->fetch_assoc();
    $lens_price = $lensRow['price'];

    // Calculate total price for the item
    $total_price = ($frame_price + $lens_price) * $quantity;

    // Step 3: Insert into cart_items table
    $cartItemQuery = "INSERT INTO cart_items (cart_id, frame_id, lens_id, prescription_id, quantity, price)
                      VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($cartItemQuery);
    $stmt->bind_param("iiiiid", $cart_id, $frame_id, $lens_id, $prescription_id, $quantity, $total_price);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Item added to cart successfully!";
    } else {
        echo "Failed to add item to cart.";
    }
} else {
    echo "Missing required fields.";
}
?>
