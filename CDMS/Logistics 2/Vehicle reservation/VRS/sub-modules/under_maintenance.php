<?php
include("../../../../connection.php");

// Define the database name
$db_name = "logs2_vehicle_reservation_system";

// Ensure the database connection exists
if (!isset($connections[$db_name])) {
    die("Database connection not found for $db_name");
}

$connection = $connections[$db_name]; // Assign the correct connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Vehicle_type = $_POST['Vehicle_type'];
    $Vehicle_color = $_POST['Vehicle_color']; 
    $Vehicle_brand = ($_POST['Vehicle_brand'] == 'other') ? $_POST['Other_brand'] : $_POST['Vehicle_brand'];
    $Plate_no = $_POST['Plate_no'];
    $Date = $_POST['date'];
    $Time = date('H:i:s');
    $Status = $_POST['Status'];
    $route = $_POST['route'];
    $driver = $_POST['driver'];

    $sql_table = "INSERT INTO `vehicles` (Vehicle_type, Vehicle_color, Vehicle_brand, Plate_no, Date, Time, Status, route, driver) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    if ($stmt = $connection->prepare($sql_table)) {
        $stmt->bind_param("sssssssss", $Vehicle_type, $Vehicle_color, $Vehicle_brand, $Plate_no, $Date, $Time, $Status, $route, $driver);
        
        if ($stmt->execute()) {
            // Redirect after successful insertion to prevent resubmission
            header("Location: vehicles.php?success=1");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        die("Error preparing statement: " . $connection->error);
    }
}

// Query to count total vehicles
$query = "SELECT 
            (SELECT COUNT(*) FROM vehicles) AS total_vehicles, 
            (SELECT COUNT(*) FROM vehicles WHERE status = 'Operational') AS Operational,
            (SELECT COUNT(*) FROM vehicles WHERE status = 'Deployed') AS Deployed,
            (SELECT COUNT(*) FROM vehicles WHERE status = 'Under maintenance') AS Under_maintenance,
            (SELECT COUNT(*) FROM vehicles WHERE status = 'Reserved') AS Reserved";

$result = mysqli_query($connection, $query);

if (!$result) {
    die("Count query failed: " . mysqli_error($connection));
}

// Fetch the counts
$row = mysqli_fetch_assoc($result);
$total_vehicles_count = $row['total_vehicles'];
$Operational_count = $row['Operational'];
$Deployed_count = $row['Deployed'];
$Under_maintenance_count = $row['Under_maintenance'];
$Reserved_count = $row['Reserved'];

// Query to fetch all vehicles
$query = "SELECT * FROM `vehicles` WHERE status = 'Under_maintenace'"; 
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

    <!-- Tailwind CSS (Only keep if you're actively using it) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Bootstrap 5 (Latest version) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.7/dist/sweetalert2.all.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../CSS/sidebar.css">
    <link rel="stylesheet" href="../CSS/mmain.css">
    <link rel="stylesheet" href="../CSS/button.css">
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
                 
                    <div class="main-container">
                            <br>

                        <div class="main-container-1">

                            <span class="span">
                                <a href="vehicles.php">Total vehicles 
                                <h1 style="font-size: 30px;"><?php echo isset($total_vehicles_count) ? $total_vehicles_count : 'N/A'; ?></h1>
                                </a>
                                <i class="bx bx-car" style="position: absolute; top: 10px; right: 15px; font-size: 30px;">
                                </i>
                            </span>
                    
                            <span class="a">
                               <a href="sub-modules/operational.php">Operational
                               <h1 style="font-size: 30px;"><?php echo $Operational_count; ?></h1>
                               </a>
                               <i class="bx bx-check-circle" style="position: absolute; top: 10px; right: 15px; font-size: 30px;"></i>
                            </span> 

                            <span class="b">
                                <a href="sub-modules/deploued.php">Deployed
                                <h1 style="font-size: 30px;"><?php echo $Deployed_count; ?></h1>
                                </a>
                                <i class="bx bx-send" style="position: absolute; top: 10px; right: 15px; font-size: 30px;"></i>
                            </span>

                            <span class="c">
                                <a href="sub-modules/under_maintenace.php">Under maintenance
                                <h1 style="font-size: 30px;"><?php echo $Under_maintenance_count; ?></h1>

                                </a> 
                                <i class="bx bx-wrench" style="position: absolute; top: 10px; right: 15px; font-size: 30px;"></i>
                            </span>

                            <span class="d">
                                <a href="sub-modules/reserved.php">Reserved
                                <h1 style="font-size: 30px;"><?php echo $Reserved_count; ?></h1>

                                </a> 
                                <i class="bx bx-calendar-check" style="position: absolute; top: 10px; right: 15px; font-size: 30px;"></i>
                            </span>
                        </div>
                        <br>

                        <!-- Button to Open Modal -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
                            Add Vehicle
                        </button>

                        
                  
                        <br>
                        <h2 style="font-family: Arial, Helvetica, sans-serif; font-size: 25px; font-weight: 600;">
                            Vehicles
                        </h2>
                        <br>
                        <div class="table-container">

                        <?php if ($result && mysqli_num_rows($result) > 0) : ?>              
    <table class="styled-table">
        <thead>
            <tr>
                <th>Plate No.</th>
                <th>Driver</th>
                <th>Status</th>
                <th>Operation</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)) : ?>
                <tr class="vehicle-row" 
                    data-vehicle-id="<?php echo htmlspecialchars($row['VehicleID']); ?>" 
                    data-vehicletype="<?php echo htmlspecialchars($row['Vehicle_type']); ?>"
                    data-vehiclecolor="<?php echo htmlspecialchars($row['Vehicle_color']); ?>"
                    data-vehiclebrand="<?php echo htmlspecialchars($row['Vehicle_brand']); ?>"
                    data-plateno="<?php echo htmlspecialchars($row['Plate_no']); ?>">
                    
                    <td><?php echo htmlspecialchars($row['Plate_no']); ?></td>
                    <td><?php echo htmlspecialchars($row['driver']); ?></td>
                    <td><?php echo htmlspecialchars($row['Status']); ?></td>
                    <td>
     

                                                                
                                <div class="div-button">

                                

                                

