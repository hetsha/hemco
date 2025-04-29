<?php
include('include/db_connect.php'); // DB connection

// Query to fetch lens categories
$sql = "SELECT * FROM lens_category";
$result = mysqli_query($conn, $sql);

// Check if the query was successful
if (!$result) {
    echo json_encode(["error" => "Database query failed"]);
    exit();
}

$categories = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row; // Add each category to the array
    }
    echo json_encode($categories); // Return categories as JSON
} else {
    echo json_encode([]);
}

mysqli_close($conn);
?>
