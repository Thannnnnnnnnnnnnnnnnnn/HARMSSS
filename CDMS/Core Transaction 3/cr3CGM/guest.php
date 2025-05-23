<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    // Redirect to login page
    header("Location: ../testing/login.php");
    exit;
}

$user = $_SESSION['user'];
$_SESSION['User_ID'] = $user['User_ID'];

?>



<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'header.php'; ?>
</head>

<body>

  <div class="flex min-h-screen w-full">

    <?php include __DIR__ . '/../partials/admin/sidebar.php'; ?>
    <?php include __DIR__ . '/../partials/admin/navbar.php'; ?>


    <!-- Main Content -->
    <main class="px-8 py-8">

      <?php if ($user['role'] === 'admin' || $user['role'] === 'manager' || $user['role'] === 'staff'): ?>
        <h2 class="text-center text-2xl font-bold text-gray-800 mb-4">Guest Table</h2>

        <div class="flex flex-wrap gap-4 justify-center">

          <!-- Stats Section -->

          <section class="flex justify-center gap-6 mb-8 overflow-x-auto">
            <?php
            include 'php/connection.php';

            // Count total checked-out guests
            $stmt = $conn->query("SELECT COUNT(*) FROM guests");
            $totalGuests = $stmt->fetchColumn();

            // Count total interactions
            $stmt = $conn->query("SELECT COUNT(*) FROM interactions");
            $totalInteractions = $stmt->fetchColumn();

            // Count total feedbacks
            $stmt = $conn->query("SELECT COUNT(*) FROM feedback");
            $totalFeedbacks = $stmt->fetchColumn();

            // Count total active users
            $stmt = $conn->query("SELECT COUNT(*) FROM guests WHERE status = 'active'");
            $totalActiveUsers = $stmt->fetchColumn();
            ?>

            <!-- Total Guests Card -->
            <div class="bg-white p-6 w-[250px] rounded-2xl border border-[#594423] shadow-md flex items-center gap-4">
              <div class="bg-[#F7E6CA] p-3 rounded-full">
                <i class="bx bx-user text-2xl"></i>
              </div>
              <div>
                <p class="text-sm text-[#6B4F38]">Total Guests</p>
                <p class="text-2xl font-bold"><?php echo $totalGuests; ?></p>
              </div>
            </div>

            <!-- Interactions Card -->
            <div class="bg-white p-6 w-[250px] rounded-2xl border border-[#594423] shadow-md flex items-center gap-4">
              <div class="bg-[#F7E6CA] p-3 rounded-full">
                <i class="bx bx-message-square-detail text-2xl"></i>
              </div>
              <div>
                <p class="text-sm text-[#6B4F38]">Interactions</p>
                <p class="text-2xl font-bold"><?php echo $totalInteractions; ?></p>
              </div>
            </div>

            <!-- Feedbacks Card -->
            <div class="bg-white p-6 w-[250px] rounded-2xl border border-[#594423] shadow-md flex items-center gap-4">
              <div class="bg-[#F7E6CA] p-3 rounded-full">
                <i class="bx bx-star text-2xl"></i>
              </div>
              <div>
                <p class="text-sm text-[#6B4F38]">Feedbacks</p>
                <p class="text-2xl font-bold"><?php echo $totalFeedbacks; ?></p>
              </div>
            </div>

          </section>



        </div>



        <div class="overflow-x-auto rounded-2xl shadow-md bg-white p-4">
          <?php
