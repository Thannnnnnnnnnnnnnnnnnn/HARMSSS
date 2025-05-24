<?php
include("../../../../connection.php");

// Define the database name
$db_name = "logs2_vehicle_reservation_system"; 

if (!isset($connections[$db_name])) {
    die("Database connection not found for $db_name");
}

$connection = $connections[$db_name]; // Assign the correct connection


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $purpose = $_POST['purpose'];
    $vehicle_type = $_POST['vehicle_type']; 
    $cargo = $_POST['cargo']; 
    $destination_to = $_POST['destination_to'];
    $destination_from = $_POST['destination_from'];
    $schedule = $_POST['schedule'];
    $arrival = $_POST['arrival'];
    $departure =  $_POST['departure'];
    $status = $_POST['status'];
 

    $sql_table = "INSERT INTO `reservation` (purpose, vehicle_type, cargo, destination_to, destination_from, schedule, arrival, departure, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    if ($stmt = $connection->prepare($sql_table)) {
        $stmt->bind_param("sssssssss", $purpose, $vehicle_type, $cargo, $destination_to, $destination_from, $schedule, $arrival, $departure, $status);
        
        if ($stmt->execute()) {
            // Redirect after successful insertion to prevent resubmission
            header("Location: reservation.php?success=1");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        die("Error preparing statement: " . $connection->error);
    }
}

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
            (SELECT COUNT(*) FROM reservation WHERE status = 'Pending for approval') AS Pending,
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
$query = "SELECT * FROM `reservation` WHERE status = 'Pending for approval'"; // Adjust the query as needed
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
    <link rel="stylesheet" href="../CSS/reservation_cards.css">

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
                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
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
                <div class="table-responsive">

                
                <div class="flex justify-center w-full">
  <!-- Dashboard Container -->
  <div class="flex flex-wrap justify-center gap-6 p-4 dashboard-cards">

    <!-- Total Reservation -->
    <a href="../reservation.php" class="dashboard-card hover:shadow-lg transition duration-300 cursor-pointer block w-72">
      <div class="flex flex-col gap-1">
        <div class="flex items-center justify-between w-full">
          <div class="flex items-center gap-2">
            <i class='bx bxs-file-doc text-xl text-blue-600'></i>
            <h2 class="dashboard-title">Total Reservation</h2>
          </div>
          <p class="dashboard-number text-black-600 font-semibold">
            <?php echo isset($total_reservation_count) ? $total_reservation_count : '0'; ?>
          </p>
        </div>
      </div>
    </a>

    <!-- Reserved -->
    <a href="reserved.php" class="dashboard-card hover:shadow-lg transition duration-300 cursor-pointer block w-72">
      <div class="flex flex-col gap-1">
        <div class="flex items-center justify-between w-full">
          <div class="flex items-center gap-2">
            <i class='bx bxs-check-circle text-xl text-purple-600'></i>
            <h2 class="dashboard-title">Reserved</h2>
          </div>
          <p class="dashboard-number text-black-600 font-semibold">
            <?php echo isset($completed_count) ? $completed_count : '0'; ?>
          </p>
        </div>
      </div>
    </a>

    <!-- Failed Reservation -->
    <a href="failed_reservation.php?" class="dashboard-card hover:shadow-lg transition duration-300 cursor-pointer block w-72">
      <div class="flex flex-col gap-1">
        <div class="flex items-center justify-between w-full">
          <div class="flex items-center gap-2">
            <i class='bx bxs-error-alt text-xl text-yellow-600'></i>
            <h2 class="dashboard-title">Failed Reservation</h2>
          </div>
          <p class="dashboard-number text-black-600 font-semibold">
            <?php echo isset($failed_count) ? $failed_count : '0'; ?>
          </p>
        </div>
      </div>
    </a>

    <!-- In Transit -->
    <a href="dispatch.php?" class="dashboard-card hover:shadow-lg transition duration-300 cursor-pointer block w-72">
      <div class="flex flex-col gap-1">
        <div class="flex items-center justify-between w-full">
          <div class="flex items-center gap-2">
            <i class='bx bx-bus text-xl text-orange-600'></i>
            <h2 class="dashboard-title">In Transit</h2>
          </div>
          <p class="dashboard-number text-black-600 font-semibold">
            <?php echo isset($in_transit_count) ? $in_transit_count : '0'; ?>
          </p>
        </div>
      </div>
    </a>

    <!-- Pending Reservation -->
    <a href="reservation_pending.php?" class="dashboard-card hover:shadow-lg transition duration-300 cursor-pointer block w-72">
      <div class="flex flex-col gap-1">
        <div class="flex items-center justify-between w-full">
          <div class="flex items-center gap-2">
            <i class='bx bx-time-five text-xl text-green-600'></i>
            <h2 class="dashboard-title">Pending Reservation</h2>
          </div>
          <p class="dashboard-number text-black-500 font-semibold">
            <?php echo isset($pending_count) ? $pending_count : '0'; ?>
          </p>
        </div>
      </div>
    </a>

    <!-- Cancelled Reservation -->
    <a href="cancelled_reservation.php?" class="dashboard-card hover:shadow-lg transition duration-300 cursor-pointer block w-72">
      <div class="flex flex-col gap-1">
        <div class="flex items-center justify-between w-full">
          <div class="flex items-center gap-2">
            <i class='bx bx-x text-xl text-red-600'></i>
            <h2 class="dashboard-title">Cancelled Reservation</h2>
          </div>
          <p class="dashboard-number text-black-500 font-semibold">
            <?php echo isset($cancelled_count) ? $cancelled_count : '0'; ?>
          </p>
        </div>
      </div>
    </a>

  </div>
