<?php
include("connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["FleetID"])) {
        // Handling fleet data
        $fleet_id = trim($_POST["FleetID"]);
        $fleet_name = trim($_POST["FleetName"]);
        $manager_id = trim($_POST["ManagerID"]);
        $employee_name = trim($_POST["Employee_name"]);
        $model = trim($_POST["Model"]);
        $capacity = trim($_POST["Capacity"]);
        $number_of_items = trim($_POST["NumberOfItems"]);

        try {
            $stmt = $conn->prepare("INSERT INTO fleet (FleetID, FleetName, ManagerID, Employee_name, Model, Capacity, NumberOfItems) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$fleet_id, $fleet_name, $manager_id, $employee_name, $model, $capacity, $number_of_items]);

            header("Location: fleet.php?inserted=success");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST["FuelLogID"])) {
        // Handling fuel log data
        $fuel_log_id = trim($_POST["FuelLogID"]);
        $vehicle_id = trim($_POST["VehicleID"]);
        $model = trim($_POST["Model"]);
        $fuel_type = trim($_POST["FuelType"]); 
        $fuel_consumption = trim($_POST["FuelConsumption"]); 
        $date_time = trim($_POST["DateTime"]);
        $total_cost = trim($_POST["TotalCost"]); 
    
        try {
            $stmt = $conn->prepare("INSERT INTO fuellogs (FuelLogID, VehicleID, Model, FuelType, FuelConsumption, DateTime, TotalCost) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$fuel_log_id, $vehicle_id, $model, $fuel_type, $fuel_consumption, $date_time, $total_cost]);
            header("Location: fuel.php?inserted=success");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST["MileageLogID"])) {
        // Handling mileage log data
        $mileage_log_id = trim($_POST["MileageLogID"]);
        $vehicle_id = trim($_POST["VehicleID"]);
        $driver = trim($_POST["Driver"]);
        $start_location = trim($_POST["StartLocation"]);
        $end_location = trim($_POST["EndLocation"]);
        $distance_traveled = trim($_POST["DistanceTraveled"]);
        $license_plate = trim($_POST["License_plate"]);
        $model = trim($_POST["Model"]);
    
        try {
            $stmt = $conn->prepare("INSERT INTO mileagelogs (MileageLogID, VehicleID, Driver, StartLocation, EndLocation, DistanceTraveled, License_plate, Model) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$mileage_log_id, $vehicle_id, $driver, $start_location, $end_location, $distance_traveled, $license_plate, $model]);
            header("Location: mileage.php?inserted=success");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST["AssignmentID"])) {
        // Handling assignment data
        $assignment_id = trim($_POST["AssignmentID"]);
        $vehicle_id = trim($_POST["VehicleID"]);
        $license_plate = trim($_POST["License_plate"]);
        $model = trim($_POST["Model"]);
        $manager_id = trim($_POST["ManagerID"]);
        $employee_name = trim($_POST["Employee_name"]);
        $start_date = trim($_POST["StartDate"]);
        $end_date = trim($_POST["EndDate"]);
        $purpose = trim($_POST["Purpose"]);
        $status = trim($_POST["Status"]);
        
        try {
            // Prepare the SQL statement with the new fields
            $stmt = $conn->prepare("INSERT INTO vehicleassignments (AssignmentID, VehicleID, License_plate, Model, ManagerID, Employee_name, StartDate, EndDate, Purpose, Status) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            // Execute the statement with the added parameters
            $stmt->execute([$assignment_id, $vehicle_id, $license_plate, $model, $manager_id, $employee_name, $start_date, $end_date, $purpose, $status]);
    
            header("Location: vehicleassignment.php?inserted=success");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST["MaintenanceID"])) {
        // Handling maintenance log data
        $maintenance_id = trim($_POST["MaintenanceID"]);
        $vehicle_id = trim($_POST["VehicleID"]);
        $maintenance_type = trim($_POST["MaintenanceType"]);
        $description = trim($_POST["Description"]);
        $maintenance_date = trim($_POST["MaintenanceDate"]);
        $next_maintenance_date = trim($_POST["NextMaintenanceDate"]);
        $cost = trim($_POST["Cost"]);
        $status = trim($_POST["Status"]);
    
        try {
            // Prepare the SQL statement with the new fields
            $stmt = $conn->prepare("INSERT INTO maintenancelogs (MaintenanceID, VehicleID, MaintenanceType, Description, MaintenanceDate, NextMaintenanceDate, Cost, Status) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            // Execute the statement with the added parameters
            $stmt->execute([$maintenance_id, $vehicle_id, $maintenance_type, $description, $maintenance_date, $next_maintenance_date, $cost, $status]);
    
            header("Location: maintenance.php?inserted=success");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    
    }
}

$conn = null;
?>