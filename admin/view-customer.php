<?php
include 'config/db.php'; // DB connection
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Check if 'id' is passed via URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
  $customer_id = $_GET['id'];

  // Fetch customer details
  $query = "
    SELECT
      id, username, email, phone_number, created_at, address_line_1, address_line_2, city, state, country, postal_code
    FROM users
    WHERE id = $customer_id
  ";
  $customer_result = mysqli_query($conn, $query);
  $customer = mysqli_fetch_assoc($customer_result);

  // Fetch customer orders and address details
  $order_query = "
    SELECT
      o.id as order_id,
      o.created_at AS order_date,
      o.status,
      p.name AS product_name,
      oi.quantity,
      oi.price,
      u.address_line_1,
      u.address_line_2,
      u.city,
      u.state,
      u.country,
      u.postal_code,
      u.phone_number
    FROM orders o
    JOIN order_items oi ON oi.order_id = o.id
    JOIN products p ON oi.product_id = p.id
    JOIN users u ON o.user_id = u.id
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
  <title>View Customer | <?= htmlspecialchars($customer['username']) ?></title>
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
          <p><strong>Customer ID:</strong> C<?= str_pad($customer['id'], 3, '0', STR_PAD_LEFT) ?></p>
          <p><strong>Name:</strong> <?= htmlspecialchars($customer['username']) ?></p>
          <p><strong>Email:</strong> <?= htmlspecialchars($customer['email']) ?></p>
          <p><strong>Phone:</strong> <?= htmlspecialchars($customer['phone_number'] ?? 'N/A') ?></p>
          <p><strong>Address:</strong> <?= htmlspecialchars($customer['address_line_1']) ?>, <?= htmlspecialchars($customer['address_line_2']) ?>, <?= htmlspecialchars($customer['city']) ?>, <?= htmlspecialchars($customer['state']) ?>, <?= htmlspecialchars($customer['country']) ?>, <?= htmlspecialchars($customer['postal_code']) ?></p>
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
                  </tr>
                </thead>
                <tbody>
                  <?php while ($order = mysqli_fetch_assoc($orders_result)) { ?>
                    <tr class="border-t border-blue-100 dark:border-gray-700">
                      <td class="p-2">#<?= str_pad($order['order_id'], 5, '0', STR_PAD_LEFT); ?></td>
                      <td class="p-2"><?= date('M d, Y', strtotime($order['order_date'])); ?></td>
                      <td class="p-2"><?= htmlspecialchars($order['status']); ?></td>
                      <td class="p-2"><?= htmlspecialchars($order['product_name']); ?></td>
                      <td class="p-2"><?= $order['quantity']; ?></td>
                      <td class="p-2"><?= '₹' . number_format($order['price'] * $order['quantity'], 2); ?></td>
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
