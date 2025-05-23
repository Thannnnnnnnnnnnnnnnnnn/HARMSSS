function openModal(interactionID, guestName) {
    document.getElementById('deleteCustomerID').textContent = interactionID;
    document.getElementById('deleteCustomerName').textContent = guestName;
    document.getElementById('customerID').value = interactionID;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

window.addEventListener('click', function(event) {
    const modal = document.getElementById('deleteModal');
    if (event.target === modal) {
        closeModal();
    }
});