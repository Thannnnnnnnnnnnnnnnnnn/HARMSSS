document.addEventListener('DOMContentLoaded', function() {
    if (!window.Swal || !window.fetch) {
        console.warn('Swal or fetch API not supported');
        alert('Some features may not work as expected. Please update your browser.');
        return;
    }

    const editButtons = document.querySelectorAll('.edit-button');

    editButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const reservationId = button.closest('form').getAttribute('action').split('=')[1];
            
            // Fetch the edit form content for the selected reservation ID
            fetch('edit_case.php?id=' + reservationId)
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP status ${response.status}`);
                    return response.text();
                })
                .then(data => {
                    // Display the form in a Swal modal
                    Swal.fire({
                        html: data,
                        showCloseButton: true,
                        customClass: { popup: 'bigger-swal-modal' },
                        width: '70%',
                        heightAuto: false,
                        showCancelButton: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            const updateButton = document.getElementById('updateButton');
                            const updateForm = document.getElementById('updateForm');

                            if (updateButton && updateForm) {
                                // Event listener for form submission
                                updateButton.addEventListener('click', function() {
                                    if (!updateForm.reportValidity()) return;  // Check form validity

                                    const formData = new FormData(updateForm);

                                    // Confirm and show loading
                                    Swal.fire({
                                        title: 'Are you sure you want to save changes?',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: 'Yes, save it!',
                                        cancelButtonText: 'Cancel',
                                        didOpen: () => Swal.showLoading()
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            // Submit the form via fetch
                                            fetch('edit_case.php', {
                                                method: 'POST',
                                                body: formData
                                            })
                                            .then(response => response.json())
                                            .then(responseData => {
                                                if (responseData.success) {
                                                    Swal.fire(
                                                        'Saved!',
                                                        'Your changes have been saved successfully.',
                                                        'success'
                                                    ).then(() => location.reload());
                                                } else {
                                                    Swal.fire(
                                                        'Error!',
                                                        responseData.error || 'An error occurred while saving changes.',
                                                        'error'
                                                    );
                                                }
                                            })
                                            .catch(error => {
                                                Swal.fire('Error!', error.message || 'An unexpected error occurred.', 'error');
                                                console.error('Error submitting the form:', error);
                                            });
                                        }
                                    });
                                });
                            }
                        }
                    });
                })
                .catch(error => {
                    Swal.fire(
                        'Error!',
                        'Could not fetch the edit form. Please try again later.',
                        'error'
                    );
                    console.error('Error fetching edit form:', error);
                });
        });
    });
});
