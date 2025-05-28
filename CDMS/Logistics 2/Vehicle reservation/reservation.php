<?php
include("../../connection.php");

// Define the database name
$db_name = "logs2_vehicle_reservation_system";

if (!isset($connections[$db_name])) {
    die("Database connection not found for $db_name");
}

$connection = $connections[$db_name]; // Assign the correct connection


$role = $_SESSION['Role'];
$permissions = include '../role_permissions.php';
$allowed_modules = $permissions[$role] ?? [];

$result = "SELECT ReservationID , purpose, destination_from, destination_to, vehicle_type, departure, arrival, schedule, cargo, bill, status, VehicleID ,  DriverID , License plate, Make, Modell, Employee name, EmployeeID , User_ID, contact, Vehicle_color, Vehicle_brand, Plate_no, driver_name FROM reservation";
$result_sql = $connection->query($result);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $purpose = $_POST['purpose'] ?? '';
    $vehicle_type = $_POST['vehicle_type'] ?? '';
    $cargo = $_POST['cargo'] ?? '';
    $destination_to = $_POST['destination_to'] ?? '';
    $destination_from = $_POST['destination_from'] ?? '';
    $schedule = $_POST['schedule'] ?? '';
    $arrival = $_POST['arrival'] ?? '';
    $departure = $_POST['departure'] ?? '';
    $status = $_POST['status'] ?? '';
}

// Unified query to count various reservation statuses
$query = "SELECT 
        (SELECT COUNT(*) FROM reservation) AS total_reservation,
        (SELECT COUNT(*) FROM reservation WHERE status = 'Reserved') AS Completed,
        (SELECT COUNT(*) FROM reservation WHERE status = 'Failed') AS Failed,
        (SELECT COUNT(*) FROM reservation WHERE status = 'Dispatch') AS Dispatch,
        (SELECT COUNT(*) FROM reservation WHERE status = 'Pending for approval') AS Pending,
        (SELECT COUNT(*) FROM reservation WHERE status = 'Cancelled') AS Cancelled
";

$result = mysqli_query($connection, $query);

if (!$result) {
    die("Count query failed: " . mysqli_error($connection));
}

// Fetch the counts
$row = mysqli_fetch_assoc($result);
$total_reservation_count = $row['total_reservation'];
$completed_count = $row['Completed'];
$failed_count = $row['Failed'];
$in_transit_count = $row['Dispatch'];
$pending_count = $row['Pending'];
$cancelled_count = $row['Cancelled'];

// Query to fetch all reservations
$query = "SELECT * FROM `reservation`";
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
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
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
<!-- Make sure this is in your <head> -->

<div class="flex justify-center w-full">
  <!-- Dashboard Container -->
  <div class="flex flex-wrap justify-center gap-6 p-4 dashboard-cards">

    <!-- Total Reservation -->
    <a href="reservation.php" class="dashboard-card hover:shadow-lg transition duration-300 cursor-pointer block w-72">
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
    <a href="sub-modules/reserved.php" class="dashboard-card hover:shadow-lg transition duration-300 cursor-pointer block w-72">
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
    <a href="sub-modules/failed_reservation.php?" class="dashboard-card hover:shadow-lg transition duration-300 cursor-pointer block w-72">
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
    <a href="sub-modules/dispatch.php?" class="dashboard-card hover:shadow-lg transition duration-300 cursor-pointer block w-72">
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
    <a href="sub-modules/reservation_pending.php?" class="dashboard-card hover:shadow-lg transition duration-300 cursor-pointer block w-72">
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
    <a href="sub-modules/cancelled_reservation.php?" class="dashboard-card hover:shadow-lg transition duration-300 cursor-pointer block w-72">
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

                        
<!-- Button to Open Modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#vehicleReservationModal">
    Add reservation
