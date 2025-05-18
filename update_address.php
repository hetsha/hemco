<?php
include('include/db_connect.php');
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login-register.php');
    exit;
}
$user_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $shipping_address = mysqli_real_escape_string($conn, $_POST['shipping_address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    // Find latest order for user
    $order_sql = "SELECT order_id FROM orders WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 1";
    $order_result = mysqli_query($conn, $order_sql);
    $order = mysqli_fetch_assoc($order_result);
    if ($order) {
        $order_id = $order['order_id'];
        $sql = "UPDATE shipping SET name='$name', shipping_address='$shipping_address', city='$city', state='$state', pincode='$pincode', country='$country', phone='$phone', email='$email' WHERE order_id=$order_id";
        mysqli_query($conn, $sql);
    }
    header('Location: accounts.php#address');
    exit;
}
// Show a simple form if needed (optional)
header('Location: accounts.php#address');
exit;
