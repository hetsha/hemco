<?php
session_start();
require_once 'vendor/autoload.php';
include('include/db_connect.php');

$client = new Google_Client();
$client->setClientId('233916660609-niu2a5u4fr5afhldk3nnq4vm12fibuqr.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/business/hemco/google-login.php');
$client->addScope("email");
$client->addScope("profile");

// Handle OAuth response
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $_SESSION['access_token'] = $token;
    $client->setAccessToken($token);
}

// Already logged in
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
}

// No token? Redirect to Google Auth
if (!$client->getAccessToken()) {
    $auth_url = $client->createAuthUrl();
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
    exit();
}

// Get user info
$oauth = new Google_Service_Oauth2($client);
$userData = $oauth->userinfo->get();

$email = $userData->email;
$name = $userData->name;

// Check if user exists
$stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['user_id'] = $row['user_id'];
} else {
    // New Google user
    $phone = '';
    $address = '';
    $zip_code = '';
    $password = ''; // No password for Google users

    $stmt = $conn->prepare("INSERT INTO user (name, email, password, phone, address, zip_code) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $email, $password, $phone, $address, $zip_code);
    $stmt->execute();
    $_SESSION['user_id'] = $conn->insert_id;
}

// ✅ Set same session variables for both login types
$_SESSION['user_email'] = $email;
$_SESSION['user_name'] = $name;

// Redirect
header('Location: index.php');
exit();
?>