</button>
<br><br>


                        <?php if ($result && mysqli_num_rows($result) > 0) : ?>              
                            <table class="styled-table w-full border-collapse border border-gray-300">
                            <thead>
        <tr>
        <th><i class='bx bx-barcode mr-1'></i>Reservation ID</th>
    <th><i class='bx bx-map-pin mr-1'></i>Destination</th>
    <th><i class='bx bx-calendar-event mr-1'></i>Schedule</th>
    <th><i class='bx bx-notepad mr-1'></i>Purpose</th>
    <th><i class='bx bx-car mr-1'></i>Vehicle Type</th>
    <th><i class='bx bx-info-circle mr-1'></i>Status</th>
        </tr>
    </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)) : ?>
                <tr class="reservation-row" 
                data-reservation-id="<?php echo $row['ReservationID']; ?>"
                data-destination="<?php echo $row['destination_to']; ?>"
                data-schedule="<?php echo $row['schedule']; ?>"
                data-purpose="<?php echo $row['purpose']; ?>"
                data-vehicle-type="<?php echo $row['vehicle_type']; ?>"
                data-status="<?php echo $row['status']; ?>">
                



                <td><?php echo $row['ReservationID']; ?></td>
                <td><?php echo $row['destination_to']; ?></td>
                <td><?php echo $row['schedule']; ?></td>
                <td><?php echo $row['purpose']; ?></td>
                <td><?php echo $row['vehicle_type']; ?></td>
                <td class="no-click"><?php echo $row['status']; ?></td>
            </tr>
            </tr>
                                    <?php endwhile; ?>
                                </table>
                                </div>
                                <?php else : ?>
                                <p>No records found</p>
                                <?php endif; ?>

                            </div>
                            </div>

<!-- Reservation Details Modal -->
<div class="modal fade" id="reservationDetailsModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reservationModalLabel"><i class="fas fa-file-alt"></i> Reservation Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p><i class='bx bx-barcode mr-1'></i><strong>Reservation ID:</strong> <span id="modalReservationID"></span></p>
                <p><i class='bx bx-map-pin mr-1'></i><strong>Destination:</strong> <span id="modalDestination"></span></p>
                <p><i class='bx bx-calendar-event mr-1'></i><strong>Schedule:</strong> <span id="modalSchedule"></span></p>
                <p><i class='bx bx-notepad mr-1'></i><strong>Purpose:</strong> <span id="modalPurpose"></span></p>
                <p><i class='bx bx-car mr-1'></i><strong>Vehicle Type:</strong> <span id="modalVehicleType"></span></p>
                <p><i class='bx bx-info-circle mr-1'></i><strong>Status:</strong> <span id="modalStatus"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Vehicle Reservation Modal -->
