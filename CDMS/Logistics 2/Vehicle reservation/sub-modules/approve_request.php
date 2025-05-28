<?php
session_start();
include("../../../../connection.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['request_id'])) {
    $reservation_id = $_POST['request_id'];

    $db_name = "logs2_vehicle_reservation_system";
    $connection = $connections[$db_name];

    // Begin transaction
    $connection->begin_transaction();

    try {
        // Step 1: Get VehicleID from reservation
        $res_stmt = $connection->prepare("SELECT VehicleID FROM reservation WHERE ReservationID = ?");
        $res_stmt->bind_param("s", $reservation_id);
        $res_stmt->execute();
        $res_result = $res_stmt->get_result();
        if ($res_result->num_rows === 0) {
            throw new Exception("Reservation not found.");
        }

        $vehicle_id = $res_result->fetch_assoc()['VehicleID'];
        $res_stmt->close();

        // Step 2: Update reservation to 'Reserved'
        $update_res = $connection->prepare("UPDATE reservation SET status = 'Reserved' WHERE ReservationID = ?");
        $update_res->bind_param("s", $reservation_id);
        $update_res->execute();
        $update_res->close();

        // Step 3: Update vehicle to 'Reserved'
        $update_vehicle = $connection->prepare("UPDATE vehicles SET Status = 'Reserved' WHERE VehicleID = ?");
        $update_vehicle->bind_param("s", $vehicle_id);
        $update_vehicle->execute();
        $update_vehicle->close();

        $connection->commit();

        echo "<script>
            alert('Reservation approved successfully.');
            window.location.href = 'reservation_pending.php';
        </script>";

    } catch (Exception $e) {
        $connection->rollback();
        echo "<script>
            alert('Error: " . $e->getMessage() . "');
            window.location.href = 'reservation_pending.php';
        </script>";
    }
} else {
    header("Location: reservation_pending.php");
    exit;
}
?>