</div>

</div>

                        <?php if ($result && mysqli_num_rows($result) > 0) : ?>              
                            <table class="styled-table w-full border-collapse border border-gray-300">
                            <thead>
            <tr>
                <th>Reservation no.</th>
                <th>Destination</th>
                <th>Schedule</th>
                <th>Purpose</th>
                <th>Vehicle type</th>
                <th>Status</th>
                <th>Action</th>


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
     
                    <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded" onclick="showApproveModal('<?php echo urlencode($row['ReservationID']); ?>')">
                    <i class="bx bx-check"></i>
                    </button>
                    <b> | </b>
                    <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded" onclick="showRejectModal('<?php echo urlencode($row['ReservationID']); ?>')">
                    <i class="bx bx-trash"></i>
                    </button>
              
                                </div> 
                                    
                              

                                    </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </table>
                                </div>
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


<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
  <div class="bg-white p-6 rounded shadow-xl w-full max-w-md">
    <h2 class="text-xl font-semibold mb-4">Approve Reservation</h2>
    <form action="approve_request.php" method="POST">
      <input type="hidden" name="request_id" id="approveReservationID">
      <p>Are you sure you want to approve this reservation?</p>
      <div class="flex justify-end mt-4 space-x-2">
        <button type="button" onclick="closeModal('approveModal')" class="px-4 py-2 bg-gray-400 rounded hover:bg-gray-500 text-white">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-green-500 rounded hover:bg-green-600 text-white">Approve</button>
      </div>
    </form>
  </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
  <div class="bg-white p-6 rounded shadow-xl w-full max-w-md">
    <h2 class="text-xl font-semibold mb-4">Reject Reservation</h2>
    <form action="reject_request.php" method="POST">
      <input type="hidden" name="request_id" id="rejectReservationID">
      <p>Are you sure you want to reject this reservation?</p>
      <div class="flex justify-end mt-4 space-x-2">
        <button type="button" onclick="closeModal('rejectModal')" class="px-4 py-2 bg-gray-400 rounded hover:bg-gray-500 text-white">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-red-500 rounded hover:bg-red-600 text-white">Reject</button>
      </div>
    </form>
  </div>
</div>




              
            </main>
        </div>
    </div>

    <script src="../JS/sidebar.js"> </script>
<script src="../JS/reservation.js"></script>  
<script src="../JS/request_approval_deny.js"></script>



</body>
</html>
