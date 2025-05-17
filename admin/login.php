<?php
include 'config/db.php';
// session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT admin_id, password FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($admin_id, $hashed_password);
    if ($stmt->fetch() && password_verify($password, $hashed_password)) {
        $_SESSION['admin_id'] = $admin_id;
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-50 dark:bg-gray-900 flex items-center justify-center h-screen">
  <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center text-blue-600 dark:text-blue-400">Admin Login</h2>
    <?php if (!empty($error)): ?>
      <div class="mb-4 text-red-600 text-center"><?= $error ?></div>
    <?php endif; ?>
    <form action="login.php" method="POST" class="space-y-4">
      <div>
        <label class="block mb-1 font-semibold">Email</label>
        <input type="email" name="email" required class="w-full px-4 py-2 rounded border border-blue-200 focus:outline-none focus:ring focus:border-blue-400 dark:bg-gray-700 dark:text-white" />
      </div>
      <div>
        <label class="block mb-1 font-semibold">Password</label>
        <input type="password" name="password" required class="w-full px-4 py-2 rounded border border-blue-200 focus:outline-none focus:ring focus:border-blue-400 dark:bg-gray-700 dark:text-white" />
      </div>
      <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Login</button>
    </form>
    <p class="text-sm text-center mt-4">Don't have an account? <a href="register.php" class="text-blue-500">Register</a></p>
    <p class="text-sm text-center mt-2"><a href="reset_password.php" class="text-blue-400">Forgot Password?</a></p>
  </div>
</body>
</html>
