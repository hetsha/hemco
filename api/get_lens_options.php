<?php
header('Content-Type: application/json');

try {
    // Include database connection
    if (!file_exists('../include/db_connect.php')) {
        throw new Exception('Database configuration file not found');
    }
    include('../include/db_connect.php');

    // Validate input
    if (!isset($_GET['category_id'])) {
        throw new Exception('Category ID is required');
    }

    $category_id = intval($_GET['category_id']);
    if ($category_id <= 0) {
        throw new Exception('Invalid category ID');
    }

    // Prepare and execute query
    $query = "SELECT lens_id, type, description, price FROM lens WHERE category_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }

    $stmt->bind_param("i", $category_id);
    if (!$stmt->execute()) {
        throw new Exception('Failed to execute query: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception('Failed to get result: ' . $stmt->error);
    }

    // Process results
    $lenses = [];
    while($row = $result->fetch_assoc()) {
        $lenses[] = [
            'id' => intval($row['lens_id']),
            'name' => $row['type'],
            'description' => $row['description'] ?? '',
            'price' => floatval($row['price'])
        ];
    }

    // Return success response
    echo json_encode([
        'success' => true,
        'lenses' => $lenses
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
