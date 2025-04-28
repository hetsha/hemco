<?php
session_start();
require_once 'vendor/autoload.php'; // Google API library
include('include/db_connect.php'); // DB Connection

$client = new Google_Client();
$client->setClientId('233916660609-niu2a5u4fr5afhldk3nnq4vm12fibuqr.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/business/hemco/google-login.php');
$client->addScope("email");
$client->addScope("profile");

if (!isset($_GET['code'])) {
    $auth_url = $client->createAuthUrl();
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
    exit();
} else {
    $client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();

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
        // Existing user: login
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $name;
    } else {
        // New user: insert into database
        $phone = '';
        $address = '';
        $zip_code = '';
        $password = ''; // empty because using Google Login

        $stmt = $conn->prepare("INSERT INTO user (name, email, password, phone, address, zip_code) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $email, $password, $phone, $address, $zip_code);
        $stmt->execute();

        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $name;
    }

    header('Location: index.php');
    exit();
}
