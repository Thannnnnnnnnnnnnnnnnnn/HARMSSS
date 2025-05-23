<?php 
   include("connection.php");
   $stmt = $conn->prepare("SELECT * FROM fuellogs");
   $stmt->execute();
   $fuellogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>                                               
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FUEL</title>
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

<!-- main Content -->
<?php if (!empty($fuellogs)) : ?>
<h2 class="pt-6 pl-6 text-xl font-medium mb-4 text-gray-800">Fuel Log</h2>
<table class="styled-table">
    <thead>
        <tr>
            <th>FuelLog ID</th>
            <th>Vehicle ID</th>
            <th>Model</th>
            <th>Fuel Type</th>
            <th>Fuel Consumption</th>
            <th>Date and Time</th>
            <th>Total Cost</th>
            <th>Operations</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($fuellogs as $row) : ?>
            <tr>
                <td><?php echo htmlspecialchars($row['FuelLogID']); ?></td>
                <td><?php echo htmlspecialchars($row['VehicleID']); ?></td>
                <td><?php echo htmlspecialchars($row['Model']); ?></td>
                <td><?php echo htmlspecialchars($row['FuelType']); ?></td>
                <td><?php echo htmlspecialchars($row['FuelConsumption']); ?></td>
                <td><?php echo htmlspecialchars($row['DateTime']); ?></td>
                <td><?php echo htmlspecialchars($row['TotalCost']); ?></td>
            <td>
<!-- Add fuel Button -->
<button type="button" id="addFuelBtn" class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#addFuelLogModal">
    <i class="fas fa-plus"></i> ADD
</button>

<!-- View Button -->
<a href="#" class="view-btn bg-blue-600 text-white w-[70px] p-2 rounded-lg inline-block text-center"
   data-bs-toggle="modal"
   data-bs-target="#viewModal"
   data-fuellogid="<?php echo htmlspecialchars($row['FuelLogID']); ?>"
   data-vehicleid="<?php echo htmlspecialchars($row['VehicleID']); ?>"
   data-model="<?php echo htmlspecialchars($row['Model']); ?>"
   data-fueltype="<?php echo htmlspecialchars($row['FuelType']); ?>"
   data-fuelconsumption="<?php echo htmlspecialchars($row['FuelConsumption']); ?>"
   data-datetime="<?php echo htmlspecialchars($row['DateTime']); ?>"
   data-totalcost="<?php echo htmlspecialchars($row['TotalCost']); ?>">
   <i class="bx bx-show"></i>
</a>

<!-- Edit Button -->
<a href="#" class="edit-btn bg-green-500 text-white w-[70px] p-2 rounded-lg inline-block text-center"
   data-bs-toggle="modal"
   data-bs-target="#fuelModal"
   data-fuellogid="<?php echo htmlspecialchars($row['FuelLogID']); ?>"
   data-vehicleid="<?php echo htmlspecialchars($row['VehicleID']); ?>"
   data-model="<?php echo htmlspecialchars($row['Model']); ?>"
   data-fueltype="<?php echo htmlspecialchars($row['FuelType']); ?>"
   data-fuelconsumption="<?php echo htmlspecialchars($row['FuelConsumption']); ?>"
   data-datetime="<?php echo htmlspecialchars($row['DateTime']); ?>"
   data-totalcost="<?php echo htmlspecialchars($row['TotalCost']); ?>">
   <i class="bx bx-edit"></i>
</a>

<!--delete button -->
<a href="#" class="delete-btn bg-red-600 text-white w-[70px] p-2 rounded-lg inline-block text-center"
   data-bs-toggle="modal"
   data-bs-target="#fueldeleteModal"
   data-fuellogid="<?php echo htmlspecialchars($row['FuelLogID']); ?>"
   data-vehicleid="<?php echo htmlspecialchars($row['VehicleID']); ?>"
   data-model="<?php echo htmlspecialchars($row['Model']); ?>">
    <i class="bx bx-trash"></i>
</a>


                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else : ?>
    <p>No records found</p>
