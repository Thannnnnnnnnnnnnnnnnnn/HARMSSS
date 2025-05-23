

document.addEventListener('DOMContentLoaded', function () {
    const viewButtons = document.querySelectorAll('.view-btn');

    viewButtons.forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('guestId').textContent = this.dataset.id || '';
            document.getElementById('guestName').textContent = this.dataset.name || '';
            document.getElementById('guestEmail').textContent = this.dataset.email || '';
            document.getElementById('guestPhone').textContent = this.dataset.phone || '';
            document.getElementById('guestAddress').textContent = this.dataset.address || '';
            document.getElementById('guestBirthday').textContent = this.dataset.birthday || '';
            document.getElementById('guestGender').textContent = this.dataset.gender || '';
            document.getElementById('guestNationality').textContent = this.dataset.nationality || '';
            document.getElementById('guestReservation').textContent = this.dataset.reservation || '';
            document.getElementById('guestCheckin').textContent = this.dataset.checkin || '';
            document.getElementById('guestCheckout').textContent = this.dataset.checkout || '';
            document.getElementById('guestStatus').textContent = this.dataset.status || '';

            // 🆕 Populate Feedback Section
            document.getElementById('guestRating').textContent = this.dataset.rating || 'No rating';
            document.getElementById('guestComment').textContent = this.dataset.comment || 'No comment';
            document.getElementById('guestFeedbackDate').textContent = this.dataset.feedbackDate || 'No date';
        });
    });
});



document.addEventListener("DOMContentLoaded", function () {
    const viewButtons = document.querySelectorAll(".view-btn");

    viewButtons.forEach(button => {
        button.addEventListener("click", function () {
            document.getElementById("interactionId").textContent = this.getAttribute("data-interactionId");
            document.getElementById("guestName").textContent = this.getAttribute("data-guestName");
            document.getElementById("interactionType").textContent = this.getAttribute("data-interactionType");
            document.getElementById("description").textContent = this.getAttribute("data-description");
            document.getElementById("interactionDate").textContent = this.getAttribute("data-interactionDate");
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const viewButtons = document.querySelectorAll(".view-btn");

    viewButtons.forEach(button => {
        button.addEventListener("click", function () {
            document.getElementById("GuestID").textContent = this.getAttribute("data-GuestID");
            document.getElementById("guestName").textContent = this.getAttribute("data-guestName");
            document.getElementById("rating").textContent = this.getAttribute("data-rating");
            document.getElementById("comment").textContent = this.getAttribute("data-comment");
        });
    });
});