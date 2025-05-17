<?php
session_start();
include('include/db_connect.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$shipping_cost = isset($_SESSION['shipping_cost']) ? floatval($_SESSION['shipping_cost']) : 0;

// Get the latest cart for the user that has items
$cart_query = mysqli_query($conn, "SELECT c.cart_id FROM cart c
    WHERE c.user_id = $user_id
    AND EXISTS (SELECT 1 FROM cart_items ci WHERE ci.cart_id = c.cart_id)
    ORDER BY c.cart_id DESC LIMIT 1");
$cart = mysqli_fetch_assoc($cart_query);
$cart_id = $cart['cart_id'] ?? 0;
if (!$cart_id) {
    echo json_encode(['success' => false, 'message' => 'No cart with items found for user']);
    exit();
}

// Sanitize POST data
$name = mysqli_real_escape_string($conn, $_REQUEST['name'] ?? '');
$address = mysqli_real_escape_string($conn, $_REQUEST['address'] ?? '');
$city = mysqli_real_escape_string($conn, $_REQUEST['city'] ?? '');
$country = mysqli_real_escape_string($conn, $_REQUEST['country'] ?? '');
$postcode = mysqli_real_escape_string($conn, $_REQUEST['postcode'] ?? '');
$phone = mysqli_real_escape_string($conn, $_REQUEST['phone'] ?? '');
$email = mysqli_real_escape_string($conn, $_REQUEST['email'] ?? '');
$order_note = mysqli_real_escape_string($conn, $_REQUEST['order_note'] ?? '');
$payment_id = mysqli_real_escape_string($conn, $_REQUEST['payment_id'] ?? '');

$shipping_address = "$address, $city, $postcode, $country";

// Calculate total again
$total = 0;
$items_query = mysqli_query($conn, "SELECT * FROM cart_items WHERE cart_id = $cart_id");
$cart_items = [];
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
    $cart_items[] = $item;
}
if (count($cart_items) === 0) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit();
}
$grand_total = $total + $shipping_cost;

// Enable MySQLi error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn->begin_transaction();

    // Insert order
    $order_sql = "INSERT INTO orders (user_id, total_price, order_note) VALUES ($user_id, $grand_total, '$order_note')";
    mysqli_query($conn, $order_sql);
    $order_id = mysqli_insert_id($conn);

    // Insert shipping
    $shipping_sql = "INSERT INTO shipping (order_id, shipping_address, name, phone, email) VALUES ($order_id, '$shipping_address', '$name', '$phone', '$email')";
    mysqli_query($conn, $shipping_sql);

    // Insert items
    foreach ($cart_items as $item) {
        $frame_id = $item['frame_id'] ? intval($item['frame_id']) : 'NULL';
        $lens_id = $item['lens_id'] ? intval($item['lens_id']) : 'NULL';
        $prescription_id = $item['prescription_id'] ? intval($item['prescription_id']) : 'NULL';
        $qty = intval($item['quantity']);
        $price = 0;
        if ($frame_id !== 'NULL') {
            $fp = mysqli_query($conn, "SELECT price FROM frames WHERE frame_id = $frame_id");
            $f = mysqli_fetch_assoc($fp);
            $price += $f['price'] ?? 0;
        }
        if ($lens_id !== 'NULL') {
            $lp = mysqli_query($conn, "SELECT price FROM lens WHERE lens_id = $lens_id");
            $l = mysqli_fetch_assoc($lp);
            $price += $l['price'] ?? 0;
        }
        $item_sql = "INSERT INTO order_items (order_id, frame_id, lens_id, prescription_id, quantity, price)
            VALUES ($order_id, $frame_id, $lens_id, $prescription_id, $qty, $price)";
        mysqli_query($conn, $item_sql);
    }

    // Insert payment
    $payment_sql = "INSERT INTO payments (order_id, amount, payment_method, status, payment_id)
        VALUES ($order_id, $grand_total, 'razorpay', 'completed', '$payment_id')";
    mysqli_query($conn, $payment_sql);

    // Clear cart
    mysqli_query($conn, "DELETE FROM cart_items WHERE cart_id = $cart_id");
    mysqli_query($conn, "DELETE FROM cart WHERE cart_id = $cart_id");

    $conn->commit();
    unset($_SESSION['shipping_cost']);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Transaction failed', 'error' => $e->getMessage()]);
}
?>
