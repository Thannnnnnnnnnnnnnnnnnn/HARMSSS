<?php
include("../CDMS/connection.php");

// Define the database name
$db_name = "logs2_usm"; 

if (!isset($connections[$db_name])) {
    die("Database connection not found for $db_name");
}

$connection = $connections[$db_name]; // Assign the correct connection


// Query to fetch all records from the department_log_history table
$query = "SELECT * FROM `department_log_history`"; 
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
    <title>department log history</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.7/dist/sweetalert2.all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="CSS/sidebar.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="CSS/mmain.css">
    <link rel="stylesheet" href="CSS/button.css">
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
                <h1 class="text-xl font-bold text-[#4E3B2A]">Logistic 2</h1>
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
                    

                     <!--- Audit management --->

                     <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('audit-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="bx bx-wallet text-lg pr-4"></i>
                                <span class="text-sm font-medium">Audit management</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                    <div id="audit-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                        <ul class="space-y-1">
                        <li>
                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                    <i class="bx bx-shield text-lg"></i> <span>Audit logs</span>
                                    </a>
                                </li>
                                <li>
                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                        <i class="bx bx-calendar text-alt text-lg"></i> <span>Audit plans</span>
                                    </a>
                                </li>
                                <li>
                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                        <i class="bx bx-list-check text-lg"></i> <span>Corrective actions</span>
                                    </a>
                                </li>

                                <li>
                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                        <i class="bx bx-search-alt text-lg"></i> <span>Findings</span>
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
                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
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


                 <!---- USM---->

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
                            <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
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
                <div class="right-nav  items-center space-x-6 hidden lg:flex">
                    <button aria-label="Notifications" class="text-[#4E3B2A] focus:outline-none border-r border-[#F7E6CA] pr-6 relative">
                        <i class="fa-regular fa-bell text-xl"></i>
                        <span class="absolute top-0.5 right-5 block w-2.5 h-2.5 bg-[#594423] rounded-full"></span>
                    </button>

                    <div class="flex items-center space-x-2">
                        <i class="fa-regular fa-user bg-[#594423] text-white px-4 py-2 rounded-lg text-lg" aria-label="User profile"></i>
                        <div class="info flex flex-col py-2">
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="px-8 py-8">
                <!-- All Content Put Here -->
                 
                 
                            
                      
                   
                        <h2 style="font-family: Arial, Helvetica, sans-serif; font-size: 25px; font-weight: 600;">
                            Department log history
                        </h2>
                        <br>
                        <div class="table-container">

                        <?php if ($result && mysqli_num_rows($result) > 0) : ?>              
    <table class="styled-table">
        <thead>
            <tr>
                <th>Log ID</th>
                <th>Dept ID</th>
                <th>User ID</th>
                <th>Name</th>
                <th>Role</th>
                <th>Log status</th>
                <th>Date/Time</th>
                <th>Unified crud</th>
              

            </tr>
        </thead>
        <tbody>
           
            <?php while($row = mysqli_fetch_assoc($result)) : ?>
                <tr class="driver-row" 

                 
            >
                   
                    <td><?php echo $row['Dept_log_ID']; ?></td>
                    <td><?php echo $row['Department_ID']; ?></td>
                    <td><?php echo $row['User_ID']; ?></td>
                    <td><?php echo $row['Name']; ?></td>
                    <td><?php echo $row['Role']; ?></td>
                    <td><?php echo $row['Log_status']; ?></td>
                    <td><?php echo $row['Log_Date_Time']; ?></td>
                    <td>
     

                                                                
                                <div class="div-button">

                                

                                

<!-- Updated Button -->
<button 
    class="manage-button"
    data-bs-toggle="modal" 
    data-bs-target="#combinedModal" 
    data-driver-id="<?php echo htmlspecialchars($row['Dept_log_ID']); ?>"
    data-status="<?php echo htmlspecialchars($row['Log_status']); ?>">
    <i class="bx bx-cog"></i> 
</button>




                                   
                                </div> 
                                    
                              

                                    </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </table>
                                <?php else : ?>
                                <p>No records found</p>
                                <?php endif; ?>

                            </div>
                            </div>


                            <!-- Driver Details Modal -->
                         <!-- Bootstrap Modal -->
<div class="modal fade" id="driverDetailsModal" tabindex="-1" aria-labelledby="driverDetailsLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="driverDetailsLabel">Driver Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Driver ID:</strong> <span id="modalDriverId"></span></p>
                <p><strong>Name:</strong> <span id="modalDriverName"></span></p>
                <p><strong>Contact:</strong> <span id="modalDriverContact"></span></p>
                <p><strong>Status:</strong> <span id="modalDriverStatus"></span></p>
                <p><strong>Age:</strong> <span id="modalDriverAge"></span></p>
                <p><strong>Gender:</strong> <span id="modalDriverGender"></span></p>
            </div>
        </div>
    </div>
</div>

<!-- Combined Modal -->
<div class="modal fade" id="combinedModal" tabindex="-1" aria-labelledby="combinedModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="combinedModalTitle">Driver Management</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Tabs -->
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#statusSection">Update Status</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#editSection">Edit Details</button>
                    </li>
                </ul>

                <div class="tab-content mt-3">
                    <!-- Status Update Section -->
                    <div class="tab-pane fade show active" id="statusSection">
                        <form id="updateStatusForm">
                            <input type="hidden" name="DriverID" id="driverIdInput">
                            <div class="mb-3">
                                <label for="driverStatus" class="form-label">Select Status</label>
                                <select class="form-select" id="driverStatus" name="Status">
                                    <option value="Available">Available</option>
                                    <option value="Not Available">Not Available</option>
                                    <option value="On Duty">On Duty</option>
                                    <option value="AWOL">AWOL</option>
                                    <option value="On Training">On Training</option>
                                    <option value="On Leave">On Leave</option>
                                    <option value="Reserved">Reserved</option>
                                    <option value="Under Evaluation">Under Evaluation</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Update Status</button>
                        </form>
                    </div>

                    <!-- Edit Section -->
                    <div class="tab-pane fade" id="editSection">
                        <form id="editDriverForm">
                            <input type="hidden" name="DriverID" id="editDriverId">
                            <div class="mb-3">
            <label for="editDriverName">Name</label>
            <input type="text" class="form-control" id="editDriverName" name="name">
        </div>

        <div class="mb-3">
            <label for="editDriverAge">Age</label>
            <input type="number" class="form-control" id="editDriverAge" name="age" min="18">
        </div>

        <div class="mb-3">
            <label for="editDriverContact">Contact</label>
            <input type="text" class="form-control" id="editDriverContact" name="contact">
        </div>

        <div class="mb-3">
            <label for="editDriverGender">Gender</label>
            <select class="form-control" id="editDriverGender" name="gender">
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
        </div>
                            <button type="submit" class="btn btn-success w-100">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





                                           

              
            </main>
        </div>
    </div>

    <script src="JS/sidebar.js"> </script>
<script src="JS/status.js"></script>  


</body>
</html>
