function showApproveModal(reservationID) {
    document.getElementById("approveReservationID").value = reservationID;
    document.getElementById("approveModal").classList.remove("hidden");
}

function showRejectModal(reservationID) {
    document.getElementById("rejectReservationID").value = reservationID;
    document.getElementById("rejectModal").classList.remove("hidden");
}

function closeModal(modalID) {
    document.getElementById(modalID).classList.add("hidden");
}