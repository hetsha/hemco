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

  <!-- Lens Category Type Modal -->
<div class="modal fade" id="lenscat">
    <div class="modal-dialog">
        <div class="modal-header">
          <h1>Select Lens Type</h1>
          <!-- Close Button -->
          <button type="button" class="btn-close" data-bs-dismiss="modal">X</button>
            <!-- Back Button -->
            <!-- <button type="button" class="btn btn-secondary" onclick="goBackToPreviousModal('#lenscompany')">Back</button> -->
        </div>
        <div class="modal-body"></div>
    </div>
</div>

 <!-- Lens Modal -->
<div class="modal fade" id="lenscompany">
    <div class="modal-dialog">
        <div class="modal-header">
          <!-- Close Button -->
          <button type="button" class="btn-close" data-bs-dismiss="modal">X</button>
            <h1>Select Lens</h1>
            <!-- Back Button -->
            <button type="button" class="btn btn-secondary" onclick="goBackToPreviousModal('#lenscat')">Back</button>
        </div>
        <div class="modal-body"></div>
    </div>
</div>

<!-- Prescription Modal -->
<div class="modal fade" id="prescriptionModal" tabindex="-1" aria-labelledby="prescriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="prescriptionModalLabel">Enter Prescription</h1>
                <!-- Back Button with Icon -->
                <button type="button" class="btn btn-secondary" onclick="goBackToPreviousModal('#lenscompany')">
                    <i class="bi bi-arrow-left-circle"></i> Back
                </button>
            </div>
            <div class="modal-body">
                <form id="prescriptionForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="left_eye_sph" class="form-label">Left Eye Spherical</label>
                            <input type="text" id="left_eye_sph" class="form-control" placeholder="Enter Spherical value" required>
                        </div>
                        <br>
                        <div class="col-md-6 mb-3">
                            <label for="right_eye_sph" class="form-label">Right Eye Spherical</label>
                            <input type="text" id="right_eye_sph" class="form-control" placeholder="Enter Spherical value" required>
                        </div>
                        <Br>
                        <div class="col-md-6 mb-3">
                            <label for="left_eye_cyl" class="form-label">Left Eye Cylindrical</label>
                            <input type="text" id="left_eye_cyl" class="form-control" placeholder="Enter Cylindrical value" required>
                        </div>
                        <br>
                        <div class="col-md-6 mb-3">
                            <label for="right_eye_cyl" class="form-label">Right Eye Cylindrical</label>
                            <input type="text" id="right_eye_cyl" class="form-control" placeholder="Enter Cylindrical value" required>
                        </div>
                        <br>
                        <div class="col-md-6 mb-3">
                            <label for="axis" class="form-label">Axis</label>
                            <input type="text" id="axis" class="form-control" placeholder="Enter Axis value" required>
                        </div>
                        <br>
                        <div class="col-md-6 mb-3">
                            <label for="addition" class="form-label">Addition</label>
                            <input type="text" id="addition" class="form-control" placeholder="Enter Addition value" required>
                        </div>
                    </div>
                    <br>
                    <hr>
                    <h5>Or Upload Prescription Image</h5>
                    <div class="input-group mb-3">
                        <input type="file" id="prescription_image" class="form-control" accept="image/*">
                        <label class="input-group-text" for="prescription_image"><i class="bi bi-file-earmark-image"></i></label>
                    </div>
                    <br>
                    <hr>
                    <p class="text-muted">If you don't have a prescription now, you can skip and we will call you.</p>
                </form>

                <div class="mt-4 d-flex justify-content-between">
                    <!-- Submit Button with Icon -->
                    <button class="btn btn-success" onclick="submitPrescription()">
                        <i class="bi bi-check-circle"></i> Submit Prescription
                    </button>
                    <!-- Skip Button with Icon -->
                    <button class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close" onclick="skipPrescription()">
                        <i class="bi bi-x-circle"></i> Skip
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <?php include('include/news.php'); ?>
  <script>
    // Close and Back button functionality
    function goBackToPreviousModal(modalId) {
        $(modalId).modal('show');
        $(modalId).siblings('.modal').modal('hide');
    }

    // Example of opening the modal with a close button functionality
    $('#addToCartBtn').click(function() {
        openLensCategoryModal();
    });

    function openLensCategoryModal() {
        $.ajax({
            url: 'get_lens_categories.php',
            method: 'GET',
            success: function(data) {
                const categories = JSON.parse(data);
                let html = '';
                categories.forEach(cat => {
                    html += `<div onclick="selectCategory(${cat.category_id})" class="list-group-item">${cat.type}</div>`;
                });
                $('#lenscat .modal-body').html(html);
                $('#lenscat').modal('show');
            },
            error: function() {
                console.log("Error fetching lens categories");
            }
        });
    }

    function selectCategory(categoryId) {
        $.ajax({
            url: 'get_lenses.php',
            method: 'GET',
            data: { category_id: categoryId },
            success: function(data) {
                const lenses = JSON.parse(data);
                let html = '';
                lenses.forEach(lens => {
                    html += `<div onclick="selectLens(${lens.lens_id})" class="list-group-item">
                            ${lens.type} - Rs ${lens.price}
                        </div>`;
                });
                $('#lenscompany .modal-body').html(html);
                $('#lenscat').modal('hide');
                $('#lenscompany').modal('show');
            },
            error: function() {
                console.log("Error fetching lenses");
            }
        });
    }

    function selectLens(lensId) {
        $('#lenscompany').modal('hide');
        $('#prescriptionModal').modal('show');
    }

    function submitPrescription() {
        const formData = new FormData(document.getElementById('prescriptionForm'));
        const image = $('#prescription_image')[0].files[0];
        if (image) {
            formData.append('prescription_image', image);
        }

        $.ajax({
            url: 'save_prescription.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                alert('Prescription saved!');
                $('#prescriptionModal').modal('hide');
            },
            error: function() {
                alert('Failed to save prescription');
            }
        });
    }

    function skipPrescription() {
        $.ajax({
            url: 'save_prescription.php',
            type: 'POST',
            data: { skip: true },
            success: function(response) {
                alert('We will call you for prescription details.');
                $('#prescriptionModal').modal('hide');
            },
            error: function() {
                alert('Failed to process your request.');
            }
        });
    }
</script>
<script>
  document.getElementById('addToCartBtn').addEventListener('click', function() {
    const frame_id = <?php echo $productRow['frame_id']; ?>;
    const lens_id = selectedLensId; // You need to get the selected lens ID
    const quantity = document.querySelector('.quantity').value;
    const prescription_id = selectedPrescriptionId; // Get the prescription ID

    const formData = new FormData();
    formData.append('frame_id', frame_id);
    formData.append('lens_id', lens_id);
    formData.append('quantity', quantity);
    formData.append('prescription_id', prescription_id);

    fetch('add_to_cart.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.text())
    .then(data => {
      alert(data);
      if (data === 'Item added to cart successfully!') {
        window.location.href = 'cart.php'; // Redirect to cart page
      }
    })
    .catch(error => console.error('Error:', error));
  });
</script>

  <?php include('include/footer.php'); ?>