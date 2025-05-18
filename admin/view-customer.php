<?php
include 'config/db.php'; // DB connection
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Check if 'id' is passed via URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
  $customer_id = intval($_GET['id']);

  // Fetch customer details
  $query = "
    SELECT
      user_id, name, email, phone, created_at
    FROM user
    WHERE user_id = $customer_id
  ";
  $customer_result = mysqli_query($conn, $query);
  $customer = mysqli_fetch_assoc($customer_result);

  // Fetch most recent shipping address for this user
  $shipping_query = "
    SELECT s.shipping_address, s.city, s.state, s.country, s.pincode
    FROM shipping s
    WHERE s.order_id = (
      SELECT o.order_id FROM orders o WHERE o.user_id = $customer_id ORDER BY o.created_at DESC LIMIT 1
    )
    ORDER BY s.created_at DESC LIMIT 1
  ";
  $shipping_result = mysqli_query($conn, $shipping_query);
  $shipping = mysqli_fetch_assoc($shipping_result);

  // Fetch customer orders and shipping details per order
  $order_query = "
    SELECT
      o.order_id,
      o.created_at AS order_date,
      o.status,
      oi.quantity,
      oi.price,
      oi.frame_id,
      oi.lens_id,
      f.name AS frame_name,
      l.type AS lens_type,
      s.shipping_address,
      s.city AS shipping_city,
      s.state AS shipping_state,
      s.country AS shipping_country,
      s.pincode AS shipping_pincode
    FROM orders o
    JOIN order_items oi ON oi.order_id = o.order_id
    LEFT JOIN frames f ON oi.frame_id = f.frame_id
    LEFT JOIN lens l ON oi.lens_id = l.lens_id
    LEFT JOIN shipping s ON s.order_id = o.order_id
    WHERE o.user_id = $customer_id
    ORDER BY o.created_at DESC
  ";
  $orders_result = mysqli_query($conn, $order_query);
} else {
  echo "Invalid customer ID.";
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>View Customer | <?= htmlspecialchars($customer['name']) ?></title>
  <?php include 'include/header.php'; ?>
</head>

<body class="bg-blue-50 text-gray-800 dark:bg-gray-900 dark:text-gray-100">
  <div class="flex h-screen overflow-hidden">
    <?php include 'include/navbar.php'; ?>

    <!-- Main content -->
    <div class="flex-1 flex flex-col">
      <header class="flex justify-between items-center bg-white dark:bg-gray-800 px-4 py-3 shadow-md">
        <div class="md:hidden">
          <button id="menu-btn" class="text-blue-500 dark:text-blue-300"><i data-lucide="menu"></i></button>
        </div>
        <input type="text" placeholder="Search..." class="px-3 py-1 rounded bg-blue-100 dark:bg-gray-700">
        <div class="flex items-center space-x-4">
          <button class="text-blue-500 dark:text-blue-300"><i data-lucide="bell"></i></button>
          <div class="w-8 h-8 bg-blue-300 dark:bg-gray-600 rounded-full flex items-center justify-center font-bold">S</div>
          <button id="theme-toggle" class="text-blue-500 dark:text-blue-300" title="Toggle Theme">
            <i data-lucide="sun"></i>
          </button>
        </div>
      </header>

      <!-- Customer Details -->
      <main class="p-4 overflow-auto">
        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
          <h3 class="text-xl font-semibold mb-4">Customer Information</h3>
          <p><strong>Customer ID:</strong> C<?= str_pad($customer['user_id'], 3, '0', STR_PAD_LEFT) ?></p>
          <p><strong>Name:</strong> <?= htmlspecialchars($customer['name']) ?></p>
          <p><strong>Email:</strong> <?= htmlspecialchars($customer['email']) ?></p>
          <p><strong>Phone:</strong> <?= htmlspecialchars($customer['phone'] ?? 'N/A') ?></p>
          <p><strong>Address:</strong> <?= htmlspecialchars($shipping['shipping_address'] ?? '') ?><?= isset($shipping['shipping_address']) ? ',' : '' ?> <?= htmlspecialchars($shipping['city'] ?? '') ?><?= isset($shipping['city']) ? ',' : '' ?> <?= htmlspecialchars($shipping['state'] ?? '') ?><?= isset($shipping['state']) ? ',' : '' ?> <?= htmlspecialchars($shipping['country'] ?? '') ?><?= isset($shipping['country']) ? ',' : '' ?> <?= htmlspecialchars($shipping['pincode'] ?? '') ?></p>
          <p><strong>Joined:</strong> <?= date('M d, Y', strtotime($customer['created_at'])) ?></p>

          <h3 class="text-xl font-semibold mt-6 mb-4">Order History</h3>
          <?php if (mysqli_num_rows($orders_result) > 0): ?>
            <div class="overflow-x-auto">
              <table class="min-w-full text-left text-sm">
                <thead>
                  <tr class="text-gray-500 dark:text-gray-300">
                    <th class="p-2">Order ID</th>
                    <th class="p-2">Date</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Products</th>
                    <th class="p-2">Quantity</th>
                    <th class="p-2">Total Price</th>
                    <th class="p-2">Shipping Address</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($order = mysqli_fetch_assoc($orders_result)) { ?>
                    <tr class="border-t border-blue-100 dark:border-gray-700">
                      <td class="p-2">#<?= str_pad($order['order_id'], 5, '0', STR_PAD_LEFT); ?></td>
                      <td class="p-2"><?= date('M d, Y', strtotime($order['order_date'])); ?></td>
                      <td class="p-2"><?= htmlspecialchars($order['status']); ?></td>
                      <td class="p-2">
                        <?php
                          if ($order['frame_name']) {
                            echo 'Frame: ' . htmlspecialchars($order['frame_name']);
                          } elseif ($order['lens_type']) {
                            echo 'Lens: ' . htmlspecialchars($order['lens_type']);
                          } else {
                            echo '-';
                          }
                        ?>
                      </td>
                      <td class="p-2"><?= $order['quantity']; ?></td>
                      <td class="p-2"><?= '₹' . number_format($order['price'] * $order['quantity'], 2); ?></td>
                      <td class="p-2"><?= htmlspecialchars($order['shipping_address']); ?><?= $order['shipping_address'] ? ',' : '' ?> <?= htmlspecialchars($order['shipping_city']); ?><?= $order['shipping_city'] ? ',' : '' ?> <?= htmlspecialchars($order['shipping_state']); ?><?= $order['shipping_state'] ? ',' : '' ?> <?= htmlspecialchars($order['shipping_country']); ?><?= $order['shipping_country'] ? ',' : '' ?> <?= htmlspecialchars($order['shipping_pincode']); ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p class="text-center py-4 text-gray-500">No orders found for this customer.</p>
          <?php endif; ?>
        </div>
      </main>
    </div>
  </div>

  <script src="assets/main.js"></script>
  <?php include 'include/footer.php'; ?>
</body>

</html>