<div class="modal fade" id="vehicleReservationModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">
                    <i class="fas fa-car"></i> Vehicle Reservation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="vehicleReservationForm" method="POST" action="add_reservation.php">

                    <!-- Purpose -->
                    <div class="mb-3">
                        <label for="purpose" class="form-label"><i class="fas fa-tasks"></i> Purpose</label>
                        <select class="form-select" id="purpose" name="purpose" onchange="toggleVehicleDropdown()">
                            <option selected disabled>Select Purpose</option>
                            <option value="For guest transportation">Guest Transport</option>
                            <option value="For staff transportation">Staff Transport</option>
                            <option value="For logistic transportation">Logistic Transport</option>
                        </select>
                    </div>

                    <!-- Guest/Staff Vehicle Types -->
                    <div class="mb-3 vehicle-dropdown" id="guestStaffDropdown" style="display: none;">
                        <label for="guestStaffVehicle" class="form-label"><i class="fas fa-car-side"></i> Vehicle Type</label>
                        <select class="form-select" id="guestStaffVehicle" onchange="updateVehicleType(); calculateBill();">
                            <option selected disabled>Select Vehicle Type</option>
                            <option value="4 Seater">4 Seater</option>
                            <option value="6 Seater">6 Seater</option>
                            <option value="10 Seater">10 Seater</option>
                        </select>
                    </div>

                    <!-- Logistic Vehicle Types -->
                    <div class="mb-3 vehicle-dropdown" id="logisticDropdown" style="display: none;">
                        <label for="logisticVehicle" class="form-label"><i class="fas fa-truck"></i> Vehicle Type</label>
                        <select class="form-select" id="logisticVehicle" onchange="updateVehicleType(); toggleCargoDropdown(); calculateBill();">
                            <option selected disabled>Select Vehicle Type</option>
                            <option value="Small Van (7ft)">Small Van (7ft)</option>
                            <option value="Large Van (12ft)">Large Van (12ft)</option>
                            <option value="Small Truck">Small Truck</option>
                            <option value="Large Truck">Large Truck</option>
                        </select>
                    </div>

                    <!-- Cargo Type -->
                    <div class="mb-3" id="cargoDropdown" style="display: none;">
                        <label for="cargo" class="form-label"><i class="fas fa-box"></i> Type of Cargo</label>
                        <select class="form-select" id="cargo" name="cargo">
                            <option selected disabled>Select Cargo Type</option>
                            <option value="Perishable">Perishable Goods</option>
                            <option value="Non-Perishable">Non-Perishable Goods</option>
                            <option value="Fragile">Fragile Items</option>
                        </select>
                    </div>

                    <!-- From / To -->
                    <div class="mb-3">
                        <label for="destination_from" class="form-label"><i class="fas fa-map-marker-alt"></i> From</label>
                        <input type="text" class="form-control" id="destination_from" name="destination_from" placeholder="Enter starting location">
                    </div>
                    <div class="mb-3">
                        <label for="destination_to" class="form-label"><i class="fas fa-map-marker-alt"></i> To</label>
                        <input type="text" class="form-control" id="destination_to" name="destination_to" placeholder="Enter destination">
                    </div>

                    <!-- Schedule -->
                    <div class="mb-3">
                        <label for="schedule" class="form-label"><i class="fas fa-calendar-alt"></i> Schedule</label>
                        <input type="date" class="form-control" id="schedule" name="schedule" onchange="calculateBill()">
                    </div>

                    <!-- Departure / Arrival -->
                    <div class="mb-3">
                        <label for="departure" class="form-label"><i class="fas fa-clock"></i> Time of Departure</label>
                        <input type="time" class="form-control" id="departure" name="departure" onchange="calculateBill()">
                    </div>
                    <div class="mb-3">
                        <label for="arrival" class="form-label"><i class="fas fa-clock"></i> Time of Arrival</label>
                        <input type="time" class="form-control" id="arrival" name="arrival" onchange="calculateBill()">
                    </div>

                    <!-- Select Vehicle from DB -->
                    <div class="mb-3">
                        <label for="VehicleID" class="form-label"><i class="fas fa-car"></i> Select Vehicle</label>
                        <select class="form-select" id="VehicleSelect" name="VehicleID" onchange="updateVehicleInfo()" required>
                            <option value="">-- Select Vehicle --</option>
                            <?php
                            include("../../../connection.php");
                            $db_name = "logs2_vehicle_reservation_system";
                            if (!isset($connections[$db_name])) {
                                die("Database connection not found for $db_name");
                            }
                            $connection = $connections[$db_name];
                            $query = "SELECT VehicleID, Vehicle_brand, Vehicle_type, Status FROM vehicles";
                            $result = $connection->query($query);
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()):
                                    $status = $row['Status'];
                                    $disabled = '';
                                    switch ($status) {
                                        case "In use":
                                        case "Maintenance":
                                        case "Reserved":
                                            $disabled = 'disabled';
                                            break;
                                        case "Pending":
                                            break;
                                        default:
                                            $disabled = 'disabled';
                                            break;
                                    }
                            ?>
                            <option value="<?php echo $row['VehicleID']; ?>"
                                    data-type="<?php echo htmlspecialchars($row['Vehicle_type'], ENT_QUOTES); ?>"
                                    <?php echo $disabled; ?>>
                                <?php echo htmlspecialchars($row['Vehicle_brand']) . " (" . $row['Vehicle_type'] . ") - " . $status; ?>
                            </option>
                            <?php
                                endwhile;
                            } else {
                                echo "<option disabled>No vehicles available</option>";
                            }
                            ?>
                        </select>
                    </div>

                <!-- Select Driver from DB -->
<div class="mb-3">
    <label for="DriverID" class="form-label"><i class="fas fa-user"></i> Select Driver</label>
    <select class="form-select" id="DriverID" name="DriverID" required>
        <option value="">-- Select Driver --</option>
        <?php
        // Query available drivers
        $driverQuery = "SELECT DriverID, driver_name, Status FROM drivers WHERE Status = 'Available'";
        $driverResult = $connection->query($driverQuery);

        if ($driverResult && $driverResult->num_rows > 0) {
            while ($driver = $driverResult->fetch_assoc()):
        ?>
        <option value="<?php echo $driver['DriverID']; ?>">
            <?php echo htmlspecialchars($driver['driver_name']) . " - " . $driver['Status']; ?>
        </option>
        <?php
            endwhile;
        } else {
            echo "<option disabled>No available drivers</option>";
        }
        ?>
    </select>
</div>


                    <!-- Bill -->
                    <div class="mb-3" id="billSection" style="display: none;">
                        <label for="bill" class="form-label"><i class="fas fa-receipt"></i> Estimated Bill (₱)</label>
                        <input type="text" class="form-control" id="bill" readonly>
                    </div>

                    <!-- Hidden vehicle_type input -->
                    <input type="hidden" name="vehicle_type" id="vehicle_type_hidden">

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="submit" class="btn btn-success" form="vehicleReservationForm">
                    <i class="fas fa-check"></i> Reserve Vehicle
                </button>
            </div>
        </div>
    </div>
</div>
              
            </main>
        </div>
    </div>

    <script src="../JS/sidebar.js"> </script>
<script src="../reservation.js"></script>  


</body>
</html>
