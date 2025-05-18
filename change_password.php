<?php
include('include/db_connect.php');
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login-register.php');
    exit;
}
$user_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $user_sql = "SELECT password FROM user WHERE user_id = $user_id";
    $user_result = mysqli_query($conn, $user_sql);
    $user = mysqli_fetch_assoc($user_result);
    if ($user && password_verify($current_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE user SET password='$hashed' WHERE user_id=$user_id";
            mysqli_query($conn, $update_sql);
            header('Location: accounts.php');
            exit;
        } else {
            // Passwords do not match
            header('Location: accounts.php?error=nomatch#change-password');
            exit;
        }
    } else {
        // Current password incorrect
        header('Location: accounts.php?error=wrongpass#change-password');
        exit;
    }
}
header('Location: accounts.php');
exit;
