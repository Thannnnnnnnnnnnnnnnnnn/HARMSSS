function toggleDropdown(dropdownId, element) {
    const dropdown = document.getElementById(dropdownId);
    const icon = element.querySelector('.arrow-icon');

    // Toggle the visibility of the dropdown
    dropdown.classList.toggle('hidden');

    // Toggle the arrow icon direction
    icon.classList.toggle('rotate');

    // Close other dropdowns and reset their icons
    const allIcons = document.querySelectorAll('.arrow-icon');

    allDropdowns.forEach(d => {
        if (d !== dropdown) {
            d.classList.add('hidden');
        }
    });

    allIcons.forEach(i => {
        if (i !== icon) {
            i.classList.remove('bx-chevron-down');
            i.classList.add('bx-chevron-right');
        }
    });
}

const menu = document.querySelector('.menu-btn');
const sidebar = document.querySelector('.sidebar');

menu.addEventListener('click', function() {
    sidebar.classList.toggle('sidebar-collapsed');
    sidebar.classList.toggle('sidebar-expanded');
});

