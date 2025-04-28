<?php
include('include/header.php');
include('include/navbar.php');

// Handle filters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$gender = isset($_GET['gender']) ? $_GET['gender'] : '';
$material = isset($_GET['material']) ? $_GET['material'] : '';

// Pagination settings
$limit = 9; // 9 products per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $limit;

// Build WHERE conditions
$where = "WHERE 1";
if (!empty($category)) {
    $where .= " AND fc.name = '" . mysqli_real_escape_string($conn, $category) . "'";
}
if (!empty($gender)) {
    $where .= " AND f.gender = '" . mysqli_real_escape_string($conn, $gender) . "'";
}
if (!empty($material)) {
    $where .= " AND f.material = '" . mysqli_real_escape_string($conn, $material) . "'";
}

// Fetch frames with filters and pagination
$query = "
SELECT
    f.frame_id, f.name AS frame_name, f.price, f.gender, f.material,
    fc.name AS category_name,
    fi.image_url
FROM frames f
LEFT JOIN frame_category_map fcm ON f.frame_id = fcm.frame_id
LEFT JOIN frame_category fc ON fcm.category_id = fc.category_id
LEFT JOIN frame_images fi ON f.frame_id = fi.frame_id
$where
GROUP BY f.frame_id
LIMIT $start_from, $limit
";
$result = mysqli_query($conn, $query);

// Count total products (for pagination)
$count_query = "
SELECT COUNT(DISTINCT f.frame_id) AS total
FROM frames f
LEFT JOIN frame_category_map fcm ON f.frame_id = fcm.frame_id
LEFT JOIN frame_category fc ON fcm.category_id = fc.category_id
$where
";
$count_result = mysqli_fetch_assoc(mysqli_query($conn, $count_query));
$total_products = $count_result['total'];
$total_pages = ceil($total_products / $limit);

?>

<body>
<style>
  .filters__form.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    align-items: end;
}

.filter__group {
    display: flex;
    flex-direction: column;
}

.filter__group label {
    margin-bottom: 0.5rem;
    font-weight: 600;
    font-size: 0.95rem;
    color: #333;
    display: flex;
    align-items: center;
    gap: 5px;
}

.filter__select {
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 0.5rem;
    background-color: #fff;
    font-size: 0.95rem;
    color: #333;
    transition: 0.3s;
}

.filter__select:focus {
    /* border-color: #ff6f61; */
    outline: none;
}

.btn--filter {
    width: 100%;
    color: #fff;
    padding: 0.75rem;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 1rem;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 5px;
    transition: background-color 0.3s;
}


</style>
<!--=============== MAIN ===============-->
<main class="main">
    <div class="blank"><br></div>

    <section class="breadcrumb">
        <ul class="breadcrumb__list flex container">
            <li><a href="index.php" class="breadcrumb__link">Home</a></li>
            <li><span class="breadcrumb__link"></span>></li>
            <li><span class="breadcrumb__link">Shop</span></li>
        </ul>
    </section>
<br>
    <!--=============== FILTERS ===============-->
    <section class="filters container section--sm">
    <form method="GET" action="shop.php" class="filters__form grid">

        <div class="filter__group">
            <label for="category"><i class="fi fi-rs-apps"></i> Category</label>
            <select id="category" name="category" class="filter__select">
                <option value="">All Categories</option>
                <option value="Sunglasses" <?php if($category == 'Sunglasses') echo 'selected'; ?>>Sunglasses</option>
                <option value="Eyeglasses" <?php if($category == 'Eyeglasses') echo 'selected'; ?>>Eyeglasses</option>
            </select>
        </div>

        <div class="filter__group">
            <label for="gender"><i class="fi fi-rs-user"></i> Gender</label>
            <select id="gender" name="gender" class="filter__select">
                <option value="">All Genders</option>
                <option value="Men" <?php if($gender == 'Men') echo 'selected'; ?>>Men</option>
                <option value="Women" <?php if($gender == 'Women') echo 'selected'; ?>>Women</option>
                <option value="Unisex" <?php if($gender == 'Unisex') echo 'selected'; ?>>Unisex</option>
            </select>
        </div>

        <div class="filter__group">
            <label for="material"><i class="fi fi-rs-diamond"></i> Material</label>
            <select id="material" name="material" class="filter__select">
                <option value="">All Materials</option>
                <option value="Metal" <?php if($material == 'Metal') echo 'selected'; ?>>Metal</option>
                <option value="Plastic" <?php if($material == 'Plastic') echo 'selected'; ?>>Plastic</option>
            </select>
        </div>

        <div class="filter__group">
            <button type="submit" class="btn btn--filter"><i class="fi fi-rs-search"></i> Filter</button>
        </div>

    </form>
</section>


    <!--=============== PRODUCTS ===============-->
    <section class="products container section--lg">
        <p class="total__products">We found <?php echo $total_products; ?> items for you!</p>
        <div class="products__container grid">
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="product__item">
                <div class="product__banner">
                    <a href="details.php?frame_id=<?php echo $row['frame_id']; ?>" class="product__images">
                        <img src="<?php echo $row['image_url']; ?>" alt="<?php echo htmlspecialchars($row['frame_name']); ?>" class="product__img default" />
                        <img src="<?php echo $row['image_url']; ?>" alt="<?php echo htmlspecialchars($row['frame_name']); ?>" class="product__img hover" />
                    </a>
                    <div class="product__actions">
                        <a href="details.php?frame_id=<?php echo $row['frame_id']; ?>" class="action__btn" aria-label="Quick View">
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
                    <span class="product__category"><?php echo $row['category_name']; ?></span>
                    <a href="details.php?frame_id=<?php echo $row['frame_id']; ?>">
                        <h3 class="product__title"><?php echo htmlspecialchars($row['frame_name']); ?></h3>
                    </a>
                    <div class="product__rating">
                        <i class="fi fi-rs-star"></i>
                        <i class="fi fi-rs-star"></i>
                        <i class="fi fi-rs-star"></i>
                        <i class="fi fi-rs-star"></i>
                        <i class="fi fi-rs-star"></i>
                    </div>
                    <div class="product__price flex">
                        <span class="new__price">$<?php echo $row['price']; ?></span>
                    </div>
                    <a href="cart.php?add=<?php echo $row['frame_id']; ?>" class="action__btn cart__btn" aria-label="Add To Cart">
                        <i class="fi fi-rs-shopping-bag-add"></i>
                    </a>
                </div>
            </div>
            <?php } ?>
        </div>

        <!--=============== PAGINATION ===============-->
        <div class="pagination flex">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo ($page-1); ?>&category=<?php echo $category; ?>&gender=<?php echo $gender; ?>&material=<?php echo $material; ?>" class="pagination__btn">Prev</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&category=<?php echo $category; ?>&gender=<?php echo $gender; ?>&material=<?php echo $material; ?>"
                   class="pagination__btn <?php if($i == $page) echo 'active'; ?>">
                   <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo ($page+1); ?>&category=<?php echo $category; ?>&gender=<?php echo $gender; ?>&material=<?php echo $material; ?>" class="pagination__btn">Next</a>
            <?php endif; ?>
        </div>

    </section>
</main>

<?php include('include/news.php') ?>
<?php include('include/footer.php') ?>
</body>
</html>
