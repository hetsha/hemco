<?php
include('include/db_connect.php');
include('include/header.php');

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header('Location: accounts.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = (int)$_GET['order_id'];
// Fetch order
$order_sql = "SELECT * FROM orders WHERE order_id = $order_id AND user_id = $user_id";
$order_result = mysqli_query($conn, $order_sql);
$order = mysqli_fetch_assoc($order_result);
if (!$order) {
    echo '<p>Order not found.</p>';
    exit;
}
// Fetch order items
$items_sql = "SELECT oi.*, f.name AS frame_name, l.type AS lens_type FROM order_items oi LEFT JOIN frames f ON oi.frame_id = f.frame_id LEFT JOIN lens l ON oi.lens_id = l.lens_id WHERE oi.order_id = $order_id";
$items_result = mysqli_query($conn, $items_sql);
$order_items = mysqli_fetch_all($items_result, MYSQLI_ASSOC);
// Fetch shipping
$shipping_sql = "SELECT * FROM shipping WHERE order_id = $order_id";
$shipping_result = mysqli_query($conn, $shipping_sql);
$shipping = mysqli_fetch_assoc($shipping_result);
?>
<body>
<?php include('include/navbar.php'); ?>
<main class="main">
    <div class="blank"><br><br></div>
    <section class="section--lg container">
    <h2>Order #<?= $order['order_id'] ?></h2>
    <p><strong>Date:</strong> <?= date('d M Y', strtotime($order['created_at'])) ?></p>
    <p><strong>Status:</strong> <?= ucfirst($order['status']) ?></p>
    <p><strong>Total:</strong> ₹<?= number_format($order['total_price'], 2) ?></p>
    <h3>Items</h3>
    <table class="placed__order-table">
      <thead>
        <tr><th>Frame</th><th>Lens</th><th>Qty</th><th>Price</th><th>Total</th></tr>
      </thead>
      <tbody>
        <?php foreach ($order_items as $item): ?>
        <tr>
          <td><?= htmlspecialchars($item['frame_name'] ?? '-') ?></td>
          <td><?= htmlspecialchars($item['lens_type'] ?? '-') ?></td>
          <td><?= (int)$item['quantity'] ?></td>
          <td>₹<?= number_format($item['price'], 2) ?></td>
          <td>₹<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php if ($shipping): ?>
    <h3>Shipping Address</h3>
    <address>
      <?= htmlspecialchars($shipping['name']) ?><br>
      <?= htmlspecialchars($shipping['shipping_address']) ?><br>
      <?= htmlspecialchars($shipping['city']) ?>, <?= htmlspecialchars($shipping['state']) ?>, <?= htmlspecialchars($shipping['pincode']) ?><br>
      <?= htmlspecialchars($shipping['country']) ?><br>
      <?= htmlspecialchars($shipping['phone']) ?><br>
      <?= htmlspecialchars($shipping['email']) ?>
    </address>
    <?php endif; ?>
    <a href="accounts.php#orders" class="btn btn--md">Back to Orders</a>
  </section>
</main>
<?php include('include/footer.php'); ?>
</body>
</html>
