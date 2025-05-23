<?php 
   include("connection.php");
   $stmt = $conn->prepare("SELECT * FROM mileagelogs");
   $stmt->execute();
   $mileagelogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>                                               
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MILEAGE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
    <style>
        /* Same styles as in the original code */
        .sidebar-collapsed { width: 85px; }
        .sidebar-expanded { width: 320px; }
        .sidebar-collapsed .menu-name span, .sidebar-collapsed .menu-name .arrow { display: none; }
        .sidebar-collapsed .menu-name i { margin-right: 0; }
        .sidebar-collapsed .menu-drop { display: none; }
        .sidebar-overlay { background-color: rgba(0, 0, 0, 0.5); position: fixed; inset: 0; z-index: 40; display: none; }
        .sidebar-overlay.active { display: block; }
        .close-sidebar-btn{ display: none; }
        @media (max-width: 968px) {
            .sidebar { position: fixed; left: -100%; transition: left 0.3s ease-in-out; }
            .sidebar.mobile-active { left: 0; }
            .main { margin-left: 0 !important; }
            .close-sidebar-btn{ display: block; }
        }
        .menu-name { position: relative; overflow: hidden; }
        .menu-name::after { content: ''; position: absolute; left: 0; bottom: 0; height: 2px; width: 0; background-color: #4E3B2A; transition: width 0.3s ease; }
        .menu-name:hover::after { width: 100%; }
    </style>
</head>
<body>
    <div class="flex min-h-screen w-full">
        <!-- Overlay -->
        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- Sidebar -->
        <div class="sidebar sidebar-expanded fixed z-50 overflow-hidden h-screen bg-white border-r border-[#F7E6CA] flex flex-col">
            <div class="h-16 border-b border-[#F7E6CA] flex items-center px-2 space-x-2">
                <img src="image/Logo.png" alt="Logo" class="h-8 w-8 object-cover rounded-full">
                <img src="image/Logo-Name.png" alt="Logo Name" class="h-8 object-contain">
            </div>
            <div class="h-16 border-b border-[#F7E6CA] flex items-center justify-center px-2 space-x-2">
                <h1 class="text-xl font-bold text-[#4E3B2A]">LOGISTIC 2</h1>
                <i id="close-sidebar-btn" class="fa-solid fa-x close-sidebar-btn font-bold text-xl"></i>
            </div>

            <div class="side-menu px-4 py-6">
                <!-- Sidebar Menu Items go here -->
                <div class="menu-option">
                <a href="finalTemplate.html" class="menu-name flex justify-between items-center space-x-3 text-[#4E3B2A] hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-tachometer-alt text-lg pr-4"></i>
                            <span class="text-sm font-medium">Dashboard</span>
                        </div>
                    
                    </a>
                </div>

                <!-- Disbursement Item  -->
                <div class="menu-option">
                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('disbursement-dropdown', this)">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-clipboard-check text-lg pr-4"></i>
                            <span class="text-sm font-medium">Audit</span>
                        </div>
                        <div class="arrow">
                            <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                        </div>
                    </div>
                    <div id="disbursement-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                        <ul class="space-y-1">
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Disbursement Request</a></li>
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Approvals</a></li>
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Payment Methods</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Budget Management Item  -->
                <div class="menu-option">
                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('budget-dropdown', this)">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-file-contract text-lg pr-4"></i>
                            <span class="text-sm font-medium">Document Tracking</span>
                        </div>
                        <div class="arrow">
                            <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                        </div>
                    </div>
                    <div id="budget-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                        <ul class="space-y-1">
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Budget</a></li>
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Budget Allocations</a></li>
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Budget Adjustments</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Collection Item  -->
                <div class="menu-option">
                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('collection-dropdown', this)">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-truck-moving text-lg pr-4"></i>
                            <a href="dashboard.html" class="text-sm font-medium text-[#4E3B2A] hover:text-[#4E3B2A]">Fleet Management</a>

                        </div>
                        <div class="arrow">
                            <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                        </div>
                    </div>
                    <div id="collection-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                        <ul class="space-y-1">
                        <li class="flex items-center">
                                <i class="fas fa-truck text-lg pr-4"></i>
                                <a href="fleet.php" class="text-sm font-medium hover:text-blue-600">Fleet</a>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-gas-pump text-lg pr-4"></i>
                                <a href="fuel.php" class="text-sm font-medium hover:text-blue-600">Fuel Logs</a>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-chart-line text-lg pr-4"></i>
                                <a href="mileage.php" class="text-sm font-medium  hover:text-blue-600">Mileage Logs</a>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-id-badge text-lg pr-4"></i>
                                <a href="vehicleassignment.php" class="text-sm font-medium  hover:text-blue-600">Vehicle Assignments</a>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-id-badge text-lg pr-4"></i>
                                <a href="maintenance.php" class="text-sm font-medium  hover:text-blue-600">Maintenance Logs</a>
                            </li>
                        </ul>
                    </div>
                    
                </div>


                <!-- General Ledger Item  -->
                <div class="menu-option">
                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('general-ledger-dropdown', this)">
                        <div class="flex items-center space-x-2">
                            <i class="fa-regular fa-calendar text-lg pr-4"></i>
                            <span class="text-sm font-medium ">Vehicle Reservation</span>
                        </div>
                        <div class="arrow">
                            <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                        </div>
                    </div>
                    <div id="general-ledger-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                        <ul class="space-y-1">
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Journal Entries</a></li>
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Account</a></li>
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Transactions</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Account Payable/Receiver Item  -->
                <div class="menu-option">
                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('account-dropdown', this)">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-store text-lg pr-4"></i>
                            <span class="text-sm font-medium">Vendor Portal</span>
                        </div>
                        <div class="arrow">
                            <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                        </div>
                    </div>
                    <div id="account-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                        <ul class="space-y-1">
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Payable Invoices</a></li>
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Vendor Payments</a></li>
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Payment Schedules</a></li>
                        </ul>
                    </div>
                </div>
            </ul>
        </div>
        </div>

        <!-- Main + Navbar -->
        <div class="main w-full bg-[#FFF6E8] md:ml-[320px] overflow-y-auto"> <!-- Added overflow-y-auto -->
            <!-- Navbar -->
            <nav class="h-16 w-full bg-white border-b border-[#F7E6CA] flex justify-between items-center px-6 py-4">
                <div class="left-nav flex items-center space-x-4 max-w-96 w-full">
                    <button aria-label="Toggle menu" class="menu-btn text-[#4E3B2A] focus:outline-none hover:bg-[#F7E6CA] hover:rounded-full">
                        <i class="fa-solid fa-bars text-[#594423] text-xl w-11 py-2"></i>
                    </button>
                    
                    <div class="relative w-full flex pr-2">
                        <input type="text" class="bg-[#FFF6E8] h-10 rounded-lg grow w-full pl-10 pr-4 focus:ring-2 focus:ring-[#F7E6CA] focus:outline-none" placeholder="Search something..." aria-label="Search input"/>
                        <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-[#4E3B2A]"></i>
                    </div>
                </div>
                <div class="right-nav  items-center space-x-6 hidden lg:flex">
                    <button aria-label="Notifications" class="text-[#4E3B2A] focus:outline-none border-r border-[#F7E6CA] pr-6 relative">
                        <i class="fa-regular fa-bell text-xl"></i>
                        <span class="absolute top-0.5 right-5 block w-2.5 h-2.5 bg-[#594423] rounded-full"></span>
                    </button>

                    <div class="flex items-center space-x-2">
                        <i class="fa-regular fa-user bg-[#594423] text-white px-4 py-2 rounded-lg text-lg" aria-label="User profile"></i>
                        <div class="info flex flex-col py-2">
                            <h1 class="text-[#4E3B2A] font-semibold font-serif text-sm">Madelyn Cline</h1>
                            <p class="text-[#594423] text-sm pl-2">Administrator</p>
                        </div>
                    </div>
                </div>
            </nav>

<!-- Main Content -->
<?php if (!empty($mileagelogs)) : ?>
<h2 class="pt-6 pl-6 text-xl font-medium mb-4 text-gray-800">MileAge Log</h2>
<table class="styled-table">
    <thead>
        <tr>
            <th>MileageLogID</th>
            <th>VehicleID</th>
            <th>Driver</th>
            <th>Start Location</th>
            <th>End Location</th>
            <th>Distance Traveled</th>
            <th>License Plate</th>
            <th>Model</th>
            <th>Operations</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($mileagelogs as $row) : ?>
            <tr>
                <td><?php echo htmlspecialchars($row['MileageLogID']); ?></td>
                <td><?php echo htmlspecialchars($row['VehicleID']); ?></td>
                <td><?php echo htmlspecialchars($row['Driver']); ?></td>
                <td><?php echo htmlspecialchars($row['StartLocation']); ?></td>
                <td><?php echo htmlspecialchars($row['EndLocation']); ?></td>
                <td><?php echo htmlspecialchars($row['DistanceTraveled']); ?> km</td>
                <td><?php echo htmlspecialchars($row['License_plate']); ?></td>
                <td><?php echo htmlspecialchars($row['Model']); ?></td>
                <td>
                <div class="d-flex gap-2 align-items-center">
<!-- Add Mileage Button  -->
<a href="#" id="addMileageBtn"
   class="bg-green-600 text-white w-[70px] p-2 rounded-lg inline-block text-center"
   data-bs-toggle="modal"
   data-bs-target="#addMileageModal">
   <i class="fas fa-plus"></i> ADD
</a>

<!-- View Button -->
<a href="#" class="view-btn bg-blue-600 text-white w-[70px] p-2 rounded-lg inline-block text-center"
   data-bs-toggle="modal"
   data-bs-target="#mileageviewModal"
   data-mileagelogid="<?php echo htmlspecialchars($row['MileageLogID']); ?>"
   data-vehicleid="<?php echo htmlspecialchars($row['VehicleID']); ?>"
   data-licenseplate="<?php echo htmlspecialchars($row['License_plate']); ?>"
   data-model="<?php echo htmlspecialchars($row['Model']); ?>"
   data-driver="<?php echo htmlspecialchars($row['Driver']); ?>"
   data-startlocation="<?php echo htmlspecialchars($row['StartLocation']); ?>"
   data-endlocation="<?php echo htmlspecialchars($row['EndLocation']); ?>"
   data-distancetraveled="<?php echo htmlspecialchars($row['DistanceTraveled']); ?>">
   <i class="bx bx-show"></i>
</a>

<!-- Edit Button -->
<a href="#" class="edit-btn bg-green-500 text-white w-[70px] p-2 rounded-lg inline-block text-center"
   data-bs-toggle="modal"
   data-bs-target="#mileageModal"
   data-mileagelogid="<?php echo htmlspecialchars($row['MileageLogID']); ?>"
   data-vehicleid="<?php echo htmlspecialchars($row['VehicleID']); ?>"
   data-licenseplate="<?php echo htmlspecialchars($row['License_plate']); ?>"
   data-model="<?php echo htmlspecialchars($row['Model']); ?>"
   data-driver="<?php echo htmlspecialchars($row['Driver']); ?>"
   data-startlocation="<?php echo htmlspecialchars($row['StartLocation']); ?>"
   data-endlocation="<?php echo htmlspecialchars($row['EndLocation']); ?>"
   data-distancetraveled="<?php echo htmlspecialchars($row['DistanceTraveled']); ?>">
   <i class="bx bx-edit"></i>
</a>

<!-- Delete Button -->
<a href="#" class="delete-btn bg-red-600 text-white w-[70px] p-2 rounded-lg inline-block text-center"
   data-bs-toggle="modal"
   data-bs-target="#mileagedeleteModal"
   data-mileagelogid="<?php echo htmlspecialchars($row['MileageLogID']); ?>"
   data-vehicleid="<?php echo htmlspecialchars($row['VehicleID']); ?>"
   data-licenseplate="<?php echo htmlspecialchars($row['License_plate']); ?>"
   data-model="<?php echo htmlspecialchars($row['Model']); ?>"
   data-driver="<?php echo htmlspecialchars($row['Driver']); ?>"
   data-startlocation="<?php echo htmlspecialchars($row['StartLocation']); ?>"
   data-endlocation="<?php echo htmlspecialchars($row['EndLocation']); ?>"
   data-distancetraveled="<?php echo htmlspecialchars($row['DistanceTraveled']); ?>">
   <i class="bx bx-trash"></i>
</a>
        </div>
</form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else : ?>
    <p>No records found</p>
<?php endif; ?>

<!-- Add Mileage Modal -->
<div id="addMileageModal" class="modal fade" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Add Mileage Log</h2>
        <button type="button" class="btn-close" onclick="closeModal()"></button>
      </div>
      <div class="modal-body">
        <form action="add.php" method="POST">
          <div class="mb-3">
            <label class="form-label">Mileage Log ID:</label>
            <input type="text" class="form-control" name="MileageLogID" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Vehicle ID:</label>
            <input type="text" class="form-control" name="VehicleID" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Driver:</label>
            <input type="text" class="form-control" name="Driver" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Start Location:</label>
            <input type="text" class="form-control" name="StartLocation" required>
          </div>
          <div class="mb-3">
            <label class="form-label">End Location:</label>
            <input type="text" class="form-control" name="EndLocation" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Distance Traveled:</label>
            <input type="number" class="form-control" name="DistanceTraveled" required>
          </div>
          <div class="mb-3">
            <label class="form-label">License Plate:</label>
            <input type="text" class="form-control" name="License_plate" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Model:</label>
            <input type="text" class="form-control" name="Model" required>
          </div>
          <button type="submit" class="btn btn-primary" name="addMileage">Submit</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="mileageviewModal" tabindex="-1" aria-labelledby="mileageviewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-blue-600 text-white">
                <h5 class="modal-title" id="mileageviewModalLabel">Mileage Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Mileage Log ID:</strong> <span id="newMileageLogID"></span></p>
                <p><strong>Vehicle ID:</strong> <span id="newVehicleID"></span></p>
                <p><strong>Driver:</strong> <span id="newDriver"></span></p>
                <p><strong>Start Location:</strong> <span id="newStartLocation"></span></p>
                <p><strong>End Location:</strong> <span id="newEndLocation"></span></p>
                <p><strong>Distance Traveled:</strong> <span id="newDistanceTraveled"></span></p>
                <p><strong>License Plate:</strong> <span id="newLicensePlate"></span></p>
                <p><strong>Model:</strong> <span id="newModel"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<form action="edit.php" method="POST" onsubmit="return confirmUpdate();">
    <div class="modal fade" id="mileageModal" tabindex="-1" aria-labelledby="mileageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mileageModalLabel">Edit Mileage Log</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="oldMileageLogID" name="old_mileage_log_id">
                    <div class="mb-3">
                        <label for="editMileageLogID" class="form-label">Mileage Log ID</label>
                        <input type="text" class="form-control" id="editMileageLogID" name="mileage_log_id">
                    </div>
                    <div class="mb-3">
                        <label for="editVehicleID" class="form-label">Vehicle ID</label>
                        <input type="text" class="form-control" id="editVehicleID" name="vehicle_id">
                    </div>
                    <div class="mb-3">
                        <label for="editDriver" class="form-label">Driver</label>
                        <input type="text" class="form-control" id="editDriver" name="driver">
                    </div>
                    <div class="mb-3">
                        <label for="editStartLocation" class="form-label">Start Location</label>
                        <input type="text" class="form-control" id="editStartLocation" name="start_location">
                    </div>
                    <div class="mb-3">
                        <label for="editEndLocation" class="form-label">End Location</label>
                        <input type="text" class="form-control" id="editEndLocation" name="end_location">
                    </div>
                    <div class="mb-3">
                        <label for="editDistanceTraveled" class="form-label">Distance Traveled</label>
                        <input type="number" class="form-control" id="editDistanceTraveled" name="distance_traveled">
                    </div>
                    <div class="mb-3">
                        <label for="editLicensePlate" class="form-label">License Plate</label>
                        <input type="text" class="form-control" id="editLicensePlate" name="license_plate">
                    </div>
                    <div class="mb-3">
                        <label for="editModel" class="form-label">Model</label>
                        <input type="text" class="form-control" id="editModel" name="model">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Delete Modal -->
<div class="modal fade" id="mileagedeleteModal" tabindex="-1" aria-labelledby="DelsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DelsModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete Mileage Log ID: <strong id="displayMileageLogID"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="delete.php">
                    <input type="hidden" name="mileagelogid" id="hiddenMileageLogID">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>


    <script>
         function toggleDropdown(dropdownId, element) {
        const dropdown = document.getElementById(dropdownId);
        const arrowIcon = element.querySelector('.arrow-icon');

        if (dropdown.classList.contains('hidden')) {
            dropdown.classList.remove('hidden');
            arrowIcon.classList.add('rotate-90');
        } else {
            dropdown.classList.add('hidden');
            arrowIcon.classList.remove('rotate-90');
        }
    }
    </script>
    <script src="view.js?v=<?php echo time(); ?>"></script>
    <script src="add.js?v=<?php echo time(); ?>"></script>
    <script src="edit.js?v=<?php echo time(); ?>"></script>
    <script src="delete.js?v=<?php echo time(); ?>"></script>

</body>
</html>