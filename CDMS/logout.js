function logout() {
    Swal.fire({
        title: 'Are you sure you want to log out?',
        text: 'You will be redirected to the login page.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, log out'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'login.php'; 
        }
    });
}