<!-- Updated Button -->
<button 
    class="manage-button"
    data-bs-toggle="modal" 
    data-bs-target="#combinedModal" 
    data-driver-id="<?php echo htmlspecialchars($row['VehicleID']); ?>"
    data-status="<?php echo htmlspecialchars($row['Status']); ?>">
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

<!-- Bootstrap 5 Modal -->
<div class="modal fade" id="addVehicleModal" tabindex="-1" aria-labelledby="addVehicleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    
                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addVehicleModalLabel">Add Vehicle</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <!-- Modal Body (Form) -->
                                    <div class="modal-body">
                                        <form action="vehicles.php" method="post">

                                            <!-- Vehicle Type -->
                                            <div class="mb-3">
                                                <label for="Vehicle_type" class="form-label">Type of Vehicle</label>
                                                <select id="Vehicle_type" name="Vehicle_type" class="form-select" required>
                                                    <option value="">Choose</option>
                                                    <option value="2 Wheel">2 Wheel</option>
                                                    <option value="4 Wheel">4 Wheel</option>
                                                </select>
                                            </div>

                                            <!-- Vehicle Color -->
                                            <div class="mb-3">
                                                <label for="Vehicle_color" class="form-label">Vehicle Color</label>
                                                <input type="text" id="Vehicle_color" name="Vehicle_color" class="form-control" required placeholder="Enter vehicle color">
                                            </div>

                                            <!-- Vehicle Brand -->
                                            <div class="mb-3">
                                                <label for="Vehicle_brand" class="form-label">Vehicle Brand</label>
                                                <select id="Vehicle_brand" name="Vehicle_brand" class="form-select" required onchange="toggleOtherBrand()">
                                                    <option value="">Choose</option>
                                                    <option value="toyota">Toyota</option>
                                                    <option value="nissan">Nissan</option>
                                                    <option value="suzuki">Suzuki</option>
                                                    <option value="honda">Honda</option>
                                                    <option value="hyundai">Hyundai</option>
                                                    <option value="ford">Ford</option>
                                                    <option value="kia">Kia</option>
                                                    <option value="isuzu">Isuzu</option>
                                                    <option value="lexus">Lexus</option>
                                                    <option value="bmw">BMW</option>
                                                    <option value="hilux">Hilux</option>
                                                    <option value="subaru">Subaru</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>

                                            <!-- Other Brand (Hidden Initially) -->
                                            <div class="mb-3 d-none" id="Other_brand_field">
                                                <label for="Other_brand" class="form-label">Other Brand</label>
                                                <input type="text" id="Other_brand" name="Other_brand" class="form-control" placeholder="Enter other brand">
                                            </div>

                                            <!-- Vehicle Plate Number -->
                                            <div class="mb-3">
                                                <label for="Plate_no" class="form-label">Vehicle Plate Number</label>
                                                <input type="text" id="Plate_no" name="Plate_no" class="form-control" required placeholder="Enter vehicle plate number">
                                            </div>

                                            <!-- Date -->
                                            <div class="mb-3">
                                                <label for="Date" class="form-label">Date</label>
                                                <input type="date" id="Date" name="date" class="form-control" required>
                                            </div>

                                            <input type="hidden" name="Status" value="Operational">

                                            <!-- Modal Footer -->
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-success">Submit</button>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
        <!-- Bootstrap Modal -->
<div class="modal fade" id="vehicleDetailsModal" tabindex="-1" aria-labelledby="vehicleDetailsLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vehicleDetailsLabel"><b>Vehicle Details</b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Vehicle ID:</strong> <span id="modalVehicleId"></span></p>
                <p><strong>Vehicle Type:</strong> <span id="modalVehicleType"></span></p>
                <p><strong>Vehicle Color:</strong> <span id="modalVehicleColor"></span></p>
                <p><strong>Vehicle Brand:</strong> <span id="modalVehicleBrand"></span></p>
                <p><strong>Plate No.:</strong> <span id="modalPlateNo"></span></p>
                <p><strong>Status:</strong> <span id="modalStatus"></span></p>
            </div>
        </div>
    </div>
</div>

<!-- Combined Modal -->
<div class="modal fade" id="combinedModal" tabindex="-1" aria-labelledby="combinedModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="combinedModalTitle">Vehicle Management</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="status-tab" data-bs-toggle="tab" data-bs-target="#statusSection" 
                                role="tab" aria-controls="statusSection" aria-selected="true">
                            Update Status
                        </button>
                    </li>
                </ul>

                <div class="tab-content mt-3">
                    <!-- Status Update Section -->
                    <div class="tab-pane fade show active" id="statusSection" role="tabpanel" aria-labelledby="status-tab">
                        <form id="updateStatusForm">
                            <input type="hidden" name="VehicleID" id="vehicleIdInput">
                            <div class="mb-3">
                                <label for="vehicleStatus" class="form-label">Select Status</label>
                                <select class="form-select" id="vehicleStatus" name="Status" required>
                                    <option value="" disabled selected>Select a status</option>
                                    <option value="Under Maintenance">Under Maintenance</option>
                                    <option value="Reserved">Reserved</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Update Status</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


                                           

              
            </main>
        </div>
    </div>

    <script src="../sidebar.js"> </script>
    <script src="../JS/vh_status_maintenace.js"></script>  
    
</body>
</html>
