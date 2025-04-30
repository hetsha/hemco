<?php
header('Content-Type: application/json');
include('../include/db_connect.php');

// Fetch categories from lens_category table
$query = "SELECT category_id as id, type as name, description FROM lens_category";
$result = $conn->query($query);

$categories = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categories[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'description' => $row['description']
        ];
    }
}

echo json_encode(['success' => true, 'categories' => $categories]);
?>
