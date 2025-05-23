document.addEventListener("DOMContentLoaded", function() {
    // Fleet Deletion Modal
    const deleteFleetModal = document.getElementById('deleteModal');
    if (deleteFleetModal) {
        deleteFleetModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget || event.target;
            const fleetID = button.getAttribute('data-fleetid');
            const fleetName = button.getAttribute('data-fleetname');

            console.log("Fleet ID:", fleetID); // Debugging
            console.log("Fleet Name:", fleetName); // Debugging

            document.getElementById('deleteFleetID').value = fleetID;
            document.getElementById('deleteFleetName').textContent = fleetName;
        });
    }

    // Fuel Log Deletion Modal
    const deleteFuelModal = document.getElementById('fueldeleteModal');
    if (deleteFuelModal) {
        deleteFuelModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget || event.target;
            const fuel_log_id = button.getAttribute('data-fuellogid');
            const vehicleID = button.getAttribute('data-vehicleid');

            console.log("Fuel Log ID:", fuel_log_id); // Debugging
            console.log("Vehicle ID:", vehicleID); // Debugging

            document.getElementById('hiddenFuelLogID').value = fuel_log_id; // Fixed ID
            document.getElementById('displayFuelLogID').textContent = fuel_log_id;
        });
    }
    // mile age deletion modal 
    const deleteMileageModal = document.getElementById('mileagedeleteModal');
    
    if (deleteMileageModal) {
        deleteMileageModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const mileagelogid = button.getAttribute('data-mileagelogid');

            console.log("Mileage Log ID:", mileagelogid); // Debugging

            // Set the hidden input and display the MileageLogID
            document.getElementById('hiddenMileageLogID').value = mileagelogid;
            document.getElementById('displayMileageLogID').textContent = mileagelogid;
        });
    }
    // Vehicle Assignment deletion modal 
const deleteAssignmentModal = document.getElementById('assignmentDeleteModal');

if (deleteAssignmentModal) {
    deleteAssignmentModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const assignmentID = button.getAttribute('data-assignmentid');

        console.log("Assignment ID:", assignmentID); // Debugging

        // Set the hidden input and display the AssignmentID
        document.getElementById('hiddenAssignmentID').value = assignmentID;
        document.getElementById('displayAssignmentID').textContent = assignmentID;
    });
}
    // Maintenance Log Deletion Modal 
const deleteMaintenanceModal = document.getElementById('maintenanceDeleteModal');

if (deleteMaintenanceModal) {
    deleteMaintenanceModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const maintenanceID = button.getAttribute('data-maintenanceid');

        console.log("Maintenance ID:", maintenanceID); // Debugging

        // Set the hidden input and display the MaintenanceID
        document.getElementById('hiddenMaintenanceID').value = maintenanceID;
        document.getElementById('displayMaintenanceID').textContent = maintenanceID;
    });
}


});
