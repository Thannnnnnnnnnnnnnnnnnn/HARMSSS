<?php include 'db_connection.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>                                               
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .sidebar-collapsed {
            width: 85px;
        }
        .sidebar-expanded {
            width: 320px;
        }
       
        .sidebar-collapsed .menu-name span,
        .sidebar-collapsed .menu-name .arrow {
            display: none;
        }
        
        .sidebar-collapsed .menu-name i {
            margin-right: 0;
        }
        
        .sidebar-collapsed .menu-drop {
            display: none;
        }

        .sidebar-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            position: fixed;
            inset: 0;
            z-index: 40;
            display: none;
        }

        .sidebar-overlay.active {
            display: block;
        }
        .close-sidebar-btn{
                display: none;
            }
        @media (max-width: 968px) {
            .sidebar {
                position: fixed;
                left: -100%;
                transition: left 0.3s ease-in-out;
            }

            .sidebar.mobile-active {
                left: 0;
            }

            .main {
                margin-left: 0 !important;
            }
            .close-sidebar-btn{
                display: block;
            }
        }
        .menu-name {
            position: relative;
            overflow: hidden;
        }

        .menu-name::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            height: 2px;
            width: 0;
            background-color: #4E3B2A;
            transition: width 0.3s ease;
        }

        .menu-name:hover::after {
            width: 100%;
        }
  .sidebar.sidebar-collapsed #logo-name {
    display: none;
  }
    </style>
</head>
<body>
    <div class="flex min-h-screen w-full">
        <!-- Overlay -->
        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- Sidebar -->
<div class="sidebar sidebar-expanded fixed z-50 overflow-hidden h-screen bg-white border-r border-[#F7E6CA] flex flex-col">
    <div class="h-16 border-b border-[#F7E6CA] flex items-center px-2 space-x-2">
        <!-- Logo Image -->
        <img src="logo.png" alt="Logo" class="h-15 w-10 rounded-x1 object-cover" />
        
        <!-- Text beside image -->
<span id="logo-name" class="transition-all duration-300 overflow-hidden">
  <img src="logo-name.png" alt="Logo Text" class="h-10 w-auto object-contain" />
