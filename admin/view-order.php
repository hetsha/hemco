<?php
include 'config/db.php';
// session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

// Fetch order and user info
$sql = "SELECT
            o.order_id,
            o.created_at,
            o.status,
            o.total_price,
            u.name AS customer_name,
            u.email,
            u.phone,
            u.address,
            u.zip_code
        FROM orders o
        LEFT JOIN user u ON o.user_id = u.user_id
        WHERE o.order_id = $order_id";
$result = mysqli_query($conn, $sql);
$order = mysqli_fetch_assoc($result);

// Fetch shipping info
$shipping_sql = "SELECT * FROM shipping WHERE order_id = $order_id";
$shipping_result = mysqli_query($conn, $shipping_sql);
$shipping = mysqli_fetch_assoc($shipping_result);

// Fetch order items (frames, lens, prescription)
$items_sql = "SELECT
                oi.order_item_id,
                f.name AS frame_name,
                l.type AS lens_type,
                oi.quantity,
                oi.price,
                oi.prescription_id
              FROM order_items oi
              LEFT JOIN frames f ON oi.frame_id = f.frame_id
              LEFT JOIN lens l ON oi.lens_id = l.lens_id
              WHERE oi.order_id = $order_id";
$items_result = mysqli_query($conn, $items_sql);
$order_items = mysqli_fetch_all($items_result, MYSQLI_ASSOC);

// Fetch prescription details for each item if present
$prescriptions = [];
foreach ($order_items as $item) {
    if ($item['prescription_id']) {
        $pid = (int)$item['prescription_id'];
        $presc_sql = "SELECT * FROM prescription WHERE prescription_id = $pid";
        $presc_result = mysqli_query($conn, $presc_sql);
        $prescriptions[$pid] = mysqli_fetch_assoc($presc_result);
    }
}

// Fetch shipping cost from shipping table if available
$shipping_cost = 0;
if ($shipping && isset($shipping['shipping_id'])) {
    // Try to get shipping cost from a custom field if you have it (e.g., $shipping['shipping_cost'])
    if (isset($shipping['shipping_cost'])) {
        $shipping_cost = (float)$shipping['shipping_cost'];
    } else {
        // If not present, try to parse from shipping_address (not ideal, but fallback)
        $shipping_cost = 0; // No cost field in shipping table, so fallback below
    }
}
// If still 0, fallback to payments calculation
if ($shipping_cost == 0) {
    $payment_sql = "SELECT amount FROM payments WHERE order_id = $order_id AND status = 'completed' ORDER BY created_at DESC LIMIT 1";
    $payment_result = mysqli_query($conn, $payment_sql);
    $payment = mysqli_fetch_assoc($payment_result);
    $shipping_cost = $payment ? max(0, (float)$payment['amount'] - (float)$order['total_price']) : 0;
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
  $new_status = mysqli_real_escape_string($conn, $_POST['status']);
  $update_status_sql = "UPDATE orders SET status = '$new_status' WHERE order_id = $order_id";
  mysqli_query($conn, $update_status_sql);
  header("Location: view-order.php?order_id=$order_id");
  exit;
}

// Handle Shiprocket order creation trigger
$shiprocket_result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_shiprocket_order'])) {
    require_once __DIR__ . '/../shiprocket/CreateOrder.php';
    $shiprocket_result = create_shiprocket_order($order_id, $conn);
}

// Handle Ship Now button
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ship_now'])) {
    // 1. Update local status
    $update_status_sql = "UPDATE orders SET status = 'pending' WHERE order_id = $order_id";
    mysqli_query($conn, $update_status_sql);
    $update_ship_sql = "UPDATE shipping SET status = 'pending' WHERE order_id = $order_id";
    mysqli_query($conn, $update_ship_sql);

    // 2. Trigger Shiprocket shipment creation (generate AWB)
    require_once __DIR__ . '/../shiprocket/CreateOrder.php';
    $shiprocket_result = create_shiprocket_order($order_id, $conn);

    // 3. If Shiprocket order created, assign AWB (delivery partner)
    $awb_result = null;
    if ($shiprocket_result && !empty($shiprocket_result['data']['shipment_id'])) {
        $shipment_id = $shiprocket_result['data']['shipment_id'];
        // Get Shiprocket token
        $email = "hetlj6315@gmail.com";
        $password = "JfZ%&vO5jsej76jz";
        $auth = [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/json',
                'content' => json_encode(['email' => $email, 'password' => $password]),
                'ignore_errors' => true
            ]
        ];
        $auth_context = stream_context_create($auth);
        $auth_response = file_get_contents("https://apiv2.shiprocket.in/v1/external/auth/login", false, $auth_context);
        $auth_data = json_decode($auth_response, true);
        $token = $auth_data['token'] ?? null;
        if ($token) {
            $awb_url = "https://apiv2.shiprocket.in/v1/external/courier/assign/awb";
            $awb_payload = [
                'shipment_id' => $shipment_id,
                'courier_id' => null // Let Shiprocket auto-assign best courier
            ];
            $opts = [
                'http' => [
                    'method'  => 'POST',
                    'header'  => [
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $token
                    ],
                    'content' => json_encode($awb_payload),
                    'ignore_errors' => true
                ]
            ];
            $context = stream_context_create($opts);
            $awb_response = file_get_contents($awb_url, false, $context);
            $awb_result = json_decode($awb_response, true);
            // If AWB assigned, update shipping table with tracking number
            if (!empty($awb_result['tracking_number'])) {
                $tracking = mysqli_real_escape_string($conn, $awb_result['tracking_number']);
                $update_tracking_sql = "UPDATE shipping SET tracking_number = '$tracking' WHERE order_id = $order_id";
                mysqli_query($conn, $update_tracking_sql);
            }
        }
    }
    // Show result to admin
    $_SESSION['shiprocket_ship_result'] = $shiprocket_result;
    $_SESSION['shiprocket_awb_result'] = $awb_result;
    header("Location: view-order.php?order_id=$order_id");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Order Details | Admin</title>
  <?php include 'include/header.php'; ?>
