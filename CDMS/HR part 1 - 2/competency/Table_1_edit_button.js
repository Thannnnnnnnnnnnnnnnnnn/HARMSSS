document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll('.edit-button');

    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const competencyId = button.getAttribute('data-competency-id');

            if (!competencyId) {
                console.error('Competency ID not found.');
                return;
            }

            fetch(`Table_1_edit.php?id=${encodeURIComponent(competencyId)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.text();
                })
                .then(data => {
                    document.getElementById('edit-case-form').innerHTML = data;
                })
                .catch(error => {
                    console.error('Error fetching competency details:', error);
                });
        });
    });

    //  SweetAlert2
    document.addEventListener('submit', function (event) {
        if (event.target && event.target.id === 'editCompetencyForm') {
            event.preventDefault(); // Prevent default form submission

            const formData = new FormData(event.target);

            fetch('Table_1_update.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Updated Successfully!',
                    text: 'The competency details have been updated.',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Close'
                }).then(() => {
                    window.location.reload();
                });
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Update Failed!',
                    text: 'Something went wrong while updating.',
                    confirmButtonColor: '#d33'
                });
                console.error('Error updating competency:', error);
            });
        }
    });
});
