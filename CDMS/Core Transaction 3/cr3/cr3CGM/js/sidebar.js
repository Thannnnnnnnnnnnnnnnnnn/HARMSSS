document.addEventListener("DOMContentLoaded", function () {
    const toggleButton = document.getElementById("toggleButton");
    const submenu = document.getElementById("submenu");
    const dropdownIcon = document.getElementById("dropdown-icon");

    toggleButton.addEventListener("click", function () {
        submenu.classList.toggle("hidden");
        dropdownIcon.classList.toggle("rotate-180");
    });
});