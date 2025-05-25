function openEditModal(purchaseId, date, purpose, typeOfItem, itemName, quantity, estimatedBudget) {
    document.getElementById('editPurchaseId').value = purchaseId;
    document.getElementById('editDate').value = date;
    document.getElementById('editPurpose').value = purpose;
    document.getElementById('editTypeOfItem').value = typeOfItem;
    document.getElementById('editItemName').value = itemName;
    document.getElementById('editQuantity').value = quantity;
    document.getElementById('editEstimatedBudget').value = estimatedBudget;

    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
  document.getElementById('editModal').classList.add('hidden');
}

// Attach event listener to form submit
const editForm = document.querySelector('#editModal form');
if (editForm) {
  editForm.addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(editForm);

    fetch('edit_request.php', {
      method: 'POST',
      body: formData,
    })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: 'Success',
            text: data.message,
            timer: 2000,
            showConfirmButton: false,
          });
          closeEditModal();
          // Optionally refresh your table or update UI here
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.message,
          });
        }
      })
      .catch(() => {
        Swal.fire({
          icon: 'error',
          title: 'Network Error',
          text: 'Could not reach the server.',
        });
      });
  });
}
