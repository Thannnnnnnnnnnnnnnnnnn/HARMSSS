<?php
include("connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["fleet_id"])) {
        $fleetID = $_POST["fleet_id"];

        $stmt = $conn->prepare("DELETE FROM fleet WHERE FleetID = ?");
        if ($stmt->execute([$fleetID])) {
            header("Location: fleet.php?fleet_deleted=success");
            exit();
        } else {
            header("Location: fleet.php?fleet_deleted=error");
            exit();
        }
    }

    if (isset($_POST["fuel_log_id"])) {
        $fuel_log_id = $_POST["fuel_log_id"];

        $stmt = $conn->prepare("DELETE FROM fuellogs WHERE FuelLogID = ?");
        if ($stmt->execute([$fuel_log_id])) {
            header("Location: fuel.php?fuel_deleted=success");
            exit();
        } else {
            header("Location: fuel.php?fuel_deleted=error");
            exit();
        }
    }
    if (isset($_POST["mileagelogid"])) {
        $mileagelogid = $_POST["mileagelogid"];
    
        $stmt = $conn->prepare("DELETE FROM mileagelogs WHERE MileageLogID = ?");
        if ($stmt->execute([$mileagelogid])) {
            header("Location: mileage.php?mileage_deleted=success");
            exit();
        } else {
            header("Location: mileage.php?mileage_deleted=error");
            exit();
        }
    }
    if (isset($_POST["vehicle_assignment_id"])) {
        $vehicle_assignment_id = $_POST["vehicle_assignment_id"];
    
        $stmt = $conn->prepare("DELETE FROM vehicleassignments WHERE AssignmentID = ?");
        if ($stmt->execute([$vehicle_assignment_id])) {
            header("Location: vehicleassignment.php?assignment_deleted=success");
            exit();
        } else {
            header("Location: vehicleassignment.php?assignment_deleted=error");
            exit();
        }
    }
    if (isset($_POST["maintenance_id"])) {
        $maintenance_id = $_POST["maintenance_id"];
    
        $stmt = $conn->prepare("DELETE FROM maintenancelogs WHERE MaintenanceID = ?");
        if ($stmt->execute([$maintenance_id])) {
            header("Location: maintenance.php?maintenance_deleted=success");
            exit();
        } else {
            header("Location: maintenance.php?maintenance_deleted=error");
            exit();
        }
    }
    
      
}
?>
