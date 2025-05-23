
function openGuestDeleteModal(guestID, guestName) {
    document.getElementById("guestID").value = guestID;
    document.getElementById("deleteGuestName").textContent = guestName;
    document.getElementById("deleteGuestID").textContent = guestID;
    document.getElementById("guestDeleteModal").classList.remove("hidden");
}

function closeGuestModal() {
    document.getElementById("guestDeleteModal").classList.add("hidden");
}


   function openFeedbackModal(feedbackID, guestName) {
        document.getElementById('feedbackDeleteModal').classList.remove('hidden');
        document.getElementById('deleteFeedbackName').textContent = guestName;
        document.getElementById('deleteFeedbackID').textContent = feedbackID;
        document.getElementById('FeedbackID').value = feedbackID;
    }

    function closeFeedbackModal() {
        document.getElementById('feedbackDeleteModal').classList.add('hidden');
    }



