<?php
// add_to_wishlist.php
session_start();
include('include/db_connect.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User must login to add to wishlist.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$frame_id = isset($_POST['frame_id']) ? intval($_POST['frame_id']) : 0;

if (!$frame_id) {
    echo json_encode(['success' => false, 'message' => 'Missing frame_id.']);
    exit();
}

// Check if already in wishlist
$check = $conn->prepare('SELECT wishlist_id FROM wishlist WHERE user_id = ? AND product_id = ?');
$check->bind_param('ii', $user_id, $frame_id);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Already in wishlist.']);
    exit();
}

// Insert into wishlist
$stmt = $conn->prepare('INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)');
$stmt->bind_param('ii', $user_id, $frame_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Added to wishlist.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add to wishlist.']);
}
