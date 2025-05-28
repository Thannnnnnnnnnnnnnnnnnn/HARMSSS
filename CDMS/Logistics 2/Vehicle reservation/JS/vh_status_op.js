$(document).ready(function () {
    // Handle Status Update Form Submission
    $("#updateStatusForm").submit(function (event) {
        event.preventDefault();
        let formData = new FormData(this);

        console.log("Submitting Status Update:", Object.fromEntries(formData.entries())); // Log form data

        $.ajax({
            url: "update_vehicles.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (response) {
                console.log("Server response (Status Update):", response); // Log server response

                if (response.success) {
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        location.reload(); // ✅ Reloads the page after clicking "OK"
                    });
                } else {
                    Swal.fire({
                        title: "Failed",
                        text: response.message,
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error (Status Update):", xhr.responseText); // Log AJAX error
                Swal.fire({
                    title: "Error!",
                    text: "Error updating status.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
            }
        });
    });

    // Handle Manage Button Click (Fetching Data)
    $(".manage-button").click(function () {
        let VehicleID = $(this).data("vehicle-id");
        if (VehicleID) {
            fetchVehicleData(VehicleID);
        } else {
            console.error("No vehicle ID found.");
        }
    });

    // Fetch Vehicle Data
    function fetchVehicleData(VehicleID) {
        console.log("Fetching vehicle data for ID:", VehicleID);

        $.ajax({
            url: "fetch_vehicles.php",
            type: "GET",
            data: { VehicleID: VehicleID },
            dataType: "json",
            success: function (response) {
                console.log("Fetched Data:", response); // Log fetched data

                if (response.status === "success") {
                    // If the data is fetched successfully, populate the fields
                    $("#vehicleIdInput").val(response.VehicleID);
                    $("#vehicleStatus").val(response.status);
                    // You can add more details here if needed.
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: response.message,
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error (Fetch Vehicle):", xhr.responseText); // Log error
                Swal.fire({
                    title: "Error!",
                    text: "Error fetching vehicle details.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
            }
        });
    }

    // Handle row click to display vehicle details in modal
    $(".vehicle-row td:not(:last-child)").click(function () {
        let row = $(this).closest("tr");

        let VehicleID = row.data("vehicle-id");
        let vehicleBrand = row.data("vehiclebrand");
        let vehicleColor = row.data("vehiclecolor");
        let vehicleType = row.data("vehicletype");
        let plateNo = row.data("plateno");
        let status = row.data("status");

        console.log("Vehicle Details:", { VehicleID, vehicleBrand, vehicleColor, vehicleType, plateNo, status }); // Log vehicle details

        // Populate modal fields
        $("#modalVehicleId").text(VehicleID);
        $("#modalVehicleBrand").text(vehicleBrand);
        $("#modalVehicleColor").text(vehicleColor);
        $("#modalVehicleType").text(vehicleType);
        $("#modalPlateNo").text(plateNo);
        $("#modalStatus").text(status);

        // Show modal
        $("#vehicleDetailsModal").modal("show");
    });1
});
