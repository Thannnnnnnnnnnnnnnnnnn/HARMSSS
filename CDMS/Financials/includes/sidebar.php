<div class="sidebar-overlay" id="sidebar-overlay"></div>
<!-- Sidebar -->
<div class="sidebar sidebar-expanded fixed z-50 overflow-hidden h-screen bg-white border-r border-[#F7E6CA] flex flex-col transition-all duration-300">
    <div class="h-16 border-b border-[#F7E6CA] flex items-center">
        <?php
            $currentPath = $_SERVER['PHP_SELF'];
            $depth = substr_count($currentPath, '/');
            $basePath = str_repeat('../', $depth - 3);

            // Get the current page file name
            $currentPage = basename($currentPath);

            // Define modules and their submodules for easier management
            $modules = [
                'Disbursement' => [
                    'path' => 'Financials/Disbursement/',
                    'dropdownId' => 'disbursement-dropdown',
                    'links' => [
                        'Disbursement_Request.php' => ['name' => 'Disbursement Request', 'icon' => 'fa-solid fa-hand-holding-dollar'],
                        'Approvals.php' => ['name' => 'Approvals', 'icon' => 'fa-solid fa-check-circle'],
                        'Employees.php' => ['name' => 'Employees', 'icon' => 'fa-solid fa-users']
                    ]
                ],
                'Budget_Management' => [
                    'path' => 'Financials/Budget_Management/',
                    'dropdownId' => 'budget-dropdown',
                    'links' => [
                        'budget.php' => ['name' => 'Budget', 'icon' => 'fa-solid fa-money-bill-wave'],
                        'budgetAllocations.php' => ['name' => 'Budget Allocations', 'icon' => 'fa-solid fa-chart-bar'],
                        'budgetAdjust.php' => ['name' => 'Budget Adjustments', 'icon' => 'fa-solid fa-sliders']
                    ]
                ],
                'Collection' => [
                    'path' => 'Financials/Collection/',
                    'dropdownId' => 'collection-dropdown',
                    'links' => [
                        'collection.php' => ['name' => 'Invoices', 'icon' => 'fa-solid fa-file-invoice']
                    ]
                ],
                'General_Ledger' => [
                    'path' => 'Financials/General_Ledger/',
                    'dropdownId' => 'general-ledger-dropdown',
                    'links' => [
                        'General-Ledger-Transactions.php' => ['name' => 'Transactions', 'icon' => 'fa-solid fa-exchange-alt'],
                        'General-Ledger-Journal-Entries.php' => ['name' => 'Journal Entries', 'icon' => 'fa-solid fa-book'],
                        'General-Ledger-Account.php' => ['name' => 'Accounts', 'icon' => 'fa-solid fa-folder']
                    ]
                ],
                'Account_Payable' => [
                    'path' => 'Financials/Account_Payable/',
                    'dropdownId' => 'payable-dropdown',
                    'links' => [
                        'PayableInvoices.php' => ['name' => 'Payables', 'icon' => 'fa-solid fa-file-invoice-dollar']
                    ]
                ],
                'User_Management' => [
                    'path' => 'Financials/User_Management/',
                    'dropdownId' => 'user-manage-dropdown',
                    'links' => [
                        'Department_Acc.php' => ['name' => 'Department Accounts', 'icon' => 'fa-solid fa-user'],
                        'Department_Logs.php' => ['name' => 'Department Logs', 'icon' => 'fa-solid fa-file-alt'],
                        'Department_Audit.php' => ['name' => 'Department Audit Trail', 'icon' => 'fa-solid fa-search'],
                        'Department_Transac.php' => ['name' => 'Department Transaction', 'icon' => 'fa-solid fa-exchange-alt']
                    ]
                ]
            ];

            // Determine the active module and submodule
            $activeModule = null;
            $activeSubmodule = null;
            foreach ($modules as $moduleName => $module) {
                foreach ($module['links'] as $file => $data) {
                    if ($currentPage === $file) {
                        $activeModule = $moduleName;
                        $activeSubmodule = $file;
                        break;
                    }
                }
            }
        ?>
        <img src="<?= $basePath ?>images/Logo.png" alt="Logo" class="w-20 h-20 py-2 pl-2 sidebar-expanded:w-20 sidebar-collapsed:w-12"/>
        <img src="<?= $basePath ?>images/Logo-Name.png" alt="Logo" class="w-30 h-12 p-3 sidebar-text"/>
        <!-- Close Button -->
        <i id="close-sidebar-btn" class="fa-solid fa-x close-sidebar-btn transform translate-x-[50px] font-bold text-xl cursor-pointer"></i>
    </div>
    <div class="side-menu px-4 py-6 flex flex-col h-full">
        <ul class="space-y-4 flex-1">
            <!-- Dashboard Item -->
            <div class="menu-option">
                <a href="<?= $basePath ?>Dashboard.php" class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer <?= $currentPage === 'Dashboard.php' ? 'bg-[#F7E6CA] text-blue-600' : '' ?>">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-house text-lg pr-4"></i>
                        <span class="text-sm font-medium sidebar-text">Dashboard</span>
                    </div>
                </a>
            </div>
            <!-- Dynamic Modules -->
            <?php foreach ($modules as $moduleName => $module): ?>
                <div class="menu-option">
                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer <?= $activeModule === $moduleName ? 'bg-[#F7E6CA]' : '' ?>" onclick="toggleDropdown('<?= $module['dropdownId'] ?>', this)">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid <?= $moduleName === 'Disbursement' ? 'fa-wallet' : ($moduleName === 'Budget_Management' ? 'fa-chart-pie' : ($moduleName === 'Collection' ? 'fa-folder-open' : ($moduleName === 'General_Ledger' ? 'fa-money-bills' : ($moduleName === 'Account_Payable' ? 'fa-file-invoice-dollar' : 'fa-users')))) ?> text-lg pr-4"></i>
                            <span class="text-sm font-medium sidebar-text"><?= str_replace('_', ' ', $moduleName) ?></span>
                        </div>
                        <div class="arrow">
                            <i class="bx bx-chevron-<?= $activeModule === $moduleName ? 'down' : 'right' ?> text-[18px] font-semibold arrow-icon sidebar-expanded:flex sidebar-collapsed:hidden"></i>
                        </div>
                    </div>
                    <div id="<?= $module['dropdownId'] ?>" class="menu-drop flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2 <?= $activeModule === $moduleName ? '' : 'hidden' ?>">
                        <ul class="space-y-1">
                            <?php foreach ($module['links'] as $file => $data): ?>
                                <li>
                                    <a href="<?= $basePath . $module['path'] . $file ?>" class="text-sm text-gray-800 hover:text-blue-600 flex items-center space-x-2 <?= $currentPage === $file ? 'text-blue-600 font-semibold' : '' ?>">
                                        <i class="<?= $data['icon'] ?> text-sm"></i>
                                        <span class="sidebar-text"><?= $data['name'] ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        </ul>
        <!-- Logout Button -->
        <div class="mt-4">
            <a href="<?= $basePath ?>login.php" class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-sign-out-alt text-lg pr-4"></i>
                    <span class="text-sm font-medium sidebar-text">Logout</span>
                </div>
            </a>
        </div>
    </div>
</div>