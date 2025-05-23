
document.addEventListener("DOMContentLoaded", function () {
    const feedbackButtons = document.querySelectorAll('[data-bs-target="#feedbackFormModal"]');

    feedbackButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Get values from the row where the button was clicked
            const guestName = this.getAttribute("data-guestname") || '';
            const rating = this.getAttribute("data-rating") || '';
            const comment = this.getAttribute("data-comment") || '';

            // Set values in modal fields
            document.getElementById("guestNameForm").value = guestName;
            document.getElementById("ratingForm").value = rating;
            document.getElementById("commentForm").value = comment;
        });
    });
});
