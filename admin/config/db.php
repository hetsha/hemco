<?php
session_start();

$conn = new mysqli("localhost", "root", "", "testing");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