if (isset($_SESSION['error_message'])) {
    echo "<div class='alert alert-danger'>" . $_SESSION['error_message'] . "</div>";
    unset($_SESSION['error_message']);
}
?>

          <div class="grid grid-flow-col auto-cols-max gap-4">

            <!-- Button to Open Modal -->
            <button
              class="bg-[#F7E6CA] text-[#4E3B2A] rounded-md px-4 py-2 hover:bg-[#594423] hover:text-white transition checkout-btn m-2"
              data-bs-toggle="modal"
              data-bs-target="#registerGuestModal">
              + Register New Guest
            </button>

            <!-- Register Guest Modal -->
            <div class="modal fade" id="registerGuestModal" tabindex="-1" aria-labelledby="registerGuestModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                  <div class="modal-header">
                    <h5 class="modal-title" id="registerGuestModalLabel">Register New Guest</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>

                  <form action="php/add_guest.php" method="POST">
                    <div class="modal-body">
                      <div class="mb-3">
                        <label for="guest_name" class="form-label">Guest Name</label>
                        <input type="text" class="form-control" id="guest_name" name="guest_name" required>
                      </div>

                      <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                      </div>

                      <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                      </div>

                      <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                      </div>

                      <div class="mb-3">
                        <label for="birthday" class="form-label">Birthday</label>
                        <input type="date" class="form-control" id="birthday" name="birthday" required>
                      </div>

                      <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <input type="text" class="form-control" id="gender" name="gender" required>
                      </div>

                      <div class="mb-3">
                        <label for="nationality" class="form-label">Nationality</label>
                        <input type="text" class="form-control" id="nationality" name="nationality" required>
                      </div>


                    </div>

                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Save Guest</button>
                    </div>
                  </form>

                </div>
              </div>
            </div>


            <input type="text"
              class="bg-[#FFF6E8] h-10 rounded-lg grow w-[570px] pl-10 pr-4 focus:ring-2 focus:ring-[#F7E6CA] focus:outline-none mt-[7px] "
              placeholder="Search something..."
              aria-label="Search input"
              id="searchInput" />

            <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-[#4E3B2A]"></i>
          </div>

           <script src="js/search.js"></script>


          <table id="customerTable" class="min-w-full text-sm text-left text-gray-700">
            <thead class="bg-white text-[#4E3B2A] text-base border-b divide-gray-200">
              <tr>
                <th class="px-6 py-1">Guest ID</th>
                <th class="px-6 py-1">Guest Name</th>
                <th class="px-6 py-1">Email</th>
                <th class="px-6 py-1">Contact</th>
                <th class="px-6 py-1">Action</th>
              </tr>
            </thead>
            <tbody id="guestTbody" class="text-gray-800 divide-y divide-gray-200">
              <?php

              $stmt = $conn->prepare("SELECT * FROM guests  ORDER BY GuestID DESC");
              $stmt->execute();
              $guests = $stmt->fetchAll(PDO::FETCH_ASSOC);

              ?>
              <?php foreach ($guests as $guests): ?>
                <tr class="hover:bg-gray-100 transition">
                  <td class="px-6 py-1"><?php echo htmlspecialchars($guests['GuestID']); ?></td>
                  <td class="px-6 py-1"><?php echo htmlspecialchars($guests['guest_name']); ?></td>
                  <td class="px-6 py-1"><?php echo htmlspecialchars($guests['email']); ?></td>
                  <td class="px-6 py-1"><?php echo htmlspecialchars($guests['phone']); ?></td>
                  <td class="px-6 py-1 space-x-1 flex flex-wrap gap-1">

                    <div class="flex justify-center gap-1 mb-6">

                  

       <?php if ($user['role'] === 'staff' || $user['role'] === 'admin' || $user['role'] === 'manager'): ?>              

                 <!-- Button to open modal -->
<button id="openReservationModal<?= htmlspecialchars($guests['GuestID'] ?? '') ?>" 
  class="bg-[#F7E6CA] text-[#4E3B2A] px-5 py-2 rounded-[8px] border border-[#594423] transition hover:bg-[#e4d3b4] active:scale-95">
  <i class='bx bx-calendar-check' style="font-size: 24px; color: #4E3B2A;"></i>
</button>

<!-- Modal backdrop -->
<div id="reservationModal<?= htmlspecialchars($guests['GuestID'] ?? '') ?>" 
  class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">

  <!-- Modal container -->
  <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">

    <!-- Close button -->
    <button id="closeReservationModal<?= htmlspecialchars($guests['GuestID'] ?? '') ?>" 
      class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-xl font-bold">&times;</button>

    <!-- Form title -->
    <h2 class="text-xl font-semibold mb-4 text-center">Make a Reservation for <?= htmlspecialchars($guests['guest_name'] ?? '') ?></h2>

    <!-- Your form -->
    <form class="space-y-4 text-sm" method="POST" action="php/update_reservation.php">
      <input type="hidden" name="ReservationID" value="">
      <input type="hidden" name="GuestID" value="<?= htmlspecialchars($guests['GuestID'] ?? '') ?>">
<?php
      // Fetch all rooms
$stmtRoom = $conn->query("SELECT room_id, room_name, description, price, image, status FROM rooms");
$rooms = $stmtRoom->fetchAll();
?>

<!-- Room Selection - One Row -->
<div class="md:col-span-2 overflow-x-auto">
  <label class="block font-medium mb-4">Rooms</label>
  <div class="flex space-x-4 min-w-max">
    

    <?php foreach ($rooms as $room): 
        $status = $room['status'];
        $isAvailable = ($status === 'Available');
        
        // Set styling based on status
        if ($status === 'Available') {
            $borderColor = "border-[#594423] peer-checked:border-[#F7E6CA] peer-checked:ring-2 peer-checked:ring-[#F7E6CA]";
            $textColor = "text-green-600";
            $bgColor = "peer-checked:bg-[#F7E6CA]";
            $cursor = "cursor-pointer";
            $disabled = "";
        } elseif ($status === 'Occupied') {
            $borderColor = "border-red-600 cursor-not-allowed opacity-60";
            $textColor = "text-red-600";
            $bgColor = "bg-white";
            $cursor = "cursor-not-allowed";
            $disabled = "disabled";
        } elseif ($status === 'Under Maintenance') {
            $borderColor = "border-yellow-600 cursor-not-allowed opacity-60";
            $textColor = "text-yellow-600";
            $bgColor = "bg-white";
            $cursor = "cursor-not-allowed";
            $disabled = "disabled";
        } else {
            // Default fallback
            $borderColor = "border-gray-400 cursor-not-allowed opacity-60";
            $textColor = "text-gray-600";
            $bgColor = "bg-white";
            $cursor = "cursor-not-allowed";
            $disabled = "disabled";
        }
    ?>
    <label class="relative block w-60 rounded-[10px] overflow-hidden transition border <?= $borderColor ?> <?= $cursor ?>">
      <input type="radio" name="" value="<?= htmlspecialchars($room['room_id']) ?>" <?= $disabled ?> required
        class="absolute inset-0 w-full h-full opacity-0 z-10 <?= $isAvailable ? 'cursor-pointer peer' : '' ?>" />
      <img src="roomPhoto/<?= htmlspecialchars($room['image']) ?>" alt="<?= htmlspecialchars($room['room_name']) ?>" class="w-full h-40 object-cover">
      <div class="p-3 bg-white <?= $bgColor ?>">
        <h3 class="font-semibold"><?= htmlspecialchars($room['room_name']) ?></h3>
        <p class="text-sm text-gray-600"><?= htmlspecialchars($room['description']) ?></p>
        <p class="text-sm font-bold <?= $textColor ?>">₱<?= number_format($room['price'], 2) ?> - <?= htmlspecialchars($status) ?></p>
      </div>
      <?php if (!$isAvailable): ?>
        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center text-white font-bold text-lg select-none"><?= htmlspecialchars($status) ?></div>
      <?php endif; ?>
    </label>
    <?php endforeach; ?>

  </div>
</div>



      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Rooms and dates same as before -->
        <div class="md:col-span-2">
          <label class="block font-medium mb-1">Select Room</label>
          <select name="room" class="w-full border border-[#594423] rounded-[10px] p-2 focus:outline-none focus:ring-2 focus:ring-[#F7E6CA]" required>
            <option disabled selected>Select Room</option>
            <option value="1">Junior Suite - A cozy room with modern amenities - ₱1500.00 - Available</option>
            <option value="2">Executive Suite - Spacious room with executive features - ₱2500.00 - Available</option>
            <option value="3">Presidential Suite - Luxurious suite with premium facilities - ₱5000.00 - Occupied</option>
            <option value="4">Royal Suite - Ultimate comfort and royal treatment - ₱7500.00 - (Status unknown)</option>
            <option value="5">Penthouse Suite - Stunning views, butler service, world-class amenities - ₱10000.00 - (Status unknown)</option>
          </select>
        </div>

        <div>
          <label class="block font-medium mb-1">Check-in Date</label>
          <input type="date" name="check_in" class="w-full border border-[#594423] rounded-[10px] p-2 focus:outline-none focus:ring-2 focus:ring-[#F7E6CA]" required>
        </div>

        <div>
          <label class="block font-medium mb-1">Check-out Date</label>
          <input type="date" name="check_out" class="w-full border border-[#594423] rounded-[10px] p-2 focus:outline-none focus:ring-2 focus:ring-[#F7E6CA]" required>
        </div>
      </div>

      <div class="flex justify-center pt-4">
        <button type="submit" class="bg-[#F7E6CA] text-[#4E3B2A] px-5 py-2 rounded-[8px] border border-[#594423] transition hover:bg-[#e4d3b4] active:scale-95">
          Save Reservation
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  (function() {
    const guestId = <?= json_encode($guests['GuestID']) ?>; // safer for JS embedding

    const openBtn = document.getElementById('openReservationModal' + guestId);
    const closeBtn = document.getElementById('closeReservationModal' + guestId);
    const modal = document.getElementById('reservationModal' + guestId);

    openBtn.addEventListener('click', () => {
      modal.classList.remove('hidden');
    });

    closeBtn.addEventListener('click', () => {
      modal.classList.add('hidden');
    });

    window.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.classList.add('hidden');
      }
    });
  })();
