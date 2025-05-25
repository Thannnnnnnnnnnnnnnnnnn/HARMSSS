 function toggleModal(show) {
    const modal = document.getElementById('purchaseModal');
    const content = document.getElementById('modalContent');

    if (show) {
      modal.classList.remove('hidden');
      setTimeout(() => {
        modal.classList.add('opacity-100');
        modal.classList.remove('opacity-0');
        content.classList.add('scale-100');
        content.classList.remove('scale-95');
      }, 10);
    } else {
      modal.classList.remove('opacity-100');
      modal.classList.add('opacity-0');
      content.classList.remove('scale-100');
      content.classList.add('scale-95');
      setTimeout(() => {
        modal.classList.add('hidden');
      }, 300);
    }
  }