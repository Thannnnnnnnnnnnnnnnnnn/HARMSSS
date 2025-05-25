<?php

session_start();
include("../../connection.php");

// Define the database name
$db_name = "logs1_procurement";

if (!isset($connections[$db_name])) {
    die("Database connection not found for $db_name");
}

$connection = $connections[$db_name]; // Assign the correct connection
// SQL Query for reservations
$result = "SELECT funding_id , User_ID, requested_date, status, purpose, type_of_item, estimated_budget, submitted_by, item_name FROM for_funding";
$result_sql = $connection->query($result);

// Error handling for the reservation query
if ($result_sql === false) {
    die("Error executing reservation query: " . $connection->error);
}

// Unified query to count various reservation statuses
$query = "SELECT 
        (SELECT COUNT(*) FROM for_funding WHERE status = 'Pending for funds request') AS total_request,
        (SELECT COUNT(*) FROM for_funding WHERE status = 'Funds successfully requested') AS For_clearance_Approval,
        (SELECT COUNT(*) FROM for_funding WHERE status = 'Funds requisition was cancelled') AS Denied_request,
        (SELECT COUNT(*) FROM for_funding WHERE status = 'Funds denied') AS Clearance_approve
";

$result = mysqli_query($connection, $query);

if (!$result) {
    die("Count query failed: " . mysqli_error($connection));
}

// Fetch the counts
$row = mysqli_fetch_assoc($result);
$total_request_count = $row['total_request'];
$FRA_count = $row['For_clearance_Approval'];
$DR_count = $row['Denied_request'];
$CA_count = $row['Clearance_approve'];

// Query to fetch all reservations
$query = "SELECT * FROM `purchase_request`";
$result = mysqli_query($connection, $query);

if (!$result) {
    die("Fetch query failed: " . mysqli_error($connection));
}
?>



<!DOCTYPE html>
<html lang="en">
<head>                                               
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>For funding</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.7/dist/sweetalert2.all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../CSS/sidebar.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/table2.css">
    <link rel="stylesheet" href="../CSS/button.css">
    <link rel="stylesheet" href="../CSS/main2.css">
    <link rel="stylesheet" href="../CSS/reservation_cards.css">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
