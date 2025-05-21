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
.lenscat-btn.bg-blue-600 { background: #2563eb !important; color: #fff !important; border-color: #2563eb !important; }
.lens-btn.bg-green-600 { background: #059669 !important; color: #fff !important; border-color: #059669 !important; }
.lenscat-box.bg-blue-600 { background: #2563eb !important; color: #fff !important; border-color: #2563eb !important; box-shadow: 0 4px 16px #2563eb22 !important; }
.lens-box.bg-green-600 { background: #059669 !important; color: #fff !important; border-color: #059669 !important; box-shadow: 0 4px 16px #05966922 !important; }
.lenscat-box, .lens-box { cursor: pointer; min-width: 180px; max-width: 220px; margin-bottom: 0.5rem; border-width:2px; transition: box-shadow 0.2s, border 0.2s, background 0.2s; text-align: left; }
.lenscat-box.bg-white, .lens-box.bg-white { background: #fff !important; }
/* Modern vertical card style for lens category/option selection */
.lenscat-col, .lens-col {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  width: 100%;
}
.lenscat-box, .lens-box {
  width: 100%;
  min-width: 0;
  max-width: 100%;
  background: #fcfbfa;
  border-radius: 18px;
  border: none;
  box-shadow: 0 2px 12px #e0e7ef33;
  padding: 1.5rem 1.5rem 1.2rem 1.5rem;
  display: flex;
  flex-direction: row;
  align-items: center;
  transition: box-shadow 0.2s, background 0.2s, color 0.2s;
  cursor: pointer;
  position: relative;
  margin-bottom: 0;
  outline: none;
  border: 2px solid transparent;
  text-align: left;
}
.lenscat-box.selected, .lens-box.selected {
  background: #e6f0ff;
  border: 2px solid #2563eb;
  box-shadow: 0 4px 24px #2563eb22;
}
.lenscat-box .icon, .lens-box .icon {
  font-size: 2.2rem;
  margin-right: 1.5rem;
  color: #2563eb;
}
.lenscat-box .cat-title, .lens-box .lens-title {
  font-weight: 700;
  font-size: 1.15rem;
  margin-bottom: 0.15rem;
  color: #1e293b;
  text-align: left;
}
.lenscat-box.selected .cat-title, .lens-box.selected .lens-title {
  color: #2563eb;
}
.lenscat-box .cat-desc, .lens-box .lens-desc {
  font-size: 1rem;
  color: #64748b;
  margin-bottom: 0;
  text-align: left;
}
.lens-box .lens-price {
  font-size: 1rem;
  color: #059669;
  margin-bottom: 0.15rem;
  text-align: left;
}
.lenscat-box .arrow, .lens-box .arrow {
  margin-left: auto;
  font-size: 1.5rem;
  color: #cbd5e1;
  transition: color 0.2s;
}
.lenscat-box.selected .arrow, .lens-box.selected .arrow {
  color: #2563eb;
}
@media (max-width: 600px) {
  .lenscat-box, .lens-box { padding: 1rem 0.7rem; }
}
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
        <label for="lens-category" class="block mb-2 font-semibold">Select Lens Category</label>
        <br>
        <div class="lenscat-col" id="lens-category-options"></div>
        <div class="lenscat-col" id="frame-only-card-container">
          <button type="button" class="lenscat-box" id="frame-only-card" style="margin-top:8px;">
            <span class="icon">👓</span>
            <div>
              <span class="cat-title">Frame Only (No Lens)</span>
              <div class="cat-desc">Add just the frame to your cart. No lens will be included.</div>
            </div>
            <span class="arrow">&#8250;</span>
          </button>
        </div>
        <input type="hidden" id="lens-category" name="lens_category" required>
        <br>
        <!-- Next button removed: step advances on card click -->
      </div>
      <!-- Step 2: Select Lens -->
      <div class="step step-2">
        <label for="lens" class="block mb-2 font-semibold">Select Lens</label>
        <div class="lens-col" id="lens-options"></div>
        <input type="hidden" id="lens" name="lens_id" required>
        <br>
        <span class="back-arrow" style="display:inline-flex;align-items:center;cursor:pointer;font-size:1.1rem;color:#2563eb;font-weight:600;gap:6px;margin-bottom:1rem;" id="step2-back-arrow">
          <span style="font-size:1.5rem;">&#8592;</span> Back
        </span>
        <!-- Next button removed: step advances on card click -->
      </div>
      <!-- Step 3: Prescription -->
      <div class="step step-3">
        <div class="prescription-step-container" style="max-width:400px;margin:0 auto;">
          <h4 style="font-weight:700;margin-bottom:1.2rem;">I know my power</h4>
          <div class="prescription-choice-cards" style="display:flex;flex-direction:column;gap:1.2rem;">
            <button type="button" id="manual-presc-btn" class="presc-card" style="display:flex;align-items:center;gap:1rem;padding:1.2rem 1rem;border-radius:12px;border:1.5px solid #e0e7ef;background:#fff;box-shadow:0 2px 8px #e0e7ef33;cursor:pointer;font-size:1.1rem;font-weight:500;">
              <span style="font-size:1.5rem;">📝</span> Enter Power Manually <span style="margin-left:auto;font-size:1.3rem;color:#bdbdbd;">&#8250;</span>
            </button>
            <button type="button" id="upload-presc-btn" class="presc-card" style="display:flex;align-items:center;gap:1rem;padding:1.2rem 1rem;border-radius:12px;border:1.5px solid #e0e7ef;background:#fff;box-shadow:0 2px 8px #e0e7ef33;cursor:pointer;font-size:1.1rem;font-weight:500;">
              <span style="font-size:1.5rem;">📄</span> Upload Prescription <span style="margin-left:auto;font-size:1.3rem;color:#bdbdbd;">&#8250;</span>
            </button>
          </div>

          <!-- Manual Entry Form -->
          <div id="manual-presc-form" style="display:none;margin-top:2rem;">
            <div style="background:#f8fafc;border-radius:10px;padding:1.2rem 1rem 1rem 1rem;margin-bottom:1.2rem;">
              <label style="font-weight:600;">Enter power manually</label>
              <div style="display:flex;gap:1rem;margin:1rem 0;">
                <div style="flex:1;">
                  <label style="font-size:0.95em;">LEFT (OS)</label>
                  <select name="left_eye_sph" class="form__input" style="width:100%;margin-top:0.3rem;">
                    <option value="">SPH</option>
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
                </div>
                <div style="flex:1;">
                  <label style="font-size:0.95em;">RIGHT (OD)</label>
                  <select name="right_eye_sph" class="form__input" style="width:100%;margin-top:0.3rem;">
                    <option value="">SPH</option>
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
                </div>
              </div>
              <div style="display:flex;gap:1rem;">
                <div style="flex:1;">
                  <label style="font-size:0.95em;">LEFT CYL</label>
                  <select name="left_eye_cyl" class="form__input" style="width:100%;margin-top:0.3rem;">
                    <option value="">CYL</option>
                    <option value="0.00">0.00</option>
                    <option value="-0.25">-0.25</option>
                    <option value="-0.50">-0.50</option>
                    <option value="-0.75">-0.75</option>
                    <option value="-1.00">-1.00</option>
                    <option value="-1.25">-1.25</option>
                    <option value="-1.50">-1.50</option>
                  </select>
                </div>
                <div style="flex:1;">
                  <label style="font-size:0.95em;">RIGHT CYL</label>
                  <select name="right_eye_cyl" class="form__input" style="width:100%;margin-top:0.3rem;">
                    <option value="">CYL</option>
                    <option value="0.00">0.00</option>
                    <option value="-0.25">-0.25</option>
                    <option value="-0.50">-0.50</option>
                    <option value="-0.75">-0.75</option>
                    <option value="-1.00">-1.00</option>
                    <option value="-1.25">-1.25</option>
                    <option value="-1.50">-1.50</option>
                  </select>
                </div>
              </div>
              <div style="display:flex;gap:1rem;margin-top:1rem;">
                <div style="flex:1;">
                  <label style="font-size:0.95em;">Axis</label>
                  <select name="axis" class="form__input" style="width:100%;margin-top:0.3rem;">
                    <option value="">Axis</option>
                    <option value="0">0</option>
                    <option value="45">45</option>
                    <option value="90">90</option>
                    <option value="135">135</option>
                    <option value="180">180</option>
                  </select>
                </div>
                <div style="flex:1;">
                  <label style="font-size:0.95em;">Addition</label>
                  <select name="addition" class="form__input" style="width:100%;margin-top:0.3rem;">
                    <option value="">Addition</option>
                    <option value="+0.75">+0.75</option>
                    <option value="+1.00">+1.00</option>
                    <option value="+1.25">+1.25</option>
                    <option value="+1.50">+1.50</option>
                    <option value="+1.75">+1.75</option>
                    <option value="+2.00">+2.00</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- Upload Prescription Form -->
          <div id="upload-presc-form" style="display:none;margin-top:2rem;">
            <div style="background:#f8fafc;border-radius:10px;padding:1.2rem 1rem 1rem 1rem;margin-bottom:1.2rem;">
              <label style="font-weight:600;">Upload Prescription</label>
              <ul style="font-size:0.98em;color:#475569;margin:0 0 1rem 1.2rem;">
                <li>PDF, JPEG, PNG formats accepted</li>
                <li>Make sure your file size under 5 MB</li>
                <li>Please upload only one file</li>
              </ul>
              <label for="prescription_image" style="display:block;width:100%;cursor:pointer;">
                <div id="presc-upload-box" style="border:2px dashed #bdbdbd;border-radius:10px;padding:2.2rem 0;text-align:center;background:#fff;transition:border 0.2s;">
                  <span style="font-size:2.2rem;">🖼️</span><br>
                  <span style="display:block;margin-top:0.5rem;font-size:1.05em;color:#64748b;">Tap here to upload prescription image<br>(Max. size: 5MB)</span>
                  <input type="file" name="prescription_image" id="prescription_image" accept="image/*,application/pdf" style="display:none;">
                </div>
              </label>
              <div id="presc-upload-filename" style="margin-top:0.7rem;font-size:0.98em;color:#059669;display:none;"></div>
            </div>
          </div>

          <div id="presc-step-actions" style="margin-top:2.2rem;text-align:center;">
            <button type="button" id="presc-back-btn" class="btn btn--md" style="margin-right:1.2rem;background:#e0e7ef;color:#333;">Back</button>
            <button type="submit" class="btn btn--md btn-success" id="presc-continue-btn" style="background:#6366f1;">Continue</button>
          </div>
        </div>
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

// Modern lens category selection with icon, description, and column layout
function loadLensCategories() {
  $.get('api/get_lens_categories.php', function(res) {
    if (res.success) {
      const container = $('#lens-category-options').empty();
      res.categories.forEach((c, i) => {
        // Use a different icon per category if you want, or fallback to a default
        let icon = `<span class='icon'>👓</span>`;
        if (c.name.toLowerCase().includes('bifocal')) icon = `<span class='icon'>👓</span>`;
        if (c.name.toLowerCase().includes('zero')) icon = `<span class='icon'>🛡️</span>`;
        if (c.name.toLowerCase().includes('frame')) icon = `<span class='icon'>🕶️</span>`;
        const desc = c.description ? `<div class='cat-desc'>${c.description}</div>` : '';
        const box = $(`
          <button type='button' class='lenscat-box' data-id='${c.id}'>
            ${icon}
            <div>
              <span class='cat-title'>${c.name}</span>
              ${desc}
            </div>
            <span class='arrow'>&#8250;</span>
          </button>
        `);
        box.on('click', function() {
          $('.lenscat-box').removeClass('selected');
          $(this).addClass('selected');
          $('#lens-category').val(c.id).trigger('change');
          // Auto-advance to Step 2
          showStep(2);
        });
        container.append(box);
      });
    }
  });
}
$('#lens-category').on('change', function() {
  const catId = $(this).val();
  const container = $('#lens-options').empty();
  $('#lens').val('');
  $.get('api/get_lens_options.php?category_id=' + catId, function(res) {
    if (res.success) {
      res.lenses.forEach(l => {
        const box = $(`
          <button type='button' class='lens-box' data-id='${l.id}'>
            <span class='icon'>🪟</span>
            <div>
              <span class='lens-title'>${l.name}</span>
              <div class='lens-price'>Rs ${l.price}</div>
              ${l.description ? `<div class='lens-desc'>${l.description}</div>` : ''}
            </div>
            <span class='arrow'>&#8250;</span>
          </button>
        `);
        box.on('click', function() {
          $('.lens-box').removeClass('selected');
          $(this).addClass('selected');
          $('#lens').val(l.id);
          // Auto-advance to Step 3
          showStep(3);
        });
        container.append(box);
      });
    } else {
      container.html('<div class="text-gray-400">No lenses found</div>');
    }
  });
});

// On page load
$(function() { loadLensCategories(); });

// Calculate and display total price in Step 3
function updateTotalPrice() {
  let framePrice = <?php echo json_encode($frame['price']); ?>;
  let lensPrice = 0;
  let lensId = $('#lens').val();
  if (lensId) {
    $.get('api/get_lens_options.php?lens_id=' + lensId, function(res) {
      if (res.success && res.lenses && res.lenses.length) {
        lensPrice = parseFloat(res.lenses[0].price);
      }
      let total = parseFloat(framePrice) + parseFloat(lensPrice);
      $('#total-price').text('Total: Rs ' + total.toFixed(2));
    });
  } else {
    $('#total-price').text('Total: Rs ' + parseFloat(framePrice).toFixed(2));
  }
}
$('#lens').on('change', updateTotalPrice);
$(function() { updateTotalPrice(); });

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
  const formData = new FormData(this);
  formData.append('frame_id', <?php echo $frame_id; ?>);
  formData.append('lens_id', lens_id);
  formData.append('prescription', JSON.stringify(prescription));
  $.ajax({
    url: 'api/save_to_cart.php',
    method: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function(res) {
      if (res.success) {
        window.location.href = 'cart.php';
      } else {
        let debug = '';
        if (res && typeof res === 'object') {
          debug = '<pre style="font-size:0.9em;color:#b91c1c;background:#fef2f2;padding:8px;overflow:auto;">' + JSON.stringify(res, null, 2) + '</pre>';
        }
        $('#add-cart-message').html('<div class="alert alert-danger">' + (res.error || res.message || 'Error adding to cart') + debug + '</div>');
      }
    },
    error: function(xhr) {
      let msg = 'Error adding to cart';
      if (xhr && xhr.responseText) {
        msg += '<pre style="font-size:0.9em;color:#b91c1c;background:#fef2f2;padding:8px;overflow:auto;">' + xhr.responseText + '</pre>';
      }
      $('#add-cart-message').html('<div class="alert alert-danger">' + msg + '</div>');
    }
  });
});

// Frame Only (No Lens) card logic
$('#frame-only-card').on('click', function() {
  $('.lenscat-box').removeClass('selected');
  $(this).addClass('selected');
  $.ajax({
    url: 'api/save_to_cart.php',
    method: 'POST',
    contentType: 'application/json',
    data: JSON.stringify({
      frame_id: <?php echo $frame_id; ?>
    }),
    success: function(res) {
      if (res.success) {
        window.location.href = 'cart.php';
      } else {
        let debug = '';
        if (res && typeof res === 'object') {
          debug = '<pre style="font-size:0.9em;color:#b91c1c;background:#fef2f2;padding:8px;overflow:auto;">' + JSON.stringify(res, null, 2) + '</pre>';
        }
        $('#add-cart-message').html('<div class="alert alert-danger">' + (res.message || 'Error adding frame to cart') + debug + '</div>');
      }
    },
    error: function() {
      $('#add-cart-message').html('<div class="alert alert-danger">Error adding frame to cart</div>');
    }
  });
});

// Step 2 back arrow logic
$('#step2-back-arrow').on('click', function() {
  showStep(1);
});

// Step 3 back arrow logic
$('#step3-back-arrow').on('click', function() {
  showStep(2);
});

// Step 3 prescription stepper UI logic
$(function() {
  // Hide both forms initially
  $('#manual-presc-form').hide();
  $('#upload-presc-form').hide();
  $('#presc-step-actions').hide();

  $('#manual-presc-btn').on('click', function() {
    $('.presc-card').removeClass('selected');
    $(this).addClass('selected');
    $('#manual-presc-form').show();
    $('#upload-presc-form').hide();
    $('#presc-step-actions').show();
  });
  $('#upload-presc-btn').on('click', function() {
    $('.presc-card').removeClass('selected');
    $(this).addClass('selected');
    $('#manual-presc-form').hide();
    $('#upload-presc-form').show();
    $('#presc-step-actions').show();
  });
  $('#presc-back-btn').on('click', function() {
    showStep(2);
  });
  // File upload UI feedback
  $('#prescription_image').on('change', function() {
    if (this.files && this.files.length > 0) {
      $('#presc-upload-filename').text(this.files[0].name).show();
      $('#presc-upload-box').css('border-color', '#059669');
    } else {
      $('#presc-upload-filename').hide();
      $('#presc-upload-box').css('border-color', '#bdbdbd');
    }
  });
});
</script>
