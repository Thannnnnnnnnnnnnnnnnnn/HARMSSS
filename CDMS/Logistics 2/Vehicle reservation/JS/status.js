$(document).ready(function () {
    // Handle Status Update Form Submission
    $("#updateStatusForm").submit(function (event) {
        event.preventDefault();
        let formData = new FormData(this);

        console.log("Submitting Status Update:", Object.fromEntries(formData.entries())); // Debugging

        $.ajax({
            url: "update_driver.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (response) {
                console.log("Server response (Status Update):", response);

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
                console.error("AJAX Error (Status Update):", xhr.responseText);
                Swal.fire({
                    title: "Error!",
                    text: "Error updating status.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
            }
        });
    });

    // Handle Edit Driver Form Submission
    $("#editDriverForm").submit(function (event) {
        event.preventDefault();
        let formData = new FormData(this);

        console.log("Submitting Edit Form Data:", Object.fromEntries(formData.entries())); // Debugging

        $.ajax({
            url: "update_driver.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (response) {
                console.log("Server response (Edit Driver):", response);

                if (response.success) {
                    Swal.fire({
                        title: "Updated!",
                        text: response.message,
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        location.reload(); // ✅ Reloads the page after clicking "OK"
                    });
                } else {
                    Swal.fire({
                        title: "Update Failed",
                        text: response.message,
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error (Edit Driver):", xhr.responseText);
                Swal.fire({
                    title: "Error!",
                    text: "Error updating driver details.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
            }
        });
    });

    // Handle Manage Button Click (Fetching Data)
    $(".manage-button").click(function () {
        let driverId = $(this).data("driver-id");
        if (driverId) {
            fetchDriverData(driverId);
        } else {
            console.error("No driver ID found.");
        }
    });

    // Fetch Driver Data
    function fetchDriverData(driverId) {
        console.log("Fetching driver data for ID:", driverId);

        $.ajax({
            url: "fetch_driver.php",
            type: "GET",
            data: { DriverID: driverId },
            dataType: "json",
            success: function (response) {
                console.log("Fetched Data:", response);

                if (response.status === "success") {
                    $("#driverIdInput").val(response.DriverID);
                    $("#editDriverId").val(response.DriverID);
                    $("#editDriverName").val(response.Name);
                    $("#editDriverAge").val(response.Age);
                    $("#editDriverContact").val(response.Contact);
                    $("#editDriverGender").val(response.Gender);
                    $("#driverStatus").val(response.Status);
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
                console.error("AJAX Error (Fetch Driver):", xhr.responseText);
                Swal.fire({
                    title: "Error!",
                    text: "Error fetching driver details.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
            }
        });
    }

    $(document).ready(function () {
        $(".driver-row td:not(:last-child)").click(function () { 
            let row = $(this).closest("tr");
    
            let driverId = row.data("driver-id");
            let name = row.data("name");
            let contact = row.data("contact");
            let status = row.data("status");
            let age = row.data("age");
            let gender = row.data("gender");
    
            console.log("Driver Details:", { driverId, name, contact, status, age, gender }); // Debugging
    
            // Populate modal fields
            $("#modalDriverId").text(driverId);
            $("#modalDriverName").text(name);
            $("#modalDriverContact").text(contact);
            $("#modalDriverStatus").text(status);
            $("#modalDriverAge").text(age);
            $("#modalDriverGender").text(gender);
    
            // Show modal
            $("#driverDetailsModal").modal("show");
        });
    });
    
    });

