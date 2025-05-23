 
    document.addEventListener('DOMContentLoaded', function() {
        const alterbuttons = document.querySelectorAll('.alter-button');
        const deleteButtons = document.querySelectorAll('.delete-button');

        function handleAction(buttons, icon, actionText, successMessage) {
            buttons.forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const form = button.closest('form');
                    if (actionText === 'Deleted') {
                        let action = '';
                        if (actionText === 'Deleted') {
                            action = 'delete this record';
                        }
                        Swal.fire({
                            title: 'Are you sure?',
                            text: `You are about to ${action}.`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, proceed!',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire({
                                    title: successMessage,
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 800, // milliseconds
                                    timerProgressBar: true
                                }).then(() => {
                                    
                                    form.submit();
                                });
                            }
                        });
                    } else {
                       
                        Swal.fire({
                            title: successMessage,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 800,// milliseconds
                            timerProgressBar: true
                        });
                        
                        setTimeout(function() {
                            form.submit();
                        }, 800); 
                    }
                });
            });
        }

        handleAction(deleteButtons, 'warning', 'Deleted', 'Deleted successfully');
    });