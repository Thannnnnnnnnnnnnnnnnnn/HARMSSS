function denyPermit(permitId) {
    Swal.fire({
        icon: 'warning',
        title: 'Deny Permit?',
        text: 'Are you sure you want to deny this permit?',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Deny'
    }).then(result => {
        if (result.isConfirmed) {
            fetch('deny_permit.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'permit_id=' + encodeURIComponent(permitId)
            })
            .then(response => response.text())
            .then(html => {
                document.open();
                document.write(html);
                document.close();
            })
            .catch(error => Swal.fire('Error', error.message, 'error'));
        }
    });
}

function openApproveModal(permitId) {
    document.getElementById("approvePermitId").value = permitId;
    document.getElementById("approveModal").classList.remove("hidden");
}

function closeApproveModal() {
    document.getElementById("approveModal").classList.add("hidden");
}

function openDenyModal(permitId) {
    document.getElementById("denyPermitId").value = permitId;
    document.getElementById("denyModal").classList.remove("hidden");
}

function closeDenyModal() {
    document.getElementById("denyModal").classList.add("hidden");
}