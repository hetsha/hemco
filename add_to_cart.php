<?php
// add_to_cart.php
include('include/db_connect.php');
include('include/header.php');
include('include/navbar.php');

$frame_id = isset($_GET['frame_id']) ? intval($_GET['frame_id']) : 0;
if ($frame_id <= 0) {
    echo '<div class="container"><h2>Invalid Frame Selected</h2></div>';
    exit;
}

// Fetch frame details
$frame = $conn->query("SELECT * FROM frames WHERE frame_id = $frame_id")->fetch_assoc();
$frame_img = $conn->query("SELECT image_url FROM frame_images WHERE frame_id = $frame_id LIMIT 1")->fetch_assoc();

?>
<style>
.add-cart-steps { max-width: 500px; margin: 2rem auto; background: #fff; border-radius: 12px; box-shadow: 0 2px 16px rgba(0,0,0,0.08); padding: 2rem; }
.step { display: none; }
.step.active { display: block; }
.stepper { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.stepper .circle { width: 32px; height: 32px; border-radius: 50%; background: #e0e0e0; color: #333; display: flex; align-items: center; justify-content: center; font-weight: bold; z-index: 1; }
.stepper .circle.active { background: #3399cc; color: #fff; }
.stepper .step-line { transition: background 0.3s; }
.stepper .circle.active ~ .step-line { background: #3399cc; }
@media (max-width:600px) { .add-cart-steps { padding: 1rem; } }
</style>
<main class="main">
  <div class="blank"><br><br></div>
    <section class="breadcrumb">
        <ul class="breadcrumb__list flex container">
            <li><a href="index.php" class="breadcrumb__link">Home</a></li>
            <li><span class="breadcrumb__link"></span>></li>
            <li><a href="shop.php" class="breadcrumb__link">Shop</a></li>
            </ul>
    </section>
  <div class="add-cart-steps">
    <div class="stepper">
      <div class="circle step-circle" data-step="1">1</div>
      <div class="step-line" style="flex:1;height:2px;background:#e0e0e0;margin:0 8px;align-self:center;"></div>
      <div class="circle step-circle" data-step="2">2</div>
      <div class="step-line" style="flex:1;height:2px;background:#e0e0e0;margin:0 8px;align-self:center;"></div>
      <div class="circle step-circle" data-step="3">3</div>
    </div>
    <div class="frame-summary text-center mb-4">
      <img src="<?php echo $frame_img['image_url'] ?? 'assets/default.jpg'; ?>" alt="Frame" style="max-width:120px; border-radius:8px;">
      <h4><?php echo htmlspecialchars($frame['name']); ?></h4>
      <p>Rs <?php echo number_format($frame['price'],2); ?></p>
    </div>
    <form id="add-cart-form">
      <!-- Step 1: Select Lens Category -->
      <div class="step step-1 active">
        <label for="lens-category">Select Lens Category</label>
        <select id="lens-category" name="lens_category" class="form__input" required></select>
        <br>
        <button type="button" class="btn btn--md btn-primary mt-3 next-btn">Next</button>
      </div>
      <!-- Step 2: Select Lens -->
      <div class="step step-2">
        <label for="lens">Select Lens</label>
        <select id="lens" name="lens_id" class="form__input" required></select><br>
        <button type="button" class="btn btn--md btn-secondary prev-btn">Back</button>
        <button type="button" class="btn btn--md btn-primary next-btn">Next</button>
      </div>
      <!-- Step 3: Prescription -->
      <div class="step step-3">
        <label>Enter Prescription (optional)</label>
        <div class="grid" style="grid-template-columns:1fr 1fr; gap:12px;">
          <select name="left_eye_sph" class="form__input">
            <option value="">Left Eye SPH</option>
            <option value="+2.00">+2.00</option>
            <option value="+1.50">+1.50</option>
            <option value="+1.00">+1.00</option>
            <option value="+0.50">+0.50</option>
            <option value="0.00">0.00</option>
            <option value="-0.50">-0.50</option>
            <option value="-1.00">-1.00</option>
            <option value="-1.50">-1.50</option>
            <option value="-2.00">-2.00</option>
          </select>
          <select name="right_eye_sph" class="form__input">
            <option value="">Right Eye SPH</option>
            <option value="+2.00">+2.00</option>
            <option value="+1.50">+1.50</option>
            <option value="+1.00">+1.00</option>
            <option value="+0.50">+0.50</option>
            <option value="0.00">0.00</option>
            <option value="-0.50">-0.50</option>
            <option value="-1.00">-1.00</option>
            <option value="-1.50">-1.50</option>
            <option value="-2.00">-2.00</option>
          </select>
          <select name="left_eye_cyl" class="form__input">
            <option value="">Left Eye CYL</option>
            <option value="0.00">0.00</option>
            <option value="-0.25">-0.25</option>
            <option value="-0.50">-0.50</option>
            <option value="-0.75">-0.75</option>
            <option value="-1.00">-1.00</option>
            <option value="-1.25">-1.25</option>
            <option value="-1.50">-1.50</option>
          </select>
          <select name="right_eye_cyl" class="form__input">
            <option value="">Right Eye CYL</option>
            <option value="0.00">0.00</option>
            <option value="-0.25">-0.25</option>
            <option value="-0.50">-0.50</option>
            <option value="-0.75">-0.75</option>
            <option value="-1.00">-1.00</option>
            <option value="-1.25">-1.25</option>
            <option value="-1.50">-1.50</option>
          </select>
          <select name="axis" class="form__input">
            <option value="">Axis</option>
            <option value="0">0</option>
            <option value="45">45</option>
            <option value="90">90</option>
            <option value="135">135</option>
            <option value="180">180</option>
          </select>
          <select name="addition" class="form__input">
            <option value="">Addition</option>
            <option value="+0.75">+0.75</option>
            <option value="+1.00">+1.00</option>
            <option value="+1.25">+1.25</option>
            <option value="+1.50">+1.50</option>
            <option value="+1.75">+1.75</option>
            <option value="+2.00">+2.00</option>
          </select>
        </div>
        <br>
        <button type="button" class="btn btn--md btn-secondary prev-btn">Back</button>
        <button type="submit" class="btn btn--md btn-success">Add to Cart</button>
      </div>
    </form>
    <div id="add-cart-message" class="mt-3"></div>
  </div>
</main>
<?php include('include/news.php'); ?>
<?php include('include/footer.php'); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Stepper logic
let currentStep = 1;
function showStep(step) {
  $('.step').removeClass('active');
  $('.step-' + step).addClass('active');
  $('.step-circle').removeClass('active');
  $('.step-circle[data-step="' + step + '"]').addClass('active');
  currentStep = step;
}
$('.next-btn').on('click', function() { showStep(currentStep + 1); });
$('.prev-btn').on('click', function() { showStep(currentStep - 1); });

// Fetch lens categories
function loadLensCategories() {
  $.get('api/get_lens_categories.php', function(res) {
    if (res.success) {
      $('#lens-category').html(res.categories.map(c => `<option value="${c.id}">${c.name}</option>`));
      $('#lens-category').trigger('change');
    }
  });
}
// Fetch lens options when category changes
$('#lens-category').on('change', function() {
  const catId = $(this).val();
  $('#lens').html('<option>Loading...</option>');
  $.get('api/get_lens_options.php?category_id=' + catId, function(res) {
    if (res.success) {
      $('#lens').html(res.lenses.map(l => `<option value="${l.id}">${l.name} (Rs ${l.price})</option>`));
    } else {
      $('#lens').html('<option>No lenses found</option>');
    }
  });
});

// On page load
$(function() { loadLensCategories(); });

// Add to cart submit
$('#add-cart-form').on('submit', function(e) {
  e.preventDefault();
  const lens_id = $('#lens').val();
  if (!lens_id) { alert('Please select a lens.'); return; }
  const prescription = {
    left_eye_sph: $('[name=left_eye_sph]').val(),
    right_eye_sph: $('[name=right_eye_sph]').val(),
    left_eye_cyl: $('[name=left_eye_cyl]').val(),
    right_eye_cyl: $('[name=right_eye_cyl]').val(),
    axis: $('[name=axis]').val(),
    addition: $('[name=addition]').val()
  };
  $.ajax({
    url: 'api/save_to_cart.php',
    method: 'POST',
    contentType: 'application/json',
    data: JSON.stringify({
      frame_id: <?php echo $frame_id; ?>,
      lens_id: lens_id,
      prescription: prescription
    }),
    success: function(res) {
      if (res.success) {
        $('#add-cart-message').html('<div class="alert alert-success">Added to cart! <a href="cart.php">Go to Cart</a></div>');
        showStep(1);
        $('#add-cart-form')[0].reset();
      } else {
        $('#add-cart-message').html('<div class="alert alert-danger">' + (res.message || 'Error adding to cart') + '</div>');
      }
    },
    error: function() {
      $('#add-cart-message').html('<div class="alert alert-danger">Error adding to cart</div>');
    }
  });
});
</script>
