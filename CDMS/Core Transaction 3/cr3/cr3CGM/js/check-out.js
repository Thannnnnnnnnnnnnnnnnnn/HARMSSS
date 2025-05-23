document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".checkout-btn").forEach(button => {
      button.addEventListener("click", function () {
          const GuestID = this.getAttribute("data-guest-id");
          const guestName = this.getAttribute("data-guest-name");
          const roomNumber = this.getAttribute("data-room-number");

          // Fill in the modal fields
          document.getElementById("checkoutGuestId").value = GuestID;
          document.getElementById("checkoutGuestIdText").textContent = GuestID;
          document.getElementById("checkoutGuestNameText").textContent = guestName;
          document.getElementById("checkoutRoomText").textContent = roomNumber;
      });
  });
});