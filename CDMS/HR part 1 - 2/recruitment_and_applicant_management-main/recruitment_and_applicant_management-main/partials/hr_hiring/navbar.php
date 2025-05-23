<?php
$notifications = [];
$notifications = $db->query('SELECT 
    notifications.*,
    applicants.user_id
    FROM notifications 
    INNER JOIN applicants ON applicants.applicant_id = notifications.applicant_id
    WHERE notifications.for = :for
    ORDER BY notifications.created_at DESC', [
    ':for' => 'hiring manager',
])->fetchAll();
?>

<header class="bg-[#FFF6E8] bg-opacity-10 backdrop-filter backdrop-blur-lg pt-4 px-4 shadow-md sticky top-0 z-30">
    <div class="flex justify-between items-center border-b border-[#594423] pb-2">
        <div class="flex items-center">
            <img src="../img/Logo-Name.png" alt="Logo" class="h-10 mr-3">
        </div>
        <nav>
            <ul class="flex space-x-4 items-center">
                <li><a href="/hr_hiring/" class="text-[#594423] font-semibold">Home</a></li>
                <li><a href="/hr_hiring/applicants" class="text-[#594423] font-semibold">Applicants</a></li>
                <li>
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="px-3 py-2 rounded-lg border border-[#594423] hover:bg-[#594423] hover:text-white transition"><i class="fa-solid fa-user"></i></div>
                        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm">
                            <li class="border border-[#594423] rounded-lg mb-3">
                                <h3><?= $_SESSION['username'] ?></h3>
                            </li>
                            <li><a href="/hr_hiring/profile" class="text-[#594423] font-semibold"><i class="fa-solid fa-gear"></i>User Settings</a></li>
                            <li><a href="/logout" class="text-[#594423] font-semibold"><i class="fa-solid fa-right-to-bracket"></i>logOut</a></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
    <!-- Page Title -->
    <div class="flex justify-between items-center">
        <div class="text-lg font-semibold text-[#594423] flex items-center gap-2">
            <i class="fa-solid fa-user"></i>
            <p><?= $_SESSION['role'] . ' ' . strtoupper($_SESSION['username']) ?> </p>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-[#594423]"><?= $heading ?></h1>
        </div>
        <div>
            <div class="px-3 py-3 relative inline-block">
                <button id="dropdownDefaultButton" data-dropdown-toggle="dropdown" class="text-white bg-[#594423] hover:bg-[#F7E6CA] border hover:border-[#594423] hover:text-[#594423] focus:ring-4 focus:outline-none focus:ring-[#594423] font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-[#594423] relative" type="button">
                    <i class="fa-solid fa-bell"></i>
                    <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                    </svg>
                    <!-- Red dot notification indicator -->
                    <?php foreach ($notifications as $notification) : ?>
                        <span class="<?= $notification['status'] == 'unread' ?: 'hidden' ?> absolute top-2 right-2 transform translate-x-1/2 -translate-y-1/2 bg-red-500 w-3 h-3 rounded-full border-2 border-white"></span>
                    <?php endforeach ?>
                </button>

                <!-- Dropdown menu -->
                <div id="dropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-64 max-h-64 overflow-y-auto  dark:bg-gray-700 ">

                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
                        <?php if (count($notifications) == 0) :  ?>
                            <div>
                                <p class="text-md ps-4">Empty notifications</p>
                            </div>
                        <?php else : ?>
                            <div class="py-2 text-center font-normal">
                                <h2>Notifications:</h2>
                            </div>
                            <?php foreach ($notifications as $notification) : ?>
                                <li>
                                    <a href="/hr_hiring/notifications?id=<?= $notification['id'] ?>" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white <?= $notification['status'] == 'unread' ? 'text-white font-semibold' : '' ?>"><?= $notification['title'] ?></a>
                                </li>
                            <?php endforeach ?>
                        <?php endif ?>
                    </ul>
                </div>
            </div>
        </div>

    </div>
    </div>
</header>