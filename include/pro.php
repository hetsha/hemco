<?php
// Reusable function to get frames by tag
function getFramesByTag($conn, $tag) {
  $stmt = $conn->prepare("
    SELECT
        f.frame_id,
        f.name AS frame_name,
        f.price,
        COALESCE(b.name, 'Unknown') AS brand_name,
        GROUP_CONCAT(DISTINCT fi.image_url ORDER BY fi.image_id ASC SEPARATOR ',') AS images,
        GROUP_CONCAT(DISTINCT fc.name SEPARATOR ', ') AS categories
    FROM frames f
    LEFT JOIN brand b ON f.brand_id = b.brand_id
    LEFT JOIN frame_images fi ON f.frame_id = fi.frame_id
    LEFT JOIN frame_category_map fcm ON f.frame_id = fcm.frame_id
    LEFT JOIN frame_category fc ON fcm.category_id = fc.category_id
    WHERE f.tag = ?
    GROUP BY f.frame_id
    LIMIT 8
  ");

  $stmt->bind_param("s", $tag);
  $stmt->execute();
  $result = $stmt->get_result();

  $frames = [];
  while ($row = $result->fetch_assoc()) {
    $imageList = explode(',', $row['images']);
    $row['default_image'] = $imageList[0] ?? 'assets/no-image.webp';
    $row['hover_image']   = $imageList[1] ?? $row['default_image'];
    $frames[] = $row;
  }

  return $frames;
}


$featured = getFramesByTag($conn, 'featured');
$popular  = getFramesByTag($conn, 'popular');
$new      = getFramesByTag($conn, 'new');
?>

<section class="products container section">
  <div class="tab__btns">
    <span class="tab__btn active-tab" data-target="#featured">Featured</span>
    <span class="tab__btn" data-target="#popular">Popular</span>
    <span class="tab__btn" data-target="#new-added">New Added</span>
  </div>

  <div class="tab__items">
    <div class="tab__item active-tab" id="featured">
      <div class="products__container grid">
        <?php foreach ($featured  as $frame): ?>
          <div class="product__item">
            <div class="product__banner">
              <a href="details.php?frame_id=<?= $frame['frame_id'] ?>" class="product__images">
                <img src="<?= $frame['default_image'] ?>" alt="" class="product__img default" />
                <img src="<?= $frame['hover_image'] ?>" alt="" class="product__img hover" />
              </a>
              <div class="product__actions">
                <a href="#" class="action__btn" aria-label="Quick View">
                  <i class="fi fi-rs-eye"></i>
                </a>
                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                  <i class="fi fi-rs-heart"></i>
                </a>
                
              </div>
              <div class="product__badge light-pink">Hot</div>
            </div>
            <div class="product__content">
              <span class="product__category"><?= ucfirst($frame['categories']) ?></span>
              <a href="details.php?frame_id=<?= $frame['frame_id'] ?>">
                <h3 class="product__title"><?= htmlspecialchars($frame['frame_name']) ?></h3>
              </a>
              <div class="product__rating">
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
              </div>
              <div class="product__price flex">
                <span class="new__price">₹<?= number_format($frame['price'], 2) ?></span>
              </div>
              <a href="#" class="action__btn cart__btn" aria-label="Add To Cart">
                <i class="fi fi-rs-shopping-bag-add"></i>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="tab__item"  id="popular">
      <div class="products__container grid">
      <?php foreach ($popular as $frame): ?>
          <div class="product__item">
            <div class="product__banner">
              <a href="details.php?frame_id=<?= $frame['frame_id'] ?>" class="product__images">
                <img src="<?= $frame['default_image'] ?>" alt="" class="product__img default" />
                <img src="<?= $frame['hover_image'] ?>" alt="" class="product__img hover" />
              </a>
              <div class="product__actions">
                <a href="#" class="action__btn" aria-label="Quick View">
                  <i class="fi fi-rs-eye"></i>
                </a>
                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                  <i class="fi fi-rs-heart"></i>
                </a>
                <a href="#" class="action__btn" aria-label="Compare">
                  <i class="fi fi-rs-shuffle"></i>
                </a>
              </div>
              <div class="product__badge light-pink">Hot</div>
            </div>
            <div class="product__content">
              <span class="product__category"><?= ucfirst($frame['categories']) ?></span>
              <a href="details.php?frame_id=<?= $frame['frame_id'] ?>">
                <h3 class="product__title"><?= htmlspecialchars($frame['frame_name']) ?></h3>
              </a>
              <div class="product__rating">
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
              </div>
              <div class="product__price flex">
                <span class="new__price">₹<?= number_format($frame['price'], 2) ?></span>
              </div>
              <a href="#" class="action__btn cart__btn" aria-label="Add To Cart">
                <i class="fi fi-rs-shopping-bag-add"></i>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="tab__item"  id="new-added">
      <div class="products__container grid">
      <?php foreach ($new as $frame): ?>
          <div class="product__item">
            <div class="product__banner">
              <a href="details.php?frame_id=<?= $frame['frame_id'] ?>" class="product__images">
                <img src="<?= $frame['default_image'] ?>" alt="" class="product__img default" />
                <img src="<?= $frame['hover_image'] ?>" alt="" class="product__img hover" />
              </a>
              <div class="product__actions">
                <a href="#" class="action__btn" aria-label="Quick View">
                  <i class="fi fi-rs-eye"></i>
                </a>
                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                  <i class="fi fi-rs-heart"></i>
                </a>

              </div>
              <div class="product__badge light-pink">Hot</div>
            </div>
            <div class="product__content">
              <span class="product__category"><?= ucfirst($frame['categories']) ?></span>
              <a href="details.php?frame_id=<?= $frame['frame_id'] ?>">
                <h3 class="product__title"><?= htmlspecialchars($frame['frame_name']) ?></h3>
              </a>
              <div class="product__rating">
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
              </div>
              <div class="product__price flex">
                <span class="new__price">₹<?= number_format($frame['price'], 2) ?></span>
              </div>
              <a href="#" class="action__btn cart__btn" aria-label="Add To Cart">
                <i class="fi fi-rs-shopping-bag-add"></i>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section><script>
document.querySelectorAll('.tab__btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.tab__btn').forEach(b => b.classList.remove('active-tab'));
    document.querySelectorAll('.tab__item').forEach(item => item.classList.remove('active-tab'));

    btn.classList.add('active-tab');
    const target = btn.getAttribute('data-target');
    document.querySelector(target).classList.add('active-tab');
  });
});
</script>
