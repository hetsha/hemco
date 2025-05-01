<?php
include('include/db_connect.php'); // Replace with your actual DB connection script
include('include/header.php');

$user_id = $_SESSION['user_id']; // Assumes user is logged in and session holds user_id

// Get cart ID for current user
$cart_query = mysqli_query($conn, "SELECT cart_id FROM cart WHERE user_id = $user_id");
$cart = mysqli_fetch_assoc($cart_query);
$cart_id = $cart['cart_id'] ?? 0;

$total = 0;

$items_query = mysqli_query($conn, "
 SELECT
  ci.cart_item_id,
  ci.frame_id,
  ci.lens_id,
  ci.quantity,
  f.name AS frame_name,
  f.price AS frame_price,
  l.type AS lens_name,
  l.price AS lens_price,
  fi.image_url AS frame_image
FROM cart_items ci
LEFT JOIN frames f ON ci.frame_id = f.frame_id
LEFT JOIN lens l ON ci.lens_id = l.lens_id
LEFT JOIN (
  SELECT frame_id, MIN(image_url) AS image_url
  FROM frame_images
  GROUP BY frame_id
) fi ON ci.frame_id = fi.frame_id
WHERE ci.cart_id = $cart_id
");
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
        <li><span class="breadcrumb__link"></span>></li>
        <li><a href="shop.php" class="breadcrumb__link">Shop</a></li>
        <li><span class="breadcrumb__link"></span>></li>
        <li><span class="breadcrumb__link">Cart</span></li>
      </ul>
    </section>

    <!--=============== CART ===============-->
    <section class="cart section--lg container">
      <div class="table__container">
        <table class="table">
          <thead>
            <tr>
              <th>Image</th>
              <th>Name</th>
              <th>Price</th>
              <th>Quantity</th>
              <th>Subtotal</th>
              <th>Rename</th>
            </tr>
          </thead>
          <tbody>
            <?php
            while ($item = mysqli_fetch_assoc($items_query)) {
              $frame_name = $item['frame_name'] ?? 'No Frame';
              $lens_name = $item['lens_name'] ?? 'No Lens';
              $frame_price = $item['frame_price'] ?? 0;
              $lens_price = $item['lens_price'] ?? 0;
              $image = $item['frame_image'] ?? 'assets/img/default.jpg';
              $qty = $item['quantity'];

              $item_price = $frame_price + $lens_price;
              $subtotal = $item_price * $qty;
              $total += $subtotal;

              echo "
              <tr>
                <td><img src='$image' alt='' class='table__img' /></td>
                <td>
                  <h3 class='table__title'>Frame: $frame_name</h3>
                  <p class='table__description'>Lens: $lens_name</p>
                </td>
                <td>
                  <span class='table__price'>Frame: $$frame_price</span><br>
                  <span class='table__price'>Lens: $$lens_price</span>
                </td>
                <td><input type='number' value='$qty' class='quantity' /></td>
                <td><span class='subtotal'>$$subtotal</span></td>
                <td><a href='remove_item.php?frame_id={$item['frame_id']}&lens_id={$item['lens_id']}'><i class='fi fi-rs-trash table__trash'></i></a></td>
              </tr>
            ";
            }
            ?>
          </tbody>
        </table>
      </div>

      <div class="cart__actions">

        <a href="shop.php" class="btn flex btn__md">
          <i class="fi-rs-shopping-bag"></i> Continue Shopping
        </a>
      </div>

      <div class="divider">
        <i class="fi fi-rs-fingerprint"></i>
      </div>

      <div class="cart__group grid">
        <div>
          <div class="cart__shippinp">
            <h3 class="section__title">Calculate Shipping</h3>
            <form action="shiprocket/CalculateShipping.php" method="POST" class="form grid">
              <input type="text" name="state" class="form__input" placeholder="State / Country" required />
              <div class="form__group grid">
                <input type="text" name="city" class="form__input" placeholder="City" required />
                <input type="text" name="postcode" class="form__input" placeholder="PostCode" required />
              </div>
              <div class="form__btn">
                <button type="submit" name="calculate_shipping" class="btn flex btn--sm">
                  <i class="fi-rs-shuffle"></i> Update
                </button>
              </div>
            </form>
          </div>
          <div class="cart__coupon">
            <h3 class="section__title">Apply Coupon</h3>
            <form action="" class="coupon__form form grid">
              <div class="form__group grid">
                <input
                  type="text"
                  class="form__input"
                  placeholder="Enter Your Coupon" />
                <div class="form__btn">
                  <button class="btn flex btn--sm">
                    <i class="fi-rs-label"></i> Apply
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <div class="cart__total">
          <h3 class="section__title">Cart Totals</h3>
          <table class="cart__total-table">
            <tr>
              <td><span class="cart__total-title">Cart Subtotal</span></td>
              <td><span class="cart__total-price">$<?php echo number_format($total, 2); ?></span></td>
            </tr>
            <tr>
              <td><span class="cart__total-title">Shipping</span></td>
              <?php
              $shipping = $_SESSION['shipping_cost'] ?? 10.00;
              ?>
              <td><span class="cart__total-price">$<?php echo number_format($shipping, 2); ?></span></td>
            </tr>
            <tr>
              <td><span class="cart__total-title">Total</span></td>
              <td><span class="cart__total-price">$<?php echo number_format($total + 10, 2); ?></span></td>
            </tr>

          </table>
          <a href="checkout.html" class="btn flex btn--md">
            <i class="fi fi-rs-box-alt"></i> Proceed To Checkout
          </a>
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