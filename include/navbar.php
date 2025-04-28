<?php
// Database connection
$mysqli = new mysqli("localhost", "root", "", "testing");

if ($mysqli->connect_error) {
  die("Connection failed: " . $mysqli->connect_error);
}

?>

<!--=============== HEADER ===============-->
<header class="header">
      <!-- <div class="header__top">
        <div class="header__container container">
          <div class="header__contact">
            <span>(+01) - 2345 - 6789</span>
            <span>Our location</span>
          </div>
          <p class="header__alert-news">
            Super Values Deals - Save more coupons
          </p>
          <a href="login-register.php" class="header__top-action">
            Log In / Sign Up
          </a>
        </div>
      </div> -->

      <nav class="nav">

        <a href="index.php" class="nav__logo">
          <img
            class="nav__logo-img"
            src="assets/img/logo.png"
            alt="website logo" style="width:70px; height: auto;"
          />
        </a>
        <div class="nav__menu" id="nav-menu">
          <div class="nav__menu-top">
            <a href="index.php" class="nav__menu-logo">
              <img src="./assets/img/logo.svg" alt="">
            </a>
            <div class="nav__close" id="nav-close">
              <i class="fi fi-rs-cross-small"></i>
            </div>
          </div>
          <ul class="nav__list">
            <li class="nav__item">
              <a href="index.php" class="nav__link active-link">Home</a>
            </li>
            <li class="nav__item">
              <a href="shop.php" class="nav__link">Shop</a>
            </li>
            <li class="nav__item">
              <a href="accounts.php" class="nav__link">My Account</a>
            </li>
            <li class="nav__item">
              <a href="compare.php" class="nav__link">Compare</a>
            </li>
            <li class="nav__item">
              <a href="contact.php" class="nav__link">Contact</a>
            </li>
            <li class="nav__item">
              <a href="login-register.php" class="nav__link">Login</a>
            </li>
          </ul>
          <div class="header__search">
            <input
              type="text"
              placeholder="Search For Items..."
              class="form__input"
            />
            <button class="search__btn">
              <img src="assets/img/search.png" alt="search icon" />
            </button>
          </div>
        </div>
        <div class="header__user-actions">
          <a href="wishlist.php" class="header__action-btn" title="Wishlist" style="color:black;">
          <i class="fa-regular fa-heart"></i>
            <span class="count">3</span>
          </a>
          <a href="cart.php" class="header__action-btn" title="Cart" style="color:black;">
          <i class="fa-regular fa-cart-shopping-fast"></i>
            <span class="count">3</span>
          </a>
          <div class="header__action-btn nav__toggle" id="nav-toggle">
            <img src="./assets//img/menu-burger.svg" alt="">
          </div>
        </div>
      </nav>
    </header>

    <nav class="navs">
      <ul class="nav__lists">
         <li>
            <a href="index.php" class="nav__links active-link" style="color:black;">
            <i class="fa-regular fa-house"></i>
            </a>
         </li>

         <li>
            <a href="accounts.php" class="nav__link" style="color:black;">
              <i class="fa-regular fa-user"></i>
            </a>
         </li>

         <!-- Expand list -->
         <!-- <li>
            <button class="nav__expands" id="nav-expand">
               <i class="ri-add-line nav__expand-icons" id="nav-expand-icon"></i>
            </button>

            <ul class="nav__expand-lists" id="nav-expand-list">
               <li>
                  <a href="#" class="nav__expand-link" style="color:black;">
                  <i class="fa-regular fa-house"></i>
                     <span>Gallery</span>
                  </a>
               </li>

               <li>
                  <a href="#" class="nav__expand-links" style="color:black;">
                     <i class="ri-archive-line"></i>
                     <span>Files</span>
                  </a>
               </li>

               <li>
                  <a href="#" class="nav__expand-links" style="color:black;">
                     <i class="ri-bookmark-3-line"></i>
                     <span>Saved</span>
                  </a>
               </li>
            </ul>
         </li> -->

         <li>
            <a href="wishlist.php" class="nav__links" style="color:black;">
              <i class="fa-regular fa-heart"></i>
            </a>
         </li>

         <li>
            <a href="cart.php" class="nav__links" style="color:black;">
              <i class="fa-regular fa-cart-shopping-fast"></i>
            </a>
         </li>
      </ul>
   </nav>
<script>
  // Get current page URL
const currentLocation = window.location.href;

// Get all nav links
const navLinks = document.querySelectorAll('.nav__link');

// Loop through links and add 'active-link' to the current page link
navLinks.forEach((link) => {
  if (link.href === currentLocation) {
    link.classList.add('active-link');
  } else {
    link.classList.remove('active-link');
  }
});

</script>