</head>
<body class="bg-blue-50 text-gray-800 dark:bg-gray-900 dark:text-gray-100">
  <div class="flex h-screen overflow-hidden">
    <?php include 'include/navbar.php'; ?>
    <div class="flex-1 flex flex-col">
      <header class="flex justify-between items-center bg-white dark:bg-gray-800 px-4 py-3 shadow-md">
        <a href="order.php">
          <h2 class="text-xl font-bold">Order #<?= htmlspecialchars($order['order_id']) ?></h2>
        </a>
        <div class="flex items-center space-x-4">
          <button class="text-blue-500 dark:text-blue-300"><i data-lucide="bell"></i></button>
          <div class="w-8 h-8 bg-blue-300 dark:bg-gray-600 rounded-full flex items-center justify-center font-bold">A</div>
          <button id="theme-toggle" class="text-blue-500 dark:text-blue-300"><i data-lucide="sun"></i></button>
        </div>
      </header>
      <main class="p-6 space-y-6 overflow-auto">
        <!-- Customer Info -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
          <h3 class="text-lg font-semibold mb-4">Customer Info</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></div>
            <div><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></div>
            <div><strong>Phone:</strong> <a href="tel:<?= htmlspecialchars($order['phone']) ?>" class="text-blue-600 underline"><?= htmlspecialchars($order['phone']) ?></a></div>
            <div><strong>Date:</strong> <?= date('d M Y', strtotime($order['created_at'])) ?></div>
            <div><strong>Status:</strong>
              <form method="POST" class="inline">
                <select name="status" class="border p-1 rounded w-40 text-sm dark:bg-gray-700 dark:border-gray-600" required>
                  <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                  <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                  <option value="delivered" <?= $order['status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                  <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
                <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-sm ml-2">Update</button>
              </form>
            </div>
          </div>
        </div>
        <!-- Shipping Address -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
          <h3 class="text-lg font-semibold mb-4">Shipping Address</h3>
          <?php if ($shipping): ?>
            <p>
              <?= htmlspecialchars($shipping['name'] ?? $order['customer_name']) ?><br>
              <?= htmlspecialchars($shipping['shipping_address']) ?><br>
              <?= htmlspecialchars($shipping['zip_code'] ?? $order['zip_code']) ?><br>
              <?= htmlspecialchars($shipping['phone'] ?? $order['phone']) ?><br>
              <?= htmlspecialchars($shipping['email'] ?? $order['email']) ?>
            </p>
            <form method="POST" class="mb-2">
              <button type="submit" name="create_shiprocket_order" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Create Shiprocket Order</button>
            </form>
            <form method="POST" class="mb-2">
              <button type="submit" name="ship_now" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Ship Now</button>
            </form>
            <?php if ($shiprocket_result): ?>
              <div class="mt-2 p-3 rounded <?php if($shiprocket_result['success']){echo 'bg-green-100 text-green-800';}else{echo 'bg-red-100 text-red-800';} ?>">
                <?php if ($shiprocket_result['success']): ?>
                  Shiprocket Order Created! ID: <strong><?= htmlspecialchars($shiprocket_result['shiprocket_order_id']) ?></strong>
                <?php else: ?>
                  Shiprocket Error: <?= htmlspecialchars($shiprocket_result['error']) ?>
                <?php endif; ?>
              </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['shiprocket_ship_result'])): ?>
              <?php $shiprocket_ship_result = $_SESSION['shiprocket_ship_result']; unset($_SESSION['shiprocket_ship_result']); ?>
              <div class="mt-2 p-3 rounded <?php if($shiprocket_ship_result['success']){echo 'bg-green-100 text-green-800';}else{echo 'bg-red-100 text-red-800';} ?>">
                <?php if ($shiprocket_ship_result['success']): ?>
                  Shiprocket Shipment Created! ID: <strong><?= htmlspecialchars($shiprocket_ship_result['shiprocket_order_id']) ?></strong>
                <?php else: ?>
                  Shiprocket Error: <?= htmlspecialchars($shiprocket_ship_result['error']) ?>
                <?php endif; ?>
              </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['shiprocket_awb_result'])): ?>
              <?php $shiprocket_awb_result = $_SESSION['shiprocket_awb_result']; unset($_SESSION['shiprocket_awb_result']); ?>
              <div class="mt-2 p-3 rounded <?php if(!empty($shiprocket_awb_result['tracking_number'])){echo 'bg-green-100 text-green-800';}else{echo 'bg-red-100 text-red-800';} ?>">
                <?php if (!empty($shiprocket_awb_result['tracking_number'])): ?>
                  AWB Assigned! Tracking Number: <strong><?= htmlspecialchars($shiprocket_awb_result['tracking_number']) ?></strong>
                <?php else: ?>
                  Shiprocket AWB Error: <?= htmlspecialchars($shiprocket_awb_result['message'] ?? 'Unknown error') ?>
                <?php endif; ?>
              </div>
            <?php endif; ?>
            <?php if (!empty($shipping['tracking_number'])): ?>
              <div class="mt-2 p-3 bg-blue-50 dark:bg-blue-900 rounded">
                <strong>Shiprocket Tracking:</strong><br>
                Tracking Number: <span class="font-mono text-blue-700 dark:text-blue-300"><?= htmlspecialchars($shipping['tracking_number']) ?></span><br>
                Status: <span class="font-semibold text-blue-600 dark:text-blue-300"><?= htmlspecialchars($shipping['status']) ?></span>
                <?php if (!empty($shipping['tracking_number'])): ?>
                  <br><a href="https://www.shiprocket.in/shipment-tracking/<?= urlencode($shipping['tracking_number']) ?>" target="_blank" class="text-blue-500 underline">Track on Shiprocket</a>
                <?php endif; ?>
              </div>
              <?php if (!empty($shipping['tracking_number']) && !empty($shipping['status']) && in_array($shipping['status'], ['in_transit','delivered'])): ?>
                <div class="mt-2">
                  <a href="https://apiv2.shiprocket.in/v1/external/orders/print/invoice?order_id=<?= urlencode($order['order_id']) ?>" target="_blank" class="btn btn--md bg-yellow-500 text-white hover:bg-yellow-600">Download Invoice (Shiprocket)</a>
                </div>
              <?php endif; ?>
            <?php endif; ?>
          <?php else: ?>
            <p><?= htmlspecialchars($order['address']) ?><br><?= htmlspecialchars($order['zip_code']) ?></p>
          <?php endif; ?>
        </div>
        <!-- Order Items -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
          <h3 class="text-lg font-semibold mb-4">Order Items</h3>
          <table class="w-full text-sm text-left">
            <thead>
              <tr class="text-gray-500 dark:text-gray-300">
                <th class="p-2">Frame</th>
                <th class="p-2">Lens</th>
                <th class="p-2">Prescription</th>
                <th class="p-2">Quantity</th>
                <th class="p-2">Price</th>
                <th class="p-2">Total</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $grand_total = 0;
              foreach ($order_items as $item):
                $grand_total += $item['price'] * $item['quantity'];
                $presc = $item['prescription_id'] ? ($prescriptions[$item['prescription_id']] ?? null) : null;
              ?>
                <tr class="border-t border-blue-100 dark:border-gray-700">
                  <td class="p-2"><?= htmlspecialchars($item['frame_name'] ?? '-') ?></td>
                  <td class="p-2"><?= htmlspecialchars($item['lens_type'] ?? '-') ?></td>
                  <td class="p-2">
                    <?php if ($presc): ?>
                      L: <?= htmlspecialchars($presc['left_eye_sph']) ?>/<?= htmlspecialchars($presc['left_eye_cyl']) ?>,
                      R: <?= htmlspecialchars($presc['right_eye_sph']) ?>/<?= htmlspecialchars($presc['right_eye_cyl']) ?>
                    <?php else: ?>
                      <span class="text-gray-400">N/A</span>
                    <?php endif; ?>
                  </td>
                  <td class="p-2"><?= (int)$item['quantity'] ?></td>
                  <td class="p-2">₹<?= number_format($item['price'], 2) ?></td>
                  <td class="p-2">₹<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                </tr>
              <?php endforeach; ?>
              <tr class="border-t border-blue-100 dark:border-gray-700">
                <td class="p-2" colspan="5">Shipping Cost (from payments)</td>
                <td class="p-2">₹<?= number_format($shipping_cost, 2) ?></td>
              </tr>
              <tr class="font-semibold border-t border-blue-200 dark:border-gray-600">
                <td class="p-2" colspan="5">Grand Total</td>
                <td class="p-2 text-blue-600">₹<?= number_format($grand_total + $shipping_cost, 2) ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>
  <?php include 'include/footer.php'; ?>
</body>
</html>