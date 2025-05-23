<?php
session_start();
include("../../../../connection.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['request_id'])) {
    $reservation_id = $_POST['request_id'];

    $db_name = "logs2_vehicle_reservation_system";
    $connection = $connections[$db_name];

    try {
        // Optional: get VehicleID if you want to revert its status
        $res_stmt = $connection->prepare("SELECT VehicleID FROM reservation WHERE ReservationID = ?");
        $res_stmt->bind_param("s", $reservation_id);
        $res_stmt->execute();
        $res_result = $res_stmt->get_result();
        $vehicle_id = null;
        if ($res_result->num_rows > 0) {
            $vehicle_id = $res_result->fetch_assoc()['VehicleID'];
        }
        $res_stmt->close();

        // Step 1: Update reservation status to 'Rejected'
        $reject_res = $connection->prepare("UPDATE reservation SET status = 'Rejected' WHERE ReservationID = ?");
        $reject_res->bind_param("s", $reservation_id);
        $reject_res->execute();
        $reject_res->close();

        // Step 2 (optional): Revert vehicle status if needed
        if ($vehicle_id) {
            $update_vehicle = $connection->prepare("UPDATE vehicles SET Status = 'Available' WHERE VehicleID = ?");
            $update_vehicle->bind_param("s", $vehicle_id);
            $update_vehicle->execute();
            $update_vehicle->close();
        }

        echo "<script>
            alert('Reservation rejected successfully.');
            window.location.href = 'reservation_pending.php';
        </script>";

    } catch (Exception $e) {
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
