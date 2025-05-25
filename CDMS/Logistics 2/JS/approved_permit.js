function denyPermit(permitId) {
    Swal.fire({
        icon: 'approval',
        title: 'approve Permit?',
        text: 'Are you sure you want to approve this permit?',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Yes, Approved'
    }).then(result => {
        if (result.isConfirmed) {
            fetch('approved_permit.php', {
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