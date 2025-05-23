<?php
include("../../../connection.php"); 
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$db_name = "logs2_vehicle_reservation_system";

// Check if database connection exists
if (!isset($connections[$db_name])) {
    echo json_encode(["success" => false, "message" => "Database connection not found."]);
    exit;
}

$connection = $connections[$db_name];

// Debugging: Log received data
error_log("Received POST request: " . json_encode($_POST));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = ["success" => false, "message" => "Invalid request."];

    if (isset($_POST["DriverID"], $_POST["Status"]) && !isset($_POST["name"])) {
        // Status Update Only
        $driverId = intval($_POST["DriverID"]);
        $status = trim($_POST["Status"]);

        $query = "UPDATE drivers SET Status = ? WHERE DriverID = ?";
        $stmt = mysqli_prepare($connection, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $status, $driverId);
            if (mysqli_stmt_execute($stmt)) {
                $response = ["success" => true, "message" => "Driver status updated successfully."];
            } else {
                error_log("SQL Error (Status Update): " . mysqli_error($connection));
                $response["message"] = "Failed to update status: " . mysqli_error($connection);
            }
            mysqli_stmt_close($stmt);
        } else {
            error_log("SQL Prepare Error (Status Update): " . mysqli_error($connection));
            $response["message"] = "Error preparing SQL statement.";
        }
    } elseif (isset($_POST["DriverID"], $_POST["name"], $_POST["age"], $_POST["gender"], $_POST["contact"])) {
        // Full Edit
        $driverId = intval($_POST["DriverID"]);
        $name = trim($_POST["name"]);
        $age = intval($_POST["age"]);
        $gender = trim($_POST["gender"]);
        $contact = trim($_POST["contact"]);

        $query = "UPDATE drivers SET Name=?, Age=?, Gender=?, Contact=? WHERE DriverID=?";
        $stmt = mysqli_prepare($connection, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sissi", $name, $age, $gender, $contact, $driverId);
            if (mysqli_stmt_execute($stmt)) {
                $response = ["success" => true, "message" => "Driver details updated successfully."];
            } else {
                error_log("SQL Error (Edit Driver): " . mysqli_error($connection));
                $response["message"] = "Failed to update details: " . mysqli_error($connection);
            }
            mysqli_stmt_close($stmt);
        } else {
            error_log("SQL Prepare Error (Edit Driver): " . mysqli_error($connection));
            $response["message"] = "Error preparing SQL statement.";
        }
    } else {
        $response["message"] = "Missing required fields.";
        error_log("Missing required fields: " . json_encode($_POST));
    }

    echo json_encode($response);
}
?>
