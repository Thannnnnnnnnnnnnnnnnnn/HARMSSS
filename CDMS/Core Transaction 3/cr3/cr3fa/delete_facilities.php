<?php

$host = 'localhost:3307';
$user = 'root';
$password = '';
$database = 'cr3_re';
$port = 3307;

$conn = mysqli_connect($host, $user, $password, $database, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['id'])) {
    $FacilityID = intval($_GET['id']);

    $query = "DELETE FROM facilities WHERE FacilityID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $FacilityID);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
                alert('Facility deleted successfully!');
                window.location.href = 'index.php';
              </script>";
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
