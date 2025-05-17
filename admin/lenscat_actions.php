<?php
// CRUD for lens categories
include 'config/db.php';
$action = $_POST['action'] ?? '';
$id = $_POST['id'] ?? null;
$name = $_POST['name'] ?? '';
if ($action == 'add') {
  $stmt = $conn->prepare('INSERT INTO lens_categories (name) VALUES (?)');
  $stmt->bind_param('s', $name);
  $stmt->execute();
  echo 'Added'; exit;
}
if ($action == 'edit' && $id) {
  $stmt = $conn->prepare('UPDATE lens_categories SET name=? WHERE id=?');
  $stmt->bind_param('si', $name, $id);
  $stmt->execute();
  echo 'Updated'; exit;
}
if ($action == 'delete' && $id) {
  $stmt = $conn->prepare('DELETE FROM lens_categories WHERE id=?');
  $stmt->bind_param('i', $id);
  $stmt->execute();
  echo 'Deleted'; exit;
}
if ($action == 'get') {
  $result = $conn->query('SELECT * FROM lens_categories');
  $rows = [];
  while ($row = $result->fetch_assoc()) $rows[] = $row;
  header('Content-Type: application/json');
  echo json_encode($rows); exit;
}
