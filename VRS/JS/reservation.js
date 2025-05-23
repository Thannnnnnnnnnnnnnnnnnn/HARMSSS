function toggleVehicleDropdown() {
    let purpose = document.getElementById("purpose").value;
    let guestStaffDropdown = document.getElementById("guestStaffDropdown");
    let logisticDropdown = document.getElementById("logisticDropdown");
    let cargoDropdown = document.getElementById("cargoDropdown");
    let billSection = document.getElementById("billSection");

    // Reset visibility
    guestStaffDropdown.style.display = "none";
    logisticDropdown.style.display = "none";
    cargoDropdown.style.display = "none";
    billSection.style.display = "none";

    if (purpose === "Guest" || purpose === "Staff") {
        guestStaffDropdown.style.display = "block";
    } else if (purpose === "Logistic") {
        logisticDropdown.style.display = "block";
    }

    // Show bill only for guest transport
    if (purpose === "Guest") {
        billSection.style.display = "block";
    }
}

function toggleCargoDropdown() {
    let logisticVehicle = document.getElementById("vehicle_type").value; // Corrected to 'vehicle_type'
    let cargoDropdown = document.getElementById("cargoDropdown");

    if (logisticVehicle) {
        cargoDropdown.style.display = "block";
    } else {
        cargoDropdown.style.display = "none";
    }
}

function calculateBill() {
    let scheduleDate = document.getElementById("schedule").value;
    let arrivalTime = document.getElementById("arrival").value;
    let billInput = document.getElementById("bill");

    if (!scheduleDate || !arrivalTime) {
        billInput.value = "";
        return;
    }

    let scheduleDateTime = new Date(`${scheduleDate}T${arrivalTime}`);
    let currentTime = new Date();

    if (scheduleDateTime.getTime() < currentTime.getTime()) {
        alert("The schedule must be in the future.");
        billInput.value = "";
        return;
    }

    let vehicleDropdown = document.getElementById("vehicle_type");
    let ratePerHour = parseFloat(vehicleDropdown.value) || 0;

    let diffInHours = Math.abs(scheduleDateTime - currentTime) / 36e5; // Difference in hours
    let totalCost = Math.ceil(diffInHours) * ratePerHour;

    billInput.value = "₱" + totalCost.toLocaleString();
}

$(document).ready(function () {
    $(".reservation-row td:not(.no-click)").click(function () { 
        let row = $(this).closest("tr");

        let reservationId = row.data("reservation-id");
        let destination = row.data("destination");
        let schedule = row.data("schedule");
        let purpose = row.data("purpose");
        let vehicleType = row.data("vehicle-type");
        let status = row.data("status");

        console.log("Reservation Details:", { reservationId, destination, schedule, purpose, vehicleType, status }); // Debugging

        // Populate modal fields
        $("#modalReservationID").text(reservationId);
        $("#modalDestination").text(destination);
        $("#modalSchedule").text(schedule);
        $("#modalPurpose").text(purpose);
        $("#modalVehicleType").text(vehicleType);
        $("#modalStatus").text(status);

        // Show modal
        $("#reservationDetailsModal").modal("show");
    });
});

document.getElementById("vehicleReservationForm").addEventListener("submit", function (e) {
    e.preventDefault(); // Prevent default form submission

    let formData = new FormData(this);

    fetch("reservation.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload(); // Reload page to reflect changes
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => console.error("Error:", error));
});
