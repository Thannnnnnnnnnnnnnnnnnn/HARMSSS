function showCancelModal(purchaseId) {
    document.getElementById('cancelPurchaseId').value = decodeURIComponent(purchaseId);
    document.getElementById('cancelModal').classList.remove('hidden');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
}