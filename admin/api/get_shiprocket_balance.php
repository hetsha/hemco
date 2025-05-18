<?php
// filepath: c:/xampp/htdocs/business/hemco/admin/api/get_shiprocket_balance.php
// Returns Shiprocket wallet balance as JSON
header('Content-Type: application/json');

// Use hardcoded credentials for Shiprocket (move to env/config for production)
$shiprocket_email = 'hetlj6315@gmail.com';
$shiprocket_password = 'JfZ%&vO5jsej76jz';

function getShiprocketToken($email, $password) {
    $ch = curl_init('https://apiv2.shiprocket.in/v1/external/auth/login');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['email' => $email, 'password' => $password]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    return $data['token'] ?? null;
}

function getShiprocketBalance($token) {
    $ch = curl_init('https://apiv2.shiprocket.in/v1/external/account/details');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ]);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $data = json_decode($response, true);
    if ($http_code === 200 && isset($data['data']['wallet_balance'])) {
        return $data['data']['wallet_balance'];
    }
    return 0; // fallback to 0 if not found or error
}

$token = getShiprocketToken($shiprocket_email, $shiprocket_password);
if ($token) {
    $balance = getShiprocketBalance($token);
    echo json_encode(['success' => true, 'balance' => $balance]);
    exit;
}
echo json_encode(['success' => false, 'balance' => 0, 'error' => 'Unable to fetch balance']);
