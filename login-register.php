<?php
include('include/db_connect.php');

$message = "";
if (isset($_GET['msg'])) {
    $message = htmlspecialchars($_GET['msg']);
}
// Login functionality
if (isset($_POST['login'])) {
  $email = $_POST['login_email'];
  $password = $_POST['login_password'];

  $result = $conn->query("SELECT * FROM user WHERE email='$email'");

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    if (empty($row['password'])) {
      // User registered via Google, cannot login manually
      $message = "Please login using Google.";
    } else {
      if (password_verify($password, $row['password'])) {
        // ✅ Set same session
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['user_name'] = $row['name'];
        $_SESSION['user_email'] = $row['email'];
        // Redirect to return URL if set, else homepage
        if (isset($_GET['return']) && !empty($_GET['return'])) {
          $returnUrl = filter_var($_GET['return'], FILTER_SANITIZE_URL);
          header("Location: " . $returnUrl);
        } else {
          header("Location: index.php");
        }
        exit;
      } else {
        $message = "Invalid password.";
      }
    }
  } else {
    $message = "Email not registered.";
  }
}

// Register functionality
if (isset($_POST['register'])) {
  $name = $_POST['register_name'];
  $email = $_POST['register_email'];
  $password = $_POST['register_password'];
  $confirm_password = $_POST['register_confirm_password'];
  $phone = "";
  $address = "";
  $zip_code = "";

  if ($password != $confirm_password) {
    $message = "Passwords do not match.";
  } else {
    $check_email = $conn->query("SELECT * FROM user WHERE email='$email'");
    if ($check_email->num_rows > 0) {
      $message = "Email already registered.";
    } else {
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);

      $insert = $conn->query("INSERT INTO user (name, email, password, phone, address, zip_code)
            VALUES ('$name', '$email', '$hashed_password', '$phone', '$address', '$zip_code')");

      if ($insert) {
        $message = "Registered successfully. Please login.";
      } else {
        $message = "Registration failed. Try again.";
      }
    }
  }
}
?>

<?php include('include/header.php') ?>
<?php include('include/navbar.php') ?>

<main class="main">
  <div class="blank"></div>

  <section class="breadcrumb">
    <ul class="breadcrumb__list flex container">
      <li><a href="index.php" class="breadcrumb__link">Home</a></li>
      <li><span class="breadcrumb__link">></span></li>
      <li><span class="breadcrumb__link">Login / Register</span></li>
    </ul>
    <?php /* if ($message != "") { ?>
      <div style="color: red; margin-bottom:20px;"><?php echo $message; ?></div>
    <?php } */ ?>
  </section>

  <section class="login-register section--lg">
    <div class="login-register__container container grid">

      <div class="login">
        <h3 class="section__title">Login</h3>
        <form class="form grid" method="POST">
          <input type="email" name="login_email" placeholder="Your Email" class="form__input" required />
          <input type="password" name="login_password" placeholder="Your Password" class="form__input" required />
          <div class="form__btn" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button type="submit" name="login" class="btn">Login</button>
            <a href="google-login.php" class="btn" style="display: flex; align-items: center; justify-content: center; gap: 8px;">
              <i class="fa-brands fa-google"></i> Login with Google
            </a>
          </div>

        </form>
      </div>

      <div class="register">
        <h3 class="section__title">Create an Account</h3>
        <form class="form grid" method="POST">
          <input type="text" name="register_name" placeholder="Username" class="form__input" required />
          <input type="email" name="register_email" placeholder="Your Email" class="form__input" required />
          <input type="password" name="register_password" placeholder="Your Password" class="form__input" required />
          <input type="password" name="register_confirm_password" placeholder="Confirm Password" class="form__input" required />
          <div class="form__btn">
            <button type="submit" name="register" class="btn">Submit & Register</button>
          </div>
        </form>
      </div>

    </div>
  </section>

  <?php include('include/news.php') ?>
</main>

<!-- Toast Popup -->
<div id="toast-message" style="display:none;position:fixed;top:30px;right:30px;z-index:9999;min-width:220px;padding:16px 28px;background:linear-gradient(90deg,#2563eb 0,#1e293b 100%);color:#fff;border-radius:10px;box-shadow:0 4px 18px #0003;font-size:1.08em;font-weight:500;letter-spacing:0.01em;transition:all 0.3s;"></div>

<script>
<?php if ($message != "") { ?>
  document.addEventListener('DOMContentLoaded', function() {
    var toast = document.getElementById('toast-message');
    toast.textContent = <?php echo json_encode($message); ?>;
    toast.style.display = 'block';
    setTimeout(function() {
      toast.style.display = 'none';
    }, 3500);
  });
<?php } ?>
</script>

<?php include('include/footer.php'); ?>
</body>

</html>