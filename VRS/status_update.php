<?php
include("../CDMS/connection.php");

header('Content-Type: application/json'); // Ensure JSON response

$response = ["status" => "error", "message" => "Something went wrong."];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST["DriverID"]) || !isset($_POST["status"])) {
        $response["message"] = "Missing required fields.";
        echo json_encode($response);
        exit;
    }

    $DriverID = $_POST["DriverID"];
    $status = $_POST["status"];

    // Debugging
    error_log("DriverID: " . $DriverID);
    error_log("Status: " . $status);

    if (!isset($connections["logs2_vehicle_reservation_system"])) {
        $response["message"] = "Database connection not found.";
        echo json_encode($response);
        exit;
    }

    $connection = $connections["logs2_vehicle_reservation_system"];

    $query = "UPDATE drivers SET status = ? WHERE DriverID = ?";
    $stmt = mysqli_prepare($connection, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $status, $DriverID);
        if (mysqli_stmt_execute($stmt)) {
            $response["status"] = "success";
            $response["message"] = "Driver status updated successfully.";
        } else {
            $response["message"] = "Failed to update driver status.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $response["message"] = "Error preparing the SQL statement.";
    }
}

echo json_encode($response);
?>
