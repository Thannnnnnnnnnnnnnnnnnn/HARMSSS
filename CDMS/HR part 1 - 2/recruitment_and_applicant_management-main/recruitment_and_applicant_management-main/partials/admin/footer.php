<script>
    function toggleNotificationDropdown() {
        document.getElementById('notif-dropdown').classList.toggle('hidden');
        document.getElementById('profile-dropdown').classList.add('hidden');
    }
    document.addEventListener('click', function(event) {
        const profileBtn = document.getElementById('profile-btn');
        const profileDropdown = document.getElementById('profile-dropdown');
        const notifBtn = document.getElementById('notif-btn');
        const notifDropdown = document.getElementById('notif-dropdown');

        if (!profileBtn.contains(event.target)) {
            profileDropdown.classList.add('hidden');
        }
        if (!notifBtn.contains(event.target)) {
            notifDropdown.classList.add('hidden');
        }
    });
    const menu = document.querySelector(".menu-btn");
    const sidebar = document.querySelector(".sidebar");
    const main = document.querySelector(".main");
    const overlay = document.getElementById("sidebar-overlay");
    const close = document.getElementById("close-sidebar-btn");

    function closeSidebar() {
        sidebar.classList.remove("mobile-active");
        overlay.classList.remove("active");
        document.body.style.overflow = "auto";
    }

    function openSidebar() {
        sidebar.classList.add("mobile-active");
        overlay.classList.add("active");
        document.body.style.overflow = "hidden";
    }

    document.addEventListener("DOMContentLoaded", function() {
        if (sidebar.classList.contains("sidebar-collapsed")) {
            main.style.marginLeft = "85px"; // Adjust accordingly
        } else {
            main.style.marginLeft = "320px"; // Adjust accordingly
        }
    });


    function toggleSidebar() {
        if (window.innerWidth <= 968) {
            sidebar.classList.add("sidebar-expanded");
            sidebar.classList.remove("sidebar-collapsed");
            sidebar.classList.contains("mobile-active") ?
                closeSidebar() :
                openSidebar();
        } else {
            sidebar.classList.toggle("sidebar-collapsed");
            sidebar.classList.toggle("sidebar-expanded");

            if (sidebar.classList.contains("sidebar-collapsed")) {
                main.style.marginLeft = "85px";
            } else {
                main.style.marginLeft = "320px";
            }
        }
    }

    menu.addEventListener("click", toggleSidebar);
    overlay.addEventListener("click", closeSidebar);
    close.addEventListener("click", closeSidebar);

    window.addEventListener("resize", () => {
        if (window.innerWidth > 968) {
            closeSidebar();
            sidebar.classList.remove("mobile-active");
            overlay.classList.remove("active");
            sidebar.classList.remove("sidebar-collapsed");
            sidebar.classList.add("sidebar-expanded");
        } else {
            sidebar.classList.add("sidebar-expanded");
            sidebar.classList.remove("sidebar-collapsed");
        }
    });

    function toggleDropdown(dropdownId, element) {
        const dropdown = document.getElementById(dropdownId);
        const icon = element.querySelector(".arrow-icon");
        const allDropdowns = document.querySelectorAll(".menu-drop");
        const allIcons = document.querySelectorAll(".arrow-icon");

        allDropdowns.forEach((d) => {
            if (d !== dropdown) d.classList.add("hidden");
        });

        allIcons.forEach((i) => {
            if (i !== icon) {
                i.classList.remove("bx-chevron-down");
                i.classList.add("bx-chevron-right");
            }
        });

        dropdown.classList.toggle("hidden");
        icon.classList.toggle("bx-chevron-right");
        icon.classList.toggle("bx-chevron-down");
    }

    const INACTIVITY_LIMIT = 1800;
    let inactivityTimer;

    function logoutUser() {
        fetch('logout.php')
            .then(() => {
                swal.fire({
                    title: "Session Expired",
                    text: "You have been logged out due to inactivity.",
                    icon: "warning",
                    button: "OK",
                });
                window.location.href = '/session_timeout';
            })
            .catch(err => console.error("Logout error:", err));
    }

    function resetInactivityTimer() {
        clearTimeout(inactivityTimer);
        inactivityTimer = setTimeout(logoutUser, INACTIVITY_LIMIT * 1000);
    }

    ['click', 'mousemove', 'keydown', 'scroll', 'touchstart'].forEach(event => {
        document.addEventListener(event, resetInactivityTimer);
    });

    resetInactivityTimer();
</script>
<script src="/js/sweetAlert.js"></script>
</body>

</html>