<?php
include('include/header.php');
include('include/navbar.php');

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$user_id) {
    echo "<script>window.location.href='login-register.php';</script>";
    exit();
}

include('include/db_connect.php');

// Fetch wishlist items for the user
$sql = "
SELECT w.wishlist_id AS wishlist_id, f.frame_id, f.name AS frame_name, f.price, f.gender, f.material,
       fc.name AS category_name, fi.image_url
FROM wishlist w
JOIN frames f ON w.product_id = f.frame_id
LEFT JOIN frame_category_map fcm ON f.frame_id = fcm.frame_id
LEFT JOIN frame_category fc ON fcm.category_id = fc.category_id
LEFT JOIN frame_images fi ON f.frame_id = fi.frame_id
WHERE w.user_id = ?
GROUP BY f.frame_id
ORDER BY w.wishlist_id DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>
<main class="main">
    <section class="wishlist container section--lg">
        <br>
        <h2 class="section__title">My Wishlist</h2>
        <div class="products__container grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="product__item">
                        <div class="product__banner">
                            <a href="details.php?frame_id=<?= $row['frame_id'] ?>" class="product__images">
                                <img src="uploads/<?= $row['image_url'] ?>" alt="<?= htmlspecialchars($row['frame_name']) ?>" class="product__img default" />
                                <img src="uploads/<?= $row['image_url'] ?>" alt="<?= htmlspecialchars($row['frame_name']) ?>" class="product__img hover" />
                            </a>
                            <div class="product__actions">
                                <a href="details.php?frame_id=<?= $row['frame_id'] ?>" class="action__btn" aria-label="Quick View">
                                    <i class="fi fi-rs-eye"></i>
                                </a>
                                <a href="remove_wishlist.php?id=<?= $row['wishlist_id'] ?>" class="action__btn" aria-label="Remove from Wishlist" onclick="return confirm('Remove this item from wishlist?');">
                                    <i class="fi fi-rs-trash"></i>
                                </a>
                            </div>
                            <div class="product__badge light-pink">Wishlist</div>
                        </div>
                        <div class="product__content">
                            <span class="product__category"><?= htmlspecialchars($row['category_name']) ?></span>
                            <a href="details.php?frame_id=<?= $row['frame_id'] ?>">
                                <h3 class="product__title"><?= htmlspecialchars($row['frame_name']) ?></h3>
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
                            </div>
                            <a href="#" class="action__btn cart__btn add-to-cart-btn" data-frame-id="<?= $row['frame_id'] ?>" aria-label="Add To Cart">
                                <i class="fi fi-rs-shopping-bag-add"></i>
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="grid-column: 1/-1; text-align:center; color:#888; font-size:1.2rem;">Your wishlist is empty.</p>
            <?php endif; ?>
        </div>
    </section>
</main>
<script>
document.querySelectorAll('.add-to-cart-btn').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        var frameId = btn.getAttribute('data-frame-id');
        var fd = new FormData();
        fd.append('frame_id', frameId);
        fetch('api/save_to_cart.php', {
            method: 'POST',
            body: fd
        })
        .then(response => response.json())
        .then(function(res) {
            if (res.success) {
                window.location.href = 'cart.php';
            } else if (res.message && res.message.toLowerCase().includes('user') && res.message.toLowerCase().includes('login')) {
                window.location.href = 'login-register.php';
            } else {
                alert(res.message || 'Error adding to cart');
            }
        })
        .catch(function() {
            alert('Error adding to cart');
        });
    });
});
</script>
<?php include('include/footer.php'); ?>
