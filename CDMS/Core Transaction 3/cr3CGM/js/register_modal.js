
document.addEventListener("DOMContentLoaded", function () {
    // Function to open modal
    const openModalButtons = document.querySelectorAll("[data-modal-toggle]");
    openModalButtons.forEach(button => {
        button.addEventListener("click", () => {
            const modalId = button.getAttribute("data-modal-toggle");
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove("hidden");
            }
        });
    });

    // Function to close modal
    const closeModalButtons = document.querySelectorAll("[data-modal-hide]");
    closeModalButtons.forEach(button => {
        button.addEventListener("click", () => {
            const modalId = button.getAttribute("data-modal-hide");
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add("hidden");
            }
        });
    });

    // Optional: close modal when clicking outside the modal content
    const modals = document.querySelectorAll(".fixed");
    modals.forEach(modal => {
        modal.addEventListener("click", (event) => {
            if (event.target === modal) {
                modal.classList.add("hidden");
            }
        });
    });
});
