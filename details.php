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
            <i class="fi-rs-crown"></i> 1 Year Al Jazeera Brand Warranty
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
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#lenscat">
            Add to Cart
          </button>
        </div>
      </div>
    </div>
  </section>
  <section class="details__tab container">
    <div class="detail__tabs">
      <span class="detail__tab active-tab" data-target="#info">
        Additional Info
      </span>
      <span class="detail__tab" data-target="#reviews">Reviews(3)</span>
    </div>
    <div class="details__tabs-content">
      <div class="details__tab-content active-tab" content id="info">
        <table class="info__table">
          <tr>
            <th>Stand Up</th>
            <td>35" L x 24"W x 37-45"H(front to back wheel)</td>
          </tr>
          <tr>
            <th>Folded (w/o wheels)</th>
            <td>32.5"L x 18.5"W x 16.5"H</td>
          </tr>
          <tr>
            <th>Folded (w/o wheels)</th>
            <td>32.5"L x 24"W x 18.5"H</td>
          </tr>
          <tr>
            <th>Door Pass THrough</th>
            <td>24</td>
          </tr>
          <tr>
            <th>Frame</th>
            <td>Aluminum</td>
          </tr>
          <tr>
            <th>Weight (w/o wheels)</th>
            <td>20 LBS</td>
          </tr>
          <tr>
            <th>Weight Capacity</th>
            <td>60 LBS</td>
          </tr>
          <tr>
            <th>Width</th>
            <td>24</td>
          </tr>
          <tr>
            <th>Handle Height (ground to handle)</th>
            <td>37-45</td>
          </tr>
          <tr>
            <th>Wheels</th>
            <td>12" air / wide track slick tread</td>
          </tr>
          <tr>
            <th>Seat back height</th>
            <td>21.5</td>
          </tr>
          <tr>
            <th>Head Room(inside canopy)</th>
            <td>25"</td>
          </tr>
          <tr>
            <th>Color</th>
            <td>Black, Blue, Red, White</td>
          </tr>
          <tr>
            <th>Size</th>
            <td>M, S</td>
          </tr>
        </table>
      </div>
      <div class="details__tab-content" content id="reviews">
        <div class="reviews__container grid">
          <div class="review__single">
            <div>
              <img
                src="./assets/img/avatar-1.jpg"
                alt=""
                class="review__img" />
              <h4 class="review__title">Jacky Chan</h4>
            </div>
            <div class="review__data">
              <div class="review__rating">
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
              </div>
              <p class="review__description">
                Thank you, very fast shipping from Poland only 3days.
              </p>
              <span class="review__date">December 4, 2022 at 3:12 pm</span>
            </div>
          </div>
          <div class="review__single">
            <div>
              <img
                src="./assets/img/avatar-2.jpg"
                alt=""
                class="review__img" />
              <h4 class="review__title">Meriem Js</h4>
            </div>
            <div class="review__data">
              <div class="review__rating">
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
              </div>
              <p class="review__description">
                Great low price and works well
              </p>
              <span class="review__date">August 23, 2022 at 19:45 pm</span>
            </div>
          </div>
          <div class="review__single">
            <div>
              <img
                src="./assets/img/avatar-3.jpg"
                alt=""
                class="review__img" />
              <h4 class="review__title">Moh Benz</h4>
            </div>
            <div class="review__data">
              <div class="review__rating">
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
              </div>
              <p class="review__description">
                Authentic and beautiful, Love these ways more than ever
                expected, They are great earphones.
              </p>
              <span class="review__date">March 2, 2021 at 10:01 am</span>
            </div>
          </div>
        </div>
        <div class="review__form">
          <h4 class="review__form-title">Add a review</h4>
          <div class="rate__product">
            <i class="fi fi-rs-star"></i>
            <i class="fi fi-rs-star"></i>
            <i class="fi fi-rs-star"></i>
            <i class="fi fi-rs-star"></i>
            <i class="fi fi-rs-star"></i>
          </div>
          <form action="" class="form grid">
            <textarea
              class="form__input textarea"
              placeholder="Write Comment"></textarea>
            <div class="form__group grid">
              <input type="text" placeholder="Name" class="form__input">
              <input type="email" placeholder="Email" class="form__input">
            </div>
            <div class="form__btn">
              <button class="btn">Submit Review</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
  <?php include('lenscat.php') ?>
  <?php include('include/product.php') ?>
  <?php include('include/news.php') ?>

</main>

<?php include('include/footer.php'); ?>