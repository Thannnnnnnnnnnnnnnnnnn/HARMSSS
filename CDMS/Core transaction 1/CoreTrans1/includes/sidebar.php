<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$basePath = '../../Core transaction 1/CoreTrans1/';
?>
<div class="sidebar-overlay" id="sidebar-overlay"></div>
<div class="sidebar sidebar-expanded fixed z-50 overflow-hidden h-screen bg-white border-r border-[#F7E6CA] flex flex-col">
    <div class="h-16 border-b border-[#F7E6CA] flex items-center">
        <img src="<?= $basePath ?>image/Logo.png" alt="Logo" class="w-20 h-20 py-2 pl-2" />
        <img src="<?= $basePath ?>image/Logo-Name.png" alt="Logo" class="w-30 h-12 p-3" />
        <i id="close-sidebar-btn" class="fa-solid fa-x close-sidebar-btn translate-x-[50px] font-bold text-xl cursor-pointer"></i>
    </div>
    <div class="side-menu px-4 py-6">
        <ul class="space-y-4">
            <li>
                <a href="<?= rtrim($basePath, '/') ?>/Dashboard.php" class="flex items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 <?= $currentPage == 'Dashboard.php' ? 'bg-[#F7E6CA] font-bold text-[#594423]' : '' ?>">
                    <i class="fa-solid fa-house text-lg"></i>
                    <span class="text-sm font-medium pl-6 ml-2">Dashboard</span>
                </a>
            </li>
            <li class="menu-option">
                <div class="menu-name flex justify-between items-center hover:bg-[#F7E6CA] px-4 py-3 rounded-lg cursor-pointer" onclick="toggleDropdown('pos-dropdown', this)">
                    <div class="flex items-center space-x-2">
                        <i class="bx bx-store text-lg pr-4"></i>
                        <span class="text-sm font-medium"><?= $currentPage == 'pos.php' || $currentPage == 'orders.php' || $currentPage == 'payments.php' ? '<b>Ordering Management</b>' : 'Ordering Management' ?></span>
                    </div>
                    <i class="bx bx-chevron-right text-xl transition-transform duration-300 <?= $currentPage == 'pos.php' || $currentPage == 'orders.php' || $currentPage == 'payments.php' ? 'rotate-90' : '' ?>"></i>
                </div>
                <div id="pos-dropdown" class="menu-drop flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2 <?= $currentPage == 'pos.php' || $currentPage == 'orders.php' || $currentPage == 'payments.php' ? '' : 'hidden' ?>">
                    <a href="<?= rtrim($basePath, '/') ?>/Module/OrderMgt/pos.php" class="flex items-center space-x-2 text-sm text-deepbrown hover:underline <?= $currentPage == 'pos.php' ? 'font-bold text-[#594423] bg-white rounded px-2 py-1' : '' ?>">
                        <i class="bx bx-cart text-sm pr-2"></i>
                        <span>POS</span>
                    </a>
                    <a href="<?= rtrim($basePath, '/') ?>/Module/OrderMgt/orders.php" class="flex items-center space-x-2 text-sm text-deepbrown hover:underline <?= $currentPage == 'orders.php' ? 'font-bold text-[#594423] bg-white rounded px-2 py-1' : '' ?>">
                        <i class="bx bx-list-ul text-sm pr-2"></i>
                        <span>Orders Record</span>
                    </a>
                    <a href="<?= rtrim($basePath, '/') ?>/Module/OrderMgt/payments.php" class="flex items-center space-x-2 text-sm text-deepbrown hover:underline <?= $currentPage == 'payments.php' ? 'font-bold text-[#594423] bg-white rounded px-2 py-1' : '' ?>">
                        <i class="bx bx-money text-sm pr-2"></i>
                        <span>Payments Transaction</span>
                    </a>
                </div>
            </li>
            <li class="menu-option">
                <div class="menu-name flex justify-between items-center hover:bg-[#F7E6CA] px-4 py-3 rounded-lg cursor-pointer" onclick="toggleDropdown('kitchen-dropdown', this)">
                    <div class="flex items-center space-x-2">
                        <i class="bx bx-bowl-rice text-lg pr-4"></i>
                        <span class="text-sm font-medium"><?= $currentPage == 'menu.php' ? '<b>Kitchen and Bar Module</b>' : 'Kitchen and Bar Module' ?></span>
                    </div>
                    <i class="bx bx-chevron-right text-xl transition-transform duration-300 <?= $currentPage == 'menu.php' ? 'rotate-90' : '' ?>"></i>
                </div>
                <div id="kitchen-dropdown" class="menu-drop flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2 <?= $currentPage == 'menu.php' ? '' : 'hidden' ?>">
                    <a href="<?= rtrim($basePath, '/') ?>/Module/Kitchen_Bar_module/menu.php" class="flex items-center space-x-2 text-sm text-deepbrown hover:underline <?= $currentPage == 'menu.php' ? 'font-bold text-[#594423] bg-white rounded px-2 py-1' : '' ?>">
                        <i class="bx bx-food-menu text-sm pr-2"></i>
                        <span>Menu Item</span>
                    </a>
                </div>
            </li>
            <li class="menu-option">
                <div class="menu-name flex justify-between items-center hover:bg-[#F7E6CA] px-4 py-3 rounded-lg cursor-pointer" onclick="toggleDropdown('inventory-dropdown', this)">
                    <div class="flex items-center space-x-2">
                        <i class="bx bx-box text-lg pr-4"></i>
                        <span class="text-sm font-medium"><?= $currentPage == 'index.php' || $currentPage == 'reorder_levels.php' || $currentPage == 'stock_movements.php' ? '<b>Inventory Management</b>' : 'Inventory Management' ?></span>
                    </div>
                    <i class="bx bx-chevron-right text-xl transition-transform duration-300 <?= $currentPage == 'index.php' || $currentPage == 'reorder_levels.php' || $currentPage == 'stock_movements.php' ? 'rotate-90' : '' ?>"></i>
                </div>
                <div id="inventory-dropdown" class="menu-drop flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2 <?= $currentPage == 'index.php' || $currentPage == 'reorder_levels.php' || $currentPage == 'stock_movements.php' ? '' : 'hidden' ?>">
                    <a href="<?= rtrim($basePath, '/') ?>/Module/InvMgt/index.php" class="flex items-center space-x-2 text-sm text-deepbrown hover:underline <?= $currentPage == 'index.php' ? 'font-bold text-[#594423] bg-white rounded px-2 py-1' : '' ?>">
                        <i class="bx bx-package text-sm pr-2"></i>
                        <span>Inventory</span>
                    </a>
                </div>
            </li>
            <li class="menu-option">
                <div class="menu-name flex justify-between items-center hover:bg-[#F7E6CA] px-4 py-3 rounded-lg cursor-pointer" onclick="toggleDropdown('user-manage-dropdown', this)">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-users text-lg pr-4"></i>
                        <span class="text-sm font-medium"><?= in_array($currentPage, ['Department_Acc.php', 'Department_Logs.php', 'Department_Audit.php', 'Department_Transac.php']) ? '<b>User Management</b>' : 'User Management' ?></span>
                    </div>
                    <i class="bx bx-chevron-right text-xl transition-transform duration-300 <?= in_array($currentPage, ['Department_Acc.php', 'Department_Logs.php', 'Department_Audit.php', 'Department_Transac.php']) ? 'rotate-90' : '' ?>"></i>
                </div>
                <div id="user-manage-dropdown" class="menu-drop flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2 <?= in_array($currentPage, ['Department_Acc.php', 'Department_Logs.php', 'Department_Audit.php', 'Department_Transac.php']) ? '' : 'hidden' ?>">
                    <?php
                    $userManagementLinks = [
                        'Department_Acc.php' => ['icon' => 'fa-solid fa-user-shield', 'name' => 'Department Accounts', 'path_segment' => 'Module/User_Management/'],
                        'Department_Logs.php' => ['icon' => 'fa-solid fa-clipboard-list', 'name' => 'Department Logs', 'path_segment' => 'Module/User_Management/'],
                        'Department_Audit.php' => ['icon' => 'fa-solid fa-magnifying-glass-chart', 'name' => 'Department Audit Trail', 'path_segment' => 'Module/User_Management/'],
                        'Department_Transac.php' => ['icon' => 'fa-solid fa-cash-register', 'name' => 'Department Transaction', 'path_segment' => 'Module/User_Management/']
                    ];
                    ?>
                    <ul class="space-y-2">
                        <?php
                        foreach ($userManagementLinks as $file => $details) {
                            $activeClass = ($currentPage == $file) ? 'font-bold text-[#594423] bg-white rounded px-2 py-1' : '';
                            $linkPath = rtrim($basePath, '/') . '/' . $details['path_segment'] . $file;
                            echo '<li><a href="' . $linkPath . '" class="flex items-center space-x-2 text-sm text-deepbrown hover:underline ' . $activeClass . '"><i class="' . $details['icon'] . ' text-sm pr-2"></i><span>' . $details['name'] . '</span></a></li>';
                        }
                        ?>
                    </ul>
                </div>
            </li>
            <li>
                <a href="<?php echo rtrim($basePath, '/') . '/logout.php'; ?>" class="flex items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg text-red-600 transition duration-300">
                    <i class="fa-solid fa-right-from-bracket text-lg"></i>
                    <span class="text-sm font-medium pl-6 ml-2">Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>

