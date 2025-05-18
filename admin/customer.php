<?php
include 'config/db.php'; // DB connection
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch all users with their most recent shipping info (if any)
$query = "
  SELECT
    u.user_id,
    u.name,
    u.email,
    u.phone,
    u.created_at,
    (
      SELECT s.shipping_address
      FROM shipping s
      WHERE s.order_id = (
        SELECT o.order_id FROM orders o WHERE o.user_id = u.user_id ORDER BY o.created_at DESC LIMIT 1
      )
      ORDER BY s.created_at DESC LIMIT 1
    ) AS shipping_address,
    (
      SELECT s.city
      FROM shipping s
      WHERE s.order_id = (
        SELECT o.order_id FROM orders o WHERE o.user_id = u.user_id ORDER BY o.created_at DESC LIMIT 1
      )
      ORDER BY s.created_at DESC LIMIT 1
    ) AS shipping_city,
    (
      SELECT s.state
      FROM shipping s
      WHERE s.order_id = (
        SELECT o.order_id FROM orders o WHERE o.user_id = u.user_id ORDER BY o.created_at DESC LIMIT 1
      )
      ORDER BY s.created_at DESC LIMIT 1
    ) AS shipping_state,
    (
      SELECT s.country
      FROM shipping s
      WHERE s.order_id = (
        SELECT o.order_id FROM orders o WHERE o.user_id = u.user_id ORDER BY o.created_at DESC LIMIT 1
      )
      ORDER BY s.created_at DESC LIMIT 1
    ) AS shipping_country,
    COUNT(o.order_id) AS total_orders
  FROM user u
  LEFT JOIN orders o ON o.user_id = u.user_id
  GROUP BY u.user_id
  ORDER BY u.created_at DESC
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin | Customers</title>
  <?php include 'include/header.php'; ?>
</head>

<body class="bg-blue-50 text-gray-800 dark:bg-gray-900 dark:text-gray-100">
  <div class="flex h-screen overflow-hidden">
    <?php include 'include/navbar.php'; ?>

    <!-- Main content -->
    <div class="flex-1 flex flex-col">
      <!-- Top Bar -->
      <header class="flex justify-between items-center bg-white dark:bg-gray-800 px-4 py-3 shadow-md">
        <div class="md:hidden">
          <button id="menu-btn" class="text-blue-500 dark:text-blue-300"><i data-lucide="menu"></i></button>
        </div>
        <input type="text" placeholder="Search Customers..." class="px-3 py-1 rounded bg-blue-100 dark:bg-gray-700 w-1/2">
        <div class="flex items-center space-x-4">
          <button class="text-blue-500 dark:text-blue-300"><i data-lucide="bell"></i></button>
          <div class="w-8 h-8 bg-blue-300 dark:bg-gray-600 rounded-full flex items-center justify-center font-bold">S</div>
          <button id="theme-toggle" class="text-blue-500 dark:text-blue-300" title="Toggle Theme">
            <i data-lucide="sun"></i>
          </button>
        </div>
      </header>

      <!-- Customers Table -->
      <main class="p-4 overflow-auto grid grid-cols-1 gap-4">
        <h2 class="text-2xl font-semibold mb-4">Customers</h2>

        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
          <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm block md:table">
              <thead>
                <tr class="text-gray-500 dark:text-gray-300">
                  <th class="p-2">Customer ID</th>
                  <th class="p-2">Name</th>
                  <th class="p-2">Email</th>
                  <th class="p-2">Phone</th>
                  <th class="p-2">Address</th>
                  <th class="p-2">City</th>
                  <th class="p-2">State</th>
                  <th class="p-2">Country</th>
                  <th class="p-2">Joined</th>
                  <th class="p-2">Orders</th>
                  <th class="p-2">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                  <tr class="border-t border-blue-100 dark:border-gray-700">
                    <td class="p-2">C<?= str_pad($row['user_id'], 3, '0', STR_PAD_LEFT); ?></td>
                    <td class="p-2"><?= htmlspecialchars($row['name']); ?></td>
                    <td class="p-2"><?= htmlspecialchars($row['email']); ?></td>
                    <td class="p-2"><?= htmlspecialchars($row['phone']); ?></td>
                    <td class="p-2"><?= htmlspecialchars($row['shipping_address'] ?: ''); ?></td>
                    <td class="p-2"><?= htmlspecialchars($row['shipping_city'] ?: ''); ?></td>
                    <td class="p-2"><?= htmlspecialchars($row['shipping_state'] ?: ''); ?></td>
                    <td class="p-2"><?= htmlspecialchars($row['shipping_country'] ?: ''); ?></td>
                    <td class="p-2"><?= date('M d, Y', strtotime($row['created_at'])); ?></td>
                    <td class="p-2"><?= $row['total_orders']; ?></td>
                    <td class="p-2 space-x-2">
                      <a href="view-customer.php?id=<?= $row['user_id']; ?>" class="text-blue-600 hover:underline">View</a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
              <?php if (mysqli_num_rows($result) === 0): ?>
                <p class="text-center py-4 text-gray-500">No customers found.</p>
              <?php endif; ?>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>

<?php include 'include/footer.php'; ?>
  </body>

</html>
