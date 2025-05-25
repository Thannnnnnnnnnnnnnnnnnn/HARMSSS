function denyPermit(permitId) {
    Swal.fire({
        icon: 'warning',
        title: 'Deny Permit?',
        text: 'Are you sure you want to deny this permit?',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Yes, Deny'
    }).then(result => {
        if (result.isConfirmed) {
            fetch('deny_permit.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'permit_id=' + encodeURIComponent(permitId)
            })
            .then(response => response.text())
            .then(html => document.write(html))
            .catch(error => Swal.fire('Error', error, 'error'));
        }
    });
}