</script>
<?php endif; ?>

<?php if ($user['role'] === 'admin' || $user['role'] === 'manager'): ?>  

                      <!-- View Button -->
                      <a href="#"
                        class="bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 view-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#viewModal"
                        data-id="<?php echo $guests['GuestID']; ?>"
                        data-name="<?php echo htmlspecialchars($guests['guest_name']); ?>"
                        data-email="<?php echo htmlspecialchars($guests['email']); ?>"
                        data-phone="<?php echo htmlspecialchars($guests['phone']); ?>"
                        data-address="<?php echo htmlspecialchars($guests['address']); ?>"
                        data-birthday="<?php echo htmlspecialchars($guests['date_of_birth']); ?>"
                        data-gender="<?php echo htmlspecialchars($guests['gender']); ?>"
                        data-nationality="<?php echo htmlspecialchars($guests['nationality']); ?>"
                        data-reservation="<?php echo htmlspecialchars($guests['reservation']); ?>"
                        data-status="<?php echo htmlspecialchars($guests['status']); ?>">
                        <i class="bx bx-show"></i>
                      </a>


                      <!-- Edit Button -->
                      <a href="#"
                        class="bg-green-500 text-white px-3 py-2 rounded-lg hover:bg-green-600 edit-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#editModal"
                        data-guest-id="<?php echo $guests['GuestID']; ?>"
                        data-name="<?php echo $guests['guest_name']; ?>"
                        data-email="<?php echo $guests['email']; ?>"
                        data-phone="<?php echo $guests['phone']; ?>"
                        data-address="<?php echo $guests['address']; ?>"
                        data-birthday="<?php echo date('Y-m-d', strtotime($guests['date_of_birth'])); ?>"
                        data-gender="<?php echo $guests['gender']; ?>"
                        data-nationality="<?php echo $guests['nationality']; ?>"
                        data-reservation="<?php echo $guests['reservation']; ?>"
                        data-status="<?php echo $guests['status']; ?>">
                        <i class="bx bx-edit"></i>
                      </a>

                      <!-- Delete Button -->
                      <button type="button"
                        onclick="openGuestDeleteModal('<?php echo $guests['GuestID']; ?>', '<?php echo htmlspecialchars($guests['guest_name'], ENT_QUOTES); ?>')"
                        class="bg-red-500 text-white px-3 py-2 rounded-lg hover:bg-red-600">
                        <i class="bx bx-trash"></i>
                      </button>
                    </div>

             <?php endif; ?>




                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <!-- No Guests Message -->
          <div id="noGuestsMessage" class="text-center text-gray-500 py-4 hidden">
            No guests are currently checked in.
          </div>
        </div>


        <!-- Modal Area -->

        <!-- View Modal -->
        <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Guest Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <p><strong>Guest ID:</strong> <span id="guestId"></span></p>
                <p><strong>Name:</strong> <span id="guestName"></span></p>
                <p><strong>Email:</strong> <span id="guestEmail"></span></p>
                <p><strong>Phone:</strong> <span id="guestPhone"></span></p>
                <p><strong>Address:</strong> <span id="guestAddress"></span></p>
                <p><strong>Birthday:</strong> <span id="guestBirthday"></span></p>
                <p><strong>Gender:</strong> <span id="guestGender"></span></p>
                <p><strong>Nationality:</strong> <span id="guestNationality"></span></p>
                <p><strong>Reservation :</strong> <span id="guestReservation"></span></p>
              </div>
            </div>
          </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Guest</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form action="php/update_guest.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                  <!-- Hidden Field for ID -->
                  <input type="hidden" id="editGuestId" name="GuestID">

                  <div class="mb-3">
                    <label for="editGuestName" class="form-label">Name</label>
                    <input type="text" class="form-control" id="editName" name="guest_name">
                  </div>

                  <div class="mb-3">
                    <label for="editEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="editEmail" name="email">
                  </div>

                  <div class="mb-3">
                    <label for="editPhone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="editPhone" name="phone">
                  </div>

                  <div class="mb-3">
                    <label for="editAddress" class="form-label">Address</label>
                    <textarea class="form-control" id="editAddress" name="address" rows="3"></textarea>
                  </div>

                  <div class="mb-3">
                    <label for="editBirthday" class="form-label">Birthday</label>
                    <input type="date" class="form-control" id="editBirthday" name="date_of_birth">
                  </div>

                  <div class="mb-3">
                    <label for="editGender" class="form-label">Gender</label>
                    <input type="text" class="form-control" id="editGender" name="gender">
                  </div>

                  <div class="mb-3">
                    <label for="editNationality" class="form-label">Nationality</label>
                    <input type="text" class="form-control" id="editNationality" name="nationality">
                  </div>

                </div>

                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <script>
  document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll('.edit-btn');

    editButtons.forEach(button => {
      button.addEventListener('click', function () {
        // Get data attributes
        const guestId = this.getAttribute('data-guest-id');
        const name = this.getAttribute('data-name');
        const email = this.getAttribute('data-email');
        const phone = this.getAttribute('data-phone');
        const address = this.getAttribute('data-address');
        const birthday = this.getAttribute('data-birthday');
        const gender = this.getAttribute('data-gender');
        const nationality = this.getAttribute('data-nationality');

        // Populate modal fields
        document.getElementById('editGuestId').value = guestId;
        document.getElementById('editName').value = name;
        document.getElementById('editEmail').value = email;
        document.getElementById('editPhone').value = phone;
        document.getElementById('editAddress').value = address;
        document.getElementById('editBirthday').value = birthday;
        document.getElementById('editGender').value = gender;
        document.getElementById('editNationality').value = nationality;
      });
    });
  });
