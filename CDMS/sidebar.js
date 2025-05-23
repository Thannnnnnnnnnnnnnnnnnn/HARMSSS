// Handle dropdown toggling
document.addEventListener('DOMContentLoaded', function () {
    // Select all dropdowns
    const dropdowns = document.querySelectorAll('.dropdown');

    dropdowns.forEach(function (dropdown) {
        const toggleButton = dropdown.querySelector('.dropdown-toggle');
        const submenu = dropdown.querySelector('.submenu');

        // Toggle the submenu visibility
        toggleButton.addEventListener('click', function (event) {
            // Toggle current submenu visibility
            submenu.classList.toggle('show');
            submenu.style.opacity = submenu.classList.contains('show') ? 1 : 0;
            submenu.style.transform = submenu.classList.contains('show') ? 'scaleY(1)' : 'scaleY(0)';
        });
    });

    // Handle submenu toggling (for nested dropdowns)
    const submenuToggles = document.querySelectorAll('.submenu-toggle');
    submenuToggles.forEach(function (submenuToggle) {
        submenuToggle.addEventListener('click', function (event) {
            const nestedSubmenu = submenuToggle.nextElementSibling;
            if (nestedSubmenu) {
                nestedSubmenu.classList.toggle('show');
                nestedSubmenu.style.opacity = nestedSubmenu.classList.contains('show') ? 1 : 0;
                nestedSubmenu.style.transform = nestedSubmenu.classList.contains('show') ? 'scaleY(1)' : 'scaleY(0)';
            }
        });
    });

    // Handle logout functionality (mocked for this example)
    window.logout = function () {
        // Replace with actual logout functionality
        console.log('Logging out...');
        // Example: redirect to login page or call logout API
        window.location.href = 'login.html'; // Replace with your logout page or function
    };
});
