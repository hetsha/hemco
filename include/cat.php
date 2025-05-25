<!--=============== CATEGORIES ===============-->
<section class="categories container section nonedown" style="display: block;">
  <h3 class="section__title">Select by Categories</h3>
  <div class="categories__container swiper">
    <div class="swiper-wrapper">
      <a href="shop.php?cat=eyeglasses" class="category__item swiper-slide enhanced-category">
        <div class="category__img-wrap-alt">
          <img src="assets/cat/eyeglasses.png" alt="Eyeglasses" class="category__img" />
        </div>
        <h3 class="category__title">Eyeglasses</h3>
      </a>
      <a href="shop.php?cat=men" class="category__item swiper-slide enhanced-category">
        <div class="category__img-wrap-alt">
          <img src="assets/cat/men.png" alt="Men" class="category__img" />
        </div>
        <h3 class="category__title">Men</h3>
      </a>
      <a href="shop.php?cat=women" class="category__item swiper-slide enhanced-category">
        <div class="category__img-wrap-alt">
          <img src="assets/cat/women.png" alt="Women" class="category__img" />
        </div>
        <h3 class="category__title">Women</h3>
      </a>
      <a href="shop.php?cat=kids" class="category__item swiper-slide enhanced-category">
        <div class="category__img-wrap-alt">
          <img src="assets/cat/kids.png" alt="Kids" class="category__img" />
        </div>
        <h3 class="category__title">Kids</h3>
      </a>
      <a href="shop.php?cat=sunglasses" class="category__item swiper-slide enhanced-category">
        <div class="category__img-wrap-alt">
          <img src="assets/cat/sunglasses.png" alt="Sunglasses" class="category__img" />
        </div>
        <h3 class="category__title">Sunglasses</h3>
      </a>
      <a href="shop.php?cat=computer" class="category__item swiper-slide enhanced-category">
        <div class="category__img-wrap-alt">
          <img src="assets/cat/computer.png" alt="Computer Glasses" class="category__img" />
        </div>
        <h3 class="category__title">Computer Glasses</h3>
      </a>
      <a href="shop.php?cat=power" class="category__item swiper-slide enhanced-category">
        <div class="category__img-wrap-alt">
          <img src="assets/cat/power.png" alt="Power Glasses" class="category__img" />
        </div>
        <h3 class="category__title">Power Glasses</h3>
      </a>
      <a href="shop.php?cat=accessories" class="category__item swiper-slide enhanced-category">
        <div class="category__img-wrap-alt">
          <img src="assets/cat/accessories.png" alt="Accessories" class="category__img" />
        </div>
        <h3 class="category__title">Accessories</h3>
      </a>
    </div>
    <div class="swiper-button-prev">
      <i class="fi fi-rs-angle-left"></i>
    </div>
    <div class="swiper-button-next">
      <i class="fi fi-rs-angle-right"></i>
    </div>
  </div>
