document.addEventListener('DOMContentLoaded', function() {
        const viewButtons = document.querySelectorAll('.view-button');

        viewButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const reservationId = button.closest('form').getAttribute('action').split('=')[1];
                
                
                fetch('view.php?id=' + reservationId)
                    .then(response => response.text())
                    .then(data => {
                       
                        Swal.fire({
                            title: '',
                            html: data,
                            showCloseButton: true
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching reservation details:', error);
                    });
            });
        });
    });