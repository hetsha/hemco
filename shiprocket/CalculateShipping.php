<?php
session_start();
$shipping_cost = 10.00; // default fallback

if (isset($_POST['calculate_shipping'])) {
    $destination_pincode = $_POST['postcode'];
    $city = $_POST['city'];
    $state = $_POST['state'];

    // Step 1: Authenticate
    $email = "hetlj6315@gmail.com";
    $password = "JfZ%&vO5jsej76jz";

    $auth_options = [
        'http' => [
            'method'  => 'POST',
            'header'  => 'Content-Type: application/json',
            'content' => json_encode(['email' => $email, 'password' => $password]),
            'ignore_errors' => true
        ]
    ];
    $auth_context = stream_context_create($auth_options);
    $auth_response = file_get_contents("https://apiv2.shiprocket.in/v1/external/auth/login", false, $auth_context);
    $auth_data = json_decode($auth_response, true);

    if (!isset($auth_data['token'])) {
        echo "<strong>Authentication failed:</strong> " . ($auth_data['error'] ?? 'Unknown error');
        exit;
    }

    $token = $auth_data['token'];

    // Step 2: Check courier serviceability
    $pickup_pincode = "380001";
    $weight = 0.5;
    $length = 10;
    $breadth = 10;
    $height = 5;

    $query = http_build_query([
        'pickup_postcode' => $pickup_pincode,
        'delivery_postcode' => $destination_pincode,
        'cod' => 0,
        'weight' => $weight,
        'length' => $length,
        'breadth' => $breadth,
        'height' => $height
    ]);

    $url = "https://apiv2.shiprocket.in/v1/external/courier/serviceability/?" . $query;

    $rate_context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "Authorization: Bearer $token\r\n"
        ]
    ]);

    $rate_response = file_get_contents($url, false, $rate_context);

    if ($rate_response === false) {
        echo "<strong>Failed to retrieve shipping rates. Check API URL or credentials.</strong>";
        exit;
    }

    $rate_data = json_decode($rate_response, true);

    if (isset($rate_data['data']) && is_array($rate_data['data'])) {
        foreach ($rate_data['data']['available_courier_companies'] as $courier) {
            if (isset($courier['rate']) && !empty($courier['rate'])) {
                $shipping_cost = $courier['rate'];
                break;
            }
        }
    }

    // Save to session
    $_SESSION['shipping_cost'] = $shipping_cost;
    $_SESSION['shipping_pincode'] = $destination_pincode;
    $_SESSION['shipping_city'] = $city;
    $_SESSION['shipping_state'] = $state;

    // Redirect to cart.php
    header("Location: ../cart.php");
    exit;
}
?>