</section>
<style>
.enhanced-category {
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 4px 18px #0001;
  padding: 1.2rem 1rem 1.1rem 1rem;
  margin: 0 10px;
  display: flex;
  flex-direction: column;
  align-items: center;
  transition: box-shadow 0.2s, transform 0.2s;
  border: 1.5px solid #e5e7eb;
  min-width: 140px;
  max-width: 170px;
}
.enhanced-category:hover {
  box-shadow: 0 8px 32px #2563eb33;
  transform: translateY(-4px) scale(1.04);
  border-color: #2563eb;
}
.category__img-wrap-alt {
  background: linear-gradient(135deg, #e0e7ff 0%, #f1f5f9 100%);
  border-radius: 16px;
  padding: 10px;
  margin-bottom: 0.7rem;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 8px #2563eb11;
  width: 70px;
  height: 70px;
}
.category__img {
  width: 44px;
  height: 44px;
  object-fit: contain;
  filter: drop-shadow(0 2px 6px #2563eb22);
  border-radius: 10px;
}
.category__title {
  font-size: 1.08em;
  font-weight: 600;
  color: #1e293b;
  margin-top: 0.2em;
  letter-spacing: 0.01em;
  text-align: center;
}
@media (max-width: 600px) {
  .enhanced-category {
    min-width: 110px;
    max-width: 120px;
    padding: 0.7rem 0.5rem 0.7rem 0.5rem;
  }
  .category__img-wrap-alt {
    width: 48px;
    height: 48px;
    padding: 6px;
    border-radius: 10px;
  }
  .category__img {
    width: 26px;
    height: 26px;
    border-radius: 6px;
  }
  .category__title {
    font-size: 0.98em;
  }
}
</style>
<!--=============== END ENHANCED CATEGORIES ===============-->
<section class="categories container section none" style="display: none;">
  <h3 class="section__title">Popular Eyeglasses</h3>
  <div class="categories__container swiper">
    <div class="swiper-wrapper">
      <a href="shop.php?cat=tshirt" class="category__item swiper-slide">
        <img src="assets/cat/test.jpg" alt="" class="category__img" />
        <h3 class="category__title">T-Shirt</h3>
      </a>
      <a href="shop.php?cat=bags" class="category__item swiper-slide">
        <img src="assets/cat/test.jpg" alt="" class="category__img" />
        <h3 class="category__title">Bags</h3>
      </a>
      <a href="shop.php?cat=sandal" class="category__item swiper-slide">
        <img src="assets/cat/test.jpg" alt="" class="category__img" />
        <h3 class="category__title">Sandal</h3>
      </a>
      <a href="shop.php?cat=scarfcap" class="category__item swiper-slide">
        <img src="assets/cat/test.jpg" alt="" class="category__img" />
        <h3 class="category__title">Scarf Cap</h3>
      </a>
    </div>
    <div class="swiper-button-prev">
      <i class="fi fi-rs-angle-left"></i>
    </div>
    <div class="swiper-button-next">
      <i class="fi fi-rs-angle-right"></i>
    </div>
  </div>
</section>
<section class="categories container section none" style="display: none;">
  <h3 class="section__title">Popular Sunglasses</h3>
  <div class="categories__container swiper">
    <div class="swiper-wrapper">
      <a href="shop.php?cat=tshirt" class="category__item swiper-slide">
        <img src="assets/cat/test.jpg" alt="" class="category__img" />
        <h3 class="category__title">T-Shirt</h3>
      </a>
      <a href="shop.php?cat=bags" class="category__item swiper-slide">
        <img src="assets/cat/test.jpg" alt="" class="category__img" />
        <h3 class="category__title">Bags</h3>
      </a>
      <a href="shop.php?cat=sandal" class="category__item swiper-slide">
        <img src="assets/cat/test.jpg" alt="" class="category__img" />
        <h3 class="category__title">Sandal</h3>
      </a>
      <a href="shop.php?cat=scarfcap" class="category__item swiper-slide">
        <img src="assets/cat/test.jpg" alt="" class="category__img" />
        <h3 class="category__title">Scarf Cap</h3>
      </a>
    </div>
    <div class="swiper-button-prev">
      <i class="fi fi-rs-angle-left"></i>
    </div>
    <div class="swiper-button-next">
      <i class="fi fi-rs-angle-right"></i>
    </div>
  </div>
</section>
<section class="categories container section none" style="display: none;">
  <h3 class="section__title">Special Powers</h3>
  <div class="categories__container swiper">
    <div class="swiper-wrapper">
      <a href="shop.php?cat=tshirt" class="category__item swiper-slide">
        <img src="assets/cat/test.jpg" alt="" class="category__img" />
        <h3 class="category__title">T-Shirt</h3>
      </a>
      <a href="shop.php?cat=bags" class="category__item swiper-slide">
        <img src="assets/cat/test.jpg" alt="" class="category__img" />
        <h3 class="category__title">Bags</h3>
      </a>
      <a href="shop.php?cat=sandal" class="category__item swiper-slide">
        <img src="assets/cat/test.jpg" alt="" class="category__img" />
        <h3 class="category__title">Sandal</h3>
      </a>
      <a href="shop.php?cat=scarfcap" class="category__item swiper-slide">
        <img src="assets/cat/test.jpg" alt="" class="category__img" />
        <h3 class="category__title">Scarf Cap</h3>
      </a>
    </div>
    <div class="swiper-button-prev">
      <i class="fi fi-rs-angle-left"></i>
    </div>
    <div class="swiper-button-next">
      <i class="fi fi-rs-angle-right"></i>
    </div>
  </div>
</section>
