
    const tbody = document.getElementById('guestTbody');
    const noGuestsMessage = document.getElementById('noGuestsMessage');

    if (!tbody || tbody.children.length === 0) {
        noGuestsMessage.classList.remove('hidden');
    }
