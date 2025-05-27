<?php

session_start();
include("../../connection.php");

// Define the database name
$db_name = "logs1_asset";

if (!isset($connections[$db_name])) {
    die("Database connection not found for $db_name");
}

$role = $_SESSION['Role'] ?? 'guest';
$permissions = include '../role_permissions.php';
$allowed_modules = $permissions[$role] ?? [];

$connection = $connections[$db_name]; // Assign the correct connection
// SQL Query for reservations
$result = "SELECT asset_id  , User_ID, asset_name, asset_type, asset_quantity, asset_status, date_created, User_ID, submitted_by FROM assets";
$result_sql = $connection->query($result);

// Error handling for the reservation query
if ($result_sql === false) {
    die("Error executing reservation query: " . $connection->error);
}

// Unified query to count various reservation statuses
$query = "SELECT 
        (SELECT COUNT(*) FROM assets WHERE asset_status = 'Pending for funds request') AS total_request,
        (SELECT COUNT(*) FROM assets WHERE asset_status = 'Funds successfully requested') AS For_clearance_Approval,
        (SELECT COUNT(*) FROM assets WHERE asset_status = 'Funds requisition was cancelled') AS Denied_request,
        (SELECT COUNT(*) FROM assets WHERE asset_status = 'Funds denied') AS Clearance_approve
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
$query = "SELECT * FROM `assets`";
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
      <?php include '../sidebar.php'; ?>


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
            <h2 class="dashboard-title">Total assets</h2>
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
            <h2 class="dashboard-title">Tangible assets</h2>
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
            <h2 class="dashboard-title">Non - Tangible assets</h2>
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
            <h2 class="dashboard-title">Digital assets</h2>
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

                        
<!-- Trigger Button -->
<button onclick="toggleModal(true)" 
  class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
  <i class='bx bx-plus'></i> Add assets
</button>


  <br><br>

        <table class="styled-table w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2">ID</th>
                    <th class="p-2">Asset name</th>
                    <th class="p-2">Asset type</th>
                    <th class="p-2">Asset quantity</th>
                    <th class="p-2">Asset status</th>
                    <th class="p-2">Date added</th>
                    <th class="p-2">Operation</th>

                    
                </tr>
            </thead>
            <tbody>
            <?php if ($result_sql ->num_rows > 0):?>
                <?php while($row = $result_sql->fetch_assoc()): ?>
                    


                    <tr>
                <td class="p-2"><?php echo htmlspecialchars($row['asset_id']); ?></td>
                <td class="p-2"><?php echo htmlspecialchars($row['asset_name']); ?></td>
                <td class="p-2"><?php echo htmlspecialchars($row['asset_type']); ?></td>
                <td class="p-2"><?php echo htmlspecialchars($row['asset_quantity']); ?></td>
                <td class="p-2"><?php echo htmlspecialchars($row['asset_status']); ?></td>
                <td class="p-2"><?php echo htmlspecialchars($row['date_created']); ?></td>

                
                <td class="p-2">
                           <!-- View Button -->
               <!-- View Button -->
   <!-- View Button -->
<button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
        onclick="showViewModal(
            '<?php echo addslashes($row['asset_id']); ?>',
            '<?php echo addslashes($row['asset_name']); ?>',
            '<?php echo addslashes($row['asset_type']); ?>',
            '<?php echo addslashes($row['asset_quantity']); ?>',
            '<?php echo addslashes($row['asset_status']); ?>',
            '<?php echo addslashes($row['date_created']); ?>',
            '<?php echo addslashes($row['submitted_by']); ?>',
        )">
    <i class="bx bx-show"></i>
</button>
<b> | </b>
<!-- Buttons -->
<button onclick="openModal('approve', '<?php echo $row['asset_id']; ?>')" 
        class="bg-green-600 hover:bg-green-800 text-white px-4 py-2 rounded">
    <i class='bx bx-check-circle'></i>
</button>

<b> | </b>

<button onclick="openModal('deny', '<?php echo $row['asset_id']; ?>')" 
        class="bg-red-600 hover:bg-red-800 text-white px-4 py-2 rounded">
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


<!-- Modal Background -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
  <!-- Modal Box -->
  <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-md">
    <h2 class="text-lg font-semibold mb-4" id="modalTitle">Confirm Action</h2>
    <p class="text-gray-700 mb-6" id="modalMessage">Are you sure you want to proceed?</p>

    <div class="flex justify-end gap-4">
      <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md">Cancel</button>
      <button id="confirmActionBtn" class="px-4 py-2 bg-blue-600 hover:bg-blue-800 text-white rounded-md">Yes, Confirm</button>
    </div>
  </div>
</div>
<!-- Modal -->
<div id="assetModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">

    <!-- Close Button -->
    <button onclick="toggleModal(false)" class="absolute top-3 right-3 text-gray-500 hover:text-black">
      <i class='bx bx-x text-2xl'></i>
    </button>

    <!-- Modal Header -->
    <h2 class="text-xl font-semibold mb-4 text-gray-800">Add New Asset</h2>

    <!-- Form -->
    <form action="add_asset_form.php" method="POST" class="space-y-4">
      <!-- Asset Name -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Asset Name</label>
        <input type="text" name="asset_name" required
               class="mt-1 w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>

        <!-- Asset Type -->
        <div>
    <label class="block text-sm font-medium text-gray-700">Asset Type</label>
    <select name="asset_type" required
            class="mt-1 w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="" disabled selected>Select asset type</option>
        <option value="Tangible assets">Tangible assets</option>
        <option value="Non-tangible assets">Non-tangible assets</option>
        <option value="Cash assets">Cash assets</option>
        <option value="Digital assets">Digital assets</option>
    </select>
    </div>


      <!-- Asset Quantity -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Asset Quantity</label>
        <input type="number" name="asset_quantity" min="1" required
               class="mt-1 w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>

      <!-- Date Added -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Date Added</label>
        <input type="date" name="date_created" required
               class="mt-1 w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>

      <!-- Actions -->
      <div class="flex justify-end pt-2">
        <button type="button" onclick="toggleModal(false)"
                class="mr-2 px-4 py-2 text-sm border border-gray-300 text-gray-700 hover:bg-gray-100 rounded-md">
          Cancel
        </button>
        <button type="submit"
                class="px-4 py-2 text-sm bg-blue-600 text-white hover:bg-blue-700 rounded-md">
          Add Asset
        </button>
      </div>
    </form>

  </div>
</div>


<!-- Modal -->
<div id="actionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded shadow-lg text-center">
        <h2 class="text-xl font-bold mb-4" id="modalTitle">Confirm Action</h2>
        <p id="modalMessage">Are you sure?</p>
        <form method="POST" action="asset_aquasition.php">
            <input type="hidden" name="asset_id" id="modalAssetId">
            <input type="hidden" name="action" id="modalAction">
            <div class="mt-4 flex justify-center space-x-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white px-4 py-2 rounded">Yes</button>
                <button type="button" onclick="closeModal()" class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded">Cancel</button>
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
    <script src="../JS/add_assets.js"> </script>
    <script src="../JS/add_asset_approve.js"> </script>










</body>
</html>