</span>
        <!-- Close Button -->
        <i id="close-sidebar-btn" class="fa-solid fa-x close-sidebar-btn transform translate-x-20 font-bold text-xl"></i>
    </div>
            <div class="side-menu px-4 py-6">
                 <ul class="space-y-4">
                    <!-- Dashboard Item -->
                   <div class="menu-option">
                        <a href="index.php" class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-house text-lg pr-4"></i>
                                <span class="text-sm font-medium">Dashboard</span>
                            </div>
                        
                        </a>
                    </div>

                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('disbursement-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-wallet text-lg pr-4"></i>
                                <span class="text-sm font-medium">New Hire Onboarding and Employee Self-Service</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="disbursement-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('training-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-file-invoice-dollar text-lg pr-4"></i>
                                <span class="text-sm font-medium">Learning & Training Management</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="training-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <li><a href="Employees.php" class="text-sm text-gray-800 hover:text-blue-600">Employee</a></li>
                                <li><a href="trainers.php" class="text-sm text-gray-800 hover:text-blue-600">Trainer</a></li>
                                <li><a href="enrollments.php" class="text-sm text-gray-800 hover:text-blue-600">Enrollments</a></li>
                                <li><a href="training_programs.php" class="text-sm text-gray-800 hover:text-blue-600">Training Program</a></li>
                                <li><a href="training_materials.php" class="text-sm text-gray-800 hover:text-blue-600">Training Materials</a></li>
                                <li><a href="assessments.php" class="text-sm text-gray-800 hover:text-blue-600">Assessments</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Budget Management Item  -->
                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('budget-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-chart-pie text-lg pr-4"></i>
                                <span class="text-sm font-medium">Performance Management</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="budget-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Collection Item  -->
                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('collection-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-folder-open text-lg pr-4"></i>
                                <span class="text-sm font-medium">Recruitment and Applicant Management</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="collection-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('general-ledger-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-money-bills text-lg pr-4"></i>
                                <span class="text-sm font-medium ">Social Recognition</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="general-ledger-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Account Payable/Receiver Item  -->
                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('account-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-file-invoice-dollar text-lg pr-4"></i>
                                <span class="text-sm font-medium">Competency Management</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="account-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('succession-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-file-invoice-dollar text-lg pr-4"></i>
                                <span class="text-sm font-medium">Succession Planning</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="succession-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                            </ul>
                        </div>
                    </div>
                    
                </ul>
            </div>
        </div>

        <!-- Main + Navbar -->
        <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
            <!-- Navbar -->
            <nav class="h-16 w-full bg-white border-b border-[#F7E6CA] flex justify-between items-center px-6 py-4">
                <!-- Left Navigation Section -->
                <div class="left-nav flex items-center space-x-4 max-w-96 w-full">
                <!-- Toggle Menu Button-->
                    <button aria-label="Toggle menu" class="menu-btn text-[#4E3B2A] focus:outline-none hover:bg-[#F7E6CA] hover:rounded-full">
                        <i class="fa-solid fa-bars text-[#594423] text-xl w-11 py-2"></i>
                    </button>
                    
                    
                </div>

                <div>
                   <i class="fa-regular fa-user bg-[#594423] text-white px-4 py-2 rounded-lg cursor-pointer text-lg lg:hidden" aria-label="User profile"></i>
                </div>

                <!-- Right Navigation Section -->
                <div class="right-nav  items-center space-x-6 hidden lg:flex relative">
                    <div class="relative">
                        <button id="notification-bell" aria-label="Notifications" class="text-[#4E3B2A] focus:outline-none border-r border-[#F7E6CA] pr-6 relative">
                            <i class="fa-regular fa-bell text-xl"></i>
                            <span class="absolute top-0.5 right-5 block w-2.5 h-2.5 bg-[#594423] rounded-full"></span>
                        </button>
                        <!-- Notification Dropdown -->
                        <div id="notification-dropdown" class="hidden absolute right-0 mt-2 w-72 bg-white border border-[#F7E6CA] rounded-lg shadow-lg z-50">
                            <div class="p-4 border-b border-[#F7E6CA] font-semibold text-[#594423]">Notifications</div>
                            <ul class="max-h-60 overflow-y-auto">
                                <li class="px-4 py-2 hover:bg-[#F7E6CA] cursor-pointer text-sm text-[#4E3B2A]">Welcome to the system!</li>
                                <li class="px-4 py-2 hover:bg-[#F7E6CA] cursor-pointer text-sm text-[#4E3B2A]">Your training program starts soon.</li>
                                <li class="px-4 py-2 hover:bg-[#F7E6CA] cursor-pointer text-sm text-[#4E3B2A]">Assessment results are available.</li>
                            </ul>
                            <div class="p-2 text-center text-xs text-[#594423] border-t border-[#F7E6CA]">No new notifications</div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fa-regular fa-user bg-[#594423] text-white px-4 py-2 rounded-lg text-lg" aria-label="User profile"></i>
                        <div class="info flex flex-col py-2">
                            <h1 class="text-[#4E3B2A] font-semibold font-serif text-sm">Madelyn Cline</h1>
                            <p class="text-[#594423] text-sm pl-2">Administrator</p>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <?php 
            include 'db_connection.php';
            // Fetch counts for dashboard
            $employeeCount = $conn->query("SELECT COUNT(*) as cnt FROM employees")->fetch_assoc()['cnt'];
            $trainerCount = $conn->query("SELECT COUNT(*) as cnt FROM trainers")->fetch_assoc()['cnt'];
            $programCount = $conn->query("SELECT COUNT(*) as cnt FROM trainingprograms")->fetch_assoc()['cnt'];
            $materialCount = $conn->query("SELECT COUNT(*) as cnt FROM trainingmaterials")->fetch_assoc()['cnt'];
            $assessmentCount = $conn->query("SELECT COUNT(*) as cnt FROM assessments")->fetch_assoc()['cnt'];
            ?>
            <main class="px-8 py-8">
                <h1 class="text-2xl font-bold text-[#4E3B2A] mb-6">Learning & Training Management System</h1>
                <!-- Dashboard Summary Boxes -->
                 <a href="employees.php" class="block">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-10">
                    <div class="bg-[#F7E6CA] border-2 border-[#594423] rounded-xl p-6 flex flex-col items-center shadow">
                        <i class="fa-solid fa-users text-3xl text-[#594423] mb-2"></i>
                        <div class="text-2xl font-bold"><?php echo $employeeCount; ?></div>
                        <div class="text-sm font-medium text-[#594423]">Employees</div>
                    </div>
    </a>
    <a href="trainers.php" class="block">
                    <div class="bg-[#F7E6CA] border-2 border-[#594423] rounded-xl p-6 flex flex-col items-center shadow">
                        <i class="fa-solid fa-chalkboard-user text-3xl text-[#594423] mb-2"></i>
                        <div class="text-2xl font-bold"><?php echo $trainerCount; ?></div>
                        <div class="text-sm font-medium text-[#594423]">Trainers</div>
                    </div>
    </a>
    <a href="training_programs.php" class="block">
                    <div class="bg-[#F7E6CA] border-2 border-[#594423] rounded-xl p-6 flex flex-col items-center shadow">
                        <i class="fa-solid fa-list-check text-3xl text-[#594423] mb-2"></i>
                        <div class="text-2xl font-bold"><?php echo $programCount; ?></div>
                        <div class="text-sm font-medium text-[#594423]">Programs</div>
                    </div>
    </a>
    <a href="training_materials.php" class="block">
                    <div class="bg-[#F7E6CA] border-2 border-[#594423] rounded-xl p-6 flex flex-col items-center shadow">
                        <i class="fa-solid fa-book text-3xl text-[#594423] mb-2"></i>
                        <div class="text-2xl font-bold"><?php echo $materialCount; ?></div>
                        <div class="text-sm font-medium text-[#594423]">Training Materials</div>
                    </div>
    </a>
    <a href="assessments.php" class="block">
                    <div class="bg-[#F7E6CA] border-2 border-[#594423] rounded-xl p-6 flex flex-col items-center shadow">
                        <i class="fa-solid fa-file-pen text-3xl text-[#594423] mb-2"></i>
                        <div class="text-2xl font-bold"><?php echo $assessmentCount; ?></div>
                        <div class="text-sm font-medium text-[#594423]">Assessments</div>
                    </div>
                </a>
                </div>
            </main>
        </div>
    </div>

    <script>
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

            allDropdowns.forEach(d => {
                if (d !== dropdown) d.classList.add('hidden');
            });

            allIcons.forEach(i => {
                if (i !== icon) {
                    i.classList.remove('bx-chevron-down');
                    i.classList.add('bx-chevron-right');
                }
            });

            dropdown.classList.toggle('hidden');
            icon.classList.toggle('bx-chevron-right');
            icon.classList.toggle('bx-chevron-down');
        }

        // Notification bell dropdown logic
        const notificationBell = document.getElementById('notification-bell');
        const notificationDropdown = document.getElementById('notification-dropdown');

        document.addEventListener('click', function(event) {
            if (notificationBell && notificationDropdown) {
                if (notificationBell.contains(event.target)) {
                    notificationDropdown.classList.toggle('hidden');
                } else if (!notificationDropdown.contains(event.target)) {
                    notificationDropdown.classList.add('hidden');
                }
            }
        });
    </script>
</body>
</html>

