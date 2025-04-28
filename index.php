<?php include('include/header.php') ?>
  <body>
    <?php include('include/navbar.php') ?>
    <!--=============== MAIN ===============-->
    <main class="main">

      <!--=============== HOME ===============-->

      <section class="home section--lg">
        <?php include('include/slider.php') ?>
      </section>

      <?php
if (isset($_SESSION['user_name'])) {
    echo "<h1>Welcome, " . htmlspecialchars($_SESSION['user_name']) . "!</h1>";
    echo "<p>You are logged in as: " . htmlspecialchars($_SESSION['user_email']) . "</p>";
    echo '<a href="logout.php">Logout</a>';
} else {
    echo '<a href="google-login.php">Login with Google</a>';
}
?>

      <?php include('include/cat.php') ?>
      <!--=============== PRODUCTS ===============-->
      <?php include('include/pro.php') ?>

      <!--=============== DEALS ===============-->

      <?php include('include/deals.php') ?>

      <!--=============== NEW ARRIVALS ===============-->
      <?php include('include/product.php') ?>

      <!--=============== SHOWCASE ===============-->
      <?php include('include/show.php') ?>

      <!--=============== NEWSLETTER ===============-->
      <?php include('include/news.php') ?>

    </main>

    <!--=============== FOOTER ===============-->
    <?php include('include/footer.php') ?>

  </body>
</html>
