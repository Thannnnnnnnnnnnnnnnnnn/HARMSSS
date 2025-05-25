
function approveFunding(fundingId) {
    Swal.fire({
        icon: 'question',
        title: 'Approve Funding?',
        text: 'Are you sure you want to approve this funding request?',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#d1d5db',
        confirmButtonText: 'Yes, Approve'
    }).then(result => {
        if (result.isConfirmed) {
            fetch('approve_funding.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'funding_id=' + encodeURIComponent(fundingId)
            })
            .then(response => response.text())
            .then(html => document.write(html))
            .catch(error => Swal.fire('Error', error, 'error'));
        }
    });
}

function denyFunding(fundingId) {
    Swal.fire({
        icon: 'warning',
        title: 'Deny Funding?',
        text: 'Are you sure you want to deny this funding request?',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#d1d5db',
        confirmButtonText: 'Yes, Deny'
    }).then(result => {
        if (result.isConfirmed) {
            fetch('deny_funding.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'funding_id=' + encodeURIComponent(fundingId)
            })
            .then(response => response.text())
            .then(html => document.write(html))
            .catch(error => Swal.fire('Error', error, 'error'));
        }
    });
}

