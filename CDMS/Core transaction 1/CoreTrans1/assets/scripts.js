
const menu = document.querySelector('.menu-btn');
const sidebar = document.querySelector('.sidebar');
const main = document.querySelector('.main');
const overlay = document.getElementById('sidebar-overlay');
const close = document.getElementById('close-sidebar-btn');
const logoImg = document.getElementById('logo-img');

function closeSidebar() {
    sidebar.classList.remove('mobile-active');
    overlay.classList.remove('active');
    document.body.style.overflow = 'auto';
    if (logoImg) logoImg.style.display = 'block';
}

function openSidebar() {
    sidebar.classList.add('mobile-active');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
    if (logoImg) logoImg.style.display = 'none';
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
        if (logoImg) logoImg.style.display = sidebar.classList.contains('sidebar-collapsed') ? 'none' : 'block';
    }
}

if (menu) menu.addEventListener('click', toggleSidebar);
if (overlay) overlay.addEventListener('click', closeSidebar);
if (close) close.addEventListener('click', closeSidebar);

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
    dropdown.classList.toggle('hidden');
    element.querySelector('.arrow')?.classList.toggle('rotate-90');
}

function loadModule(page) {
    fetch(page)
        .then(res => res.text())
        .then(html => document.getElementById('main-content').innerHTML = html);
}