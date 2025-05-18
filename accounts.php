<?php
include('include/db_connect.php');
include('include/header.php');
if (!isset($_SESSION['user_id'])) {
    header('Location: login-register.php');
    exit;
}
$user_id = $_SESSION['user_id'];
// Fetch user info
$user_sql = "SELECT * FROM user WHERE user_id = $user_id";
$user_result = mysqli_query($conn, $user_sql);
$user = mysqli_fetch_assoc($user_result);
// Fetch orders
$order_sql = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC";
$order_result = mysqli_query($conn, $order_sql);
$orders = mysqli_fetch_all($order_result, MYSQLI_ASSOC);
// Fetch shipping address (most recent)
$shipping_sql = "SELECT * FROM shipping WHERE order_id IN (SELECT order_id FROM orders WHERE user_id = $user_id) ORDER BY created_at DESC LIMIT 1";
$shipping_result = mysqli_query($conn, $shipping_sql);
$shipping = mysqli_fetch_assoc($shipping_result);
?>
<body>
    <!--=============== HEADER ===============-->
    <?php include('include/navbar.php') ?>
    <!--=============== MAIN ===============-->
    <main class="main">
      <div class="blank"><br></div>
      <!--=============== BREADCRUMB ===============-->
      <section class="breadcrumb">
        <ul class="breadcrumb__list flex container">
          <li><a href="index.php" class="breadcrumb__link">Home</a></li>
          <li><span class="breadcrumb__link">></span></li>
          <li><span class="breadcrumb__link">Account</span></li>
        </ul>
      </section>
      <!--=============== ACCOUNTS ===============-->
      <section class="accounts section--lg">
        <div class="accounts__container container grid">
          <div class="account__tabs">
            <p class="account__tab active-tab" data-target="#dashboard">
              <i class="fi fi-rs-settings-sliders"></i> Dashboard
            </p>
            <p class="account__tab" data-target="#orders">
              <i class="fi fi-rs-shopping-bag"></i> Orders
            </p>
            <p class="account__tab" data-target="#update-profile">
              <i class="fi fi-rs-user"></i> Update Profile
            </p>
            <p class="account__tab" data-target="#address">
              <i class="fi fi-rs-marker"></i> My Address
            </p>
            <p class="account__tab" data-target="#change-password">
              <i class="fi fi-rs-settings-sliders"></i> Change Password
            </p>
            <a href="logout.php" class="account__tab"><i class="fi fi-rs-exit"></i> Logout</a>
          </div>
          <div class="tabs__content">
            <div class="tab__content active-tab" content id="dashboard">
              <h3 class="tab__header">Hello <?= htmlspecialchars($user['name']) ?></h3>
              <div class="tab__body">
                <p class="tab__description">
                  From your account dashboard, you can easily check & view your
                  recent orders, manage your shipping address, and edit your password and account details.
                </p>
              </div>
            </div>
            <div class="tab__content" content id="orders">
              <h3 class="tab__header">Your Orders</h3>
              <div class="tab__body">
                <table class="placed__order-table">
                  <thead>
                    <tr>
                      <th>Order ID</th>
                      <th>Date</th>
                      <th>Status</th>
                      <th>Total</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($orders as $order): ?>
                    <tr>
                      <td>#<?= $order['order_id'] ?></td>
                      <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                      <td><?= ucfirst($order['status']) ?></td>
                      <td>₹<?= number_format($order['total_price'], 2) ?></td>
                      <td><a href="view-order.php?order_id=<?= $order['order_id'] ?>" class="view__order">View</a></td>
                    </tr>
                  <?php endforeach; ?>
                  <?php if (empty($orders)): ?>
                    <tr><td colspan="5">No orders found.</td></tr>
                  <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="tab__content" content id="update-profile">
              <h3 class="tab__header">Update Profile</h3>
              <div class="tab__body">
                <form class="form grid" method="POST" action="update_profile.php">
                  <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" placeholder="Username" class="form__input" required />
                  <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="Email" class="form__input" required />
                  <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" placeholder="Phone" class="form__input" required />
                  <input type="text" name="address" value="<?= htmlspecialchars($user['address']) ?>" placeholder="Address" class="form__input" required />
                  <input type="text" name="zip_code" value="<?= htmlspecialchars($user['zip_code']) ?>" placeholder="Zip Code" class="form__input" required />
                  <div class="form__btn">
                    <button class="btn btn--md">Save</button>
                  </div>
                </form>
              </div>
            </div>
            <div class="tab__content" content id="address">
              <h3 class="tab__header">Shipping Address</h3>
              <div class="tab__body">
                <?php if ($shipping): ?>
                <address class="address">
                  <?= htmlspecialchars($shipping['name']) ?><br />
                  <?= htmlspecialchars($shipping['shipping_address']) ?><br />
                  <?= htmlspecialchars($shipping['city']) ?>, <?= htmlspecialchars($shipping['state']) ?>, <?= htmlspecialchars($shipping['pincode']) ?><br />
                  <?= htmlspecialchars($shipping['country']) ?><br />
                  <?= htmlspecialchars($shipping['phone']) ?><br />
                  <?= htmlspecialchars($shipping['email']) ?>
                </address>
                <?php else: ?>
                <p>No shipping address found.</p>
                <?php endif; ?>
                <a href="update_address.php" class="edit">Edit</a>
              </div>
            </div>
            <div class="tab__content" content id="change-password">
              <h3 class="tab__header">Change Password</h3>
              <div class="tab__body">
                <form class="form grid" method="POST" action="change_password.php">
                  <input type="password" name="current_password" placeholder="Current Password" class="form__input" required />
                  <input type="password" name="new_password" placeholder="New Password" class="form__input" required />
                  <input type="password" name="confirm_password" placeholder="Confirm Password" class="form__input" required />
                  <div class="form__btn">
                    <button class="btn btn--md">Save</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!--=============== NEWSLETTER ===============-->
      <?php include('include/news.php') ?>
    </main>
    <!--=============== FOOTER ===============-->
    <?php include('include/footer.php') ?>
  </body>
</html>
