<?php
$servername = "localhost:3307"; // Change if using a different host
$username = "root"; // Change to your database username
$password = ""; // Change to your database password
$dbname = "logs2_fleet_management"; // Change to your database name

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>
