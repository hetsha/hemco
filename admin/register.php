<?php
include 'config/db.php';
// session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO admin (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        $_SESSION['admin_id'] = $stmt->insert_id;
        header("Location: index.php");
        exit;
    } else {
        $error = "Registration failed. Email may already exist.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white-50 dark:bg-gray-900 flex items-center justify-center h-screen">
  <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center text-blue-600 dark:text-blue-400">Admin Register</h2>
    <form action="register.php" method="POST" class="space-y-4">
      <div>
        <label class="block mb-1 font-semibold">Name</label>
        <input type="text" name="name" required class="w-full px-4 py-2 rounded border border-blue-200 focus:outline-none focus:ring focus:border-blue-400 dark:bg-gray-700 dark:text-white" />
      </div>
      <div>
        <label class="block mb-1 font-semibold">Email</label>
        <input type="email" name="email" required class="w-full px-4 py-2 rounded border border-blue-200 focus:outline-none focus:ring focus:border-blue-400 dark:bg-gray-700 dark:text-white" />
      </div>
      <div>
        <label class="block mb-1 font-semibold">Password</label>
        <input type="password" name="password" required class="w-full px-4 py-2 rounded border border-blue-200 focus:outline-none focus:ring focus:border-blue-400 dark:bg-gray-700 dark:text-white" />
      </div>
      <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Register</button>
    </form>
    <p class="text-sm text-center mt-4">Already have an account? <a href="login.html" class="text-blue-500">Login</a></p>
  </div>
</body>
</html>
