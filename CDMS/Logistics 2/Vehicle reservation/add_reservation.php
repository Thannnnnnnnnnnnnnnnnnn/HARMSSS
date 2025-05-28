<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
include("../../../connection.php");

$db_name = "logs2_vehicle_reservation_system";

if (!isset($connections[$db_name])) {
    echo json_encode(["success" => false, "message" => "Database connection not found."]);
    exit;
}

$connection = $connections[$db_name];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Clean inputs
    $purpose = mysqli_real_escape_string($connection, $_POST['purpose'] ?? '');
    $cargo = isset($_POST['cargo']) ? mysqli_real_escape_string($connection, $_POST['cargo']) : null;
    $destination_to = mysqli_real_escape_string($connection, $_POST['destination_to'] ?? '');
    $destination_from = mysqli_real_escape_string($connection, $_POST['destination_from'] ?? '');
    $schedule = mysqli_real_escape_string($connection, $_POST['schedule'] ?? '');
    $arrival = mysqli_real_escape_string($connection, $_POST['arrival'] ?? '');
    $departure = mysqli_real_escape_string($connection, $_POST['departure'] ?? '');
    $status = mysqli_real_escape_string($connection, $_POST['status'] ?? 'Pending for approval');
    $vehicle_id = mysqli_real_escape_string($connection, $_POST['VehicleID'] ?? '');

    if (!$vehicle_id) {
        echo json_encode(["success" => false, "message" => "Vehicle ID is required."]);
        exit;
    }

    $user_id = $_SESSION['User_ID'] ?? null;
    if (!$user_id) {
        echo json_encode(["success" => false, "message" => "User not logged in."]);
        exit;
    }

    // Step 1: Fetch vehicle details
    $vehicle_query = "SELECT DriverID, Vehicle_type, Vehicle_brand, Vehicle_color, Plate_no, driver_name, contact FROM vehicles WHERE VehicleID = ?";
    $vehicle_stmt = mysqli_prepare($connection, $vehicle_query);
    mysqli_stmt_bind_param($vehicle_stmt, 'i', $vehicle_id);
    mysqli_stmt_execute($vehicle_stmt);
    mysqli_stmt_store_result($vehicle_stmt);

    if (mysqli_stmt_num_rows($vehicle_stmt) === 0) {
        echo json_encode(["success" => false, "message" => "Vehicle not found."]);
        exit;
    }

    mysqli_stmt_bind_result($vehicle_stmt, $driver_id, $vehicle_type, $vehicle_brand, $vehicle_color, $plate_no, $driver_name, $driver_contact);
    mysqli_stmt_fetch($vehicle_stmt);
    mysqli_stmt_close($vehicle_stmt);

    if (empty($driver_id)) {
        echo json_encode(["success" => false, "message" => "No driver assigned to this vehicle."]);
        exit;
    }

    // Step 2: Insert reservation
    $insert_query = "INSERT INTO reservation (
        User_ID, purpose, vehicle_type, cargo, destination_to, destination_from,
        schedule, arrival, departure, VehicleID, status,
        Vehicle_brand, Vehicle_color, Plate_no, driver_name, contact
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($connection, $insert_query);
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Prepare failed: " . mysqli_error($connection)]);
        exit;
    }

    mysqli_stmt_bind_param($stmt, 'issssssssissssss',
        $user_id, $purpose, $vehicle_type, $cargo, $destination_to,
        $destination_from, $schedule, $arrival, $departure, $vehicle_id, $status,
        $vehicle_brand, $vehicle_color, $plate_no, $driver_name, $driver_contact
    );

    if (mysqli_stmt_execute($stmt)) {
        $reservation_id = mysqli_insert_id($connection);

        // Step 3: Update vehicle status and set driver name
        $vehicle_status = 'For reservation';
        $updateVehicleStmt = mysqli_prepare($connection, "UPDATE vehicles SET Status = ?, driver_name = ? WHERE VehicleID = ?");
        mysqli_stmt_bind_param($updateVehicleStmt, 'ssi', $vehicle_status, $driver_name, $vehicle_id);
        mysqli_stmt_execute($updateVehicleStmt);
        mysqli_stmt_close($updateVehicleStmt);

        // Step 4: Update driver status
        $updateDriverStmt = mysqli_prepare($connection, "UPDATE drivers SET Status = 'On duty' WHERE DriverID = ?");
        mysqli_stmt_bind_param($updateDriverStmt, 'i', $driver_id);
        mysqli_stmt_execute($updateDriverStmt);
        mysqli_stmt_close($updateDriverStmt);

        echo json_encode([
            "success" => true,
            "message" => "Vehicle successfully reserved.",
            "reservation_id" => $reservation_id
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Reservation failed: " . mysqli_stmt_error($stmt)]);
    }

    mysqli_stmt_close($stmt);
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>
