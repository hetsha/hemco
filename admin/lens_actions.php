<?php
// CRUD for lenses
include 'config/db.php';
$action = $_POST['action'] ?? '';
$id = $_POST['id'] ?? null;
$name = $_POST['name'] ?? '';
$category_id = $_POST['category_id'] ?? null;
$brand_id = $_POST['brand_id'] ?? null;
$price = $_POST['price'] ?? null;
$description = $_POST['description'] ?? '';

if ($action == 'add') {
  $stmt = $conn->prepare('INSERT INTO lenses (name, category_id, brand_id, price, description) VALUES (?, ?, ?, ?, ?)');
  $stmt->bind_param('siiis', $name, $category_id, $brand_id, $price, $description);
  $stmt->execute();
  echo 'Added'; exit;
}
if ($action == 'edit' && $id) {
  $stmt = $conn->prepare('UPDATE lenses SET name=?, category_id=?, brand_id=?, price=?, description=? WHERE id=?');
  $stmt->bind_param('siiisi', $name, $category_id, $brand_id, $price, $description, $id);
  $stmt->execute();
  echo 'Updated'; exit;
}
if ($action == 'delete' && $id) {
  $stmt = $conn->prepare('DELETE FROM lenses WHERE id=?');
  $stmt->bind_param('i', $id);
  $stmt->execute();
  echo 'Deleted'; exit;
}
if ($action == 'get') {
  $result = $conn->query('SELECT * FROM lenses');
  $rows = [];
  while ($row = $result->fetch_assoc()) $rows[] = $row;
  header('Content-Type: application/json');
  echo json_encode($rows); exit;
}
