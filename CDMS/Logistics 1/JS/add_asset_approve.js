let selectedAssetId = null;
let selectedAction = null;

function openModal(action, assetId) {
  selectedAssetId = assetId;
  selectedAction = action;

  const modal = document.getElementById('confirmModal');
  const title = document.getElementById('modalTitle');
  const message = document.getElementById('modalMessage');

  title.textContent = action === 'approve' ? 'Approve Funding' : 'Deny Funding';
  message.textContent = `Are you sure you want to ${action} asset ID ${assetId}?`;

  modal.classList.remove('hidden');
  modal.classList.add('flex');
}

function closeModal() {
  const modal = document.getElementById('confirmModal');
  modal.classList.remove('flex');
  modal.classList.add('hidden');
}

document.getElementById('confirmActionBtn').addEventListener('click', () => {
  fetch('../asset_aquasition.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `asset_id=${encodeURIComponent(selectedAssetId)}&action=${selectedAction}`
  })
  .then(res => res.json())
  .then(data => {
    closeModal();
    alert(data.message); // Swap with SweetAlert if needed
    if (data.success) location.reload();
  })
  .catch(err => {
    closeModal();
    alert('Something went wrong.');
    console.error(err);
  });
});