document.addEventListener("DOMContentLoaded", function () {
    let editButtons = document.querySelectorAll(".edit-btn");

    editButtons.forEach(button => {
        button.addEventListener("click", function () {
            let id = this.getAttribute("data-guest-id");
            let name = this.getAttribute("data-name");
            let email = this.getAttribute("data-email");
            let phone = this.getAttribute("data-phone");
            let address = this.getAttribute("data-address");
            let birthday = this.getAttribute("data-birthday");
            let gender = this.getAttribute("data-gender");
            let nationality = this.getAttribute("data-nationality");
            let reservation = this.getAttribute("data-reservation");
            let checkin = this.getAttribute("data-checkin");
            let checkout = this.getAttribute("data-checkout");
            let status = this.getAttribute("data-status");

            // Populate form fields
            document.getElementById("editGuestId").value = id;
            document.getElementById("editName").value = name;
            document.getElementById("editEmail").value = email;
            document.getElementById("editPhone").value = phone;
            document.getElementById("editAddress").value = address;
            document.getElementById("editBirthday").value = birthday;
            document.getElementById("editGender").value = gender;
            document.getElementById("editNationality").value = nationality;
            document.getElementById("editReservation").value = reservation;
            document.getElementById("editCheckin").value = check_in;
            document.getElementById("editCheckout").value = check_out;
            document.getElementById("editStatus").value = status;
        });
    });
});





document.addEventListener("DOMContentLoaded", function () {
  const editButtons = document.querySelectorAll(".edit-btn");

  editButtons.forEach(button => {
    button.addEventListener("click", function () {
      const interactionId = this.getAttribute("data-interactionId");
      const interactionType = this.getAttribute("data-interactionType");
      const description = this.getAttribute("data-description");
      const interactionDate = this.getAttribute("data-interactionDate");
      const status = this.getAttribute("data-status");

      console.log("Status:", status); // 🧪 Debug

      document.getElementById("editInteractionId").value = interactionId;
      document.getElementById("editInteractionType").value = interactionType;
      document.getElementById("editDescription").value = description;
      document.getElementById("editInteractionDate").value = interactionDate;

      // Safely set status
      const statusSelect = document.getElementById("editStatus");
      if (status && statusSelect) {
        statusSelect.value = status;
      } else {
        statusSelect.selectedIndex = 0; // fallback to first option
      }
    });
  });
});




  (function() {
    const guestId = <?= json_encode($guests['GuestID']) ?>; // safer for JS embedding

    const openBtn = document.getElementById('openReservationModal' + guestId);
    const closeBtn = document.getElementById('closeReservationModal' + guestId);
    const modal = document.getElementById('reservationModal' + guestId);

    openBtn.addEventListener('click', () => {
      modal.classList.remove('hidden');
    });

    closeBtn.addEventListener('click', () => {
      modal.classList.add('hidden');
    });

    window.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.classList.add('hidden');
      }
    });
  })();




