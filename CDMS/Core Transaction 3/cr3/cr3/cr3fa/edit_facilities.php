<?php

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'cr3_fa';
$port = 3307;

$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $FacilityID = mysqli_real_escape_string($conn, $_POST['FacilityID']);
    $Asset_Repairs = mysqli_real_escape_string($conn, $_POST['Asset_Repairs']);
    $Room_No = intval($_POST['Room_No']);
    $date = $_POST['Date'];

    $conn->query("UPDATE facilities SET Asset_Repairs='$Asset_Repairs', Room_No='$Room_No', date='$date' WHERE id='$FacilityID'");
    echo "Facilities updated successfully";
}
