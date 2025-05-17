<?php
// Create Shiprocket Order API integration
// Usage: include and call create_shiprocket_order($order_id)

function create_shiprocket_order($order_id, $conn) {
    // 1. Fetch order, user, shipping, and items
    $order_sql = "SELECT o.*, s.shipping_address, s.name AS shipping_name, s.phone AS shipping_phone, s.email AS shipping_email, s.city AS shipping_city, s.state AS shipping_state, s.pincode AS shipping_pincode, s.country AS shipping_country
                  FROM orders o
                  LEFT JOIN shipping s ON o.order_id = s.order_id
                  WHERE o.order_id = $order_id";
    $order_result = mysqli_query($conn, $order_sql);
    $order = mysqli_fetch_assoc($order_result);
    if (!$order) return ['success'=>false, 'error'=>'Order not found'];

    $items_sql = "SELECT SUM(oi.quantity) as quantity, f.name AS name, oi.price, oi.frame_id
                  FROM order_items oi
                  LEFT JOIN frames f ON oi.frame_id = f.frame_id
                  WHERE oi.order_id = $order_id
                  GROUP BY oi.frame_id, oi.price";
    $items_result = mysqli_query($conn, $items_sql);
    $items = [];
    while ($row = mysqli_fetch_assoc($items_result)) {
        $sku = !empty($row['frame_id']) ? 'FRAME-' . $row['frame_id'] : 'SKU-' . uniqid();
        $items[] = [
            'name' => $row['name'],
            'sku' => $sku,
            'units' => (int)$row['quantity'],
            'selling_price' => (float)$row['price'],
            'discount' => 0,
            'tax' => 0,
        ];
    }

    // 2. Authenticate
    $email = "hetlj6315@gmail.com";
    $password = "JfZ%&vO5jsej76jz";
    $auth = [
        'http' => [
            'method'  => 'POST',
            'header'  => 'Content-Type: application/json',
            'content' => json_encode(['email' => $email, 'password' => $password]),
            'ignore_errors' => true
        ]
    ];
    $auth_context = stream_context_create($auth);
    $auth_response = file_get_contents("https://apiv2.shiprocket.in/v1/external/auth/login", false, $auth_context);
    $auth_data = json_decode($auth_response, true);
    if (!isset($auth_data['token'])) return ['success'=>false, 'error'=>'Shiprocket auth failed'];
    $token = $auth_data['token'];

    // 3. Prepare order payload
    // Use shipping table fields for billing/delivery
    $billing_address = trim($order['shipping_address']);
    $billing_pincode = isset($order['shipping_pincode']) ? trim($order['shipping_pincode']) : '';
    $billing_city = isset($order['shipping_city']) ? trim($order['shipping_city']) : '';
    $billing_state = isset($order['shipping_state']) ? trim($order['shipping_state']) : '';
    $billing_country = isset($order['shipping_country']) ? trim($order['shipping_country']) : 'India';
    $billing_name = isset($order['shipping_name']) ? trim($order['shipping_name']) : '';
    $billing_email = isset($order['shipping_email']) ? trim($order['shipping_email']) : '';
    $billing_phone = isset($order['shipping_phone']) ? trim($order['shipping_phone']) : '';

    // Fallback: Try to parse city/state/pincode from shipping_address if not set
    if (empty($billing_city) || empty($billing_state) || empty($billing_pincode)) {
        $address_parts = explode(',', $billing_address);
        $address_parts = array_map('trim', $address_parts);
        if (count($address_parts) >= 3) {
            if (empty($billing_city)) $billing_city = $address_parts[count($address_parts)-3];
            if (empty($billing_state)) $billing_state = $address_parts[count($address_parts)-2];
            if (empty($billing_pincode)) $billing_pincode = preg_replace('/\D/', '', $address_parts[count($address_parts)-1]);
        }
    }
    if (empty($billing_city)) $billing_city = 'City';
    if (empty($billing_state)) $billing_state = 'State';
    if (empty($billing_pincode)) $billing_pincode = '000000';
    if (empty($billing_country)) $billing_country = 'India';

    // --- VALIDATION ---
    $errors = [];
    if (empty($billing_name)) $errors[] = 'Recipient name is required';
    if (empty($billing_address)) $errors[] = 'Shipping address is required';
    if (empty($billing_city)) $errors[] = 'City is required';
    if (empty($billing_state)) $errors[] = 'State is required';
    if (empty($billing_country)) $errors[] = 'Country is required';
    if (empty($billing_pincode) || !preg_match('/^\d{6}$/', $billing_pincode)) $errors[] = 'Valid 6-digit pincode is required';
    if (empty($billing_email) || !filter_var($billing_email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
    if (empty($billing_phone) || !preg_match('/^\d{10}$/', $billing_phone)) $errors[] = 'Valid 10-digit phone is required';
    if (empty($items)) $errors[] = 'Order must have at least one item';
    if (empty($order['total_price']) || $order['total_price'] <= 0) $errors[] = 'Order total must be greater than zero';
    if (!empty($errors)) {
        return ['success'=>false, 'error'=>'Shiprocket required fields missing or invalid: '.implode('; ', $errors)];
    }

    $payload = [
        'order_id' => $order['order_id'],
        'order_date' => date('Y-m-d', strtotime($order['created_at'])),
        'pickup_location' => 'Home',
        'billing_customer_name' => $billing_name,
        'billing_last_name' => '',
        'billing_address' => $billing_address,
        'billing_city' => $billing_city,
        'billing_pincode' => $billing_pincode,
        'billing_state' => $billing_state,
        'billing_country' => $billing_country,
        'billing_email' => $billing_email,
        'billing_phone' => $billing_phone,
        'shipping_is_billing' => true,
        'order_items' => $items,
        'payment_method' => 'Prepaid',
        'sub_total' => (float)$order['total_price'],
        'length' => 10,
        'breadth' => 10,
        'height' => 5,
        'weight' => 0.5
    ];

    // 4. Create order
    $opts = [
        'http' => [
            'method'  => 'POST',
            'header'  => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ],
            'content' => json_encode($payload),
            'ignore_errors' => true
        ]
    ];
    $context = stream_context_create($opts);
    $result = file_get_contents("https://apiv2.shiprocket.in/v1/external/orders/create/adhoc", false, $context);
    $data = json_decode($result, true);
    if (isset($data['order_id'])) {
        return ['success'=>true, 'shiprocket_order_id'=>$data['order_id'], 'data'=>$data];
    } else {
        // Return the payload and Shiprocket response for debugging
        return [
            'success'=>false,
            'error'=>$data['message'] ?? 'Unknown error',
            'shiprocket_response'=>$data,
            'payload_sent'=>$payload
        ];
    }
}
// Usage example (uncomment to test):
// $res = create_shiprocket_order(1, $conn);
// var_dump($res);

