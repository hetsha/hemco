<?php
include('include/db_connect.php');
include('include/header.php');
include('include/navbar.php');

if (isset($_GET['frame_id'])) {
  $product_id = $_GET['frame_id'];

  $productQuery = "SELECT * FROM frames WHERE frame_id = $product_id";
  $productResult = $conn->query($productQuery);

  $imageQuery = "SELECT * FROM frame_images WHERE frame_id = $product_id";
  $imageResult = $conn->query($imageQuery);

  if ($productResult->num_rows > 0 && $imageResult->num_rows > 0) {
    $productRow = $productResult->fetch_assoc();
    $imageRow = $imageResult->fetch_assoc();
  } else {
    echo "Product not found!";
    exit();
  }
} else {
  echo "No product selected!";
  exit();
}
?>
<style>
  .modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
  }

  .modal.show {
    display: flex;
  }

  .modal-dialog {
    background-color: rgb(250, 249, 247);
    border-radius: 8px;
    overflow: hidden;
    max-width: 55vw;
    width: 100%;
    height: 100vh;
    position: fixed;
    right: 0;
    top: 0;
    transform: translateX(100%);
    transition: transform 0.4s ease-in-out;
  }

  .modal.show .modal-dialog {
    transform: translateX(0);
  }

  .modal-header {
    /* display: flex; */
    /* justify-content: space-between; */
    /* padding: 25px; */
    background-color: #faf9f7;
    color: black;
    display: flex;
    -webkit-box-align: center;
    align-items: center;
    -webkit-box-pack: justify;
    justify-content: space-between;
    padding: 20px 46px;
  }

  .modal-body {
    padding: 20px;
    overflow-y: auto;
    max-height: calc(100vh - 60px);
  }

  .close-btn {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    color: white;
  }

  svg {
    pointer-events: auto !important;
    cursor: pointer;
  }

  .list-group {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .list-group-item {
    display: flex;
    flex-direction: row;
    -webkit-box-align: center;
    align-items: center;
    -webkit-box-pack: justify;
    /* justify-content: space-between; */
    height: 10vh;
    align-items: center;
    padding: 24px;
    gap: 24px;
    margin: 10px 0;
    border-radius: 5px;
    border-bottom: 1px solid #ddd;
    opacity: 0;
    cursor: pointer;
    transition: box-shadow 0.2s ease-in-out;
    box-shadow: rgba(0, 0, 66, 0.06) 0px 0px 12px;
    background-color: white;
    transform: translateY(20px);
    animation: fadeInUp 0.5s ease-in-out forwards;
    width: 100%;
  }

  .list-group-item:nth-child(1) {
    animation-delay: 0.2s;
  }

  .list-group-item:nth-child(2) {
    animation-delay: 0.4s;
  }

  .list-group-item:nth-child(3) {
    animation-delay: 0.6s;
  }

  .list-group-item img {
    width: 50px;
    height: 50px;
    margin-right: 15px;
    border-radius: 5px;
  }

  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(20px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @media (max-width: 992px) {
    .modal-dialog {
      position: fixed;
      bottom: 0;
      top: auto;
      width: 100%;
      height: 80vh;
      max-width: none;
      border-radius: 15px 15px 0 0;
      transform: translateY(100%);
    }

    .modal.show .modal-dialog {
      transform: translateY(0);
    }
  }
</style>
<main class="main">
  <div class="blank"><br></div>

  <section class="breadcrumb">
    <ul class="breadcrumb__list flex container">
      <li><a href="index.html" class="breadcrumb__link">Home</a></li>
      <li><span class="breadcrumb__link"></span>></li>
      <li><span class="breadcrumb__link">Shop</span></li>
    </ul>
  </section>

  <section class="details section--lg">
    <div class="details__container container grid">
      <div class="details__group">
        <img src="<?php echo $imageRow['image_url']; ?>" alt="img" class="details__img">
      </div>
      <div class="details__group">
        <h3 class="details__title"><?php echo $productRow['name']; ?></h3>
        <p class="details__brand">Brand: <span><?php echo $productRow['material']; ?></span></p>
        <div class="details__price flex">
          <span class="new__price">Rs<?php echo $productRow['price']; ?></span>
        </div>
        <p class="short__description"><?php echo $productRow['description']; ?></p>
        <ul class="products__list">
          <li class="list__item flex">
            <i class="fi-rs-crown"></i> 1 Year Warranty
          </li>
          <li class="list__item flex">
            <i class="fi-rs-refresh"></i> 30 Days Return Policy
          </li>
          <li class="list__item flex">
            <i class="fi-rs-credit-card"></i> Cash on Delivery available
          </li>
        </ul>
        <div class="details__action">
          <input type="number" class="quantity" value="1" />
          <button type="button" class="btn btn-primary" id="addToCartBtn">Add to Cart</button>
        </div>
      </div>
    </div>
  </section>

 <!-- Lens Category Modal -->
<div class="modal fade" id="lensCategoryModal">
    <div class="modal-dialog">
            <div class="modal-header">
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
              <h5 class="modal-title">Select Lens Category</h5>
            </div>
            <div class="modal-body" id="lensCategoryList">
                    <!-- Categories will be loaded here -->
            </div>

    </div>
</div>


<!-- Lens Company Modal -->
<div class="modal fade" id="lensCompanyModal" tabindex="-1" aria-labelledby="lensCompanyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 pb-0">
                <div class="w-100 d-flex justify-content-between align-items-center">
                    <h4 class="modal-title fw-bold" id="lensCompanyModalLabel">Select Lens Type</h4>
                    <div>
                        <button type="button" class="btn btn-outline-secondary btn-sm me-2" onclick="goBackToPreviousModal('#lenscat')">
                            <i class="bi bi-arrow-left"></i> Back
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
            </div>
            <div class="modal-body pt-2"></div>
        </div>
    </div>
</div>

<!-- Prescription Modal -->
<div class="modal fade" id="prescriptionModal" tabindex="-1" aria-labelledby="prescriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 pb-0">
                <div class="w-100 d-flex justify-content-between align-items-center">
                    <h4 class="modal-title fw-bold" id="prescriptionModalLabel">Enter Prescription</h4>
                    <div>
                        <button type="button" class="btn btn-outline-secondary btn-sm me-2" onclick="goBackToPreviousModal('#lenscompany')">
                            <i class="bi bi-arrow-left"></i> Back
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <form id="prescriptionForm" onsubmit="submitPrescription(event)">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="left_eye_sph" class="form-label">Left Eye Spherical</label>
                            <input type="text" id="left_eye_sph" name="left_eye_sph" class="form-control" placeholder="Enter Spherical value" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="right_eye_sph" class="form-label">Right Eye Spherical</label>
                            <input type="text" id="right_eye_sph" name="right_eye_sph" class="form-control" placeholder="Enter Spherical value" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="left_eye_cyl" class="form-label">Left Eye Cylindrical</label>
                            <input type="text" id="left_eye_cyl" name="left_eye_cyl" class="form-control" placeholder="Enter Cylindrical value" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="right_eye_cyl" class="form-label">Right Eye Cylindrical</label>
                            <input type="text" id="right_eye_cyl" name="right_eye_cyl" class="form-control" placeholder="Enter Cylindrical value" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="axis" class="form-label">Axis</label>
                            <input type="text" id="axis" name="axis" class="form-control" placeholder="Enter Axis value" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="addition" class="form-label">Addition</label>
                            <input type="text" id="addition" name="addition" class="form-control" placeholder="Enter Addition value">
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5>Or Upload Prescription Image</h5>
                        <div class="input-group">
                            <input type="file" id="prescription_image" name="prescription_image" class="form-control" accept="image/*">
                            <label class="input-group-text" for="prescription_image"><i class="bi bi-file-earmark-image"></i></label>
                        </div>
                        <small class="text-muted">If you don't have a prescription now, you can skip and we will call you.</small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Submit Prescription
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="skipPrescription()">
                            <i class="bi bi-x-circle"></i> Skip Prescription
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End of Prescription Modal -->

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pb-4">
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                </div>
                <h4 class="fw-bold mb-3">Added to Cart!</h4>
                <p class="text-muted mb-4">Your item has been successfully added to your cart.</p>
                <div class="d-grid gap-2">
                    <a href="cart.php" class="btn btn-primary">
                        <i class="bi bi-cart me-2"></i>View Cart
                    </a>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Continue Shopping
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End of Success Modal -->

<?php include 'include/news.php'; ?>
<?php include 'include/footer.php'; ?>

<!-- jQuery first -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Then Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Initialize Bootstrap -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize all modals
    var modals = document.querySelectorAll('.modal');
    modals.forEach(function(modal) {
      new bootstrap.Modal(modal);
    });
  });
</script>
<!-- Custom cart functionality -->
<script src="assets/js/cart.js"></script>
</body>
</html>