</head>
<body>
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
                 <ul class="space-y-4">
                    <!-- Dashboard Item -->
                   <div class="menu-option">
                        <a href="finalTemplate.html" class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <i class="bx bx-server text-lg pr-4"></i>
                                <span class="text-sm font-medium">Dashboard</span>
                            </div>
                        
                        </a>
                    </div>
                    

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
                                <a href="purchase_request" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                    <i class="bx bx-shield text-lg"></i> <span>Purchase request</span>
                                    </a>
                                </li>
                                <li>
                                <a href="For_funding.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                        <i class="bx bx-calendar text-alt text-lg"></i> <span>For funding request</span>
                                    </a>
                                </li>
                                <li>
                                <a href="purchase_order.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                        <i class="bx bx-list-check text-lg"></i> <span>Purchase order</span>
                                    </a>
                                </li>

                                <li>
                                <a href="procurement_logs.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                        <i class="bx bx-search-alt text-lg"></i> <span>Procuremnt logs</span>
                                    </a>
                                </li>

                        </ul>
                    </div>
                </div>

                <!---- Document tracking system --->
                <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('DTS-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="bx bx-calculator text-lg pr-4"></i>
                                <span class="text-sm font-medium">Document tracking</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                    <div id="DTS-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                        <ul class="space-y-1">
                        <li>
                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                    <i class="bx bx-check-shield text-lg"></i> <span>Approvals</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                    <i class="bx bx-folder text-lg"></i> <span>Document categories</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                    <i class="bx bx-file text-lg"></i> <span>Documents</span>
                                </a>
                            </li>
                            

                        </ul>
                    </div>
                </div>
                <!---- Fleet management--->

                <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('fleet-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="bx bx-car text-lg pr-4"></i>
                                <span class="text-sm font-medium">Fleet management</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                    <div id="fleet-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                        <ul class="space-y-1">

                            <li>
                                <a href="drivers.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                    <i class="bx bx-user text-lg"></i> <span>Drivers</span>
                                </a>
                            </li>

                            <li>
                                <a href="vehicles.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                    <i class="bx bx-car text-lg"></i> <span>Vehicles</span>
                                </a>
                            </li>

                            <li>
                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                    <i class="bx bx-user-check text-lg"></i> <span>Vehicle assignments</span>
                                </a>
                            </li>
                            
                        </ul>
                    </div>
                </div>


                    <!-- VRS  -->
                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('VRS-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="bx bx-calendar text-lg pr-4"></i>
                                <span class="text-sm font-medium">Vehicle reservation</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                    <div id="VRS-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                        <ul class="space-y-1">
                           
                            <li>
                                <a href="reservation.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                    <i class="bx bx-calendar-event text-lg"></i> <span>Reservation</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>

                <!---- Vendor portal---->

                <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('vendor-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="bx bx-store text-lg pr-4"></i>
                                <span class="text-sm font-medium">Vendor portal</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                    <div id="vendor-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                        <ul class="space-y-1">
                        <li>
                            <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                <i class="bx bx-receipt text-lg"></i> <span>Vendor invoices</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                <i class="bx bx-box text-lg"></i> <span>Vendor products</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                <i class="bx bx-star text-lg"></i> <span>Vendor ratings</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                <i class="bx bx-user-check text-lg"></i> <span>Vendors</span>
                            </a>
                        </li>

                        
                        </ul>
                    </div>
                </div>

                <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('USM-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="bx bx-user-circle text-lg pr-4"></i>
                                <span class="text-sm font-medium">User management</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                    <div id="USM-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                        <ul class="space-y-1">
                        <li>
                            <a href="dept_accounts.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                <i class="bx bx-id-card text-lg"></i> <span>Department accounts</span>
                            </a>
                        </li>
                        <li>
                            <a href="dept_logs.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                <i class="bx bx-history text-lg"></i> <span>Department log history</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                <i class="bx bx-search-alt-2 text-lg"></i> <span>Department audit trail</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                <i class="bx bx-transfer text-lg"></i> <span>Department transaction</span>
                            </a>
                        </li>

                        
                        </ul>
                    </div>
                </div>


                   

                    
                </ul>
            </div>
        </div>

        <!-- Main + Navbar -->
        <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
            <!-- Navbar -->
            <nav class="h-16 w-full bg-white border-b border-[#F7E6CA] flex justify-between items-center px-6 py-4">
                <!-- Left Navigation Section -->
                <div class="left-nav flex items-center space-x-4 max-w-96 w-full">
                <!-- Toggle Menu Button-->
                    <button aria-label="Toggle menu" class="menu-btn text-[#4E3B2A] focus:outline-none hover:bg-[#F7E6CA] hover:rounded-full">
                        <i class="fa-solid fa-bars text-[#594423] text-xl w-11 py-2"></i>
                    </button>
                    
                    
                </div>

                <div>
                   <i class="fa-regular fa-user bg-[#594423] text-white px-4 py-2 rounded-lg cursor-pointer text-lg lg:hidden" aria-label="User profile"></i>
                </div>

              <!-- Right Navigation Section -->
<div class="right-nav items-center space-x-6 hidden lg:flex">
  <!-- Notification Button -->
  <button id="notifBtn" aria-label="Notifications" class="text-[#4E3B2A] focus:outline-none border-r border-[#F7E6CA] pr-6 relative">
  <i class="fa-regular fa-bell text-xl"></i>
  <span id="notifBadge" class="absolute top-0.5 right-5 w-2.5 h-2.5 bg-[#594423] rounded-full hidden"></span>
</button>

</div>
            </nav>

            <!-- Main Content -->
            <main class="px-8 py-8">
                <!-- All Content Put Here -->
                <div class="table-responsive">
<!-- Make sure this is in your <head> -->

<div class="flex justify-center w-full">
  <!-- Dashboard Container -->
  <div class="flex flex-wrap justify-center gap-6 p-4 dashboard-cards">

    <!-- Total Reservation -->
    <a href="purchase_request.php" class="dashboard-card hover:shadow-lg transition duration-300 cursor-pointer block w-72">
      <div class="flex flex-col gap-1">
        <div class="flex items-center justify-between w-full">
          <div class="flex items-center gap-2">
            <i class='bx bxs-file-doc text-xl text-blue-600'></i>
            <h2 class="dashboard-title">Pending for funds request</h2>
          </div>
          <p class="dashboard-number text-black-600 font-semibold">
            <?php echo isset($total_request_count) ? $total_request_count : '0'; ?>
          </p>
        </div>
      </div>
    </a>

    <!-- Total Reservation -->
    <a href="purchase_request.php" class="dashboard-card hover:shadow-lg transition duration-300 cursor-pointer block w-72">
      <div class="flex flex-col gap-1">
        <div class="flex items-center justify-between w-full">
          <div class="flex items-center gap-2">
            <i class='bx bxs-file-doc text-xl text-blue-600'></i>
            <h2 class="dashboard-title">No. Funds requested</h2>
          </div>
          <p class="dashboard-number text-black-600 font-semibold">
            <?php echo isset($FRA_count) ? $FRA_count : '0'; ?>
          </p>
        </div>
      </div>
    </a>

    <!-- Reserved -->
    <a href="sub-modules/reserved.php" class="dashboard-card hover:shadow-lg transition duration-300 cursor-pointer block w-72">
      <div class="flex flex-col gap-1">
        <div class="flex items-center justify-between w-full">
          <div class="flex items-center gap-2">
            <i class='bx bxs-check-circle text-xl text-purple-600'></i>
            <h2 class="dashboard-title">Funds approved</h2>
          </div>
          <p class="dashboard-number text-black-600 font-semibold">
            <?php echo isset($CA_count) ? $CA_count : '0'; ?>
          </p>
        </div>
      </div>
    </a>
    

    <!-- Pending request -->
    <a href="sub-modules/reservation_pending.php?" class="dashboard-card hover:shadow-lg transition duration-300 cursor-pointer block w-72">
      <div class="flex flex-col gap-1">
        <div class="flex items-center justify-between w-full">
          <div class="flex items-center gap-2">
            <i class='bx bx-time-five text-xl text-green-600'></i>
            <h2 class="dashboard-title">Funds denied</h2>
          </div>
          <p class="dashboard-number text-black-500 font-semibold">
            <?php echo isset($DR_count) ? $DR_count : '0'; ?>
          </p>
        </div>
      </div>
    </a>


  </div>
</div>

</div>

                        


        <table class="styled-table w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2">ID</th>
                    <th class="p-2">Request date</th>
                    <th class="p-2">Item name</th>
                    <th class="p-2">Funds status</th>
                    <th class="p-2">Funds purpose</th>
                    <th class="p-2">Estimated budget</th>
                    <th class="p-2">Operation</th>

                    
                </tr>
            </thead>
            <tbody>
            <?php if ($result_sql ->num_rows > 0):?>
                <?php while($row = $result_sql->fetch_assoc()): ?>
                    


                    <tr>
                <td class="p-2"><?php echo htmlspecialchars($row['funding_id']); ?></td>
                <td class="p-2"><?php echo htmlspecialchars($row['requested_date']); ?></td>
                <td class="p-2"><?php echo htmlspecialchars($row['item_name']); ?></td>
                <td class="p-2"><?php echo htmlspecialchars($row['status']); ?></td>
                <td class="p-2"><?php echo htmlspecialchars($row['purpose']); ?></td>
                <td class="p-2"><?php echo htmlspecialchars($row['estimated_budget']); ?></td>

                
                <td class="p-2">
                           <!-- View Button -->
               <!-- View Button -->
   <!-- View Button -->
<button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
        onclick="showViewModal(
            '<?php echo addslashes($row['funding_id']); ?>',
            '<?php echo addslashes($row['requested_date']); ?>',
            '<?php echo addslashes($row['item_name']); ?>',
            '<?php echo addslashes($row['status']); ?>',
            '<?php echo addslashes($row['purpose']); ?>',
            '<?php echo addslashes($row['estimated_budget']); ?>',
            '<?php echo addslashes($row['submitted_by']); ?>',
        )">
    <i class="bx bx-show"></i>
</button>
<b> | </b>
<!-- Approve Button -->
<button class="bg-green-600 hover:bg-green-800 text-white px-4 py-2 rounded"
        onclick="approveFunding('<?php echo urlencode($row['funding_id']); ?>')">
    <i class='bx bx-check-circle'></i>
</button>

<b> | </b>

<!-- Deny Button -->
<button class="bg-red-600 hover:bg-red-800 text-white px-4 py-2 rounded"
        onclick="denyFunding('<?php echo urlencode($row['funding_id']); ?>')">
    <i class='bx bx-x-circle'></i>
</button>



            </tr>

    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="7" class="text-center p-4">No request records found.</td>
    </tr>
<?php endif; ?>
</tbody>

            
            
        </table>
    </div>
            </main>
        </div>
    </div>


    <!-- View Modal -->
<div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md relative">
<h2 class="text-2xl font-bold mb-4 text-blue-600 flex items-center gap-2">
  <i class='bx bx-cart-alt text-3xl'></i>
  Funding Details
</h2>
   

        <div class="space-y-2">
           <p><i class="bx bx-id-card text-blue-600 mr-2"></i><strong>ID:</strong> <span id="ID" class="text-gray-700"></span></p>
<p><i class="bx bx-calendar text-blue-600 mr-2"></i><strong>Request date:</strong> <span id="RD" class="text-gray-700"></span></p>
<p><i class="bx bx-bar-chart-alt text-blue-600 mr-2"></i><strong>Status:</strong> <span id="ST" class="text-gray-700"></span></p>
<p><i class="bx bx-category text-blue-600 mr-2"></i><strong>Item type:</strong> <span id="IT" class="text-gray-700"></span></p>
<p><i class="bx bx-package text-blue-600 mr-2"></i><strong>Item name:</strong> <span id="TN" class="text-gray-700"></span></p>
<p><i class="bx bx-sort-alt-2 text-blue-600 mr-2"></i><strong>Quantity:</strong> <span id="QU" class="text-gray-700"></span></p>
<p><i class="bx bx-money text-blue-600 mr-2"></i><strong>Estimated budget:</strong> <span id="EB" class="text-gray-700"></span></p>
<p><i class="bx bx-comment-detail text-blue-600 mr-2"></i><strong>Purpose:</strong> <span id="PE" class="text-gray-700"></span></p>


        </div>

        <div class="mt-6 text-right">
            <button onclick="closeViewModal()" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Notification Modal -->
<div id="notifModal" class="fixed top-4 right-4 bg-white rounded-lg shadow-lg w-80 max-w-full p-4 opacity-0 pointer-events-none transition-opacity duration-500 z-50">
  <h2 class="text-xl font-semibold mb-4 text-[#4E3B2A]">Notifications</h2>
  <button id="closeNotif" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-xl">
    <i class="bx bx-x"></i>
  </button>
  <div id="notifList" class="max-h-64 overflow-y-auto space-y-2 text-sm text-gray-700">
    <p class="text-center text-gray-400">Loading...</p>
  </div>
</div>


<!-- Cancel Confirmation Modal -->
<div id="cancelModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-2xl shadow-lg p-6 max-w-md w-full text-center">
        <h2 class="text-xl font-semibold text-red-600 mb-4">
            <i class="bx bx-x-circle text-3xl align-middle mr-1"></i> Cancel Request?
        </h2>
        <p class="mb-6 text-gray-700">Are you sure you want to cancel this purchase request? This action cannot be undone.</p>
        <form id="cancelForm" method="POST" action="cancel_purchase.php">
            <input type="hidden" name="purchase_id" id="cancelPurchaseId" />
            <div class="flex justify-center space-x-4">
                <button type="button" onclick="closeCancelModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                    No, Go Back
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Yes, Cancel
                </button>
            </div>
        </form>
    </div>
</div>


    <script src="../JS/sidebar.js"> </script>
    <script src="../JS/purchase_modal.js"> </script>
    <script src="../JS/view_modal.js"> </script>
    <script src="../JS/edit_purchase.js"> </script>
    <script src="../JS/cancel.js"> </script>
    <script src="../JS/notification_pr.js"> </script>
    <script src="../JS/funding.js"> </script>








</body>
</html>
