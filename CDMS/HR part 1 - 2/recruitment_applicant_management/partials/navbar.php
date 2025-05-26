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

    <div class="text-center md:py-3 flex flex-col items-center">

        <h1 class="text-xl md:text-2xl font-semibold text-[#594423] text-center"><?= $heading ?></h1>

        <div class="relative inline-block">
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
        </div> -->
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