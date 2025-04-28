<?php
session_start();

// Optional: If you want to revoke Google's access token
if (isset($_SESSION['access_token'])) {
    require_once 'vendor/autoload.php';
    $client = new Google_Client();
    $client->revokeToken($_SESSION['access_token']);
}

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to homepage or login page
header("Location: index.php");
exit();
