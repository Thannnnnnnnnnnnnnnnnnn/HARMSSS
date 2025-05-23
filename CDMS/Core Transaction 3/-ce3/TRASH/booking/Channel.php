<!DOCTYPE html>
<html lang="en">
<head>                                               
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Channel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body{
        font-family: "cinzel",Georgia;
  
         }
         td{
            padding: 7px;
         }
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
            <img src="Logo.png" alt="Company Logo" class="h-20 w-auto p-2 sticky top-0 left-0 z-50">
            <img src="Logo-Name.png" alt="Company Logo" class="h-10 w-auto p-2 sticky top-0 left-0 z-50">
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

                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('account-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-file-invoice-dollar text-lg pr-4"></i>
                                <span class="text-sm font-medium">Booking </span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="account-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <li><a href="booking.php" class="text-sm text-gray-800 hover:text-blue-600">Booking Logs</a></li>
                                <li><a href="Channel.php" class="text-sm text-gray-800 hover:text-blue-600">Booking Channel</a></li>
                                <li><a href="Guest.php" class="text-sm text-gray-800 hover:text-blue-600">Booking Preference</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('disbursement-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-wallet text-lg pr-4"></i>
                                <span class="text-sm font-medium">Reservation</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="disbursement-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Disbursement Request</a></li>
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Approvals</a></li>
                                <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Payment Methods</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('budget-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-chart-pie text-lg pr-4"></i>
                                <span class="text-sm font-medium">Facility Management</span>
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
                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('collection-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-folder-open text-lg pr-4"></i>
                                <span class="text-sm font-medium">Customer/Guest Management</span>
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
                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('general-ledger-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-money-bills text-lg pr-4"></i>
                                <span class="text-sm font-medium ">Customer Relationship Management</span>
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
            <main class="px-4 py-4">
 <h2 class="text-center text-2xl font-bold text-gray-800 mb-4">Booking Channels</h2>

            <div class="bg-gradient-to-b from-white to-orange-100 min-h-screen p-8">


 


  <div class="overflow-x-auto bg-white shadow-lg rounded-xl">
  <table class="min-w-full text-sm text-left">
      <thead class="bg-[#f1ddbe] text-black uppercase">
      <tr>
          <th class="px-4 py-3">Channel ID</th>
          <th class="px-4 py-3">Channel Name</th>
          <th class="px-4 py-3">Channel Type</th>
          <th class="px-4 py-3">Description</th>
          <th class="px-4 py-3">Action</th>
        </tr>
      </thead>
      <tbody>

    <tr class="border-t">
      <td class="px-4 py-2">1</td>
      <td class="px-4 py-2">Agoda</td>
      <td class="px-4 py-2 bg-green-100 text-green-800 font-medium rounded">Online</td>
      <td class="px-4 py-2">Online travel agency platform</td>
      <td><button class="bg-blue-600 text-white w-[70px] p-2 rounded-lg"><i class="bx bx-show"></i></button></td>
    </tr>
  
    <tr class="border-t">
      <td class="px-4 py-2">2</td>
      <td class="px-4 py-2">Walk-in</td>
      <td class="px-4 py-2 bg-red-100 text-red-800 font-medium rounded">Offline</td>
      <td class="px-4 py-2">Direct on-site bookings</td>
      <td><button class="bg-blue-600 text-white w-[70px] p-2 rounded-lg"><i class="bx bx-show"></i></button></td>
    </tr>

    <tr class="border-t">
      <td class="px-4 py-2">3</td>
      <td class="px-4 py-2">Booking.com</td>
      <td class="px-4 py-2 bg-green-200 text-green-800 px-2 py-1 rounded">Online</td>
      <td class="px-4 py-2">Popular hotel booking site</td>
      <td><button class="bg-blue-600 text-white w-[70px] p-2 rounded-lg"><i class="bx bx-show"></i></button></td>
    </tr>

    <tr class="border-t">
      <td class="px-4 py-2">4</td>
      <td class="px-4 py-2">Traveloka</td>
      <td class="px-4 py-2 bg-green-100 text-green-800 font-medium rounded">Online</td>
      <td class="px-4 py-2">Travel and hotel booking platform</td>
      <td><button class="bg-blue-600 text-white w-[70px] p-2 rounded-lg"><i class="bx bx-show"></i></button></td>
    </tr>

    <tr class="border-t">
      <td class="px-4 py-2">5</td>
      <td class="px-4 py-2">Front Desk</td>
      <td class="px-4 py-2 bg-red-100 text-red-800 font-medium rounded">Offline</td>
      <td class="px-4 py-2">Reservations via reception</td>
      <td><button class="bg-blue-600 text-white w-[70px] p-2 rounded-lg"><i class="bx bx-show"></i></button></td>
    </tr>

    <tr class="border-t">
      <td class="px-4 py-2">6</td>
      <td class="px-4 py-2">Expedia</td>
      <td class="px-4 py-2 bg-green-200 text-green-800 px-2 py-1 rounded">Online</td>
      <td class="px-4 py-2">Global booking network</td>
      <td><button class="bg-blue-600 text-white w-[70px] p-2 rounded-lg"><i class="bx bx-show"></i></button></td>
    </tr>
  
    <tr class="border-t">
      <td class="px-4 py-2">7</td>
      <td class="px-4 py-2">Phone Call</td>
      <td class="px-4 py-2 bg-red-100 text-red-800 font-medium rounded">Offline</td>
      <td class="px-4 py-2">Reservations via direct call</td>
      <td><button class="bg-blue-600 text-white w-[70px] p-2 rounded-lg"><i class="bx bx-show"></i></button></td>
    </tr>
  </tbody>
    </table>
  </div>
  <div class="flex items-center justify-between mt-4">
      <div class="text-sm text-gray-700">
        10 of 100
      </div>
      
      <div class="flex items-center space-x-1">
      <button class="px-3 py-1 bg-[#f1ddbe]  hover:bg-gray-300 rounded-md"><</button>
        <button class="px-3 py-1 bg-[#f1ddbe]  hover:bg-gray-300 rounded-md">1</button>
        <button class="px-3 py-1 bg-[#f1ddbe]  hover:bg-gray-300 rounded-md">2</button>
        <button class="px-3 py-1 bg-[#f1ddbe]  hover:bg-gray-300 rounded-md">3</button>
        <button class="px-3 py-1 bg-[#f1ddbe]  hover:bg-gray-300 rounded-md">4</button>
        <button class="px-3 py-1 bg-[#f1ddbe]  hover:bg-gray-300 rounded-md">5</button>
        <button class="px-3 py-1 bg-[#f1ddbe]  hover:bg-gray-300 rounded-md">6</button>
        <button class="px-3 py-1 bg-[#f1ddbe]  hover:bg-gray-300 rounded-md">7</button>
        <button class="px-3 py-1 bg-[#f1ddbe]  hover:bg-gray-300 rounded-md">8</button>
        <button class="px-3 py-1 bg-[#f1ddbe]  hover:bg-gray-300 rounded-md">9</button>
        <button class="px-3 py-1 bg-[#f1ddbe]  hover:bg-gray-300 rounded-md">></button>
      </div>

      <div class="text-sm text-gray-700">
        Items per page:
        <select class="bg-[#f1ddbe]  border border-gray-300 rounded-md py-1 px-2 ml-2">
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="50">50</option>
        </select>
      </div>
    </div>
            </main>
        </div>
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
