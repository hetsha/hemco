<?php
include 'config/db.php'; // Include your DB connection

$order_id = $_GET['order_id'] ?? 0;

$sql = "SELECT
            orders.id AS order_id,
            orders.created_at,
            orders.status,
            orders.total_amount,
            orders.shipping_cost,
            users.username,
            users.email,
            users.phone_number
        FROM orders
        JOIN users ON orders.user_id = users.id
        WHERE orders.id = $order_id";

$result = mysqli_query($conn, $sql);
$order = mysqli_fetch_assoc($result);

if ($order) {
    $phone_number = $order['phone_number'];
    $message = urlencode("Hello, {$order['username']}! Your order from Padmavati Gruh Udhyog with ID #{$order['order_id']} is {$order['status']}. Total Amount: ₹" . ($order['total_amount'] + $order['shipping_cost']) . ". Thank you for shopping with us. Please pay the amount to get delivery.");

    // WhatsApp message link
    $whatsapp_url = "https://wa.me/{$phone_number}?text={$message}";

    // View Order link
    $view_order_url = "view-order.php?order_id={$order_id}";

    // Display the link to view the order
    echo "<p><a href='{$view_order_url}' target='_blank'>Click here to view the order details</a></p>";

    // Display the static QR code (assuming the image is uploaded to the server)
    echo "<p>Scan this QR code to send the WhatsApp message directly:</p>";
    echo "<img src='path/to/your/whatsapp-qr-code.png' alt='WhatsApp QR Code' width='200' height='200'/>";

    // Redirect to WhatsApp Web
    header("Location: $whatsapp_url");
    exit;
} else {
    echo "Order not found.";
}
?>
