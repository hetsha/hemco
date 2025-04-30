<?php
include('include/db_connect.php');
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON header
header('Content-Type: application/json');

// Get JSON data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Debug log
$logFile = fopen('debug.log', 'a');
fwrite($logFile, "\n\n" . date('Y-m-d H:i:s') . " - New Request\n");
fwrite($logFile, "Raw input: " . $json . "\n");
fwrite($logFile, "Decoded data: " . print_r($data, true) . "\n");

$user_id = $_SESSION['user_id'] ?? null; // ensure session is set
fwrite($logFile, "User ID: " . ($user_id ?? 'null') . "\n");

if (!$user_id) {
    fwrite($logFile, "Error: User not logged in\n");
    fclose($logFile);
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Handle skip prescription case
if (isset($data['skip']) && $data['skip'] === true) {
    echo json_encode(['success' => true, 'message' => 'Prescription skipped']);
    exit;
}

// Validate required fields
if (!isset($data['left_eye_sph']) || !isset($data['right_eye_sph']) ||
    !isset($data['left_eye_cyl']) || !isset($data['right_eye_cyl']) ||
    !isset($data['axis']) || !isset($data['addition'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required prescription data']);
    exit;
}

// Insert prescription data
$prescriptionQuery = "INSERT INTO prescriptions (user_id, left_eye_sph, right_eye_sph, left_eye_cyl, right_eye_cyl, axis, addition)
                     VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($prescriptionQuery);
$stmt->bind_param("issssss",
    $user_id,
    $data['left_eye_sph'],
    $data['right_eye_sph'],
    $data['left_eye_cyl'],
    $data['right_eye_cyl'],
    $data['axis'],
    $data['addition']
);

if ($stmt->execute()) {
    $prescription_id = $stmt->insert_id;
    fwrite($logFile, "Success: Prescription saved with ID " . $prescription_id . "\n");
    fclose($logFile);
    echo json_encode(['success' => true, 'prescription_id' => $prescription_id]);
} else {
    fwrite($logFile, "Error: Failed to save prescription - " . $stmt->error . "\n");
    fclose($logFile);
    echo json_encode(['success' => false, 'message' => 'Failed to save prescription: ' . $stmt->error]);
}

?>
 $e->getMessage()]);
}
?>