</script>


        <!-- Delete Confirmation Modal for Guest -->
        <div id="guestDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
          <div class="bg-white p-6 rounded-lg shadow-lg w-[350px] text-center">
            <h2 class="text-xl font-bold mb-4">Confirm Deletion</h2>
            <p class="mb-4">
              Are you sure you want to delete guest <strong id="deleteGuestName"></strong>
              (ID: <strong id="deleteGuestID"></strong>)?
            </p>

            <!-- Delete Form -->
            <form id="guestDeleteForm" action="php/delete.php" method="POST">
              <input type="hidden" name="GuestID" id="guestID">
              <div class="flex justify-center space-x-4">
                <button type="button" onclick="closeGuestModal()" class="bg-gray-400 text-white px-4 py-2 rounded-lg">
                  Cancel
                </button>
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg">
                  Delete
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- End of Modal Area  -->
      <?php endif; ?>
      <!-- For Manager Staff and Admin  -->


      <!-- For Guest  -->
      <?php if ($user['role'] === 'guest'): ?>

       <div class="w-full mx-auto mt-10 bg-white border border-[#594423] rounded-[12px] shadow-md p-6">

          <h2 class="text-xl font-semibold text-[#4E3B2A] mb-4 text-center">My Information</h2>
          <?php

          // Make sure the database connection $conn is established


