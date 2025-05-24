<?php
include("../../CDMS/connection.php");

// Define the database name
$db_name = "logs2_vehicle_reservation_system"; 

if (!isset($connections[$db_name])) {
    die("Database connection not found for $db_name");
}

$connection = $connections[$db_name]; // Assign the correct connection



// Query to count the number of reservation
$query = "SELECT COUNT(*) AS total_reservation FROM reservation"; 
$result = mysqli_query($connection, $query);

$row = mysqli_fetch_assoc($result);
$Reservation_count = $row['total_reservation'];


//completed
$query = "SELECT COUNT(*) AS complete_reservation FROM reservation WHERE status = 'Completed'"; 
$result = mysqli_query($connection, $query);


$row = mysqli_fetch_assoc($result);
$completed_count = $row['complete_reservation'];

//In transit
$query = "SELECT COUNT(*) AS In_transit_reservation FROM reservation WHERE status = 'Dispatch'"; 
$result = mysqli_query($connection, $query);


$row = mysqli_fetch_assoc($result);
$In_transit_count = $row['In_transit_reservation'];

//Pending reservation
$query = "SELECT COUNT(*) AS pending_reservation FROM reservation WHERE status = 'Pending'"; 
$result = mysqli_query($connection, $query);


$row = mysqli_fetch_assoc($result);
$pending_count = $row['pending_reservation'];

//Cancelled reservation
$query = "SELECT COUNT(*) AS cancelled_reservation FROM reservation WHERE status = 'Cancelled'"; 
$result = mysqli_query($connection, $query);


$row = mysqli_fetch_assoc($result);
$cancelled_count = $row['cancelled_reservation'];


if (!$result) {
    die("Count query failed: " . mysqli_error($connection));
}


// Query to count total vehicles
$query = "SELECT 
            (SELECT COUNT(*) FROM reservation) AS total_reservation, 
            (SELECT COUNT(*) FROM reservation WHERE status = 'Complete') AS Complete,
            (SELECT COUNT(*) FROM reservation WHERE status = 'Failed') AS Failed,
            (SELECT COUNT(*) FROM reservation WHERE status = 'Disbatch') AS Disbatch,
            (SELECT COUNT(*) FROM reservation WHERE status = 'Pending') AS Pending,
            (SELECT COUNT(*) FROM reservation WHERE status = 'Cancelled') AS Cancelled";

$result = mysqli_query($connection, $query);

if (!$result) {
    die("Count query failed: " . mysqli_error($connection));
}

// Fetch the counts
$row = mysqli_fetch_assoc($result);
$total_reservation_count = $row['total_reservation'];
$completed_count = $row['Complete'];
$failed_count = $row['Failed'];

$in_transit_count = $row['Disbatch'];
$pending_count = $row['Pending'];
$cancelled_count = $row['Cancelled'];

// Query to fetch all drivers (reusing $result)
$query = "SELECT * FROM `reservation` WHERE status = 'Complete'"; // Adjust the query as needed
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
    <title>Logistic 2</title>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
</head>
<body>
    <div class="flex min-h-screen w-full">
        <!-- Overlay -->
        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- Sidebar -->
        <div class="sidebar sidebar-expanded fixed z-50 overflow-hidden h-screen bg-white border-r border-[#F7E6CA] flex flex-col">
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
                 
                    <div class="main-container">
                            <br>
                            <div class="main-container-1">

                    <span class="span">
                        <a href="../reservation.php">Total Reservation 
                        <h1 style="font-size: 30px;"><?php echo isset($total_reservation_count) ? $total_reservation_count : 'N/A'; ?></h1>
                        </a>
                        <i class="fa-solid fa-users" style="position: absolute; top: 10px; right: 15px; font-size: 20px;">
                        </i>
                    </span>

                    <span class="a">
                    <a href="completed_reservation.php">Complete reservation
                    <h1 style="font-size: 30px;"><?php echo $completed_count; ?></h1>
                    </a>
                    <i class="fa-solid fa-check-circle " style="position: absolute; top: 10px; right: 15px; font-size: 20px;"></i>
                    </span> 

                    <span class="aaa">
                    <a href="failed_reservation.php">Failed reservation
                    <h1 style="font-size: 30px;"><?php echo $failed_count; ?></h1>
                    </a>
                    <i class="fa-solid fa-times-circle" style="position: absolute; top: 10px; right: 15px; font-size: 20px;"></i>
                    </span> 

                    <span class="b">
                        <a href="dispatch.php">In transit
                        <h1 style="font-size: 30px;"><?php echo $In_transit_count; ?></h1>
                        </a>
                        <i class="fa-solid fa-truck" style="position: absolute; top: 10px; right: 15px; font-size: 20px;"></i>
                    </span>

                    <span class="c">
                        <a href="reservation_pending.php">Pending reservation
                        <h1 style="font-size: 30px;"><?php echo $pending_count; ?></h1>

                        </a> 
                        <i class="fa-solid fa-clock" style="position: absolute; top: 10px; right: 15px; font-size: 20px;"></i>
                    </span>

                    <span class="d">
                        <a href="cancelled_reservation.php">Cancelled reservation
                        <h1 style="font-size: 30px;"><?php echo $cancelled_count; ?></h1>

                        </a> 
                        <i class="fa-solid fa-calendar-xmark" style="position: absolute; top: 10px; right: 15px; font-size: 20px;"></i>
                    </span>
                    </div>

                       
                
                        <br>
                        <h2 style="font-family: Arial, Helvetica, sans-serif; font-size: 25px; font-weight: 600;">
                            Complete reservation list
                        </h2>
                        <br>
                        
                        <div class="table-container">

                        <?php if ($result && mysqli_num_rows($result) > 0) : ?>              
    <table class="styled-table">
        <thead>
            <tr>
                <th>Reservation no.</th>
                <th>Destination</th>
                <th>Schedule</th>
                <th>Purpose</th>
                <th>Vehicle type</th>
                <th>Status</th>
                <th></th>



            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)) : ?>
                <tr class="driver-row" 
                   ">
                    
                    <td><?php echo $row['ReservationID']; ?></td>
                    <td><?php echo $row['destination_to']; ?></td>
                    <td><?php echo $row['schedule']; ?></td>
                    <td><?php echo $row['purpose']; ?></td>
                    <td><?php echo $row['vehicle_type']; ?></td>
                    <td><?php echo $row['status']; ?></td>

                    <td>
              
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


   

              
            </main>
        </div>
    </div>

    <script src="JS/sidebar.js"> </script>
<script src="JS/reservation.js"></script>  


</body>
</html>
