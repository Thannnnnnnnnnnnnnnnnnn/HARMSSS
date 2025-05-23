<?php
if (!isset($basePath)) {
    $basePath = '/cr1/';
}

?>
<nav class="h-16 w-full bg-white border-b border-[#F7E6CA] flex justify-between items-center px-6 py-4">
    <div class="left-nav flex items-center space-x-4 max-w-96 w-full">
        <button aria-label="Toggle menu" class="menu-btn text-[#4E3B2A] focus:outline-none hover:bg-[#F7E6CA] hover:rounded-full">
            <i class="fa-solid fa-bars text-[#594423] text-xl w-11 py-2"></i>
        </button>
    </div>

    <div class="lg:hidden">
        <?php if (isset($_SESSION['user_name'])): ?>
            <i class="fa-regular fa-user bg-[#594423] text-white px-4 py-2 rounded-lg cursor-pointer text-lg" aria-label="User profile"></i>
        <?php else: ?>
            <a href="<?php echo rtrim($basePath, '/') . '/login.php'; ?>" aria-label="Login">
                <i class="fa-solid fa-sign-in-alt bg-[#594423] text-white px-4 py-2 rounded-lg cursor-pointer text-lg"></i>
            </a>
        <?php endif; ?>
    </div>

    <div class="right-nav hidden lg:flex items-center space-x-6">
        <div class="relative">
            <button id="notificationButton" aria-label="Notifications" class="text-deepbrown focus:outline-none relative">
                <i class="fa-regular fa-bell text-xl"></i>
                <span class="absolute top-0 right-0 block w-5 h-5 bg-deepbrown text-white text-xs rounded-full flex items-center justify-center">3</span>
            </button>
            <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white border-2 border-champagnelbeige rounded-lg shadow-lg z-30 top-full">
                <div class="px-4 py-2 text-deepbrown font-semibold border-b border-champagnelbeige">Notifications</div>
                <div class="max-h-64 overflow-y-auto">
                    <div class="px-4 py-2 text-sm text-deepbrown hover:bg-champagnelbeige transition-all duration-150">
                        New order #123 received at 10:30 AM
                    </div>
                    <div class="px-4 py-2 text-sm text-deepbrown hover:bg-champagnelbeige transition-all duration-150">
                        Low stock alert for Coffee Beans
                    </div>
                    <div class="px-4 py-2 text-sm text-deepbrown hover:bg-champagnelbeige transition-all duration-150">
                        Order #124 completed at 10:15 AM
                    </div>
                </div>
                <button class="w-full px-4 py-2 text-sm text-deepbrown hover:bg-champagnelbeige transition-all duration-150 text-center" onclick="clearNotifications()">
                    Clear All
                </button>
            </div>
        </div>

        <?php if (isset($_SESSION['user_name']) && isset($_SESSION['user_role'])): ?>
            <div class="flex items-center space-x-2">
                <i class="fa-regular fa-user bg-[#594423] text-white px-3 py-2 sm:px-4 sm:py-2 rounded-lg text-base sm:text-lg" aria-label="User profile icon"></i>
                <div class="info flex flex-col py-1 sm:py-2">
                    <h1 class="text-[#4E3B2A] font-semibold font-serif text-xs sm:text-sm"><?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
                    <p class="text-[#594423] text-xs sm:text-sm sm:pl-2"><?php echo htmlspecialchars(ucfirst($_SESSION['user_role'])); ?></p>

        <?php else: ?>
             <a href="<?php echo rtrim($basePath, '/') . '/login.php'; ?>" class="text-[#4E3B2A] font-semibold hover:underline">Login</a>
        <?php endif; ?>
    </div>
</nav>
<script>
if (document.getElementById('notificationButton')) {
    document.getElementById('notificationButton').addEventListener('click', function(event) {
        event.stopPropagation(); 
        const dropdown = document.getElementById('notificationDropdown');
        if (dropdown) {
            dropdown.classList.toggle('hidden');
        }
    });
}

document.addEventListener('click', function(event) {
    const notificationButton = document.getElementById('notificationButton');
    const notificationDropdown = document.getElementById('notificationDropdown');
    if (notificationButton && notificationDropdown) {
        if (!notificationButton.contains(event.target) && !notificationDropdown.contains(event.target)) {
            notificationDropdown.classList.add('hidden');
        }
    }
});

function clearNotifications() {
    const notificationDropdown = document.getElementById('notificationDropdown');
    if (notificationDropdown) {
        const notificationList = notificationDropdown.querySelector('div.max-h-64');
        if (notificationList) {
            notificationList.innerHTML = '<div class="px-4 py-2 text-sm text-deepbrown">No new notifications</div>';
        }
        const notificationButtonSpan = document.getElementById('notificationButton')?.querySelector('span');
        if (notificationButtonSpan) {
            notificationButtonSpan.textContent = '0';
        }
    }
}
</script>