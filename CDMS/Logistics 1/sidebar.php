
<?php
$role = $_SESSION['Role'] ?? 'guest';
$permissions = include 'role_permissions.php';
$allowed_modules = $permissions[$role] ?? [];
function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die;
}

if (in_array('project_management', $allowed_modules)):
?>
<div class="menu-option">
    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('vendor-dropdown', this)">
        <div class="flex items-center space-x-2">
            <i class="bx bx-store text-lg pr-4"></i>
            <span class="text-sm font-medium">Project management</span>
        </div>
        <div class="arrow">
            <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
        </div>
    </div>

    <div id="vendor-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
        <ul class="space-y-1">
            <li>
                <a href="../PM/projects.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                    <i class="bx bx-receipt text-lg"></i> <span>Projects</span>
                </a>
            </li>
            <li>
                <a href="../PM/contractor.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                    <i class="bx bx-box text-lg"></i> <span>Project contractor</span>
                </a>
            </li>
            <li>
                <a href="../PM/supplier.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                    <i class="bx bx-star text-lg"></i> <span>Project supplier</span>
                </a>
            </li>
            <li>
                <a href="../PM/pm_logs.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                    <i class="bx bx-user-check text-lg"></i> <span>Project logs</span>
                </a>
            </li>

            <li>
                <form action="../../USM/logout.php" method="POST">
                    <button type="submit" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg w-full text-left">
                        <i class="bx bx-log-out text-lg"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </li>

        </ul>
    </div>
</div>
<?php endif; ?>


<?php
$role = $_SESSION['Role'] ?? 'guest';
$permissions = include 'role_permissions.php';
$allowed_modules = $permissions[$role] ?? [];

if (in_array('warehousing', $allowed_modules)):
?>
<div class="menu-option">
    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('fleet-dropdown', this)">
        <div class="flex items-center space-x-2">
            <i class="bx bx-car text-lg pr-4"></i>
            <span class="text-sm font-medium">Warehousing</span>
        </div>
        <div class="arrow">
            <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
        </div>
    </div>

    <div id="fleet-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
        <ul class="space-y-1">
            <li>
                <a href="../Warehousing/warehouses.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                    <i class="bx bx-user text-lg"></i> <span>Warehouses</span>
                </a>
            </li>
            <li>
                <a href="../Warehousing/warehouse_inv.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                    <i class="bx bx-car text-lg"></i> <span>Warehouse inventory</span>
                </a>
            </li>
            <li>
                <a href="../Warehousing/warehousing_logs.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                    <i class="bx bx-user-check text-lg"></i> <span>Warehousing logs</span>
                </a>
            </li>
        </ul>
    </div>
</div>
<?php endif; ?>

<?php
$role = $_SESSION['Role'];
$permissions = include 'role_permissions.php';
$allowed_modules = $permissions[$role] ?? [];

if (in_array('asset_management', $allowed_modules)):
?>

 <div class="flex min-h-screen w-full">
        <!-- Overlay -->
        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- Sidebar -->
        <div class="sidebar sidebar-expanded fixed z-50 overflow-y-auto overflow-x-hidden h-screen bg-white border-r border-[#F7E6CA] flex flex-col">
            <div class="h-16 border-b border-[#F7E6CA] flex items-center px-2 space-x-2">
                <h1 class="text-xl font-bold text-black bg-[#D9D9D9] p-2 rounded-xl">LOGO</h1>
                <h1 class="text-xl font-bold text-[#4E3B2A]">Logistic 1</h1>
                <!--Close Button-->
            </div>
            <div class="side-menu px-4 py-6">
                     <!--- Procurement --->

        <div class="menu-option">
            <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('DTS-dropdown', this)">
                <div class="flex items-center space-x-2">
                    <i class="bx bx-calculator text-lg pr-4"></i>
                    <span class="text-sm font-medium">Asset management</span>
                </div>
                <div class="arrow">
                    <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                </div>
            </div>

            <div id="DTS-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                <ul class="space-y-1">
                    <li>
                        <a href="../Asset management/add_asset.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                            <i class="bx bx-check-shield text-lg"></i> <span>Assets</span>
                        </a>
                    </li>
                    <li>
                        <a href="../Asset management/Transfer_assets.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                            <i class="bx bx-folder text-lg"></i> <span>Transfer assets</span>
                        </a>
                    </li>
                    <li>
                        <a href="../Asset management/Assets_logs.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                            <i class="bx bx-file text-lg"></i> <span>Assets logs</span>
                        </a>
                    </li>

                                        <!-- Logout Button -->
                 <!-- Logout Button -->
                        <li>
                            <form action="../../USM/logout.php" method="POST">
                                <button type="submit" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg w-full text-left">
                                    <i class="bx bx-log-out text-lg"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </li>
                </ul>         
                    </div>
                </div>



            

                    
                </ul>
            </div>
        </div>
<?php endif; ?>

<?php
$role = $_SESSION['Role'];
$permissions = include 'role_permissions.php';
$allowed_modules = $permissions[$role] ?? [];

if (in_array('procurement', $allowed_modules)):

?>
 <div class="flex min-h-screen w-full">
        <!-- Overlay -->
        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- Sidebar -->
        <div class="sidebar sidebar-expanded fixed z-50 overflow-y-auto overflow-x-hidden h-screen bg-white border-r border-[#F7E6CA] flex flex-col">
            <div class="h-16 border-b border-[#F7E6CA] flex items-center px-2 space-x-2">
                <h1 class="text-xl font-bold text-black bg-[#D9D9D9] p-2 rounded-xl">LOGO</h1>
                <h1 class="text-xl font-bold text-[#4E3B2A]">Logistic 1</h1>
                <!--Close Button-->
            </div>
            <div class="side-menu px-4 py-6">
                     <!--- Procurement --->

                     <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('audit-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="bx bx-wallet text-lg pr-4"></i>
                                <span class="text-sm font-medium">Procurement</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                    <div id="audit-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                        <ul class="space-y-1">
                        <li>
                                <a href="../Procurement/purchase_request.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                    <i class="bx bx-shield text-lg"></i> <span>Purchase request</span>
                                    </a>
                                </li>
                                <li>
                                <a href="../Procurement/For_funding.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                        <i class="bx bx-calendar text-alt text-lg"></i> <span>For funding request</span>
                                    </a>
                                </li>
                                <li>
                                <a href="../Procurement/purchase_order.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                        <i class="bx bx-list-check text-lg"></i> <span>Purchase order</span>
                                    </a>
                                </li>

                                <li>
                                <a href="../Procurement/procurement_logs.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                        <i class="bx bx-search-alt text-lg"></i> <span>Procuremnt logs</span>
                                    </a>
                                </li>
                                <li>
                                    <form action="../../USM/logout.php" method="POST">
                                        <button type="submit" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg w-full text-left">
                                            <i class="bx bx-log-out text-lg"></i>
                                            <span>Logout</span>
                                        </button>
                                    </form>
                                </li>


                        </ul>
                    </div>
                </div>



            

                    
                </ul>
            </div>
        </div>
<?php endif; ?>




<script src="JS/sidebar.js"></script>