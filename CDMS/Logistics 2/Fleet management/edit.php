<?php
include("connection.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fleet Update Logic
    if (isset($_POST['old_fleet_id'])) {
        // Collect form data
        $old_fleet_id = $_POST['old_fleet_id'];
        $new_fleet_id = $_POST['fleet_id'];
        $fleet_name = $_POST['fleet_name'];
        $manager_id = $_POST['manager_id'];
        $employee_name = $_POST['employee_name'];
        $model = $_POST['model']; // Added model field
        $capacity = $_POST['capacity']; // Added capacity field
        $number_of_items = $_POST['number_of_items']; // Added number_of_items field

        // Basic validation
        if (empty($old_fleet_id) || empty($new_fleet_id) || empty($fleet_name) || empty($manager_id) || empty($employee_name) || empty($model) || empty($capacity) || empty($number_of_items)) {
            die("<script>alert('Error: Some fields are missing!'); window.location.href='fleet.php';</script>");
        }

        // Check if the original Fleet ID exists
        $check_old_stmt = $conn->prepare("SELECT * FROM fleet WHERE FleetID = ?");
        $check_old_stmt->execute([$old_fleet_id]);

        if ($check_old_stmt->rowCount() == 0) {
            echo "<script>alert('Error: Original Fleet ID does not exist!'); window.location.href='fleet.php';</script>";
            exit();
        }

        // Check if the new Fleet ID already exists (if it's different from the old one)
        if ($old_fleet_id !== $new_fleet_id) {
            $check_new_stmt = $conn->prepare("SELECT * FROM fleet WHERE FleetID = ?");
            $check_new_stmt->execute([$new_fleet_id]);
            if ($check_new_stmt->rowCount() > 0) {
                echo "<script>alert('Error: New Fleet ID already exists!'); window.location.href='fleet.php';</script>";
                exit();
            }
        }

        $stmt = $conn->prepare("UPDATE fleet SET FleetID = ?, FleetName = ?, ManagerID = ?, Employee_name = ?, Model = ?, Capacity = ?, NumberOfItems = ? WHERE FleetID = ?");
        $stmt->execute([$new_fleet_id, $fleet_name, $manager_id, $employee_name, $model, $capacity, $number_of_items, $old_fleet_id]);
        echo "<script>alert('Fleet updated successfully!'); window.location.href='fleet.php';</script>";

    // Fuel Log Update Logic
    } elseif (isset($_POST['old_fuel_log_id'])) {
        $old_fuel_log_id = $_POST['old_fuel_log_id'];
        $new_fuel_log_id = $_POST['fuel_log_id'];
        $vehicle_id = $_POST['vehicle_id'];
        $model = $_POST['model'];
        $fuel_type = $_POST['fuel_type']; 
        $fuel_consumption = $_POST['fuel_consumption'];
        $date_time = $_POST['datetime']; 
        $total_cost = $_POST['total_cost']; 
    
        if (empty($old_fuel_log_id) || empty($new_fuel_log_id) || empty($vehicle_id) || empty($model) || empty($fuel_type) || empty($fuel_consumption) || empty($date_time) || empty($total_cost)) {
            die("<script>alert('Error: Some fields are missing!'); window.location.href='fuel.php';</script>");
        }
    
        $check_old_stmt = $conn->prepare("SELECT * FROM fuellogs WHERE FuelLogID = ?");
        $check_old_stmt->execute([$old_fuel_log_id]);
    
        if ($check_old_stmt->rowCount() == 0) {
            echo "<script>alert('Error: Original Fuel Log ID does not exist!'); window.location.href='fuel.php';</script>";
            exit();
        }
    
        if ($old_fuel_log_id !== $new_fuel_log_id) {
            $check_new_stmt = $conn->prepare("SELECT * FROM fuellogs WHERE FuelLogID = ?");
            $check_new_stmt->execute([$new_fuel_log_id]);
            if ($check_new_stmt->rowCount() > 0) {
                echo "<script>alert('Error: New Fuel Log ID already exists!'); window.location.href='fuel.php';</script>";
                exit();
            }
        }
    
        $stmt = $conn->prepare("UPDATE fuellogs SET FuelLogID = ?, VehicleID = ?, Model = ?, FuelType = ?, FuelConsumption = ?, DateTime = ?, TotalCost = ? WHERE FuelLogID = ?");
        $stmt->execute([$new_fuel_log_id, $vehicle_id, $model, $fuel_type, $fuel_consumption, $date_time, $total_cost, $old_fuel_log_id]);
        echo "<script>alert('Fuel log updated successfully!'); window.location.href='fuel.php';</script>";

    // Mileage Log Update Logic
    } elseif (isset($_POST['old_mileage_log_id'])) {
        $old_mileage_log_id = $_POST['old_mileage_log_id'];
        $new_mileage_log_id = $_POST['mileage_log_id'];
        $vehicle_id = $_POST['vehicle_id'];
        $driver = $_POST['driver'];
        $start_location = $_POST['start_location'];
        $end_location = $_POST['end_location'];
        $distance_traveled = $_POST['distance_traveled'];
        $license_plate = $_POST['license_plate'];
        $model = $_POST['model'];
    
        if (empty($old_mileage_log_id) || empty($new_mileage_log_id) || empty($vehicle_id) || empty($driver) || empty($start_location) || empty($end_location) || empty($distance_traveled) || empty($license_plate) || empty($model)) {
            die("<script>alert('Error: Some fields are missing!'); window.location.href='mileage.php';</script>");
        }
    
        $check_old_stmt = $conn->prepare("SELECT * FROM mileagelogs WHERE MileageLogID = ?");
        $check_old_stmt->execute([$old_mileage_log_id]);
    
        if ($check_old_stmt->rowCount() == 0) {
            echo "<script>alert('Error: Original Mileage Log ID does not exist!'); window.location.href='mileage.php';</script>";
            exit();
        }
    
        if ($old_mileage_log_id !== $new_mileage_log_id) {
            $check_new_stmt = $conn->prepare("SELECT * FROM mileagelogs WHERE MileageLogID = ?");
            $check_new_stmt->execute([$new_mileage_log_id]);
            if ($check_new_stmt->rowCount() > 0) {
                echo "<script>alert('Error: New Mileage Log ID already exists!'); window.location.href='mileage.php';</script>";
                exit();
            }
        }
    
        $stmt = $conn->prepare("UPDATE mileagelogs SET MileageLogID = ?, VehicleID = ?, Driver = ?, StartLocation = ?, EndLocation = ?, DistanceTraveled = ?, License_plate = ?, Model = ? WHERE MileageLogID = ?");
        $stmt->execute([$new_mileage_log_id, $vehicle_id, $driver, $start_location, $end_location, $distance_traveled, $license_plate, $model, $old_mileage_log_id]);
    
        echo "<script>alert('Mileage log updated successfully!'); window.location.href='mileage.php';</script>";
    
    
    // Vehicle Assignment Update Logic
    } elseif (isset($_POST['old_assignment_id'])) {
        $old_assignment_id = $_POST['old_assignment_id'];
        $new_assignment_id = $_POST['assignment_id'];
        $vehicle_id = $_POST['vehicle_id'];
        $license_plate = $_POST['license_plate'];
        $model = $_POST['model'];
        $manager_id = $_POST['manager_id'];
        $employee_name = $_POST['employee_name'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $purpose = $_POST['purpose'];
        $status = $_POST['status'];
    
        if (empty($old_assignment_id) || empty($new_assignment_id) || empty($vehicle_id) || empty($license_plate) || empty($model) || empty($manager_id) || empty($employee_name) || empty($start_date) || empty($end_date) || empty($purpose) || empty($status)) {
            die("<script>alert('Error: Some fields are missing!'); window.location.href='vehicleassignment.php';</script>");
        }
    
        $check_old_stmt = $conn->prepare("SELECT * FROM vehicleassignments WHERE AssignmentID = ?");
        $check_old_stmt->execute([$old_assignment_id]);
    
        if ($check_old_stmt->rowCount() == 0) {
            echo "<script>alert('Error: Original Assignment ID does not exist!'); window.location.href='vehicleassignment.php';</script>";
            exit();
        }
    
        if ($old_assignment_id !== $new_assignment_id) {
            $check_new_stmt = $conn->prepare("SELECT * FROM vehicleassignments WHERE AssignmentID = ?");
            $check_new_stmt->execute([$new_assignment_id]);
            if ($check_new_stmt->rowCount() > 0) {
                echo "<script>alert('Error: New Assignment ID already exists!'); window.location.href='vehicleassignment.php';</script>";
                exit();
            }
        }
    
        $stmt = $conn->prepare("UPDATE vehicleassignments SET AssignmentID = ?, VehicleID = ?, License_plate = ?, Model = ?, ManagerID = ?, Employee_name = ?, StartDate = ?, EndDate = ?, Purpose = ?, Status = ? WHERE AssignmentID = ?");
        $stmt->execute([$new_assignment_id, $vehicle_id, $license_plate, $model, $manager_id, $employee_name, $start_date, $end_date, $purpose, $status, $old_assignment_id]);
       
        echo "<script>alert('Assignment updated successfully!'); window.location.href='vehicleassignment.php';</script>";
    // MAINTENANCE LOG UPDATE LOGIC
} elseif (isset($_POST['old_maintenance_id'])) {
    $old_maintenance_id        = $_POST['old_maintenance_id'];
    $new_maintenance_id        = $_POST['maintenance_id'];
    $vehicle_id                = $_POST['vehicle_id'];
    $maintenance_type          = $_POST['maintenance_type'];
    $description               = $_POST['description'];
    $maintenance_date          = $_POST['maintenance_date'];
    $next_maintenance_date     = $_POST['next_maintenance_date'];
    $cost                       = $_POST['cost'];
    $status                     = $_POST['status'];

    // Check if any required field is empty
    if (empty($old_maintenance_id) || empty($new_maintenance_id) || empty($vehicle_id) || empty($maintenance_type) || empty($description) || empty($maintenance_date) || empty($next_maintenance_date) || empty($cost) || empty($status)) {
        die("<script>alert('Error: Some fields are missing!'); window.location.href='maintenancelogs.php';</script>");
    }

    // Check if the old Maintenance ID exists
    $check_old_stmt = $conn->prepare("SELECT * FROM maintenancelogs WHERE MaintenanceID = ?");
    $check_old_stmt->execute([$old_maintenance_id]);

    if ($check_old_stmt->rowCount() == 0) {
        echo "<script>alert('Error: Original Maintenance ID does not exist!'); window.location.href='maintenancelogs.php';</script>";
        exit();
    }

    // Check if the new Maintenance ID is unique
    if ($old_maintenance_id !== $new_maintenance_id) {
        $check_new_stmt = $conn->prepare("SELECT * FROM maintenancelogs WHERE MaintenanceID = ?");
        $check_new_stmt->execute([$new_maintenance_id]);
        if ($check_new_stmt->rowCount() > 0) {
            echo "<script>alert('Error: New Maintenance ID already exists!'); window.location.href='maintenance.php';</script>";
            exit();
        }
    }

    // Update the record in the database
    $stmt = $conn->prepare("UPDATE maintenancelogs SET MaintenanceID = ?, VehicleID = ?, MaintenanceType = ?, Description = ?, MaintenanceDate = ?, NextMaintenanceDate = ?, Cost = ?, Status = ? WHERE MaintenanceID = ?");
    $stmt->execute([$new_maintenance_id, $vehicle_id, $maintenance_type, $description, $maintenance_date, $next_maintenance_date, $cost, $status, $old_maintenance_id]);

    echo "<script>alert('Maintenance log updated successfully!'); window.location.href='maintenance.php';</script>";

    }else {
        echo "<script>alert('Error: Invalid request!'); window.location.href='vehicleassignment.php';</script>";
    }
}
?>
