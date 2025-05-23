document.addEventListener("DOMContentLoaded", function () {
    const openModal = document.getElementById("openModal");
    const closeModal = document.getElementById("closeModal");
    const modal = document.getElementById("insertModal");
    const insertForm = document.getElementById("insertForm");
    const submitButton = insertForm.querySelector("button[type='submit']");

    // Ensure all required elements exist before proceeding
    if (!openModal || !closeModal || !modal || !insertForm || !submitButton) {
        console.error("One or more required elements are missing from the DOM.");
        return;
    }

    // Open modal
    openModal.addEventListener("click", function () {
        modal.classList.remove("hidden");
    });

    // Close modal
    closeModal.addEventListener("click", function () {
        modal.classList.add("hidden");
    });

    // Close modal when clicking outside of it
    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.classList.add("hidden");
        }
    });

    // Handle form submission using AJAX
    insertForm.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent normal form submission

        let formData = new FormData(insertForm);

        // Disable submit button to prevent multiple submissions
        submitButton.disabled = true;

        // Show loading SweetAlert
        Swal.fire({
            title: "Processing...",
            text: "Please wait.",
            icon: "info",
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch("Table_1_insert.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json()) // Expect JSON response
        .then(data => {
            Swal.close(); // Close loading indicator
            submitButton.disabled = false; // Re-enable submit button

            if (data.status === "success") {
                Swal.fire({
                    title: "Success!",
                    text: data.message,
                    icon: "success",
                    confirmButtonText: "OK"
                }).then(() => {
                    modal.classList.add("hidden"); // Close modal
                    insertForm.reset(); // Clear form fields
                    location.reload(); // Refresh to update table data
                });
            } else {
                Swal.fire({
                    title: "Error!",
                    text: data.message,
                    icon: "error",
                    confirmButtonText: "OK"
                });
            }
        })
        .catch(error => {
            Swal.close(); // Close loading indicator
            submitButton.disabled = false; // Re-enable submit button

            Swal.fire({
                title: "Error!",
                text: "An unexpected error occurred. Please try again.",
                icon: "error",
                confirmButtonText: "OK"
            });

            console.error("Fetch error:", error);
        });
    });
});
