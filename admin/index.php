<?php
include 'config/db.php';
// session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Total Sales (sum of completed payments)
$totalSales = 0;
$result = $conn->query("SELECT SUM(amount) as total FROM payments WHERE status = 'completed'");
if ($row = $result->fetch_assoc()) {
  $totalSales = $row['total'] ?? 0;
}

// Total Orders
$result = $conn->query("SELECT COUNT(*) as count FROM orders");
$totalOrders = $result->fetch_assoc()['count'] ?? 0;

// Active Customers (users who placed at least 1 order)
$result = $conn->query("SELECT COUNT(DISTINCT user_id) as count FROM orders");
$activeCustomers = $result->fetch_assoc()['count'] ?? 0;

// Out of Stock Frames (frames with no details or 0 stock if you track stock)
// For now, count frames with no details as 'out of stock'
$result = $conn->query("SELECT COUNT(f.frame_id) as count FROM frames f LEFT JOIN frame_details fd ON f.frame_id = fd.frame_id WHERE fd.detail_id IS NULL");
$outOfStock = $result->fetch_assoc()['count'] ?? 0;

// Sales Over Time (monthly total sales for last 6 months)
$salesData = [];
$monthLabels = [];
$result = $conn->query("
    SELECT DATE_FORMAT(created_at, '%b %Y') AS month, SUM(amount) AS total
    FROM payments
    WHERE status = 'completed'
    GROUP BY YEAR(created_at), MONTH(created_at)
    ORDER BY created_at DESC
    LIMIT 6
");
while ($row = $result->fetch_assoc()) {
  array_unshift($monthLabels, $row['month']);
  array_unshift($salesData, (float) $row['total']);
}

// Top-Selling Frames (top 5 frames by quantity sold)
$frameLabels = [];
$frameQuantities = [];
$result = $conn->query("
    SELECT f.name, SUM(oi.quantity) AS total_sold
    FROM order_items oi
    JOIN frames f ON oi.frame_id = f.frame_id
    GROUP BY oi.frame_id
    ORDER BY total_sold DESC
    LIMIT 5
");
while ($row = $result->fetch_assoc()) {
  $frameLabels[] = $row['name'];
  $frameQuantities[] = (int) $row['total_sold'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Eyewear Shop Admin Dashboard</title>
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
        <input type="text" placeholder="Search..." class="px-3 py-1 rounded bg-blue-100 dark:bg-gray-700">
        <div class="flex items-center space-x-4">
          <button class="text-blue-500 dark:text-blue-300"><i data-lucide="bell"></i></button>
          <div class="w-8 h-8 bg-blue-300 dark:bg-gray-600 rounded-full flex items-center justify-center font-bold">A</div>
          <button id="theme-toggle" class="text-blue-500 dark:text-blue-300" title="Toggle Theme">
            <i data-lucide="sun"></i>
          </button>
        </div>
      </header>

      <!-- Dashboard content -->
      <main class="p-4 space-y-6 overflow-auto">

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
            <p class="text-sm">Total Sales</p>
            <h2 class="text-xl font-bold text-green-600">₹<?= number_format($totalSales, 2) ?></h2>
          </div>
          <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
            <p class="text-sm">Total Orders</p>
            <h2 class="text-xl font-bold text-blue-500"><?= $totalOrders ?></h2>
          </div>
          <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
            <p class="text-sm">Active Customers</p>
            <h2 class="text-xl font-bold text-indigo-500"><?= $activeCustomers ?></h2>
          </div>
          <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
            <p class="text-sm">Out of Stock Frames</p>
            <h2 class="text-xl font-bold text-red-500"><?= $outOfStock ?></h2>
          </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
          <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
            <h3 class="mb-2 font-semibold">Sales Over Time</h3>
            <canvas id="salesChart" class="w-full h-60"></canvas>
          </div>
          <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
            <h3 class="mb-2 font-semibold">Top-Selling Frames</h3>
            <canvas id="topFramesChart" class="w-full h-60"></canvas>
          </div>
        </div>

        <!-- Recent Orders Table -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
          <h3 class="mb-4 font-semibold">Recent Orders</h3>
          <div class="overflow-x-auto">
            <table class="w-full text-left">
              <thead>
                <tr class="text-sm text-gray-500 dark:text-gray-300">
                  <th class="p-2">Order ID</th>
                  <th class="p-2">Customer</th>
                  <th class="p-2">Date</th>
                  <th class="p-2">Status</th>
                  <th class="p-2">Total</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $result = $conn->query("
  SELECT o.order_id, u.name AS customer_name, o.created_at, o.status, o.total_price
  FROM orders o
  LEFT JOIN user u ON o.user_id = u.user_id
  ORDER BY o.created_at DESC
  LIMIT 10
");
                while ($row = $result->fetch_assoc()):
                ?>
                  <tr class="border-t border-blue-100 dark:border-gray-700">
                    <td class="p-2">#<?= $row['order_id'] ?></td>
                    <td class="p-2"><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td class="p-2"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                    <td class="p-2 text-green-600"><?= ucfirst($row['status']) ?></td>
                    <td class="p-2">₹<?= number_format($row['total_price'], 2) ?></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>
  <?php include 'include/footer.php'; ?>
  <script>
    const salesChartCtx = document.getElementById('salesChart').getContext('2d');
    const topFramesCtx = document.getElementById('topFramesChart').getContext('2d');

    const salesChart = new Chart(salesChartCtx, {
      type: 'line',
      data: {
        labels: <?= json_encode($monthLabels) ?>,
        datasets: [{
          label: 'Monthly Sales (₹)',
          data: <?= json_encode($salesData) ?>,
          backgroundColor: 'rgba(59, 130, 246, 0.2)',
          borderColor: 'rgba(59, 130, 246, 1)',
          borderWidth: 2,
          tension: 0.4,
          fill: true,
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });

    const framesChart = new Chart(topFramesCtx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($frameLabels) ?>,
        datasets: [{
          label: 'Units Sold',
          data: <?= json_encode($frameQuantities) ?>,
          backgroundColor: [
            '#38bdf8', '#818cf8', '#f472b6', '#facc15', '#34d399'
          ],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        indexAxis: 'y',
        scales: {
          x: {
            beginAtZero: true
          }
        }
      }
    });
  </script>
</body>

</html>