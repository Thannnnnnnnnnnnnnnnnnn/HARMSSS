<?php
// Database configuration
$host = "localhost:3306";
$username = "root";
$password = "";
$database = "hr_1_2_social_recognition";

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to utf8mb4
mysqli_set_charset($conn, "utf8mb4");

// Error reporting
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Start session (ensure this is at the top and no output is sent before this)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper functions
if (!function_exists('sanitize_input')) {
    function sanitize_input($data)
    {
        global $conn;
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        $data = mysqli_real_escape_string($conn, $data);
        return $data;
    }
}

if (!function_exists('redirect')) {
    function redirect($url)
    {
        header("Location: " . $url);
        exit();
    }
}
