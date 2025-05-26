 function openModal(action, assetId) {
        const modal = document.getElementById('actionModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalMessage = document.getElementById('modalMessage');
        const modalAssetId = document.getElementById('modalAssetId');
        const modalAction = document.getElementById('modalAction');

        modalAssetId.value = assetId;
        modalAction.value = action;

        modalTitle.textContent = (action === 'approve' ? 'Approve Asset' : 'Deny Asset');
        modalMessage.textContent = `Are you sure you want to ${action} this asset?`;

        modal.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('actionModal').classList.add('hidden');
    }