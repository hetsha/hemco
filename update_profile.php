<?php
include('include/db_connect.php');
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login-register.php');
    exit;
}
$user_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $zip_code = mysqli_real_escape_string($conn, $_POST['zip_code']);
    $sql = "UPDATE user SET name='$name', email='$email', phone='$phone', address='$address', zip_code='$zip_code' WHERE user_id=$user_id";
    mysqli_query($conn, $sql);
    header('Location: accounts.php');
    exit;
}
header('Location: accounts.php');
exit;
