<?php
// Fetch frames with a single main image (first one), along with details
// Fetch frames with only one (the first) image using subquery
$sql = "
  SELECT f.frame_id, f.name, f.price, f.description,
         fd.size, fd.color, fd.weight,
         first_image.image_url AS first_image,
         second_image.image_url AS second_image,
         fc.name AS gender
  FROM frames f
  LEFT JOIN frame_details fd ON f.frame_id = fd.frame_id
  LEFT JOIN (
    SELECT fi1.frame_id, fi1.image_url
    FROM frame_images fi1
    WHERE fi1.image_id = (
      SELECT MIN(image_id)
      FROM frame_images
      WHERE frame_id = fi1.frame_id
    )
  ) AS first_image ON f.frame_id = first_image.frame_id
  LEFT JOIN (
    SELECT fi2.frame_id, fi2.image_url
    FROM frame_images fi2
    WHERE fi2.image_id = (
      SELECT MIN(image_id)
      FROM frame_images
      WHERE frame_id = fi2.frame_id AND image_id NOT IN (
        SELECT MIN(image_id)
        FROM frame_images
        GROUP BY frame_id
      )
    )
  ) AS second_image ON f.frame_id = second_image.frame_id
  LEFT JOIN frame_category_map fcm ON f.frame_id = fcm.frame_id
  LEFT JOIN frame_category fc ON fcm.category_id = fc.category_id
  WHERE fc.name IN ('Men', 'Women', 'Child')
  GROUP BY f.frame_id
  ORDER BY f.frame_id DESC
  LIMIT 8
";



$result = $conn->query($sql);

?>

<section class="new__arrivals container section">
  <h3 class="section__title"><span>New</span> Arrivals</h3>
  <div class="new__container swiper">
    <div class="swiper-wrapper">
      <?php while ($row = $result->fetch_assoc()): ?>
        <?php
        $firstImage = !empty($row['first_image']) ? $row['first_image'] : 'assets/default.jpg';
        $secondImage = !empty($row['second_image']) ? $row['second_image'] : $firstImage;
        ?>
        <div class="product__item swiper-slide">
          <div class="product__banner">
            <a href="details.php?frame_id=<?= $row['frame_id'] ?>" class="product__images">
              <img src="<?= $firstImage ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="product__img default" />
              <img src="<?= $secondImage ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="product__img hover" />
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
            <div class="product__badge light-green">Hot</div>
          </div>
          <div class="product__content">
            <span class="product__category"><?= ucfirst($row['gender']) ?> Glasses</span>
            <a href="details.php?frame_id=<?= $row['frame_id'] ?>">
              <h3 class="product__title"><?= htmlspecialchars($row['name']) ?></h3>
            </a>
            <div class="product__rating">
              <i class="fi fi-rs-star"></i>
              <i class="fi fi-rs-star"></i>
              <i class="fi fi-rs-star"></i>
              <i class="fi fi-rs-star"></i>
              <i class="fi fi-rs-star"></i>
            </div>
            <div class="product__price flex">
              <span class="new__price">$<?= number_format($row['price'], 2) ?></span>
              <span class="old__price">$<?= number_format($row['price'] + 7.5, 2) ?></span>
            </div>
            <a href="#" class="action__btn cart__btn" aria-label="Add To Cart">
              <i class="fi fi-rs-shopping-bag-add"></i>
            </a>
          </div>
        </div>
      <?php endwhile; ?>

    </div>

    <div class="swiper-button-prev">
      <i class="fi fi-rs-angle-left"></i>
    </div>
    <div class="swiper-button-next">
      <i class="fi fi-rs-angle-right"></i>
    </div>
  </div>
</section>