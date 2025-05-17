<?php
// remove_item.php
include('include/db_connect.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login-register.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$cart_item_id = isset($_GET['cart_item_id']) ? intval($_GET['cart_item_id']) : 0;

if ($cart_item_id > 0) {
    // Get the user's cart_ids
    $cart_ids = [];
    $cart_query = mysqli_query($conn, "SELECT cart_id FROM cart WHERE user_id = $user_id");
    while ($row = mysqli_fetch_assoc($cart_query)) {
        $cart_ids[] = $row['cart_id'];
    }
    $cart_ids_str = implode(',', $cart_ids ?: [0]);

    // Only allow delete if the item belongs to the user's cart(s)
    $check = mysqli_query($conn, "SELECT cart_id FROM cart_items WHERE cart_item_id = $cart_item_id");
    $item = mysqli_fetch_assoc($check);
    if ($item && in_array($item['cart_id'], $cart_ids)) {
        mysqli_query($conn, "DELETE FROM cart_items WHERE cart_item_id = $cart_item_id");
    }
}

header('Location: cart.php');
exit();
