const menu = document.querySelector('.menu-btn');
const sidebar = document.querySelector('.sidebar');
const main = document.querySelector('.main');
const overlay = document.getElementById('sidebar-overlay');
const close = document.getElementById('close-sidebar-btn');

function closeSidebar() {
    sidebar.classList.remove('mobile-active');
    overlay.classList.remove('active');
    document.body.style.overflow = 'auto';
}

function openSidebar() {
    sidebar.classList.add('mobile-active');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function toggleSidebar() {
    if (window.innerWidth <= 968) {
        sidebar.classList.add('sidebar-expanded'); 
        sidebar.classList.remove('sidebar-collapsed');
        sidebar.classList.contains('mobile-active') ? closeSidebar() : openSidebar();
    } else {
        sidebar.classList.toggle('sidebar-collapsed');
        sidebar.classList.toggle('sidebar-expanded');
        main.classList.toggle('md:ml-[85px]');
        main.classList.toggle('md:ml-[360px]');
    }
}

menu.addEventListener('click', toggleSidebar);
overlay.addEventListener('click', closeSidebar);
close.addEventListener('click', closeSidebar);

window.addEventListener('resize', () => {
    if (window.innerWidth > 968) {
        closeSidebar();
        sidebar.classList.remove('mobile-active');
        overlay.classList.remove('active');
        sidebar.classList.remove('sidebar-collapsed');
        sidebar.classList.add('sidebar-expanded'); 
    } else {
        sidebar.classList.add('sidebar-expanded'); 
        sidebar.classList.remove('sidebar-collapsed');
    }
});

function toggleDropdown(dropdownId, element) {
    const dropdown = document.getElementById(dropdownId);
    const icon = element.querySelector('.arrow-icon');
    const allDropdowns = document.querySelectorAll('.menu-drop');
    const allIcons = document.querySelectorAll('.arrow-icon');

    // Only close other dropdowns if the current one is not active
    allDropdowns.forEach(d => {
        if (d !== dropdown && !d.classList.contains('active')) {
            d.classList.add('hidden');
        }
    });

    allIcons.forEach(i => {
        if (i !== icon && !i.closest('.menu-name').classList.contains('bg-[#F7E6CA]')) {
            i.classList.remove('bx-chevron-down');
            i.classList.add('bx-chevron-right');
        }
    });

    dropdown.classList.toggle('hidden');
    icon.classList.toggle('bx-chevron-right');
    icon.classList.toggle('bx-chevron-down');
}