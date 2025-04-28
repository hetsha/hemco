<?php include('include/header.php') ?>

<!--=============== HEADER ===============-->
<?php include('include/navbar.php') ?>
<!--=============== MAIN ===============-->
<main class="main">
  <div class="blank"><br></div>
  <!--=============== BREADCRUMB ===============-->
  <section class="breadcrumb">
    <ul class="breadcrumb__list flex container">
      <li><a href="index.php" class="breadcrumb__link">Home</a></li>
      <li><span class="breadcrumb__link">></span></li>
      <li><span class="breadcrumb__link">Login / Register</span></li>
    </ul>
  </section>

  <!--=============== LOGIN-REGISTER ===============-->
  <section class="login-register section--lg">
    <div class="login-register__container container grid">
      <div class="login">
        <h3 class="section__title">Login</h3>
        <form class="form grid">
          <input
            type="email"
            placeholder="Your Email"
            class="form__input" />
          <input
            type="password"
            placeholder="Your Password"
            class="form__input" />
          <div class="form__btn" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button type="submit" class="btn">Login</button>

            <a href="google-login.php" class="btn" style="display: flex; align-items: center; justify-content: center; gap: 8px;">
              <i class="fa-brands fa-google"></i> Login with Google
            </a>
          </div>

        </form>
      </div>
      <div class="register">
        <h3 class="section__title">Create an Account</h3>
        <form class="form grid">
          <input
            type="text"
            placeholder="Username"
            class="form__input" />
          <input
            type="email"
            placeholder="Your Email"
            class="form__input" />
          <input
            type="password"
            placeholder="Your Password"
            class="form__input" />
          <input
            type="password"
            placeholder="Confirm Password"
            class="form__input" />
          <div class="form__btn">
            <button class="btn">Submit & Register</button>
          </div>
        </form>
      </div>
    </div>
  </section>

  <!--=============== NEWSLETTER ===============-->
  <?php include('include/news.php') ?>
</main>

<!--=============== FOOTER ===============-->
<?php include('include/footer.php'); ?>
</body>

</html>