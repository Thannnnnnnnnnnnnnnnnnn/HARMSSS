document.addEventListener("DOMContentLoaded", function () {
    let editButtons = document.querySelectorAll(".edit-btn");

    editButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            // Fleet Edit Logic
            if (this.hasAttribute("data-fleetid")) {
                let fleetId = this.getAttribute("data-fleetid") || "";
                let fleetName = this.getAttribute("data-fleetname") || "";
                let managerId = this.getAttribute("data-managerid") || "";
                let employeeName = this.getAttribute("data-employeename") || "";
                let model = this.getAttribute("data-model") || ""; 
                let capacity = this.getAttribute("data-capacity") || ""; 
                let numberOfItems = this.getAttribute("data-numberofitems") || "";

                document.getElementById("editFleetID").value = fleetId;
                document.getElementById("oldFleetID").value = fleetId;
                document.getElementById("editFleetName").value = fleetName;
                document.getElementById("editManagerID").value = managerId;
                document.getElementById("editEmployeeName").value = employeeName;
                document.getElementById("editModel").value = model; 
                document.getElementById("editCapacity").value = capacity; 
                document.getElementById("editNumberOfItems").value = numberOfItems; 
            
                let fleetModal = new bootstrap.Modal(document.getElementById('fleetModal'));
                fleetModal.show();

            // Fuel Log Edit Logic
            } else if (this.hasAttribute("data-fuellogid")) {
                let fuelLogId = this.getAttribute("data-fuellogid") || "";
                let vehicleId = this.getAttribute("data-vehicleid") || "";
                let model = this.getAttribute("data-model") || "";
                let fuelType = this.getAttribute("data-fueltype") || "";
                let fuelConsumption = this.getAttribute("data-fuelconsumption") || ""; 
                let dateTime = this.getAttribute("data-datetime") || ""; 
                let totalCost = this.getAttribute("data-totalcost") || "";  
            
                document.getElementById("editFuelLogID").value = fuelLogId;
                document.getElementById("oldFuelLogID").value = fuelLogId;
                document.getElementById("editVehicleID").value = vehicleId;
                document.getElementById("editModel").value = model;
                document.getElementById("editFuelType").value = fuelType;
                document.getElementById("editFuelConsumption").value = fuelConsumption;
                document.getElementById("editDateTime").value = dateTime;
                document.getElementById("editTotalCost").value = totalCost;
            
                let fuelModal = new bootstrap.Modal(document.getElementById('fuelModal'));
                fuelModal.show();
            
            // Mileage Log Edit Logic
            } else if (this.hasAttribute("data-mileagelogid")) {
                let mileageLogId = this.getAttribute("data-mileagelogid") || "";
                let vehicleId = this.getAttribute("data-vehicleid") || "";
                let licensePlate = this.getAttribute("data-licenseplate") || "";
                let model = this.getAttribute("data-model") || "";
                let driver = this.getAttribute("data-driver") || "";
                let startLocation = this.getAttribute("data-startlocation") || "";
                let endLocation = this.getAttribute("data-endlocation") || "";
                let distanceTraveled = this.getAttribute("data-distancetraveled") || "";
            
                document.getElementById("editMileageLogID").value = mileageLogId;
                document.getElementById("oldMileageLogID").value = mileageLogId;
                document.getElementById("editMileageVehicleID").value = vehicleId;
                document.getElementById("editLicensePlate").value = licensePlate;
                document.getElementById("editMileageModel").value = model;
                document.getElementById("editDriver").value = driver;
                document.getElementById("editStartLocation").value = startLocation;
                document.getElementById("editEndLocation").value = endLocation;
                document.getElementById("editDistanceTraveled").value = distanceTraveled;
            
                let mileageModal = new bootstrap.Modal(document.getElementById('mileageModal'));
                mileageModal.show();
                
            // VEHICLE ASSIGNMENT EDIT LOGIC
            } else if (this.hasAttribute("data-assignmentid")) {
                let assignmentId = this.getAttribute("data-assignmentid") || "";
                let vehicleId = this.getAttribute("data-vehicleid") || "";
                let licensePlate = this.getAttribute("data-licenseplate") || "";
                let model = this.getAttribute("data-model") || "";
                let managerId = this.getAttribute("data-managerid") || "";
                let employeeName = this.getAttribute("data-employeename") || "";
                let startDate = this.getAttribute("data-startdate") || ""; 
                let endDate = this.getAttribute("data-enddate") || "";
                let purpose = this.getAttribute("data-purpose") || "";    
                let status = this.getAttribute("data-status") || "";        
            
                document.getElementById("editAssignmentID").value = assignmentId;
                document.getElementById("oldAssignmentID").value = assignmentId;
                document.getElementById("editVehicleID").value = vehicleId;
                document.getElementById("editLicensePlate").value = licensePlate;
                document.getElementById("editModel").value = model;
                document.getElementById("editManagerID").value = managerId;
                document.getElementById("editEmployeeName").value = employeeName;
                document.getElementById("editStartDate").value = startDate;  
                document.getElementById("editEndDate").value = endDate;      
                document.getElementById("editPurpose").value = purpose;      
                document.getElementById("editStatus").value = status;        
            
                let assignmentModal = new bootstrap.Modal(document.getElementById('assignmentModal'));
                assignmentModal.show();

             // MAINTENANCE LOG EDIT LOGIC
        } else if (this.hasAttribute("data-maintenanceid")) {
            let maintenanceId      = this.getAttribute("data-maintenanceid")      || "";
            let vehicleId          = this.getAttribute("data-vehicleid")          || "";
            let maintenanceType    = this.getAttribute("data-maintenancetype")    || "";
            let description        = this.getAttribute("data-description")        || "";
            let maintenanceDate    = this.getAttribute("data-maintenancedate")    || "";
            let nextMaintenanceDate= this.getAttribute("data-nextmaintenancedate")|| "";
            let cost               = this.getAttribute("data-cost")               || "";
            let status             = this.getAttribute("data-status")             || "";
        
            document.getElementById("editMaintenanceID").value          = maintenanceId;
            document.getElementById("oldMaintenanceID").value           = maintenanceId;
            document.getElementById("editVehicleID").value              = vehicleId;
            document.getElementById("editMaintenanceType").value        = maintenanceType;
            document.getElementById("editDescription").value            = description;
            document.getElementById("editMaintenanceDate").value        = maintenanceDate;
            document.getElementById("editNextMaintenanceDate").value    = nextMaintenanceDate;
            document.getElementById("editCost").value                   = cost;
            document.getElementById("editStatus").value                 = status;
        
            let maintenanceModal = new bootstrap.Modal(document.getElementById('maintenanceModal'));
            maintenanceModal.show();
        }
        
        });
    });
});

// Confirm edit button click 
function confirmEdit(element) {
    let confirmation = confirm("Are you sure you want to edit this record?");
    if (!confirmation) {
        return false; 
    }
    return true; 
}

function confirmUpdate() {
    return confirm("Are you sure you want to update this record?");
}
