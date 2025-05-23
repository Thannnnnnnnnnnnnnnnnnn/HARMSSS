
document.addEventListener("DOMContentLoaded", function () {
    document.body.addEventListener("click", function (event) {
        const button = event.target.closest(".view-btn");
        if (!button) return; // Exit if not a view button

        event.preventDefault(); // Prevent default link behavior

        if (button.hasAttribute("data-mileagelogid")) {
            // Mileage Log Data
            const mileageLogID = button.getAttribute("data-mileagelogid");
            const vehicleID = button.getAttribute("data-vehicleid");
            const licensePlate = button.getAttribute("data-licenseplate");
            const model = button.getAttribute("data-model");
            const driver = button.getAttribute("data-driver");
            const startLocation = button.getAttribute("data-startlocation");
            const endLocation = button.getAttribute("data-endlocation");
            const distanceTraveled = button.getAttribute("data-distancetraveled");
            
            document.getElementById("newMileageLogID").textContent = mileageLogID;
            document.getElementById("newVehicleID").textContent = vehicleID;
            document.getElementById("newLicensePlate").textContent = licensePlate;
            document.getElementById("newModel").textContent = model;
            document.getElementById("newDriver").textContent = driver;
            document.getElementById("newStartLocation").textContent = startLocation;
            document.getElementById("newEndLocation").textContent = endLocation;
            document.getElementById("newDistanceTraveled").textContent = distanceTraveled;
            
        } 
        else if (button.hasAttribute("data-fuellogid")) {
            // Fuel Log Data
            const fuelLogID = button.getAttribute("data-fuellogid");
            const vehicleID = button.getAttribute("data-vehicleid");
            const model = button.getAttribute("data-model");
            const fuelType = button.getAttribute("data-fueltype");  
            const fuelConsumption = button.getAttribute("data-fuelconsumption"); 
            const dateTime = button.getAttribute("data-datetime"); 
            const totalCost = button.getAttribute("data-totalcost"); 
        
            // Populate Fuel Log Modal
            document.getElementById("modalFuelLogID").textContent = fuelLogID;
            document.getElementById("modalVehicleID").textContent = vehicleID;
            document.getElementById("modalModel").textContent = model;
            document.getElementById("modalFuelType").textContent = fuelType; 
            document.getElementById("modalFuelConsumption").textContent = fuelConsumption; 
            document.getElementById("modalDateTime").textContent = dateTime; 
            document.getElementById("modalTotalCost").textContent = totalCost;
        } 
        else if (button.hasAttribute("data-fleet-id")) {
            // Fleet Data
            const fleetID = button.getAttribute("data-fleet-id");
            const fleetName = button.getAttribute("data-fleet-name");
            const managerID = button.getAttribute("data-manager-id");
            const employeeName = button.getAttribute("data-employee-name");
            const model = button.getAttribute("data-model") || ""; 
            const capacity = button.getAttribute("data-capacity") || ""; 
            const numberOfItems = button.getAttribute("data-number-of-items") || "";

            // Populate Fleet Modal
            document.getElementById("viewFleetID").textContent = fleetID;
            document.getElementById("viewFleetName").textContent = fleetName;
            document.getElementById("viewManagerID").textContent = managerID;
            document.getElementById("viewEmployeeName").textContent = employeeName;
            document.getElementById("viewModel").textContent = model;
            document.getElementById("viewCapacity").textContent = capacity;
            document.getElementById("viewNumberOfItems").textContent = numberOfItems;
        }
        else if (button.hasAttribute("data-assignmentid")) {
            // Assignment Data
            const assignmentID = button.getAttribute("data-assignmentid");
            const vehicleID = button.getAttribute("data-vehicleid");
            const licensePlate = button.getAttribute("data-licenseplate");
            const model = button.getAttribute("data-model");
            const managerID = button.getAttribute("data-managerid");
            const employeeName = button.getAttribute("data-employeename");
            const startDate = button.getAttribute("data-startdate") || "";
            const endDate = button.getAttribute("data-enddate") || "";
            const purpose = button.getAttribute("data-purpose") || "";
            const status = button.getAttribute("data-status") || "";
        
            document.getElementById("viewAssignmentID").textContent = assignmentID;
            document.getElementById("viewVehicleID").textContent = vehicleID;
            document.getElementById("viewLicensePlate").textContent = licensePlate;
            document.getElementById("viewModel").textContent = model;
            document.getElementById("viewManagerID").textContent = managerID;
            document.getElementById("viewEmployeeName").textContent = employeeName;
            document.getElementById("viewStartDate").textContent = startDate;
            document.getElementById("viewEndDate").textContent = endDate;
            document.getElementById("viewPurpose").textContent = purpose;
            document.getElementById("viewStatus").textContent = status;
        
        }
        else if (button.hasAttribute("data-maintenanceid")) {
            // Maintenance Log Data
            const maintenanceID = button.getAttribute("data-maintenanceid");
            const vehicleID = button.getAttribute("data-vehicleid");
            const maintenanceType = button.getAttribute("data-maintenancetype");
            const description = button.getAttribute("data-description");
            const maintenanceDate = button.getAttribute("data-maintenancedate") || "";
            const nextMaintenanceDate = button.getAttribute("data-nextmaintenancedate") || "";
            const cost = button.getAttribute("data-cost") || "";
            const status = button.getAttribute("data-status") || "";
        
            document.getElementById("viewMaintenanceID").textContent = maintenanceID;
            document.getElementById("viewVehicleID").textContent = vehicleID;
            document.getElementById("viewMaintenanceType").textContent = maintenanceType;
            document.getElementById("viewDescription").textContent = description;
            document.getElementById("viewMaintenanceDate").textContent = maintenanceDate;
            document.getElementById("viewNextMaintenanceDate").textContent = nextMaintenanceDate;
            document.getElementById("viewCost").textContent = cost;
            document.getElementById("viewStatus").textContent = status;
        }
    
    });
});
