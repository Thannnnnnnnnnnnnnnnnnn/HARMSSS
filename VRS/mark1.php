<?php
include("../CDMS/connection.php");
// Define the database name
$db_name = "logs2_vehicle_reservation_system"; 

if (!isset($connections[$db_name])) {
    die("Database connection not found for $db_name");
}

$connection = $connections[$db_name]; // Assign the correct connection

if (isset($_GET['id'])) {
    $ReservationID = $_GET['id'];
    
    // Validate and sanitize the input
    $ReservationID = mysqli_real_escape_string($connection, $ReservationID);
    
    // Correctly format the query with quotes around the value
    $query = "UPDATE `reservation` SET Status = 'Complete' WHERE ReservationID = '$ReservationID'";
    $result = mysqli_query($connection, $query);
    
    if ($result) {
        header("Location: sub-modules/dispatch.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($connection);
    }
} else {
    echo "No student ID provided.";
}
?>
