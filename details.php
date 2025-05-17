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
          <button class="btn btn--md btn-primary" id="add-to-cart-btn">Add to Cart</button>
        </div>
      </div>
    </div>
  </section>

<?php include 'include/news.php'; ?>
<?php include 'include/footer.php'; ?>

<!-- jQuery first -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Then Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
  $('#add-to-cart-btn').on('click', function() {
    <?php if (!isset($_SESSION['user_id'])): ?>
      window.location.href = 'login-register.php';
      return;
    <?php else: ?>
      const frameId = <?php echo json_encode($productRow['frame_id']); ?>;
      window.location.href = 'add_to_cart.php?frame_id=' + frameId;
    <?php endif; ?>
  });
});
</script>
</body>
</html>
