<?php
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE admin SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $new_password, $email);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $success = "Password reset successful. You can now <a href='login.php'>login</a>.";
    } else {
        $error = "Email not found or error updating password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Reset Password</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-50 dark:bg-gray-900 flex items-center justify-center h-screen">
  <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center text-blue-600 dark:text-blue-400">Reset Password</h2>
    <?php if (!empty($success)): ?>
      <div class="mb-4 text-green-600 text-center"><?= $success ?></div>
    <?php elseif (!empty($error)): ?>
      <div class="mb-4 text-red-600 text-center"><?= $error ?></div>
    <?php endif; ?>
    <form action="reset_password.php" method="POST" class="space-y-4">
      <div>
        <label class="block mb-1 font-semibold">Email</label>
        <input type="email" name="email" required class="w-full px-4 py-2 rounded border border-blue-200 focus:outline-none focus:ring focus:border-blue-400 dark:bg-gray-700 dark:text-white" />
      </div>
      <div>
        <label class="block mb-1 font-semibold">New Password</label>
        <input type="password" name="new_password" required class="w-full px-4 py-2 rounded border border-blue-200 focus:outline-none focus:ring focus:border-blue-400 dark:bg-gray-700 dark:text-white" />
      </div>
      <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Reset Password</button>
    </form>
    <p class="text-sm text-center mt-4"><a href="login.php" class="text-blue-500">Back to Login</a></p>
  </div>
</body>
</html>
