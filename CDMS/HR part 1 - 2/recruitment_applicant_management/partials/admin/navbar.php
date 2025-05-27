<?php
$config = require '../../config.php';
// require "../../functions.php";
$db = new Database($config['database']);
// $pm = new Database($config['pm']);
// $soc = new Database($config['socreg']);
$notifs = [];
$notifications = $db->query('SELECT title, message FROM notifications WHERE `for` = :for', [
    ':for' => 'admin'
])->fetchAll();

// $pm_notifs = $pm->query('SELECT action FROM user_audit_trail')->fetchAll();
// dd($pm_notifs);
// $socreg_notifs = $soc->query('SELECT title, message FROM notifications')->fetchAll();
// dd($socreg_notifs);
// $notifs = $notifications + $socreg_notifs;
// dd($_SESSION);
?>

<div class="shadow-sm sticky top-0 z-50">
    <nav class="bg-white h-16 w-full border-b border-[#F7E6CA] flex justify-between items-center px-6 py-4">
        <div class="left-nav flex items-center space-x-4 max-w-96 w-full">
            <button
                aria-label="Toggle menu"
                class="menu-btn text-[#4E3B2A] focus:outline-none hover:bg-[#F7E6CA] hover:rounded-full">
                <i class="fa-solid fa-bars text-[#594423] text-xl w-11 py-2"></i>
            </button>

            <div class="relative w-full flex pr-2">
                <?= $heading ?>
            </div>
        </div>
        <div class="right-nav items-center lg:flex md:flex sm:flex space-x-4">
            <button aria-label="Notifications" class="text-[#4E3B2A] focus:outline-none pl-6 relative">


            </button>

            <div class="relative" id="notif-container">
                <div class="cursor-pointer" onclick="toggleNotificationDropdown()" id="notif-btn">
                    <i class="fa-regular fa-bell bg-[#594423] text-white px-4 py-2 rounded-lg text-lg" aria-label="Notifications"></i>
                    <span class="absolute top-0 right-0 block h-2 w-2 rounded-full ring-2 ring-white bg-red-500"></span>
                </div>

                <div id="notif-dropdown"
                    class="absolute right-0 mt-2 w-64 bg-white border border-gray-200 rounded shadow-lg z-50 hidden">
                    <div class="px-4 py-2 text-sm text-[#4E3B2A] font-semibold border-b">Notifications</div>
                    <div class="max-h-60 overflow-y-auto">
                        <?php foreach ($notifications as $notification) : ?>
                            <li class="px-4 py-2 text-sm text-gray-600 border-b hover:bg-[#f7e6ca]"><a><?= $notification['title'] ?></a></li>
                        <?php endforeach; ?>
                        <!-- <?php foreach ($pm_notifs as $pm_notif) : ?>
                            <li class="px-4 py-2 text-sm text-gray-600 border-b hover:bg-[#f7e6ca"><a><?= $pm_notif['action'] ?></a></li>
                        <?php endforeach; ?>
                        <?php foreach ($socreg_notifs as $soc_notif) : ?>
                            <li class="px-4 py-2 text-sm text-gray-600 border-b hover:bg-[#f7e6ca"><a><?= $soc_notif['message'] ?></a></li>
                        <?php endforeach; ?> -->
                    </div>
                </div>
            </div>

            <div class="relative">
                <button id="userDropdownButton" class="flex items-center space-x-2 focus:outline-none">
                    <i class="fa-regular fa-user bg-[#594423] text-white px-4 py-2 rounded-lg text-lg" aria-label="User profile"></i>
                    <span class="text-[#4E3B2A] md:hidden sm:hidden font-semibold font-serif text-sm">
                        <!-- <?= $_SESSION['username'] . ' ' . strtoupper($_SESSION['username']) ?> -->
                    </span>
                </button>

                <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                    <div class="py-1">
                        <div class="border-b border-gray-200">
                            <span class="block px-4 py-2 text-sm text-gray-700"><?= strtoupper($_SESSION['email']) ?></span>
                            <span class="block px-4 py-2 text-sm text-gray-500">Administrator</span>
                        </div>
                        <div class="flex items-center justify-center px-4 py-2 text-sm text-gray-800 hover:bg-gray-100">
                            <box-icon name='log-out'></box-icon>
                            <a href="../../../../USM/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Log Out
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownButton = document.getElementById('userDropdownButton');
        const dropdown = document.getElementById('userDropdown');

        dropdownButton.addEventListener('click', function() {
            dropdown.classList.toggle('hidden');
        });

        window.addEventListener('click', function(event) {
            if (!dropdownButton.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    });
</script>