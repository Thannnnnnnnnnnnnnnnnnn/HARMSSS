<?php
$notifications = [];
$notifications = $db->query('SELECT
    notifications.*
    FROM notifications
    INNER JOIN applicants ON applicants.applicant_id = notifications.applicant_id
    AND notifications.for = :for
    ORDER BY notifications.created_at DESC', [
    ':for' => 'applicant',
])->fetchAll();
?>

<header class="bg-[#FFF6E8] bg-opacity-10 backdrop-filter backdrop-blur-lg shadow-md sticky top-0 z-40">
    <div class="flex flex-col sm:flex-row justify-between items-center border-b px-3 py-4 border-[#594423]">
        <div class="flex items-center justify-between w-full sm:w-auto mb-2 sm:mb-0">
            <img src="../img/Logo-Name.png" alt="Logo" class="h-8 md:h-10 mr-3">
            <button id="menu-toggle" class="sm:hidden text-[#594423] focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>

        <nav class="w-full sm:w-auto">
            <ul id="menu" class="flex flex-col sm:flex-row space-x-0 sm:space-x-4 items-center mt-2 sm:mt-0 sm:items-center sm:justify-end hidden sm:flex">
                <li><a href="home.php" class="text-[#594423] font-semibold block py-2 px-2 sm:py-0 sm:px-0 hover:text-[#3D2F1F] transition-colors">Home</a></li>
                <li><a href="application.php" class="text-[#594423] font-semibold block py-2 px-2 sm:py-0 sm:px-0 hover:text-[#3D2F1F] transition-colors">My Applications</a></li>
                <li>
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="px-3 py-2 rounded-lg border border-[#594423] hover:bg-[#594423] hover:text-white transition"><i class="fa-solid fa-user"></i></div>
                        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm">
                            <li><a href="logout.php" class="text-[#594423] font-semibold"><i class="fa-solid fa-right-to-bracket"></i>logOut</a></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </nav>
    </div>

    <div class="flex flex-col md:flex-row justify-between items-center px-2 py-2 md:py-0">

        <h1 class="text-xl md:text-2xl font-semibold text-[#594423] mb-2 md:mb-0 text-center"><?= $heading ?></h1>

        <div class="relative inline-block">
            <button id="dropdownDefaultButton" data-dropdown-toggle="dropdown" class="text-white bg-[#594423] hover:bg-[#F7E6CA] border hover:border-[#594423] hover:text-[#594423] focus:ring-4 focus:outline-none focus:ring-[#594423] font-medium rounded-lg text-sm px-3 py-2 md:px-5 md:py-2.5 text-center inline-flex items-center relative" type="button" data-turbo="false">
                <i class="fa-solid fa-bell"></i>
                <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                </svg>
                <?php if (count(array_filter($notifications, function ($n) {
                    return $n['status'] == 'unread';
                })) > 0): ?>
                    <span class="absolute top-2 right-2 transform translate-x-1/2 -translate-y-1/2 bg-red-500 w-3 h-3 rounded-full border-2 border-white"></span>
                <?php endif; ?>
            </button>

            <div id="dropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-64 max-h-64 overflow-y-auto dark:bg-gray-700 absolute right-0 mt-2">
                <div class="text-white font-normal py-1 px-2">
                    NOTIFICATIONS
                </div>
                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
                    <?php if (count($notifications) == 0) :  ?>
                        <div>
                            <p class="text-md ps-4">Empty notifications</p>
                        </div>
                    <?php else : ?>
                        <?php foreach ($notifications as $notification) : ?>
                            <li>
                                <a href="/notifications?id=<?= $notification['id'] ?>" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white <?= $notification['status'] == 'unread' ? 'font-semibold' : '' ?>"><?= $notification['title'] ?></a>
                            </li>
                        <?php endforeach ?>
                    <?php endif ?>
                </ul>
            </div>
        </div>
    </div>
</header>

<script>
    const menuToggle = document.getElementById('menu-toggle');
    const menu = document.getElementById('menu');

    menuToggle.addEventListener('click', () => {
        menu.classList.toggle('hidden');
    });

    // JavaScript to handle dropdown
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownButton = document.getElementById('dropdownDefaultButton');
        const dropdown = document.getElementById('dropdown');

        dropdownButton.addEventListener('click', function() {
            dropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        window.addEventListener('click', function(event) {
            if (!dropdownButton.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    });
</script>