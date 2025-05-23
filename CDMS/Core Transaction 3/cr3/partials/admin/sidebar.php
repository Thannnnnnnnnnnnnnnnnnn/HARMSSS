<?php
 switch ($user['role']):
    case 'admin': ?>
        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- Sidebar -->
        <div class="sidebar sidebar-expanded fixed z-50 overflow-hidden h-screen bg-white border-r border-[#F7E6CA] flex flex-col">
            <div class="h-16 border-b border-[#F7E6CA] flex items-center px-2 space-x-2">
                <img src="../assets/room.png" alt="Avalon Hotel Logo" class="w-20" />
                <img src="../assets/logo.png" alt="Avalon Hotel Logo" class="w-40" />
                <!--Close Button-->
                <i id="close-sidebar-btn" class="fa-solid fa-x close-sidebar-btn transform translate-x-20 font-bold text-xl"></i>
            </div>
            <div class="side-menu px-4 py-6">
                <ul class="space-y-4">
                    <!-- Dashboard Item -->

                    <div class="menu-option">
                        <a href="../testing/dashboard.php" class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-house text-lg pr-4"></i>
                                <span class="text-sm font-medium">Dashboard</span>
                            </div>

                        </a>
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer mt-4" onclick="toggleDropdown('booking-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-book text-lg pr-4"></i>
                                <span class="text-sm font-medium">Booking</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="booking-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <li><a href="../cr3bo/booking.php" class="menu-name text-sm hover:bg-[#F7E6CA] rounded-lg transition duration-300 ease-in-out cursor-pointer">Booking Logs</a></li>
                                <li><a href="../cr3bo/Channel.php" class="menu-name text-sm hover:bg-[#F7E6CA] rounded-lg transition duration-300 ease-in-out cursor-pointer">Booking Channel</a></li>
                                <li><a href="../cr3bo/guest.php" class="menu-name text-sm hover:bg-[#F7E6CA] rounded-lg transition duration-300 ease-in-out cursor-pointer">Booking Preference</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Disbursement Item  -->
                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('reservation-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-calendar-check text-lg pr-4"></i>
                                <span class="text-sm font-medium">Reservation</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="reservation-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">

                                <li><a href="../cr3re/rs.php" class="menu-name text-sm hover:bg-[#F7E6CA] rounded-lg transition duration-300 ease-in-out cursor-pointer">Reservation Status</a></li>
                                <li><a href="../cr3re/rooms.php" class="menu-name text-sm hover:bg-[#F7E6CA] rounded-lg transition duration-300 ease-in-out cursor-pointer">Rooms Status</a></li>

                            </ul>
                        </div>
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
                                <li><a href="../cr3cgm/guest.php" class="text-sm text-gray-800 hover:text-blue-600">Guest</a></li>
                                <li><a href="../cr3cgm/interactions.php" class="text-sm text-gray-800 hover:text-blue-600">Interaction</a></li>
                                <li><a href="../cr3cgm/history_feedback.php" class="text-sm text-gray-800 hover:text-blue-600">Guest History/Feedback</a></li>
                            </ul>
                        </div>
                    </div>


                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('user-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-user-circle text-lg pr-4"></i>
                                <span class="text-sm font-medium ">User Management</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="user-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <li><a href="../usm/dep_accounts.php" class="menu-name text-sm hover:bg-[#F7E6CA] rounded-lg transition duration-300 ease-in-out cursor-pointer">Department Accounts</a></li>
                                <li><a href="../usm/dep_audit_trail.php" class="menu-name text-sm hover:bg-[#F7E6CA] rounded-lg transition duration-300 ease-in-out cursor-pointer">Department Audit trail</a></li>
                                <li><a href="../usm/dep_log_history.php" class="menu-name text-sm hover:bg-[#F7E6CA] rounded-lg transition duration-300 ease-in-out cursor-pointer">Department log history</a></li>
                                <li><a href="../usm/dep_transaction.php" class="menu-name text-sm hover:bg-[#F7E6CA] rounded-lg transition duration-300 ease-in-out cursor-pointer">Department Transactions</a></li>
                            </ul>
                        </div>
                    </div>

                      <!-- Budget Management Item  -->
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
                                <li><a href="../cr3fa/index.php" class="menu-name text-sm hover:bg-[#F7E6CA] rounded-lg transition duration-300 ease-in-out cursor-pointer">Facility Management</a></li>
                                </ul>
                        </div>
                    </div>
                    <!-- Logout -->
<div class="menu-option mt-4">
    <a href="../testing/login.php" class="flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer">
        <div class="flex items-center space-x-2 text-red-600">
            <i class='bx bx-log-out'></i>
            <span class="text-sm font-medium">Logout</span>
        </div>
    </a>
</div>
                </ul>
            </div>
        </div>
        <!----------------------------------------------------------------------------------------- Sidebar ADMIN---------------------------------------------------------------------------->
    <?php break;
    case 'manager': ?>
        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- Sidebar -->
        <div class="sidebar sidebar-expanded fixed z-50 overflow-hidden h-screen bg-white border-r border-[#F7E6CA] flex flex-col">
            <div class="h-16 border-b border-[#F7E6CA] flex items-center px-2 space-x-2">
                <img src="../assets/room.png" alt="Avalon Hotel Logo" class="w-20" />
                <img src="../assets/logo.png" alt="Avalon Hotel Logo" class="w-40" />
                <!--Close Button-->
                <i id="close-sidebar-btn" class="fa-solid fa-x close-sidebar-btn transform translate-x-20 font-bold text-xl"></i>
            </div>
            <div class="side-menu px-4 py-6">
                <ul class="space-y-4">
                    <!-- Dashboard Item -->

                    <div class="menu-option">
                        <a href="../testing/dashboard.php" class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-house text-lg pr-4"></i>
                                <span class="text-sm font-medium">Dashboard</span>
                            </div>

                        </a>
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer mt-4" onclick="toggleDropdown('booking-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-book text-lg pr-4"></i>
                                <span class="text-sm font-medium">Booking</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="booking-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <li><a href="../cr3bo/booking.php" class="menu-name text-sm hover:bg-[#F7E6CA] rounded-lg transition duration-300 ease-in-out cursor-pointer">Booking Logs</a></li>
                                <li><a href="../cr3bo/Channel.php" class="menu-name text-sm hover:bg-[#F7E6CA] rounded-lg transition duration-300 ease-in-out cursor-pointer">Booking Channel</a></li>
                                <li><a href="../cr3bo/guest.php" class="menu-name text-sm hover:bg-[#F7E6CA] rounded-lg transition duration-300 ease-in-out cursor-pointer">Booking Preference</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Disbursement Item  -->
                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('reservation-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-calendar-check text-lg pr-4"></i>
                                <span class="text-sm font-medium">Reservation</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="reservation-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">

                                <li><a href="../cr3re/rs.php" class="menu-name text-sm hover:bg-[#F7E6CA] rounded-lg transition duration-300 ease-in-out cursor-pointer">Reservation Status</a></li>
                                <li><a href="../cr3re/rooms.php" class="menu-name text-sm hover:bg-[#F7E6CA] rounded-lg transition duration-300 ease-in-out cursor-pointer">Rooms Status</a></li>

                            </ul>
                        </div>
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
                                <li><a href="../cr3cgm/guest.php" class="text-sm text-gray-800 hover:text-blue-600">Guest</a></li>
                                <li><a href="../cr3cgm/interactions.php" class="text-sm text-gray-800 hover:text-blue-600">Interaction</a></li>
                                <li><a href="../cr3cgm/history_feedback.php" class="text-sm text-gray-800 hover:text-blue-600">Guest History/Feedback</a></li>
                            </ul>
                        </div>
                    </div>


                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('user-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-user-circle text-lg pr-4"></i>
                                <span class="text-sm font-medium ">User Management</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="user-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <li><a href="../usm/dep_accounts.php" class="menu-name text-sm hover:bg-[#F7E6CA] rounded-lg transition duration-300 ease-in-out cursor-pointer">Department Accounts</a></li>
                                <li><a href="../usm/dep_transaction.php" class="menu-name text-sm hover:bg-[#F7E6CA] rounded-lg transition duration-300 ease-in-out cursor-pointer">Department Transactions</a></li>
                            </ul>
                        </div>
                    </div>

                      <!-- Budget Management Item  -->
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
                                <li><a href="../cr3fa/index.php" class="menu-name text-sm hover:bg-[#F7E6CA] rounded-lg transition duration-300 ease-in-out cursor-pointer">Facility Management</a></li>
                                </ul>
                        </div>
                    </div>
                    <!-- Logout -->
<div class="menu-option mt-4">
    <a href="../testing/login.php" class="flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer">
        <div class="flex items-center space-x-2 text-red-600">
            <i class='bx bx-log-out'></i>
            <span class="text-sm font-medium">Logout</span>
        </div>
    </a>
</div>
                </ul>
            </div>
        </div>

        <!----------------------------------------------------------------------------------------- Sidebar manager-------------------------------------------------------------------------->
    <?php break;
    case 'staff': ?>
        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- Sidebar -->
        <div class="sidebar sidebar-expanded fixed z-50 overflow-hidden h-screen bg-white border-r border-[#F7E6CA] flex flex-col">
            <div class="h-16 border-b border-[#F7E6CA] flex items-center px-2 space-x-2">
                <img src="../assets/room.png" alt="Avalon Hotel Logo" class="w-20" />
                <img src="../assets/logo.png" alt="Avalon Hotel Logo" class="w-40" />
                <!--Close Button-->
                <i id="close-sidebar-btn" class="fa-solid fa-x close-sidebar-btn transform translate-x-20 font-bold text-xl"></i>
            </div>
            <div class="side-menu px-4 py-6">
                <ul class="space-y-4">
                    <!-- Dashboard Item -->

                    <div class="menu-option">
                        <a href="../testing/dashboard.php" class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-house text-lg pr-4"></i>
                                <span class="text-sm font-medium">Dashboard</span>
                            </div>

                        </a>
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer mt-4" onclick="toggleDropdown('booking-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-book text-lg pr-4"></i>
                                <span class="text-sm font-medium">Booking</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="booking-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <li><a href="../cr3bo/booking.php" class="menu-name text-sm hover:bg-[#F7E6CA] rounded-lg transition duration-300 ease-in-out cursor-pointer">Booking Logs</a></li>
                                <li><a href="../cr3bo/Channel.php" class="menu-name text-sm hover:bg-[#F7E6CA] rounded-lg transition duration-300 ease-in-out cursor-pointer">Booking Channel</a></li>
                                <li><a href="../cr3bo/guest.php" class="menu-name text-sm hover:bg-[#F7E6CA] rounded-lg transition duration-300 ease-in-out cursor-pointer">Booking Preference</a></li>
                            </ul>
                        </div>
                    </div>


                     <!-- Disbursement Item  -->
                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('reservation-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-calendar-check text-lg pr-4"></i>
                                <span class="text-sm font-medium">Reservation</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="reservation-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">

                                <li><a href="../cr3re/rs.php" class="menu-name text-sm hover:bg-[#F7E6CA] rounded-lg transition duration-300 ease-in-out cursor-pointer">Reservation Status</a></li>
                                <li><a href="../cr3re/rooms.php" class="menu-name text-sm hover:bg-[#F7E6CA] rounded-lg transition duration-300 ease-in-out cursor-pointer">Rooms Status</a></li>

                            </ul>
                        </div>
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
                                <li><a href="../cr3cgm/guest.php" class="text-sm text-gray-800 hover:text-blue-600">Guest</a></li>
                                <li><a href="../cr3cgm/interactions.php" class="text-sm text-gray-800 hover:text-blue-600">Interaction</a></li>
                                <li><a href="../cr3cgm/history_feedback.php" class="text-sm text-gray-800 hover:text-blue-600">Guest History/Feedback</a></li>
                            </ul>
                        </div>
                    </div>


                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('user-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-user-circle text-lg pr-4"></i>
                                <span class="text-sm font-medium ">User Management</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="user-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                 <li><a href="../usm/dep_accounts.php" class="menu-name text-sm hover:bg-[#F7E6CA] rounded-lg transition duration-300 ease-in-out cursor-pointer">Department Accounts</a></li>
                            </ul>
                        </div>
                    </div>

                      <!-- Budget Management Item  -->
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
                                <li><a href="../cr3fa/index.php" class="menu-name text-sm hover:bg-[#F7E6CA] rounded-lg transition duration-300 ease-in-out cursor-pointer">Facility Management</a></li>
                                </ul>
                        </div>
                    </div>
                   <!-- Logout -->
<div class="menu-option mt-4">
    <a href="../testing/login.php" class="flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer">
        <div class="flex items-center space-x-2 text-red-600">
            <i class='bx bx-log-out'></i>
            <span class="text-sm font-medium">Logout</span>
        </div>
    </a>
</div>
                </ul>
            </div>
        </div>

        <!----------------------------------------------------------------------------------------- Sidebar staff----------------------------------------------------------------------------->
    <?php break;
    case 'guest': ?>

        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- Sidebar -->
        <div class="sidebar sidebar-expanded fixed z-50 overflow-hidden h-screen bg-white border-r border-[#F7E6CA] flex flex-col">
            <div class="h-16 border-b border-[#F7E6CA] flex items-center px-2 space-x-2">
                <img src="../assets/room.png" alt="Avalon Hotel Logo" class="w-20" />
                <img src="../assets/logo.png" alt="Avalon Hotel Logo" class="w-40" />
                <!--Close Button-->
                <i id="close-sidebar-btn" class="fa-solid fa-x close-sidebar-btn transform translate-x-20 font-bold text-xl"></i>
            </div>
            <div class="side-menu px-4 py-6">
                <ul class="space-y-4">
                    <!-- Dashboard Item -->

                    <div class="menu-option">
                        <a href="../testing/dashboard.php" class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-house text-lg pr-4"></i>
                                <span class="text-sm font-medium">Dashboard</span>
                            </div>

                        </a>
                       
                    <!-- Guest Management  -->
                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('general-ledger-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class='bx bxs-user-voice'></i>
                                <span class="text-sm font-medium ">Interact!</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="general-ledger-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <li><a href="../cr3cgm/guest.php" class="text-sm text-gray-800 hover:text-blue-600">Reservation</a></li>
                                <li><a href="../cr3cgm/interactions.php" class="text-sm text-gray-800 hover:text-blue-600">Interaction</a></li>
                                <li><a href="../cr3cgm/history_feedback.php" class="text-sm text-gray-800 hover:text-blue-600">Feedback</a></li>
                            </ul>
                        </div>
                    </div>

                
                    <!-- Logout -->
<div class="menu-option mt-4">
    <a href="../testing/login.php" class="flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer">
        <div class="flex items-center space-x-2 text-red-600">
            <i class='bx bx-log-out'></i>
            <span class="text-sm font-medium">Logout</span>
        </div>
    </a>
</div>
 </ul>
            </div>
        </div>

        <!----------------------------------------------------------------------------------------- Sidebar guest----------------------------------------------------------------------------->
    <?php break;
    default: ?>

        <p>Unknown role.</p>
<?php endswitch; ?>