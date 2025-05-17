<?php
include 'config/db.php'; // DB connection
// session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin | Orders</title>
  <?php include 'include/header.php'; ?>
</head>

<body class="bg-blue-50 text-gray-800 dark:bg-gray-900 dark:text-gray-100">
  <div class="flex h-screen overflow-hidden">

    <?php include 'include/navbar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">

      <!-- Top Bar -->
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

      <!-- Orders Table -->
      <main class="p-4 space-y-6 overflow-auto grid grid-cols-1 gap-4">
        <h2 class="text-2xl font-semibold mb-4">Orders</h2>

        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
          <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm block md:table">
              <thead>
                <tr class="text-gray-500 dark:text-gray-300">
                  <th class="p-2">Order ID</th>
                  <th class="p-2">Customer</th>
                  <th class="p-2">Date</th>
                  <th class="p-2">Status</th>
                  <th class="p-2">Total</th>
                  <th class="p-2">Action</th>
                </tr>
              </thead>
              <tbody id="ordersTableBody">
                <?php

                $sql = "SELECT
            o.order_id,
            u.name AS customer_name,
            o.created_at,
            o.status,
            o.total_price
        FROM orders o
        LEFT JOIN user u ON o.user_id = u.user_id
        ORDER BY o.created_at DESC";

                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                  while ($row = mysqli_fetch_assoc($result)) {
                    $orderDate = date("d M Y", strtotime($row['created_at']));
                    $statusClass = match (strtolower($row['status'])) {
                      'completed', 'delivered' => 'text-green-600',
                      'pending' => 'text-yellow-600',
                      'cancelled' => 'text-red-600',
                      'shipped' => 'text-blue-600',
                      default => 'text-gray-600',
                    };
                    echo "<tr class='border-t border-blue-100 dark:border-gray-700'>
            <td class='p-2'>#{$row['order_id']}</td>
            <td class='p-2'>" . htmlspecialchars($row['customer_name']) . "</td>
            <td class='p-2'>{$orderDate}</td>
            <td class='p-2 {$statusClass}'>" . ucfirst($row['status']) . "</td>
            <td class='p-2'>₹" . number_format($row['total_price'], 2) . "</td>
            <td class='p-2 space-x-2'>
              <a href='view-order.php?order_id={$row['order_id']}' class='text-blue-600 hover:underline'>View</a>
              <button class='text-red-600 hover:underline'>Cancel</button>
            </td>
          </tr>";
                  }
                } else {
                  echo "<tr><td colspan='6' class='p-2 text-center'>No orders found.</td></tr>";
                }
                ?>
              </tbody>

            </table>
          </div>
        </div>
      </main>

    </div>
  </div>

  <?php include 'include/footer.php'; ?>

</body>

</html>