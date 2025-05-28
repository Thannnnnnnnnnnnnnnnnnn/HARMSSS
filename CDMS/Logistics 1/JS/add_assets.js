 function toggleModal(show) {
    const modal = document.getElementById('assetModal');
    if (show) {
      modal.classList.remove('hidden');
    } else {
      modal.classList.add('hidden');
    }
  }