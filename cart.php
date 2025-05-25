<?php
include('include/db_connect.php'); // Replace with your actual DB connection script
include('include/header.php');
if (!isset($_SESSION['user_id'])) {
    // Redirect to login with message and return URL
    $msg = urlencode('Please login to view your cart.');
    $return = urlencode('cart.php');
    header("Location: login-register.php?msg=$msg&return=$return");
    exit;
}
$user_id = $_SESSION['user_id']; // Assumes user is logged in and session holds user_id

$cart_ids = [];
$cart_query = mysqli_query($conn, "SELECT cart_id FROM cart WHERE user_id = $user_id");
while ($row = mysqli_fetch_assoc($cart_query)) {
    $cart_ids[] = $row['cart_id'];
}
$cart_ids_str = implode(',', $cart_ids ?: [0]);

$total = 0;

$items_query = mysqli_query($conn, "
 SELECT
  ci.cart_item_id,
  ci.frame_id,
  ci.lens_id,
  ci.quantity,
  ci.prescription_id,
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
WHERE ci.cart_id IN ($cart_ids_str)
ORDER BY ci.cart_item_id DESC
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
            // Fetch all prescription details for cart items in one query
            $prescription_map = [];
            $presc_ids = [];
            mysqli_data_seek($items_query, 0); // Reset pointer
            while ($item = mysqli_fetch_assoc($items_query)) {
              if ($item['prescription_id']) {
                $presc_ids[] = (int)$item['prescription_id'];
              }
            }
            $presc_ids_str = implode(',', $presc_ids ?: [0]);
            $presc_query = mysqli_query($conn, "SELECT * FROM prescription WHERE prescription_id IN ($presc_ids_str)");
            while ($presc = mysqli_fetch_assoc($presc_query)) {
              $prescription_map[$presc['prescription_id']] = $presc;
            }
            mysqli_data_seek($items_query, 0); // Reset pointer again for main loop
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
              $presc_id = $item['prescription_id'];
              $presc = $presc_id && isset($prescription_map[$presc_id]) ? $prescription_map[$presc_id] : null;
              $presc_data = $presc ? htmlspecialchars(json_encode($presc), ENT_QUOTES, 'UTF-8') : '';
              $lens_html = $lens_name;
              if ($presc) {
                $lens_html = "<a href=\"#\" class='lens-presc-link' data-presc='$presc_data'>" . htmlspecialchars($lens_name) . " <i class='fi fi-rs-eye'></i></a>";
              }
              echo "
              <tr>
                <td><img src='$image' alt='' class='table__img' /></td>
                <td>
                  <h3 class='table__title'>Frame: $frame_name</h3>
                  <p class='table__description'>Lens: $lens_html</p>
                </td>
                <td>
                  <span class='table__price'>Frame: ₹$frame_price</span><br>
                  <span class='table__price'>Lens: ₹$lens_price</span>
                </td>
                <td><input type='number' value='$qty' class='quantity' readonly /></td>
                <td><span class='subtotal'>₹$subtotal</span></td>
                <td><a href='remove_item.php?cart_item_id={$item['cart_item_id']}'><i class='fi fi-rs-trash table__trash'></i></a></td>
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
            <?php
            $shipping_cost = isset($_SESSION['shipping_cost']) ? floatval($_SESSION['shipping_cost']) : 0;
            ?>
            <tr>
              <td><span class="cart__total-title">Cart Subtotal</span></td>
              <td><span class="cart__total-price">₹<?php echo number_format($total, 2); ?></span></td>
            </tr>
            <tr>
              <td><span class="cart__total-title">Shipping</span></td>
              <td><span class="cart__total-price">₹<?php echo number_format($shipping_cost, 2); ?></span></td>
            </tr>
            <tr>
              <td><span class="cart__total-title">Total</span></td>
              <td><span class="cart__total-price">₹<?php echo number_format($total + $shipping_cost, 2); ?></span></td>
            </tr>
          </table>
          <a href="checkout.php" class="btn flex btn--md" id="proceed-checkout-btn">
            <i class="fi fi-rs-box-alt"></i> Proceed To Checkout
          </a>
        </div>
      </div>
    </section>

    <!-- Prescription Modal -->
    <div id="prescription-modal" class="modal" style="display:none;position:fixed;z-index:9999;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;">
      <div class="modal-content" style="background:#fff;padding:2rem;max-width:400px;margin:auto;position:relative;border-radius:8px;">
        <span id="close-presc-modal" style="position:absolute;top:10px;right:16px;font-size:1.5rem;cursor:pointer;">&times;</span>
        <h3>Prescription Details</h3>
        <div id="presc-modal-body">
          <!-- Populated by JS -->
        </div>
      </div>
    </div>

    <!--=============== NEWSLETTER ===============-->
    <?php include('include/news.php') ?>
  </main>

  <!--=============== FOOTER ===============-->
  <?php include('include/footer.php') ?>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('proceed-checkout-btn').addEventListener('click', function(e) {
      var shipping = <?php echo isset($_SESSION['shipping_cost']) && $_SESSION['shipping_cost'] > 0 ? 'true' : 'false'; ?>;
      if (!shipping) {
        e.preventDefault();
        alert('Please calculate shipping charge before proceeding to checkout.');
        document.querySelector('input[name=state]').focus();
      }
    });

    // Prescription modal logic
    document.querySelectorAll('.lens-presc-link').forEach(function(link) {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        var presc = JSON.parse(this.getAttribute('data-presc'));
        var html = '';
        if (presc.prescription_image) {
          html += '<div><b>Image:</b><br><img src="'+presc.prescription_image+'" alt="Prescription Image" style="max-width:100%;border:1px solid #eee;margin-bottom:1em;"/></div>';
        }
        if (presc.left_eye_sph || presc.right_eye_sph || presc.left_eye_cyl || presc.right_eye_cyl || presc.axis || presc.addition) {
          html += '<div><b>Manual Entry:</b><br>';
          html += '<table style="width:100%;border-collapse:collapse;">';
          html += '<tr><th></th><th>Left</th><th>Right</th></tr>';
          html += '<tr><td>Sphere</td><td>'+(presc.left_eye_sph||'')+'</td><td>'+(presc.right_eye_sph||'')+'</td></tr>';
          html += '<tr><td>Cylinder</td><td>'+(presc.left_eye_cyl||'')+'</td><td>'+(presc.right_eye_cyl||'')+'</td></tr>';
          html += '<tr><td>Axis</td><td>'+(presc.axis||'')+'</td><td>'+(presc.axis||'')+'</td></tr>';
          html += '<tr><td>Addition</td><td>'+(presc.addition||'')+'</td><td>'+(presc.addition||'')+'</td></tr>';
          html += '</table></div>';
        }
        if (!html) html = '<em>No prescription details available.</em>';
        document.getElementById('presc-modal-body').innerHTML = html;
        document.getElementById('prescription-modal').style.display = 'flex';
      });
    });
    document.getElementById('close-presc-modal').onclick = function() {
      document.getElementById('prescription-modal').style.display = 'none';
    };
    window.onclick = function(event) {
      var modal = document.getElementById('prescription-modal');
      if (event.target == modal) {
        modal.style.display = 'none';
      }
    };
  });
  </script>
</body>

</html>