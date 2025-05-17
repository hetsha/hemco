<?php
include('include/db_connect.php');
include('include/header.php');

$user_id = $_SESSION['user_id'] ?? 0;

$cart_query = mysqli_query($conn, "SELECT cart_id FROM cart WHERE user_id = $user_id");
$cart = mysqli_fetch_assoc($cart_query);
$cart_id = $cart['cart_id'] ?? 0;

$total = 0;
$shipping_cost = $_SESSION['shipping_cost'] ?? 0;

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

<head>
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>

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

    <!--=============== CHECKOUT ===============-->
    <section class="checkout section--lg">
      <div class="checkout__container container grid">
        <div class="checkout__group">
          <h3 class="section__title">Billing Details</h3>
          <form class="form grid" id="checkout-form">
            <input type="text" name="name" placeholder="Name" class="form__input" required />
            <input type="text" name="address" placeholder="Address" class="form__input" required />
            <input type="text" name="city" placeholder="City" class="form__input" required />
            <input type="text" name="country" placeholder="Country" class="form__input" required />
            <input type="text" name="postcode" placeholder="Postcode" class="form__input" required />
            <input type="text" name="phone" placeholder="Phone" class="form__input" required />
            <input type="email" name="email" placeholder="Email" class="form__input" required />
            <textarea name="order_note" placeholder="Order note" class="form__input textarea"></textarea>
          </form>
        </div>
        <div class="checkout__group">
          <h3 class="section__title">Cart Totals</h3>
          <table class="order__table">
            <thead>
              <tr>
                <th colspan="2">Products</th>
                <th>Total</th>
              </tr>
            </thead>

            <tbody>
              <?php
              // Reset pointer for items_query
              mysqli_data_seek($items_query, 0);
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
    <td><img src='$image' alt='' class='order__img' /></td>
    <td>
      <h3 class='table__title'>Frame: $frame_name</h3>
      <p class='table__quantity'>Lens: $lens_name x $qty</p>
    </td>
    <td><span class='table__price'>₹" . number_format($subtotal, 2) . "</span></td>
  </tr>";
              }
              ?>
              <tr>
                <td><span class="order__subtitle">Subtotal</span></td>
                <td colspan="2"><span class="table__price">₹<?php echo number_format($total, 2); ?></span></td>
              </tr>
              <tr>
                <td><span class="order__subtitle">Shipping</span></td>
                <td colspan="2"><span class="table__price">₹<?php echo number_format($shipping_cost, 2); ?></span></td>
              </tr>
              <tr>
                <td><span class="order__subtitle">Total</span></td>
                <td colspan="2"><span class="order__grand-total">₹<?php echo number_format($total + $shipping_cost, 2); ?></span></td>
              </tr>
            </tbody>
          </table>
          <div class="payment__methods">
            <h3 class="checkout__title payment__title">Payment</h3>
            <div class="payment__option flex">
              <input
                type="radio"
                name="radio"
                id="l1"
                checked
                class="payment__input" />
              <label for="l1" class="payment__label">Direct Bank Transfer</label>
            </div>
            <div class="payment__option flex">
              <input
                type="radio"
                name="radio"
                id="l2"
                class="payment__input" />
              <label for="l2" class="payment__label">Check Payment</label>
            </div>
            <div class="payment__option flex">
              <input
                type="radio"
                name="radio"
                id="l3"
                class="payment__input" />
              <label for="l3" class="payment__label">Paypal</label>
            </div>
          </div>
          <button class="btn btn--md" id="pay-btn">Place Order</button>
        </div>
      </div>
    </section>

    <!--=============== NEWSLETTER ===============-->
    <?php include('include/news.php') ?>

    <!--=============== FOOTER ===============-->
  </main>

  <?php include('include/footer.php') ?>
  <script>
  document.getElementById('pay-btn').onclick = function(e) {
    e.preventDefault();

    const form = document.getElementById('checkout-form');
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }
    const formData = new FormData(form);

    const options = {
      "key": "rzp_test_d72gC817C1EAM8",
      "amount": <?php echo ($total + $shipping_cost) * 100; ?>,
      "currency": "INR",
      "name": "Hemco opticals",
      "description": "Order Payment",
      "image": "assets/img/logo.png",
      "handler": function(response) {
        // Append payment ID to form data
        formData.append('payment_id', response.razorpay_payment_id);

        // Send everything to backend
        fetch('payment_success.php', {
          method: 'POST',
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            window.location.href = 'thank_you.php';
          } else {
            alert((data.message || 'Something went wrong saving your order.') + (data.error ? '\n' + data.error : ''));
          }
        });
      },
      "prefill": {
        "name": form.name.value,
        "email": form.email.value,
        "contact": form.phone.value
      },
      "theme": {
        "color": "#3399cc"
      },
      "method": ["card", "upi", "netbanking"]  // Include UPI in the list of available methods
    };

    const rzp = new Razorpay(options);
    rzp.open();
  };
</script>

</body>

</html>