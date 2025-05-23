document.addEventListener('DOMContentLoaded', function () {
    const viewButtons = document.querySelectorAll('.view-button');
    const modal = document.getElementById('viewModal');
    const closeModalButtons = document.querySelectorAll('#closeModal, #closeModalBtn');

    viewButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();

            const competencyId = button.getAttribute('data-competency-id');

            if (!competencyId) {
                console.error('Competency ID not found.');
                return;
            }

            fetch(`Table_1_view.php?id=${encodeURIComponent(competencyId)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.text();
                })
                .then(data => {
                    document.getElementById('caseDetails').innerHTML = data;

                    modal.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error fetching competency details:', error);
                });
        });
    });

    // Close modal when clicking the close buttons or outside the content
    closeModalButtons.forEach(button => {
        button.addEventListener('click', function () {
            modal.classList.add('hidden'); 
        });
    });

    // Close modal when clicking outside the content vise versa
    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    });
});
