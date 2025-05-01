<?php
session_start();
$shipping_cost = 10.00; // default value

if (isset($_POST['calculate_shipping'])) {
    $destination_pincode = $_POST['postcode'];
    $city = $_POST['city'];
    $state = $_POST['state'];

    // Step 1: Authenticate with Shiprocket
    $email = "hetshah6312@gmail.com";
    $password = "lmlCZ^f$@v93PSht";

    // Prepare the context for the POST request
    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => 'Content-Type: application/json',
            'content' => json_encode(['email' => $email, 'password' => $password]),
            'ignore_errors' => true // Allow reading response even on failure
        ]
    ];
    $context = stream_context_create($options);

    // Send request to Shiprocket API
    $auth_response = file_get_contents("https://apiv2.shiprocket.in/v1/external/auth/login", false, $context);

    // Debugging: Check if we have received a valid response
    echo "<script>alert('Authentication response: " . $auth_response . "');</script>"; // Debugging API response
    $response_headers = $http_response_header; // Built-in variable
    foreach ($response_headers as $header) {
        if (strpos($header, 'HTTP/') === 0) {
            echo "<script>alert('HTTP Status: " . $header . "');</script>"; // Debugging HTTP status
        }
    }

    // Decode the JSON response
    $auth_data = json_decode($auth_response, true);

    // Debugging: Check if we successfully decoded the response
    echo "<script>alert('Decoded Authentication Response: " . print_r($auth_data, true) . "');</script>"; // Check if response is decoded

    // Check if the token is returned
    if (!isset($auth_data['token'])) {
        $_SESSION['shipping_error'] = "Authentication failed: " . ($auth_data['error'] ?? 'Unknown error');
        echo "<script>alert('Authentication failed: " . ($auth_data['error'] ?? 'Unknown error') . "');</script>"; // Debugging failed authentication
        header("Location: ../cart.php");
        exit;
    }

    $token = $auth_data['token'];
    echo "<script>alert('Token received: " . $token . "');</script>"; // Debugging token

    // Step 2: Call shipping rate API
    $pickup_pincode = "380001";
    $weight = 0.5;
    $length = 10;
    $breadth = 10;
    $height = 5;

    $rate_url = "https://apiv2.shiprocket.in/v1/external/courier/serviceability/";
    $query = http_build_query([
        'pickup_postcode' => $pickup_pincode,
        'delivery_postcode' => $destination_pincode,
        'cod' => 0,
        'weight' => $weight,
        'length' => $length,
        'breadth' => $breadth,
        'height' => $height
    ]);

    // Get the shipping rates
    $rate_response = file_get_contents($rate_url . "?" . $query, false, stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "Authorization: Bearer $token\r\n"
        ]
    ]));

    // Debugging: Check the shipping rate response
    echo "<script>alert('Shipping rate response: " . $rate_response . "');</script>"; // Debugging shipping rate API response

    $rate_data = json_decode($rate_response, true);
    // Debugging: Check if the shipping data is retrieved
    echo "<script>alert('Decoded Shipping Rate Response: " . print_r($rate_data, true) . "');</script>"; // Check decoded shipping rate response

    if (!empty($rate_data['data']) && count($rate_data['data']) > 0) {
        $shipping_cost = $rate_data['data'][0]['rate'];
        echo "<script>alert('Shipping cost: " . $shipping_cost . "');</script>"; // Debugging shipping cost
    } else {
        echo "<script>alert('No shipping data found.');</script>"; // If no data found
    }

    $_SESSION['shipping_cost'] = $shipping_cost;
    $_SESSION['shipping_pincode'] = $destination_pincode;
    $_SESSION['shipping_city'] = $city;
    $_SESSION['shipping_state'] = $state;
}

// Redirect back to cart page
header("Location: ../cart.php");
exit;
?>
