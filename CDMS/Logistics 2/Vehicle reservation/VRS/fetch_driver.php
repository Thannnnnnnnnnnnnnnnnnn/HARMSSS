<?php
include("../../../connection.php"); 
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = ["status" => "error", "message" => "Invalid request."];

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["DriverID"])) {
    $DriverID = intval($_GET["DriverID"]);

    // Check if the database connection exists
    if (!isset($connections["logs2_vehicle_reservation_system"]) || !$connections["logs2_vehicle_reservation_system"]) {
        $response["message"] = "Database connection not found.";
        error_log("Database connection not found.");
        echo json_encode($response);
        exit;
    }

    $connection = $connections["logs2_vehicle_reservation_system"];

    // Prepare SQL query
    $query = "SELECT DriverID, Name, Age, Gender, Contact, Status FROM drivers WHERE DriverID = ?";
    $stmt = mysqli_prepare($connection, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $DriverID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $response = ["status" => "success"] + $row;
        } else {
            $response["message"] = "Driver not found.";
            error_log("Driver not found: ID " . $DriverID);
        }

        mysqli_stmt_close($stmt);
    } else {
        $response["message"] = "SQL Error: " . mysqli_error($connection);
        error_log("SQL Prepare Error: " . mysqli_error($connection));
    }
}

// Send JSON response
echo json_encode($response, JSON_PRETTY_PRINT);
?>
