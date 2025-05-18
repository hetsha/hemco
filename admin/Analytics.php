<?php
include 'config/db.php'; // DB connection
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// 1. Total Revenue (paid orders)
$res = mysqli_query($conn, "SELECT IFNULL(SUM(total_price), 0) AS total_revenue FROM orders WHERE status = 'paid'");
$total_revenue = mysqli_fetch_assoc($res)['total_revenue'];

// 2. Orders This Month
$res = mysqli_query($conn, "SELECT COUNT(*) AS orders_this_month FROM orders WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
$orders_this_month = mysqli_fetch_assoc($res)['orders_this_month'];

// 3. New Customers This Month
$res = mysqli_query($conn, "SELECT COUNT(*) AS new_customers FROM user WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
$new_customers = mysqli_fetch_assoc($res)['new_customers'];

// 4. Shipping Cost (set to 0, since not tracked in orders table)
$shipping_cost = 0;

// 5. Revenue Breakdown (Product only, since no shipping cost)
$res = mysqli_query($conn, "SELECT IFNULL(SUM(total_price), 0) AS product_revenue FROM orders WHERE status = 'paid'");
$revenue_data = mysqli_fetch_assoc($res);
$product_revenue = $revenue_data['product_revenue'];
$shipping_revenue = 0;

// Top 5 Selling Products (frames and lens)
$res = mysqli_query($conn, "
  SELECT
    COALESCE(f.name, l.type) AS product_name,
    SUM(oi.quantity) AS total_sold
  FROM order_items oi
  LEFT JOIN frames f ON oi.frame_id = f.frame_id
  LEFT JOIN lens l ON oi.lens_id = l.lens_id
  JOIN orders o ON o.order_id = oi.order_id
  WHERE o.status = 'paid'
  GROUP BY product_name
  ORDER BY total_sold DESC
  LIMIT 5
");
$product_names = [];
$product_sales = [];
while ($row = mysqli_fetch_assoc($res)) {
  $product_names[] = $row['product_name'];
  $product_sales[] = $row['total_sold'];
}

// Top 6 Categories (frame_category and lens_category)
$res = mysqli_query($conn, "
  SELECT category, SUM(total_sold) AS total_sold FROM (
    SELECT c.name AS category, SUM(oi.quantity) AS total_sold
    FROM order_items oi
    LEFT JOIN frames f ON oi.frame_id = f.frame_id
    LEFT JOIN frame_category_map fmap ON fmap.frame_id = f.frame_id
    LEFT JOIN frame_category c ON fmap.category_id = c.category_id
    JOIN orders o ON o.order_id = oi.order_id
    WHERE o.status = 'paid' AND c.name IS NOT NULL
    GROUP BY c.name
    UNION ALL
    SELECT lc.type AS category, SUM(oi.quantity) AS total_sold
    FROM order_items oi
    LEFT JOIN lens l ON oi.lens_id = l.lens_id
    LEFT JOIN lens_category lc ON l.category_id = lc.category_id
    JOIN orders o ON o.order_id = oi.order_id
    WHERE o.status = 'paid' AND lc.type IS NOT NULL
    GROUP BY lc.type
  ) AS all_cats
  GROUP BY category
  ORDER BY total_sold DESC
  LIMIT 6
");

$category_labels = [];
$category_values = [];
while ($row = mysqli_fetch_assoc($res)) {
  $category_labels[] = $row['category'];
  $category_values[] = (int)$row['total_sold'];
}

$res = mysqli_query($conn, "
  SELECT DATE_FORMAT(created_at, '%b') AS month, COUNT(*) AS count
  FROM user
  WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
  GROUP BY MONTH(created_at)
  ORDER BY MONTH(created_at)
");

$customer_labels = [];
$customer_values = [];
while ($row = mysqli_fetch_assoc($res)) {
  $customer_labels[] = $row['month'];
  $customer_values[] = (int)$row['count'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin | Analytics</title>
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
        <h1 class="text-xl font-semibold">Analytics Dashboard</h1>
        <div class="flex items-center space-x-4">
          <button class="text-blue-500 dark:text-blue-300"><i data-lucide="bell"></i></button>
          <div class="w-8 h-8 bg-blue-300 dark:bg-gray-600 rounded-full flex items-center justify-center font-bold">S</div>
          <button id="theme-toggle" class="text-blue-500 dark:text-blue-300" title="Toggle Theme">
            <i data-lucide="sun"></i>
          </button>
        </div>
      </header>

      <!-- Analytics Content -->
      <main class="p-4 space-y-6 overflow-auto">

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
            <p class="text-sm">Total Revenue</p>
            <h2 class="text-xl font-bold text-green-600">₹<?= number_format($total_revenue) ?></h2>
          </div>
          <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
            <p class="text-sm">Orders This Month</p>
            <h2 class="text-xl font-bold text-blue-500"><?= $orders_this_month ?></h2>
          </div>
          <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
            <p class="text-sm">New Customers</p>
            <h2 class="text-xl font-bold text-indigo-500"><?= $new_customers ?></h2>
          </div>
          <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
            <p class="text-sm">Shipping Cost</p>
            <h2 class="text-xl font-bold text-red-500">₹<?= number_format($shipping_cost) ?></h2>
          </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
          <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
            <h3 class="mb-2 font-semibold">Monthly Revenue</h3>
            <canvas id="revenueChart" class="w-full h-64"></canvas>
          </div>
          <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
            <h3 class="mb-2 font-semibold">Category Sales</h3>
            <canvas id="categoryChart" class="w-full h-64"></canvas>
          </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
          <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
            <h3 class="mb-2 font-semibold">Revenue Breakdown</h3>
            <canvas id="revenueBreakdownChart" class="w-5 h-40"></canvas>
          </div>
<div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
  <h3 class="mb-2 font-semibold">Top Selling Products</h3>
  <canvas id="topProductsChart" class="w-full h-64"></canvas>
</div>

          <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
            <h3 class="mb-2 font-semibold">Customer Growth</h3>
            <canvas id="customerChart" class="w-full h-64"></canvas>
          </div>
        </div>
      </main>
    </div>
  </div>
  <script>
    // Revenue Breakdown Chart (Product only as Pie Chart)
    new Chart(document.getElementById('revenueBreakdownChart'), {
      type: 'bar',
      data: {
        labels: ['Product Revenue'],
        datasets: [{
          data: [<?= $product_revenue ?>],
          backgroundColor: ['#34d399'],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'bottom'
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                let label = context.label || '';
                let value = context.raw || 0;
                return `${label}: ₹${value.toLocaleString()}`;
              }
            }
          }
        }
      }
    });
  </script>
<script>
  new Chart(document.getElementById('topProductsChart'), {
    type: 'bar',
    data: {
      labels: <?= json_encode($product_names) ?>,
      datasets: [{
        label: 'Units Sold',
        data: <?= json_encode($product_sales) ?>,
        backgroundColor: '#f59e0b'
      }]
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      plugins: {
        legend: {
          display: false
        }
      }
    }
  });
</script>
<script>
  // Monthly Revenue
  new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
      labels: <?= json_encode($revenue_labels) ?>,
      datasets: [{
        label: 'Revenue (₹)',
        data: <?= json_encode($revenue_values) ?>,
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59,130,246,0.1)',
        fill: true,
        tension: 0.4
      }]
    }
  });

  // Category Sales
  new Chart(document.getElementById('categoryChart'), {
    type: 'bar',
    data: {
      labels: <?= json_encode($category_labels) ?>,
      datasets: [{
        label: 'Units Sold',
        data: <?= json_encode($category_values) ?>,
        backgroundColor: ['#3b82f6', '#22d3ee', '#34d399', '#facc15', '#f472b6', '#8b5cf6']
      }]
    }
  });

  // Customer Growth
  new Chart(document.getElementById('customerChart'), {
    type: 'line',
    data: {
      labels: <?= json_encode($customer_labels) ?>,
      datasets: [{
        label: 'New Customers',
        data: <?= json_encode($customer_values) ?>,
        borderColor: '#8b5cf6',
        backgroundColor: 'rgba(139,92,246,0.1)',
        fill: true,
        tension: 0.4
      }]
    }
  });
</script>

  <?php include 'include/footer.php'; ?>
</body>

</html>