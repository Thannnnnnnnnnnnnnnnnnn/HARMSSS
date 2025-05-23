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
    </style>
</head>
<body>
    <div class="flex min-h-screen w-full">
        <!-- Overlay -->
        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- Sidebar -->
        <div class="sidebar sidebar-expanded fixed z-50 overflow-hidden h-screen bg-white border-r border-[#F7E6CA] flex flex-col">
            <div class="h-16 border-b border-[#F7E6CA] flex items-center px-2 space-x-2">
                <h1 class="text-xl font-bold text-black bg-[#D9D9D9] p-2 rounded-xl">LOGO</h1>
                <h1 class="text-xl font-bold text-[#4E3B2A]"><i class="bx bxs-server"></i>Centralized Database</h1>
                <!--Close Button-->
                <i id="close-sidebar-btn" class="fa-solid fa-x close-sidebar-btn transform translate-x-20 font-bold text-xl"></i>
            </div>
            <div class="side-menu px-4 py-6">
                 <ul class="space-y-4">
                    <!-- Dashboard Item -->
                   <div class="menu-option">
                        <a href="finalTemplate.html" class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-house text-lg pr-4"></i>
                                <span class="text-sm font-medium">Dashboard</span>
                            </div>
                        
                        </a>
                    </div>

                    <!-- Disbursement Item  -->
                    <div class="menu-option">
                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('Databases- HR part 1 - 2 - dropdown', this)">
                    <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-wallet text-lg pr-4"></i>
                                <span class="text-sm font-medium">HR Part 1 - 2</span>
                                </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="Databases- HR part 1 - 2 - dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                            <li>
                        <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                        <i class="bx bx-medal text-lg"></i> <span>Competencies</span>
                                    </a>
                                </li>
                                <li>
                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                        <i class="bx bx-bar-chart-alt text-lg"></i> <span>Competency Levels</span>
                                    </a>
                                </li>
                                <li>
                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                        <i class="bx bx-user-check text-lg"></i> <span>Employee Competencies</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Budget Management Item  -->
                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('budget-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-chart-pie text-lg pr-4"></i>
                                <span class="text-sm font-medium">Budget Management</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="budget-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Budget</a></li>
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Budget Allocations</a></li>
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Budget Adjustments</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Collection Item  -->
                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('collection-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-folder-open text-lg pr-4"></i>
                                <span class="text-sm font-medium">Collection</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="collection-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Invoices</a></li>
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Payments</a></li>
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Payment Methods</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- General Ledger Item  -->
                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('general-ledger-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-money-bills text-lg pr-4"></i>
                                <span class="text-sm font-medium ">General Ledger</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="general-ledger-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Journal Entries</a></li>
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Account</a></li>
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Transactions</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Account Payable/Receiver Item  -->
                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('account-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-file-invoice-dollar text-lg pr-4"></i>
                                <span class="text-sm font-medium">Account Payable/Receiver</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="account-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Payable Invoices</a></li>
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Vendor Payments</a></li>
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Payment Schedules</a></li>
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
                    
                    <div class="relative w-full flex pr-2">
                        <input type="text" 
                               class="bg-[#FFF6E8] h-10 rounded-lg grow w-full pl-10 pr-4 focus:ring-2 focus:ring-[#F7E6CA] focus:outline-none" 
                               placeholder="Search something..." 
                               aria-label="Search input"/>
                        <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-[#4E3B2A]"></i>
                    </div>
                </div>

                <div>
                   <i class="fa-regular fa-user bg-[#594423] text-white px-4 py-2 rounded-lg cursor-pointer text-lg lg:hidden" aria-label="User profile"></i>
                </div>

                <!-- Right Navigation Section -->
                <div class="right-nav  items-center space-x-6 hidden lg:flex">
                    <button aria-label="Notifications" class="text-[#4E3B2A] focus:outline-none border-r border-[#F7E6CA] pr-6 relative">
                        <i class="fa-regular fa-bell text-xl"></i>
                        <span class="absolute top-0.5 right-5 block w-2.5 h-2.5 bg-[#594423] rounded-full"></span>
                    </button>

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
            <main class="px-8 py-8">
                <!-- All Content Put Here -->
                Hello World
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
    </script>
</body>
</html>
