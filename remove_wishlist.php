<?php
// remove_wishlist.php
session_start();
include('include/db_connect.php');

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login-register.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$wishlist_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$wishlist_id) {
    echo "<script>alert('Invalid request.'); window.location.href='wishlist.php';</script>";
    exit();
}

// Only allow removing wishlist items belonging to the logged-in user
$stmt = $conn->prepare('DELETE FROM wishlist WHERE wishlist_id = ? AND user_id = ?');
$stmt->bind_param('ii', $wishlist_id, $user_id);
if ($stmt->execute()) {
    echo "<script>alert('Removed from wishlist.'); window.location.href='wishlist.php';</script>";
} else {
    echo "<script>alert('Failed to remove from wishlist.'); window.location.href='wishlist.php';</script>";
}