// Include DB connection
include('php/connection.php');

$userid = $user['User_ID'];

// Prepare the SQL query with a placeholder
$sqlGuestInfo = "SELECT * FROM guests WHERE user_id = ?";

$stmtGuestInfo = $conn->prepare($sqlGuestInfo);
$stmtGuestInfo->bindParam(1, $userid , PDO::PARAM_INT);
$stmtGuestInfo->execute();

$guest = $stmtGuestInfo->fetch(PDO::FETCH_ASSOC);

echo $userid;

          ?>


          <form class="space-y-4 text-sm" method="POST" action="php/update_customer.php">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <input type="hidden" name="GuestID" value="<?php echo htmlspecialchars($guest['GuestID']); ?>">
              <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userid) ?>">
              <!-- Full Name -->
              <div>
                <label class="block font-medium mb-1">Full Name</label>
                <input type="text" name="guest_name" value="<?= $guest['guest_name'] ?? '' ?>"
                  class="w-full border border-[#594423] rounded-[10px] p-2 focus:outline-none focus:ring-2 focus:ring-[#F7E6CA]"
                  required>
              </div>

              <!-- Email -->
              <div>
                <label class="block font-medium mb-1">Email</label>
                <input type="email" name="email" value="<?= $guest['email'] ?? '' ?>"
                  class="w-full border border-[#594423] rounded-[10px] p-2 focus:outline-none focus:ring-2 focus:ring-[#F7E6CA]"
                  required>
              </div>

              <!-- Phone -->
              <div>
                <label class="block font-medium mb-1">Phone Number</label>
                <input type="text" name="phone" value="<?= $guest['phone'] ?? '' ?>"
                  class="w-full border border-[#594423] rounded-[10px] p-2 focus:outline-none focus:ring-2 focus:ring-[#F7E6CA]"
                  required>
              </div>

              <!-- Address -->
              <div>
                <label class="block font-medium mb-1">Address</label>
                <input type="text" name="address" value="<?= $guest['address'] ?? '' ?>"
                  class="w-full border border-[#594423] rounded-[10px] p-2 focus:outline-none focus:ring-2 focus:ring-[#F7E6CA]"
                  required>
              </div>

              <!-- Gender -->
              <div>
                <label class="block font-medium mb-1">Gender</label>
                <select name="gender"
                  class="w-full border border-[#594423] rounded-[10px] p-2 focus:outline-none focus:ring-2 focus:ring-[#F7E6CA]"
                  required>
                  <option disabled <?php if (!isset($guest['gender'])) echo 'selected'; ?>>Select Gender</option>
                  <option value="Male" <?php if (isset($guest['gender']) && $guest['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                  <option value="Female" <?php if (isset($guest['gender']) && $guest['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                  <option value="Other" <?php if (isset($guest['gender']) && $guest['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                </select>
              </div>

              <!-- Date of Birth -->
              <div>
                <label class="block font-medium mb-1">Date of Birth</label>
                <input type="date" name="date_of_birth" value="<?php echo htmlspecialchars($guest['date_of_birth']); ?>"
                  class="w-full border border-[#594423] rounded-[10px] p-2 focus:outline-none focus:ring-2 focus:ring-[#F7E6CA]"
                  required>
              </div>


              <!-- Nationality -->
              <div class="md:col-span-2">
                <label class="block font-medium mb-1">Nationality</label>
                <input type="text" name="nationality" value="<?= $guest['nationality'] ?? '' ?>"
                  class="w-full border border-[#594423] rounded-[10px] p-2 focus:outline-none focus:ring-2 focus:ring-[#F7E6CA]"
                  required>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center pt-4">
              <button type="submit"
                class="bg-[#F7E6CA] text-[#4E3B2A] px-5 py-2 rounded-[8px] border border-[#594423] transition hover:bg-[#e4d3b4] active:scale-95">
                Save Changes
              </button>
            </div>
          </form>

       

       <?php


if ($guest && is_array($guest)) {
    $guestid = $guest['GuestID'];
    $guestname = $guest['guest_name'];
    $guestphone = $guest['phone'];

    $sql = "
    SELECT 
      r.reservation_id,
      g.phone AS guest_phone,
      r.checkin_date,
      r.checkout_date,
      r.guests,
      r.created_at,
      rm.room_name,
      rs.status
    FROM reservations r
    JOIN guests g 
      ON CONCAT(r.first_name, ' ', r.last_name) = g.guest_name
      AND CAST(r.phone AS CHAR) = g.phone
    JOIN rooms rm 
      ON r.room_id = rm.room_id
    JOIN reservationstatus rs
      ON r.reservation_id = rs.reservation_id
      AND rs.updated_at = (
        SELECT MAX(updated_at) 
        FROM reservationstatus 
        WHERE reservation_id = r.reservation_id
      )
    WHERE g.GuestID = :guestid
    ORDER BY r.created_at DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute(['guestid' => $guestid]);
    $reservations = $stmt->fetchAll();
} else {
    echo "<div class='text-red-500 font-semibold'>Please fill out the form above to make a reservation.</div>";
}

?>
    
  <h2 class="text-xl md:text-2xl font-semibold text-[#4E3B2A] mb-6 text-center mt-10">Reservation</h2>
  <?php
if (isset($_SESSION['error_message'])) {
    echo "<div class='alert alert-danger'>" . $_SESSION['error_message'] . "</div>";
    unset($_SESSION['error_message']);
}
?>


  <form class="space-y-4 text-sm" method="POST" action="php/update_reservation.php">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- Hidden Fields -->
      <input type="hidden" name="ReservationID" value="">
      <input type="hidden" name="GuestID" value="<?= $guestid ?>">

    <style>
  /* Extra style for checked room */
  input[type="radio"].peer:checked + img {
    /* maybe slightly dim the image or add border */
    border: 3px solid rgb(11, 215, 0); /* golden border */
  }

  input[type="radio"].peer:checked ~ div {
    background-color: #F7E6CA;
  }

  label.relative:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  }
</style>

<?php

// Fetch all rooms
$stmtRoom = $conn->query("SELECT room_id, room_name, description, price, image, status FROM rooms");
$rooms = $stmtRoom->fetchAll();
?>

<!-- Room Selection - One Row -->
<div class="md:col-span-2 overflow-x-auto">
  <label class="block font-medium mb-4">Select Room</label>
  <div class="flex space-x-4 min-w-max">
    

    <?php foreach ($rooms as $room): 
        $status = $room['status'];
        $isAvailable = ($status === 'Available');
        
        // Set styling based on status
        if ($status === 'Available') {
            $borderColor = "border-[#594423] peer-checked:border-[#F7E6CA] peer-checked:ring-2 peer-checked:ring-[#F7E6CA]";
            $textColor = "text-green-600";
            $bgColor = "peer-checked:bg-[#F7E6CA]";
            $cursor = "cursor-pointer";
            $disabled = "";
        } elseif ($status === 'Occupied') {
            $borderColor = "border-red-600 cursor-not-allowed opacity-60";
            $textColor = "text-red-600";
            $bgColor = "bg-white";
            $cursor = "cursor-not-allowed";
            $disabled = "disabled";
        } elseif ($status === 'Under Maintenance') {
            $borderColor = "border-yellow-600 cursor-not-allowed opacity-60";
            $textColor = "text-yellow-600";
            $bgColor = "bg-white";
            $cursor = "cursor-not-allowed";
            $disabled = "disabled";
        } else {
            // Default fallback
            $borderColor = "border-gray-400 cursor-not-allowed opacity-60";
            $textColor = "text-gray-600";
            $bgColor = "bg-white";
            $cursor = "cursor-not-allowed";
            $disabled = "disabled";
        }
    ?>
    <label class="relative block w-60 rounded-[10px] overflow-hidden transition border <?= $borderColor ?> <?= $cursor ?>">
      <input type="radio" name="room" value="<?= htmlspecialchars($room['room_id']) ?>" <?= $disabled ?> required
        class="absolute inset-0 w-full h-full opacity-0 z-10 <?= $isAvailable ? 'cursor-pointer peer' : '' ?>" />
      <img src="roomPhoto/<?= htmlspecialchars($room['image']) ?>" alt="<?= htmlspecialchars($room['room_name']) ?>" class="w-full h-40 object-cover">
      <div class="p-3 bg-white <?= $bgColor ?>">
        <h3 class="font-semibold"><?= htmlspecialchars($room['room_name']) ?></h3>
        <p class="text-sm text-gray-600"><?= htmlspecialchars($room['description']) ?></p>
        <p class="text-sm font-bold <?= $textColor ?>">₱<?= number_format($room['price'], 2) ?> - <?= htmlspecialchars($status) ?></p>
      </div>
      <?php if (!$isAvailable): ?>
        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center text-white font-bold text-lg select-none"><?= htmlspecialchars($status) ?></div>
      <?php endif; ?>
    </label>
    <?php endforeach; ?>

  </div>
</div>



      <!-- Check-in Date -->
      <div>
        <label class="block font-medium mb-1">Check-in Date</label>
        <input type="date" name="check_in" value=""
          class="w-full border border-[#594423] rounded-[10px] p-2 focus:outline-none focus:ring-2 focus:ring-[#F7E6CA]"
          required>
      </div>

      <!-- Check-out Date -->
      <div>
        <label class="block font-medium mb-1">Check-out Date</label>
        <input type="date" name="check_out" value=""
          class="w-full border border-[#594423] rounded-[10px] p-2 focus:outline-none focus:ring-2 focus:ring-[#F7E6CA]"
          required>
      </div>
</div>

    <!-- Submit Button -->
    <div class="flex justify-center pt-4">
      <button type="submit"
        class="bg-[#F7E6CA] text-[#4E3B2A] px-5 py-2 rounded-[8px] border border-[#594423] transition hover:bg-[#e4d3b4] active:scale-95">
        Save Reservation
      </button>
    </div>
  </form>

</div>



<!-- Trigger Modal Button -->
<div class="flex justify-center mt-8">
  <button type="button" onclick="document.getElementById('historyModal').classList.remove('hidden')"
    class="bg-[#F7E6CA] text-[#4E3B2A] px-5 py-2 rounded-[8px] border border-[#594423] transition hover:bg-[#e4d3b4] active:scale-95">
    View Reservation History
  </button>
</div>

<!-- Reservation History Modal -->
<div id="historyModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
  <div class="bg-white w-full max-w-4xl rounded-xl shadow-lg p-6 relative overflow-y-auto max-h-[90vh]">
    
    <!-- Close Button -->
    <button onclick="document.getElementById('historyModal').classList.add('hidden')" 
      class="absolute top-3 right-4 text-2xl font-bold text-gray-600 hover:text-black">&times;</button>


<h2 class="text-xl md:text-2xl font-semibold text-[#4E3B2A] mb-4 text-center">Reservation History</h2>



<?php if (!empty($reservations) && is_array($reservations)): ?>
  <div class="overflow-x-auto">
    <table class="min-w-full border border-[#594423] rounded-lg text-sm text-left">
      <thead class="bg-[#F7E6CA] text-[#4E3B2A]">
        <tr>
          <th class="px-4 py-2 border border-[#594423]">Room</th>
          <th class="px-4 py-2 border border-[#594423]">Check-in</th>
          <th class="px-4 py-2 border border-[#594423]">Check-out</th>
          <th class="px-4 py-2 border border-[#594423]">Guests</th>
          <th class="px-4 py-2 border border-[#594423]">Phone</th>
          <th class="px-4 py-2 border border-[#594423]">Date Created</th>
          <th class="px-4 py-2 border border-[#594423]">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($reservations as $row): ?>
          <tr class="bg-white hover:bg-[#F7E6CA]/50">
            <td class="px-4 py-2 border border-[#594423]"><?= htmlspecialchars($row['room_name']) ?></td>
            <td class="px-4 py-2 border border-[#594423]"><?= htmlspecialchars($row['checkin_date']) ?></td>
            <td class="px-4 py-2 border border-[#594423]"><?= htmlspecialchars($row['checkout_date']) ?></td>
            <td class="px-4 py-2 border border-[#594423]"><?= htmlspecialchars($guest['guest_name']) ?></td>
            <td class="px-4 py-2 border border-[#594423]"><?= htmlspecialchars($row['guest_phone']) ?></td>
            <td class="px-4 py-2 border border-[#594423]"><?= htmlspecialchars($row['created_at']) ?></td>
            <td class="px-4 py-2 border border-[#594423]"><?= htmlspecialchars($row['status']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <p class="text-center text-gray-600">No reservation history found.</p>
<?php endif; ?>



      <?php endif; ?>

      <!-- For Guest Page -->



  </div>

  </main>
  </div>
   
  <?php include 'footer.php'; ?>

  
</body>

</html>