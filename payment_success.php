<?php
session_start();
include('include/db_connect.php');

$user_id = $_SESSION['user_id'] ?? 0;
$cart_id = $_SESSION['cart_id'] ?? 0;
$shipping_cost = $_SESSION['shipping_cost'] ?? 0;

$name = mysqli_real_escape_string($conn, $_POST['name']);
$address = mysqli_real_escape_string($conn, $_POST['address']);
$city = mysqli_real_escape_string($conn, $_POST['city']);
$country = mysqli_real_escape_string($conn, $_POST['country']);
$postcode = mysqli_real_escape_string($conn, $_POST['postcode']);
$phone = mysqli_real_escape_string($conn, $_POST['phone']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$order_note = mysqli_real_escape_string($conn, $_POST['order_note']);
$payment_id = mysqli_real_escape_string($conn, $_POST['payment_id']);

$shipping_address = "$address, $city, $postcode, $country";

// Calculate total again
$total = 0;
$items_query = mysqli_query($conn, "SELECT * FROM cart_items WHERE cart_id = $cart_id");
while ($item = mysqli_fetch_assoc($items_query)) {
  $frame_price = 0;
  $lens_price = 0;
  $qty = $item['quantity'];

  if ($item['frame_id']) {
    $fp = mysqli_query($conn, "SELECT price FROM frames WHERE frame_id = {$item['frame_id']}");
    $f = mysqli_fetch_assoc($fp);
    $frame_price = $f['price'] ?? 0;
  }

  if ($item['lens_id']) {
    $lp = mysqli_query($conn, "SELECT price FROM lens WHERE lens_id = {$item['lens_id']}");
    $l = mysqli_fetch_assoc($lp);
    $lens_price = $l['price'] ?? 0;
  }

  $total += ($frame_price + $lens_price) * $qty;
}

$grand_total = $total + $shipping_cost;

// Insert order
mysqli_query($conn, "INSERT INTO orders (user_id, total_price, order_note) VALUES ($user_id, $grand_total, '$order_note')");
$order_id = mysqli_insert_id($conn);

// Insert shipping
mysqli_query($conn, "INSERT INTO shipping (order_id, shipping_address) VALUES ($order_id, '$shipping_address')");

// Insert items
mysqli_data_seek($items_query, 0); // reset pointer
while ($item = mysqli_fetch_assoc($items_query)) {
  $frame_id = $item['frame_id'] ?? "NULL";
  $lens_id = $item['lens_id'] ?? "NULL";
  $prescription_id = $item['prescription_id'] ?? "NULL";
  $qty = $item['quantity'];

  $price = 0;
  if ($frame_id) {
    $fp = mysqli_query($conn, "SELECT price FROM frames WHERE frame_id = $frame_id");
    $f = mysqli_fetch_assoc($fp);
    $price += $f['price'] ?? 0;
  }

  if ($lens_id) {
    $lp = mysqli_query($conn, "SELECT price FROM lens WHERE lens_id = $lens_id");
    $l = mysqli_fetch_assoc($lp);
    $price += $l['price'] ?? 0;
  }

  mysqli_query($conn, "INSERT INTO order_items (order_id, frame_id, lens_id, prescription_id, quantity, price)
    VALUES ($order_id, $frame_id, $lens_id, $prescription_id, $qty, $price)");
}

// Insert payment
mysqli_query($conn, "INSERT INTO payments (order_id, amount, payment_method, status, payment_id)
  VALUES ($order_id, $grand_total, 'razorpay', 'completed', '$payment_id')");

// Clear cart
mysqli_query($conn, "DELETE FROM cart_items WHERE cart_id = $cart_id");

echo json_encode(['success' => true]);
?>
