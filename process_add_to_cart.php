<?php
include('include/db_connect.php');
session_start();

$user_id = $_SESSION['user_id']; // Make sure user is logged in
$frame_id = $_POST['frame_id'];
$lens_id = $_POST['lens_id'];
$quantity = $_POST['quantity'];
$prescription = $_POST['prescription'];

// 1. Insert prescription
$stmt = $conn->prepare("INSERT INTO prescription (user_id, left_eye_sph, right_eye_sph, left_eye_cyl, right_eye_cyl, axis, addition) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issssss", $user_id, $prescription['left_eye_sph'], $prescription['right_eye_sph'], $prescription['left_eye_cyl'], $prescription['right_eye_cyl'], $prescription['axis'], $prescription['addition']);
$stmt->execute();
$prescription_id = $conn->insert_id;

// 2. Get/Create user's active cart
$cartQuery = $conn->query("SELECT cart_id FROM cart WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 1");
if ($cartQuery->num_rows > 0) {
    $cart_id = $cartQuery->fetch_assoc()['cart_id'];
} else {
    $conn->query("INSERT INTO cart (user_id) VALUES ($user_id)");
    $cart_id = $conn->insert_id;
}

// 3. Calculate price (you'd fetch price from `lens` table here)
$price = 1200; // Example value

// 4. Insert into cart_items
$stmt = $conn->prepare("INSERT INTO cart_items (cart_id, frame_id, lens_id, prescription_id, quantity, price) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiiiid", $cart_id, $frame_id, $lens_id, $prescription_id, $quantity, $price);
$stmt->execute();

echo "success";
?>
