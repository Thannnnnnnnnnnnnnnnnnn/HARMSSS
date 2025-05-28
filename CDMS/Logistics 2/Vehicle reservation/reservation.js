function toggleVehicleDropdown() {
    const purpose = document.getElementById("purpose").value;
    const guestStaff = document.getElementById("guestStaffDropdown");
    const logistic = document.getElementById("logisticDropdown");
    const cargo = document.getElementById("cargoDropdown");
    const bill = document.getElementById("billSection");

    guestStaff.style.display = logistic.style.display = cargo.style.display = bill.style.display = "none";

    if (purpose === "Guest" || purpose === "Staff") {
        guestStaff.style.display = "block";
    }
    if (purpose === "Logistic") {
        logistic.style.display = "block";
    }
    if (purpose === "Guest") {
        bill.style.display = "block";
    }
}

function toggleCargoDropdown() {
    const selectedVehicle = document.querySelector('#logisticDropdown select')?.value;
    const cargoDropdown = document.getElementById("cargoDropdown");
    cargoDropdown.style.display = selectedVehicle ? "block" : "none";
}

function calculateBill() {
    const purpose = document.getElementById("purpose").value;
    const schedule = document.getElementById("schedule").value;
    const dep = document.getElementById("departure").value;
    const arr = document.getElementById("arrival").value;
    const bill = document.getElementById("bill");

    if (!schedule || !dep || !arr || purpose !== "Guest") {
        bill.value = "";
        return;
    }

    const start = new Date(`${schedule}T${dep}`);
    const end = new Date(`${schedule}T${arr}`);

    if (end <= start) {
        alert("Arrival time must be after departure time.");
        bill.value = "";
        return;
    }

    const hours = (end - start) / 3600000; // 36e5 = 3600000 ms
    const vehicleType = document.querySelector('#guestStaffDropdown select')?.value || "";

    const rates = {
        "4 Seater": 300,
        "6 Seater": 400,
        "10 Seater": 500
    };

    const total = Math.ceil(hours) * (rates[vehicleType] || 0);
    bill.value = "₱" + total.toLocaleString();
}

function updateVehicleInfo() {
    const vehicleSelect = document.getElementById("VehicleSelect");
    const type = vehicleSelect.options[vehicleSelect.selectedIndex]?.dataset.type || "";
    document.getElementById("vehicle_type_hidden").value = type;
}

document.addEventListener("DOMContentLoaded", () => {
    $(".reservation-row td:not(.no-click)").click(function () {
        const row = $(this).closest("tr");

        $("#modalReservationID").text(row.data("reservation-id"));
        $("#modalDestination").text(row.data("destination"));
        $("#modalSchedule").text(row.data("schedule"));
        $("#modalPurpose").text(row.data("purpose"));
        $("#modalVehicleType").text(row.data("vehicle-type"));
        $("#modalStatus").text(row.data("status"));

        $("#reservationDetailsModal").modal("show");
    });

    document.getElementById("vehicleReservationForm").addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch("add_reservation.php", {
            method: "POST",
            body: formData
        })
        .then(async response => {
            const text = await response.text();
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert("Error: " + data.message);
                }
            } catch (err) {
                console.error("Non-JSON Response:\n", text);
                alert("Server returned invalid response:\n" + text);
            }
        })
        .catch(error => {
            console.error("Fetch Error:", error);
            alert("A network error occurred. Please check the console.");
        });
    });
});
