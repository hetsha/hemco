<?php
include 'include/db_connect.php';

$category_id = $_GET['category_id'];
$sql = "SELECT * FROM lens WHERE category_id = $category_id";
$result = mysqli_query($conn, $sql);

$lenses = [];
while ($row = mysqli_fetch_assoc($result)) {
    $lenses[] = $row;
}
echo json_encode($lenses);
?>
