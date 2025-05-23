<?php
include("../CDMS/connection.php");
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$db_name = "logs2_vehicle_reservation_system";

// Check if database connection exists
if (!isset($connections[$db_name])) {
    error_log("Database connection not found: " . $db_name);
    echo json_encode(["success" => false, "message" => "Database connection not found."]);
    exit;
}

$connection = $connections[$db_name];

error_log("Received POST request: " . json_encode($_POST)); // Log the incoming POST data

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = ["success" => false, "message" => "Invalid request."];

    if (isset($_POST["VehicleID"], $_POST["Status"])) {
        // Status Update Only
        $vehicleId = intval($_POST["VehicleID"]);
        $status = trim($_POST["Status"]);

        // Log the received data
        error_log("Updating vehicle with ID: $vehicleId to status: $status");

        $query = "UPDATE vehicles SET Status = ? WHERE VehicleID = ?";
        $stmt = mysqli_prepare($connection, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $status, $vehicleId);
            if (mysqli_stmt_execute($stmt)) {
                $response = ["success" => true, "message" => "Vehicle status updated successfully."];
            } else {
                error_log("SQL Error (Status Update): " . mysqli_error($connection));
                $response["message"] = "Failed to update status: " . mysqli_error($connection);
            }
            mysqli_stmt_close($stmt);
        } else {
            error_log("SQL Prepare Error (Status Update): " . mysqli_error($connection));
            $response["message"] = "Error preparing SQL statement.";
        }
    } else {
        $response["message"] = "Missing required fields.";
        error_log("Missing required fields: " . json_encode($_POST));
    }

    echo json_encode($response);
}
?>