<?php endif; ?>
<!-- Add Fuel Modal -->
<div id="addFuelLogModal" class="modal fade" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Add Fuel Log</h2>
        <button type="button" class="btn-close" onclick="closeModal()"></button>
      </div>
      <div class="modal-body">
        <form action="add.php" method="POST">
          <div class="mb-3">
            <label for="FuelLogID" class="form-label">Fuel Log ID:</label>
            <input type="text" class="form-control" name="FuelLogID" required>
          </div>
          <div class="mb-3">
            <label for="VehicleID" class="form-label">Vehicle ID:</label>
            <input type="text" class="form-control" name="VehicleID" required>
          </div>
          <div class="mb-3">
            <label for="Model" class="form-label">Model:</label>
            <input type="text" class="form-control" name="Model" required>
          </div>
          <div class="mb-3">
            <label for="FuelType" class="form-label">Fuel Type:</label>
            <input type="text" class="form-control" name="FuelType" required>
          </div>
          <div class="mb-3">
            <label for="FuelConsumption" class="form-label">Fuel Consumption:</label>
            <input type="text" class="form-control" name="FuelConsumption"  required>
          </div>
          <div class="mb-3">
            <label for="DateTime" class="form-label">Date and Time:</label>
            <input type="datetime-local" class="form-control" name="DateTime" required>
          </div>
          <div class="mb-3">
            <label for="TotalCost" class="form-label">Total Cost:</label>
            <input type="text" class="form-control" name="TotalCost"  required>
          </div>
          <button type="submit" class="btn btn-primary" name="addFuel">Submit</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-blue-600 text-white">
                <h5 class="modal-title" id="viewModalLabel">Fuel Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>FuelLog ID:</strong> <span id="modalFuelLogID"></span></p>
                <p><strong>Vehicle ID:</strong> <span id="modalVehicleID"></span></p>
                <p><strong>Model:</strong> <span id="modalModel"></span></p>
                <p><strong>Fuel Type:</strong> <span id="modalFuelType"></span></p>
                <p><strong>Fuel Consumption:</strong> <span id="modalFuelConsumption"></span></p>
                <p><strong>Date and Time:</strong> <span id="modalDateTime"></span></p>
                <p><strong>Total Cost:</strong> <span id="modalTotalCost"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<form action="edit.php" method="POST" onsubmit="return confirmUpdate();">
    <div class="modal fade" id="fuelModal" tabindex="-1" aria-labelledby="fuelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fuelModalLabel">Edit Fuel Log</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Hidden field for old Fuel Log ID -->
                    <input type="hidden" id="oldFuelLogID" name="old_fuel_log_id">

                    <div class="mb-3">
                        <label for="editFuelLogID" class="form-label">Fuel Log ID</label>
                        <input type="text" class="form-control" id="editFuelLogID" name="fuel_log_id">
                    </div>
                    <div class="mb-3">
                        <label for="editVehicleID" class="form-label">Vehicle ID</label>
                        <input type="text" class="form-control" id="editVehicleID" name="vehicle_id">
                    </div>
                    <div class="mb-3">
                        <label for="editModel" class="form-label">Model</label>
                        <input type="text" class="form-control" id="editModel" name="model">
                    </div>
                    <!-- New fields for editing -->
                    <div class="mb-3">
                        <label for="editFuelType" class="form-label">Fuel Type</label>
                        <input type="text" class="form-control" id="editFuelType" name="fuel_type">
                    </div>
                    <div class="mb-3">
                        <label for="editFuelConsumption" class="form-label">Fuel Consumption</label>
                        <input type="text" class="form-control" id="editFuelConsumption" name="fuel_consumption" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label for="editDateTime" class="form-label">Date and Time</label>
                        <input type="datetime-local" class="form-control" id="editDateTime" name="datetime">
                    </div>
                    <div class="mb-3">
                        <label for="editTotalCost" class="form-label">Total Cost</label>
                        <input type="number" class="form-control" id="editTotalCost" name="total_cost" step="0.01">
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
<div class="modal fade" id="fueldeleteModal" tabindex="-1" aria-labelledby="DelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DelModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="displayFuelLogID"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="delete.php">
                    <input type="hidden" name="fuel_log_id" id="hiddenFuelLogID">
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
