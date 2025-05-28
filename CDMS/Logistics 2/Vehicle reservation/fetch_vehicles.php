<?php
include("../../../connection.php"); 
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = ["status" => "error", "message" => "Invalid request."];

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["VehicleID"])) {
    $VehicleID = intval($_GET["VehicleID"]);

    if (!isset($connections["logs2_vehicle_reservation_system"]) || !$connections["logs2_vehicle_reservation_system"]) {
        $response["message"] = "Database connection not found.";
        error_log("Database connection not found.");
        echo json_encode($response);
        exit;
    }

    $connection = $connections["logs2_vehicle_reservation_system"];

    $query = "SELECT VehicleID, plate_no, Vehicle_type, Vehicle_color, Vehicle_brand, Status FROM vehicles WHERE VehicleID = ?";
    $stmt = mysqli_prepare($connection, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $VehicleID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $response = ["status" => "success"] + $row;
        } else {
            $response["message"] = "Vehicle not found.";
            error_log("Vehicle not found: ID " . $VehicleID);
        }

        mysqli_stmt_close($stmt);
    } else {
        $response["message"] = "SQL Error: " . mysqli_error($connection);
        error_log("SQL Prepare Error: " . mysqli_error($connection));
    }
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
