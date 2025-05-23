<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'cr3_fa';
$port = 3307;

$conn = mysqli_connect($host, $user, $password, $database, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $FacilityID = mysqli_real_escape_string($conn, $_POST['FacilityID']);
    $Asset_Repairs = mysqli_real_escape_string($conn, $_POST['Asset_Repairs']);
    $Room_No = intval($_POST['Room_No']);
    $date = $_POST['date'];

    $query = "INSERT INTO facilities (FacilityID, Asset_Repairs, Room_No, date) VALUES ('$FacilityID', '$Asset_Repairs', '$Room_No', '$date')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Maintenance Request Pending!'); window.location.href='index.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
