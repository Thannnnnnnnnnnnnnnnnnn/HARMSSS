<?php
// ob_start();

// // Security headers
// header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://fonts.googleapis.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com https://cdnjs.cloudflare.com; img-src 'self' data:; font-src 'self' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; connect-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self';");
// header("X-Content-Type-Options: nosniff");
// header("X-Frame-Options: DENY");
// header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prevent caching to avoid back button showing cached pages after logout
// header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
// header("Pragma: no-cache"); // HTTP 1.0.
// header("Expires: 0"); // Proxies

// Redirect to login if not logged in
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

// Sanitize GET parameter
function sanitize_page($page)
{
    return preg_replace('/[^a-zA-Z0-9_-]/', '', $page);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Cinzel:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#594423',
                        secondary: '#4E3B2A',
                        accent: '#F7E6CA',
                        background: '#FFF6E8',
                    },
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                        'cinzel': ['Cinzel', 'serif'],
                    },
                }
            }
        }
    </script>
</head>
<?php require "partials/admin/head.php" ?>

<body class="bg-background font-inter text-primary">
    <div class="flex min-h-screen w-full">
        <!-- Overlay $$ Sidebar -->
        <?php require 'partials/admin/sidebar.php' ?>
        <!-- <div class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" id="sidebar-overlay"></div>

    <div class="sidebar fixed z-50 h-screen bg-white border-r border-accent flex flex-col transition-all duration-300 ease-in-out w-[320px]">
        <div class="h-16 border-b border-accent flex items-center px-6 justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-full border-2 border-primary bg-accent flex items-center justify-center">
                    <img src="includes/logo.png" alt="Logo" class="w-10 h-10 rounded-full">
                </div>
                <div class="h-6 transition-opacity duration-300">
                    <img src="includes/logo-name.png" alt="Logo-name" class="h-6">
                </div>
            </div>
        </div>

        <div class="px-4 py-6 flex-1 overflow-y-auto">
            <ul class="space-y-4">
                <div class="menu-option">
                    <a href="?page=dashboard" class="flex justify-between items-center space-x-3 hover:bg-accent px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer group">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-house text-lg pr-4 text-secondary"></i>
                            <span class="text-sm font-medium text-secondary transition-opacity duration-300">Dashboard</span>
                        </div>
                    </a>
                </div>

                <div class="menu-option">
                    <div class="flex justify-between items-center space-x-3 hover:bg-accent px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('recruitment-dropdown', this)">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-user-plus text-lg pr-4 text-secondary"></i>
                            <span class="text-sm font-medium text-secondary transition-opacity duration-300">Recruitment and Applicant Management</span>
                        </div>
                        <div class="arrow">
                            <i class="bx bx-chevron-right text-lg font-semibold arrow-icon text-secondary"></i>
                        </div>
                    </div>
                    <div id="recruitment-dropdown" class="hidden flex-col w-full bg-accent rounded-lg p-4 space-y-2 mt-2">
                        <ul class="space-y-2">
                            <li><a href="#" class="text-sm text-secondary hover:text-primary block pl-11">Job Postings</a></li>
                            <li><a href="#" class="text-sm text-secondary hover:text-primary block pl-11">Applicant Tracking</a></li>
                            <li><a href="#" class="text-sm text-secondary hover:text-primary block pl-11">Interview Management</a></li>
                        </ul>
                    </div>
                </div>

                <div class="menu-option">
                    <div class="flex justify-between items-center space-x-3 hover:bg-accent px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('onboarding-dropdown', this)">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-user-check text-lg pr-4 text-secondary"></i>
                            <span class="text-sm font-medium text-secondary">Onboarding & Self-Service</span>
                        </div>
                        <div class="arrow">
                            <i class="bx bx-chevron-right text-lg font-semibold arrow-icon text-secondary"></i>
                        </div>
                    </div>
                    <div id="onboarding-dropdown" class="hidden flex-col w-full bg-accent rounded-lg p-4 space-y-2 mt-2">
                        <ul class="space-y-2">
                            <li><a href="#" class="text-sm text-secondary hover:text-primary block pl-11">New Hire Onboarding</a></li>
                            <li><a href="#" class="text-sm text-secondary hover:text-primary block pl-11">Employee Portal</a></li>
                            <li><a href="#" class="text-sm text-secondary hover:text-primary block pl-11">Document Management</a></li>
                        </ul>
                    </div>
                </div>

                <div class="menu-option">
                    <div class="flex justify-between items-center space-x-3 hover:bg-accent px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('learning-dropdown', this)">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-graduation-cap text-lg pr-4 text-secondary"></i>
                            <span class="text-sm font-medium text-secondary">Learning & Training</span>
                        </div>
                        <div class="arrow">
                            <i class="bx bx-chevron-right text-lg font-semibold arrow-icon text-secondary"></i>
                        </div>
                    </div>
                    <div id="learning-dropdown" class="hidden flex-col w-full bg-accent rounded-lg p-4 space-y-2 mt-2">
                        <ul class="space-y-2">
                            <li><a href="#" class="text-sm text-secondary hover:text-primary block pl-11">Training Programs</a></li>
                            <li><a href="#" class="text-sm text-secondary hover:text-primary block pl-11">Course Management</a></li>
                            <li><a href="#" class="text-sm text-secondary hover:text-primary block pl-11">Learning Paths</a></li>
                        </ul>
                    </div>
                </div>

                <div class="menu-option">
                    <div class="flex justify-between items-center space-x-3 hover:bg-accent px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('performance-dropdown', this)">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-chart-line text-lg pr-4 text-secondary"></i>
                            <span class="text-sm font-medium text-secondary">Performance Management</span>
                        </div>
                        <div class="arrow">
                            <i class="bx bx-chevron-right text-lg font-semibold arrow-icon text-secondary"></i>
                        </div>
                    </div>
                    <div id="performance-dropdown" class="hidden flex-col w-full bg-accent rounded-lg p-4 space-y-2 mt-2">
                        <ul class="space-y-2">
                            <li><a href="#" class="text-sm text-secondary hover:text-primary block pl-11">Performance Reviews</a></li>
                            <li><a href="#" class="text-sm text-secondary hover:text-primary block pl-11">Goal Setting</a></li>
                            <li><a href="#" class="text-sm text-secondary hover:text-primary block pl-11">Feedback Management</a></li>
                        </ul>
                    </div>
                </div>

                <div class="menu-option">
                    <div class="flex justify-between items-center space-x-3 hover:bg-accent px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('recognition-dropdown', this)">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-award text-lg pr-4 text-secondary"></i>
                            <span class="text-sm font-medium text-secondary">Social Recognition</span>
                        </div>
                        <div class="arrow">
                            <i class="bx bx-chevron-right text-lg font-semibold arrow-icon text-secondary"></i>
                        </div>
                    </div>
                    <div id="recognition-dropdown" class="hidden flex-col w-full bg-accent rounded-lg p-4 space-y-2 mt-2">
                        <ul class="space-y-2">
                            <li><a href="?page=recognition-dashboard" class="text-sm text-secondary hover:text-primary block pl-11">Dashboard</a></li>
                            <li><a href="?page=awards" class="text-sm text-secondary hover:text-primary block pl-11">Awards</a></li>
                            <li><a href="?page=recognitions" class="text-sm text-secondary hover:text-primary block pl-11">Employee Recognition</a></li>
                            <li><a href="?page=categories" class="text-sm text-secondary hover:text-primary block pl-11">Categories</a></li>
                        </ul>
                    </div>
                </div>

                <div class="menu-option">
                    <div class="flex justify-between items-center space-x-3 hover:bg-accent px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('competency-dropdown', this)">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-list-check text-lg pr-4 text-secondary"></i>
                            <span class="text-sm font-medium text-secondary">Competency Management</span>
                        </div>
                        <div class="arrow">
                            <i class="bx bx-chevron-right text-lg font-semibold arrow-icon text-secondary"></i>
                        </div>
                    </div>
                    <div id="competency-dropdown" class="hidden flex-col w-full bg-accent rounded-lg p-4 space-y-2 mt-2">
                        <ul class="space-y-2">
                            <li><a href="#" class="text-sm text-secondary hover:text-primary block pl-11">Competency Framework</a></li>
                            <li><a href="#" class="text-sm text-secondary hover:text-primary block pl-11">Skill Assessment</a></li>
                            <li><a href="#" class="text-sm text-secondary hover:text-primary block pl-11">Development Planning</a></li>
                        </ul>
                    </div>
                </div>

                <div class="menu-option">
                    <div class="flex justify-between items-center space-x-3 hover:bg-accent px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('succession-dropdown', this)">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-people-arrows text-lg pr-4 text-secondary"></i>
                            <span class="text-sm font-medium text-secondary">Succession Planning</span>
                        </div>
                        <div class="arrow">
                            <i class="bx bx-chevron-right text-lg font-semibold arrow-icon text-secondary"></i>
                        </div>
                    </div>
                    <div id="succession-dropdown" class="hidden flex-col w-full bg-accent rounded-lg p-4 space-y-2 mt-2">
                        <ul class="space-y-2">
                            <li><a href="#" class="text-sm text-secondary hover:text-primary block pl-11">Talent Pools</a></li>
                            <li><a href="#" class="text-sm text-secondary hover:text-primary block pl-11">Succession Plans</a></li>
                            <li><a href="#" class="text-sm text-secondary hover:text-primary block pl-11">Career Pathing</a></li>
                        </ul>
                    </div>
                </div>

                <div class="menu-option">
                    <div class="flex justify-between items-center space-x-3 hover:bg-accent px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('user-management-dropdown', this)">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-users-cog text-lg pr-4 text-secondary"></i>
                            <span class="text-sm font-medium text-secondary">User Management</span>
                        </div>
                        <div class="arrow">
                            <i class="bx bx-chevron-right text-lg font-semibold arrow-icon text-secondary"></i>
                        </div>
                    </div>
                    <div id="user-management-dropdown" class="hidden flex-col w-full bg-accent rounded-lg p-4 space-y-2 mt-2">
                        <ul class="space-y-2">
                            <li><a href="?page=department-accounts" class="text-sm text-secondary hover:text-primary block pl-11">Department Accounts</a></li>
                            <li><a href="?page=department-log-history" class="text-sm text-secondary hover:text-primary block pl-11">Department Log History</a></li>
                            <li><a href="?page=department-audit-trail" class="text-sm text-secondary hover:text-primary block pl-11">Department Audit Trail</a></li>
                            <li><a href="?page=department-transaction" class="text-sm text-secondary hover:text-primary block pl-11">Department Transaction</a></li>
                        </ul>
                    </div>
                </div>
            </ul>
        </div>
    </div> -->

        <!-- Main Content -->
        <div class="main w-full md:ml-[320px] bg-[#f7e6ca]">
            <!-- Navbar -->
            <?php require 'partials/admin/navbar.php' ?>
            <!-- <nav class="h-16 w-full bg-white border-b border-accent flex justify-between items-center px-6">
            <div class="flex items-center space-x-4">
                <button id="menu-toggle" class="text-secondary hover:bg-accent p-2 rounded-lg">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
            </div>

            <div class="flex items-center space-x-4">
                <div class="relative">
                    <button id="notification-btn" class="relative p-2 hover:bg-accent rounded-lg">
                        <i class="fa-regular fa-bell text-xl text-secondary"></i>
                        <span id="notification-badge" class="absolute -top-1 -right-1 bg-red-500 rounded-full w-2 h-2 hidden"></span>
                    </button>
                    <div id="notification-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-accent z-50">
                        <div class="p-4 border-b border-accent">
                            <h3 class="text-sm font-medium text-secondary">Notifications</h3>
                        </div>
                        <div id="notification-list" class="max-h-96 overflow-y-auto">
                        </div>
                        <div class="p-2 border-t border-accent text-center">
                            <button id="mark-all-read" class="text-sm text-secondary hover:text-primary">Mark all as read</button>
                        </div>
                    </div>
                </div>
                <div class="relative flex items-center space-x-3 cursor-pointer" id="userDropdownToggle">
                    <div class="w-10 h-10 bg-secondary rounded-lg flex items-center justify-center">
                        <i class="fa-regular fa-user text-white"></i>
                    </div>
                    <div class="hidden md:block">
                        <h3 class="text-sm font-medium text-secondary"><?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'Guest'; ?></h3>
                        <p class="text-xs text-primary"><?php echo isset($_SESSION['role']) ? htmlspecialchars(ucfirst($_SESSION['role'])) : 'Visitor'; ?></p>
                    </div>
                    <div id="userDropdownMenu" class="hidden absolute right-0 mt-10 w-40 bg-white rounded-lg shadow-lg border border-accent z-50">
                        <a href="logout.php" class="block px-4 py-2 text-sm text-secondary hover:bg-accent hover:text-primary rounded-b-lg transition-colors">Logout</a>
                    </div>
                </div>
            </div>
        </nav> -->

            <!-- Page Content -->
            <div class="p-6">
                <?php
                // Determine which page to include based on the query parameter
                if (isset($_GET['page'])) {
                    $page = sanitize_page($_GET['page']);
                    switch ($page) {
                        case 'recognition-dashboard':
                            include 'recognition-dashboard.php';
                            break;
                        case 'awards':
                            include 'awards.php';
                            break;
                        case 'recognitions':
                            include 'recognitions.php';
                            break;
                        case 'categories':
                            include 'categories.php';
                            break;
                        case 'dashboard':
                            echo '<h1 class="text-2xl font-bold text-secondary mb-6">Dashboard</h1>';
                            echo '<div class="bg-white rounded-lg p-6 shadow-sm border border-accent">';
                            echo '<p class="text-primary">Welcome to the HR Management System Dashboard.</p>';
                            echo '</div>';
                            break;
                        case 'department-accounts':
                            include 'user_management/department-accounts.php';
                            break;
                        case 'department-log-history':
                            include 'user_management/department-log-history.php';
                            break;
                        case 'department-audit-trail':
                            include 'user_management/department-audit-trail.php';
                            break;
                        case 'department-transaction':
                            include 'user_management/department-transaction.php';
                            break;
                        default:
                            echo '<h1 class="text-2xl font-bold text-secondary mb-6">Page Not Found</h1>';
                            echo '<div class="bg-white rounded-lg p-6 shadow-sm border border-accent">';
                            echo '<p class="text-primary">The requested page could not be found.</p>';
                            echo '</div>';
                    }
                } else {
                    echo '<h1 class="text-2xl font-bold text-secondary mb-6">Dashboard</h1>';
                    echo '<div class="bg-white rounded-lg p-6 shadow-sm border border-accent">';
                    echo '<p class="text-primary">Welcome to the HR Management System Dashboard.</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <!-- <script>
        function toggleDropdown(dropdownId, element) {
            const dropdown = document.getElementById(dropdownId);
            const icon = element.querySelector('.arrow-icon');
            const allDropdowns = document.querySelectorAll('.menu-drop');
            const allIcons = document.querySelectorAll('.arrow-icon');

            // Close all other dropdowns
            allDropdowns.forEach(d => {
                if (d.id !== dropdownId && !d.classList.contains('hidden')) {
                    d.classList.add('hidden');
                }
            });

            // Reset all other icons
            allIcons.forEach(i => {
                if (i !== icon) {
                    i.classList.remove('bx-chevron-down');
                    i.classList.add('bx-chevron-right');
                }
            });

            // Toggle current dropdown
            dropdown.classList.toggle('hidden');
            icon.classList.toggle('bx-chevron-right');
            icon.classList.toggle('bx-chevron-down');
        }


        // Mobile menu toggle
        const menuToggle = document.getElementById('menu-toggle');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const mainContent = document.querySelector('.main');
        const menuTexts = document.querySelectorAll('.menu-option span, .menu-option .arrow');
        const logoName = document.querySelector('.h-6');

        function toggleSidebar() {
            if (window.innerWidth <= 768) { // Mobile view
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
                document.body.classList.toggle('overflow-hidden');
            } else { // Desktop view
                if (sidebar.classList.contains('w-[320px]')) {
                    // Collapse sidebar
                    sidebar.classList.remove('w-[320px]');
                    sidebar.classList.add('w-[100px]');
                    mainContent.classList.remove('md:ml-[320px]');
                    mainContent.classList.add('md:ml-[100px]');
                    // Hide text and arrows
                    menuTexts.forEach(text => text.classList.add('opacity-0', 'hidden'));
                    logoName.classList.add('opacity-0', 'hidden');
                } else {
                    // Expand sidebar
                    sidebar.classList.remove('w-[100px]');
                    sidebar.classList.add('w-[320px]');
                    mainContent.classList.remove('md:ml-[100px]');
                    mainContent.classList.add('md:ml-[320px]');
                    // Show text and arrows
                    menuTexts.forEach(text => text.classList.remove('opacity-0', 'hidden'));
                    logoName.classList.remove('opacity-0', 'hidden');
                }
            }
        }

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) { // Desktop view
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                if (!sidebar.classList.contains('w-[320px]') && !sidebar.classList.contains('w-[100px]')) {
                    sidebar.classList.add('w-[320px]');
                    mainContent.classList.add('md:ml-[320px]');
                    // Show text and arrows
                    menuTexts.forEach(text => text.classList.remove('opacity-0', 'hidden'));
                    logoName.classList.remove('opacity-0', 'hidden');
                }
            } else { // Mobile view
                sidebar.classList.add('-translate-x-full');
                if (!overlay.classList.contains('hidden')) {
                    overlay.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }
                // Show text and arrows for mobile
                menuTexts.forEach(text => text.classList.remove('opacity-0', 'hidden'));
                logoName.classList.remove('opacity-0', 'hidden');
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize sidebar state
            if (window.innerWidth <= 768) {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }

            // Add event listeners
            menuToggle.addEventListener('click', toggleSidebar);
            overlay.addEventListener('click', toggleSidebar);

            // Close sidebar when clicking a menu link (for mobile)
            const menuLinks = document.querySelectorAll('.menu-option a');
            menuLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 768) {
                        sidebar.classList.add('-translate-x-full');
                        overlay.classList.add('hidden');
                        document.body.classList.remove('overflow-hidden');
                    }
                });
            });

            // Ensure overlay is hidden and sidebar is interactive on page load
            overlay.classList.remove('active');
            sidebar.classList.remove('mobile-active');
            document.body.classList.remove('overflow-hidden');
        });

        // Auto-expand the Social Recognition dropdown if on an awards/recognition page
        document.addEventListener('DOMContentLoaded', function() {
            const currentPage = window.location.search;
            if (currentPage.includes('page=awards') ||
                currentPage.includes('page=recognitions') ||
                currentPage.includes('page=categories') ||
                currentPage.includes('page=recognition-dashboard')) {

                const recognitionDropdown = document.getElementById('recognition-dropdown');
                const recognitionIcon = document.querySelector('[onclick="toggleDropdown(\'recognition-dropdown\', this)"] .arrow-icon');

                if (recognitionDropdown && recognitionIcon) {
                    recognitionDropdown.classList.remove('hidden');
                    recognitionIcon.classList.remove('bx-chevron-right');
                    recognitionIcon.classList.add('bx-chevron-down');
                }
            }

            // Auto-expand the User Management dropdown if on a user management subpage
            if (currentPage.includes('page=department-accounts') ||
                currentPage.includes('page=department-log-history') ||
                currentPage.includes('page=department-audit-trail') ||
                currentPage.includes('page=department-transaction')) {

                const userManagementDropdown = document.getElementById('user-management-dropdown');
                const userManagementIcon = document.querySelector('[onclick="toggleDropdown(\'user-management-dropdown\', this)"] .arrow-icon');

                if (userManagementDropdown && userManagementIcon) {
                    userManagementDropdown.classList.remove('hidden');
                    userManagementIcon.classList.remove('bx-chevron-right');
                    userManagementIcon.classList.add('bx-chevron-down');
                }
            }
        });

        // Notification System
        document.addEventListener('DOMContentLoaded', function() {
            const notificationBtn = document.getElementById('notification-btn');
            const notificationDropdown = document.getElementById('notification-dropdown');
            const notificationList = document.getElementById('notification-list');
            const notificationBadge = document.getElementById('notification-badge');
            const markAllReadBtn = document.getElementById('mark-all-read');

            // Function to mark notification as read
            window.markAsRead = function(id) {
                console.log(`Marking notification ${id} as read`);

                fetch('notifications.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: `action=mark_read&id=${id}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Response:', data);

                        if (!data.success) {
                            console.error('Failed to mark as read:', data.message);
                            return;
                        }

                        // Find the notification item
                        const notificationItem = document.querySelector(`.notification-item[data-id="${id}"]`);
                        if (notificationItem) {
                            const actionsDiv = notificationItem.querySelector('.notification-actions');
                            notificationItem.classList.remove('unread');
                            actionsDiv.innerHTML = `
                            <button class="btn btn-link btn-sm text-decoration-none p-0 mark-unread" onclick="markAsUnread(${id})">
                                Mark as unread
                            </button>
                        `;
                        }

                        // Update all notifications to refresh counts
                        loadNotifications();
                    })
                    .catch(error => {
                        console.error('Error marking as read:', error);
                    });
            };

            // Function to mark notification as unread
            window.markAsUnread = function(id) {
                console.log(`Marking notification ${id} as unread`);

                fetch('notifications.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: `action=mark_unread&id=${id}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Response:', data);

                        if (!data.success) {
                            console.error('Failed to mark as unread:', data.message);
                            return;
                        }

                        // Find the notification item
                        const notificationItem = document.querySelector(`.notification-item[data-id="${id}"]`);
                        if (notificationItem) {
                            const actionsDiv = notificationItem.querySelector('.notification-actions');
                            notificationItem.classList.add('unread');
                            actionsDiv.innerHTML = `
                            <button class="text-xs text-secondary hover:text-primary mark-read" onclick="markAsRead(${id})">
                                Mark as read
                            </button>
                        `;
                        }

                        // Update all notifications to refresh counts
                        loadNotifications();
                    })
                    .catch(error => {
                        console.error('Error marking as unread:', error);
                    });
            };

            // Toggle notification dropdown
            notificationBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                notificationDropdown.classList.toggle('hidden');
                loadNotifications();
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!notificationDropdown.contains(e.target) && !notificationBtn.contains(e.target)) {
                    notificationDropdown.classList.add('hidden');
                }
            });

            // Load notifications
            function loadNotifications() {
                // Fetch notifications from the server
                fetch('notifications.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: 'action=get_notifications'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            console.error('Failed to fetch notifications:', data.message);
                            return;
                        }

                        notificationList.innerHTML = '';
                        let unreadCount = 0;

                        if (data.notifications.length === 0) {
                            notificationList.innerHTML = `
                            <div class="p-4 text-center text-sm text-primary">
                                No notifications
                            </div>
                        `;
                        } else {
                            data.notifications.forEach(notification => {
                                if (!notification.read) unreadCount++;
                                notificationList.innerHTML += `
                                <div class="p-4 border-b border-accent hover:bg-accent transition-colors ${!notification.read ? 'bg-background' : ''} notification-item" data-id="${notification.id}">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-8 h-8 bg-accent rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid ${notification.icon} text-secondary"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-secondary">${notification.title}</p>
                                            <p class="text-xs text-primary mt-1">${notification.message}</p>
                                            <p class="text-xs text-primary mt-2">${notification.time_ago}</p>
                                        </div>
                                        <div class="notification-actions">
                                            ${notification.read ? `
                                                <button class="text-xs text-secondary hover:text-primary mark-unread" onclick="markAsUnread(${notification.id})">
                                                    Mark as unread
                                                </button>
                                            ` : `
                                                <button class="text-xs text-secondary hover:text-primary mark-read" onclick="markAsRead(${notification.id})">
                                                    Mark as read
                                                </button>
                                            `}
                                        </div>
                                    </div>
                                </div>
                            `;
                            });
                        }

                        // Update badge
                        if (unreadCount > 0) {
                            notificationBadge.classList.remove('hidden');
                        } else {
                            notificationBadge.classList.add('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading notifications:', error);
                        notificationList.innerHTML = `
                        <div class="p-4 text-center text-sm text-red-500">
                            Error loading notifications
                        </div>
                    `;
                    });
            }

            // Mark all as read
            markAllReadBtn.addEventListener('click', function() {
                fetch('notifications.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: 'action=mark_all_read'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            console.error('Failed to mark all as read:', data.message);
                            return;
                        }
                        loadNotifications();
                    })
                    .catch(error => console.error('Error marking all as read:', error));
            });

            // Update notifications every 30 seconds
            setInterval(loadNotifications, 30000);

            // Initial load of notifications
            loadNotifications();
        });

        // User Dropdown
        document.addEventListener('DOMContentLoaded', function() {
            const userDropdownToggle = document.getElementById('userDropdownToggle');
            const userDropdownMenu = document.getElementById('userDropdownMenu');

            userDropdownToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdownMenu.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!userDropdownMenu.contains(e.target) && !userDropdownToggle.contains(e.target)) {
                    userDropdownMenu.classList.add('hidden');
                }
            });
        });
    </script>
</body>

</html> -->

    <?php require "partials/admin/footer.php" ?>