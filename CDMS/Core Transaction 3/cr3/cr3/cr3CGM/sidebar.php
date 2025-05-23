<?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'manager' || $_SESSION['role'] === 'staff'): ?>
    <!-- Overlay -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Sidebar -->
    <div class="sidebar sidebar-expanded fixed z-50 overflow-hidden h-screen bg-white border-r border-[#F7E6CA] flex flex-col">
        <div class="h-16 border-b border-[#F7E6CA] flex items-center px-2 space-x-2">


            <img src="Logo.png" alt="Logo" class="h-12 w-12 object-contain rounded-xl bg-[#D9D9D9] p-2" />
            <h1 class="text-xl font-bold text-[#4E3B2A]">Core transaction III</h1>
            <!--Close Button-->
            <i id="close-sidebar-btn" class="fa-solid fa-x close-sidebar-btn transform translate-x-20 font-bold text-xl"></i>
        </div>


        <div class="side-menu px-4 py-6">
            <ul class="space-y-4">
                <!-- Dashboard Item -->
                <div class="menu-option">
                    <a href="../guest_management/dashboard.php" class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-house text-lg pr-4"></i>
                            <span class="text-sm font-medium">Dashboard</span>
                        </div>

                    </a>
                </div>


                <!-- Guest Management  -->
                <div class="menu-option">
                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('general-ledger-dropdown', this)">
                        <div class="flex items-center space-x-2">
                            <i class='bx bxs-user-voice'></i>
                            <span class="text-sm font-medium ">Guest Management</span>
                        </div>
                        <div class="arrow">
                            <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                        </div>
                    </div>
                    <div id="general-ledger-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                        <ul class="space-y-1">
                            <li><a href="../guest_management/guest.php" class="text-sm text-gray-800 hover:text-blue-600">Guest</a></li>
                            <li><a href="../guest_management/interactions.php" class="text-sm text-gray-800 hover:text-blue-600">Interaction</a></li>
                            <li><a href="../guest_management/history_feedback.php" class="text-sm text-gray-800 hover:text-blue-600">Guest History/Feedback</a></li>
                        </ul>
                    </div>
                </div>


        </div>
    </div>
<?php endif; ?>


<?php if ($_SESSION['role'] === 'guest'): ?>
    <!-- Overlay -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Sidebar -->
    <div class="sidebar sidebar-expanded fixed z-50 overflow-hidden h-screen bg-white border-r border-[#F7E6CA] flex flex-col">
        <div class="h-16 border-b border-[#F7E6CA] flex items-center px-2 space-x-2">


            <img src="Logo.png" alt="Logo" class="h-12 w-12 object-contain rounded-xl bg-[#D9D9D9] p-2" />
            <h1 class="text-xl font-bold text-[#4E3B2A]">Hotel Guest</h1>
            <!--Close Button-->
            <i id="close-sidebar-btn" class="fa-solid fa-x close-sidebar-btn transform translate-x-20 font-bold text-xl"></i>
        </div>


        <div class="side-menu px-4 py-6">
            <ul class="space-y-4">


                <!-- Guest Management  -->
                <div class="menu-option">
                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('general-ledger-dropdown', this)">
                        <div class="flex items-center space-x-2">
                            <i class='bx bxs-user-voice'></i>
                            <span class="text-sm font-medium ">Interact</span>
                        </div>
                        <div class="arrow">
                            <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                        </div>
                    </div>
                    <div id="general-ledger-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                        <ul class="space-y-1">
                            <li><a href="../guest_management/guest.php" class="text-sm text-gray-800 hover:text-blue-600">My profile</a></li>
                            <li><a href="../guest_management/interactions.php" class="text-sm text-gray-800 hover:text-blue-600">Interaction</a></li>
                            <li><a href="../guest_management/history_feedback.php" class="text-sm text-gray-800 hover:text-blue-600">Feedback</a></li>
                        </ul>
                    </div>
                </div>


        </div>
    </div>
<?php endif; ?>