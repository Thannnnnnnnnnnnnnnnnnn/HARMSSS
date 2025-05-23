
<?php include "../partials/admin/sidebar.php";
include "../partials/admin/head.php";
include "../partials/admin/navbar.php";
include "../partials/admin/footer.php";
?>

            <main class="px-4 py-4">

<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4 text-[#594423] text-center">Add New Reservation</h2>

    <?php



   $bookingDb = $connections["cr3_re"];
   $guestDb = $connections["cr3_re"];
     $usmDb = $connections["cr3_re"];
   $notif = $connections["cr3_re"]; 

   $query = "SELECT ChannelID, ChannelName FROM booking_channels WHERE ChannelType = 'Offline'";
   $result = mysqli_query($bookingDb, $query);
   $channels = [];
   while ($row = mysqli_fetch_assoc($result)) {
       $id = trim($row['ChannelID']);
       $name = trim($row['ChannelName']);
       $channels[] = ['id' => $id, 'name' => $name];
   }


   $guestQuery = "SELECT * FROM guests"; 
   $guestResult = mysqli_query($guestDb, $guestQuery);
    
  
   if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create'])) {
    $guest_name = $_POST['guest_name'];
    $check_in = date("Y-m-d", strtotime($_POST['check_in']));
    $check_out = date("Y-m-d", strtotime($_POST['check_out']));
    $room_number = $_POST['room_number'];
    $no_of_guests = $_POST['no_of_guests'];
    $status = $_POST['status'];
    $booking_channel_id = $_POST['booking_channel_id'];
    $special_requests = $_POST['special_requests'];
    $guest_preference = $_POST['Guest_preference']; 
    $preference_details = $_POST['preference_details'];
    $booking_channel_name = $_POST['booking_channel_name'];


    $stmt = $guestDb->prepare("INSERT INTO guests (guest_name) VALUES (?)");
    $stmt->bind_param("s", $guest_name);
    $stmt->execute();
    $guest_id = $stmt->insert_id;
    $stmt->close();

  
    $stmt = $bookingDb->prepare("INSERT INTO guest_preferences (PreferenceType, PreferenceDetail, GuestID) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $guest_preference, $preference_details, $guest_id); 
    $stmt->execute();
    $stmt->close();


  
    $stmt = $bookingDb->prepare("INSERT INTO booking (GuestID, CheckinDate, CheckOutDate, RoomNumber, NumberOfGuests, ReservationStatus, BookingChannelID, SpecialRequests) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssisss", $guest_id, $check_in, $check_out, $room_number, $no_of_guests, $status, $booking_channel_id, $special_requests);

    if ($stmt->execute()) {
      // d2 para sa notification
          $id=mysqli_insert_id($bookingDb);
          $role = mysqli_real_escape_string($notif, $role);
          $department = mysqli_real_escape_string($notif, $department);
          $email = mysqli_real_escape_string($notif, $email);
          
          $notifcation = $notif->prepare("INSERT INTO notifications (User_ID, notifType, message, status, Department, date_sent) 
                               VALUES (?, 'create', ?, 'Unread', ?, NOW())");
        $message = "$role Create New Booking $id";
        $notifcation->bind_param("iss", $user_id, $message, $department);
          $notifcation->execute();

// Transaction d2 boss
$desc = "$user_id Insert Booking Data";
$transac = $usmDb->prepare("INSERT INTO department_transaction (department_id, user_id , transaction_type, description) VALUES (?, ?, 'Insert', ?)");
$transac->bind_param("iis", $department_id, $user_id, $desc);
$transac->execute();
$transac->close();

// Audit trail d2 boss
$action = 'Insert';
$module = 'Booking';
$description = "$user_id Insert Booking Data";

$audit = $usmDb->prepare("INSERT INTO department_audit_trail 
    (department_id, user_id, action, department_affected, module_affected, description)
    VALUES (?, ?, ?, ?, ?, ?)");
$audit->bind_param("iissss", $department_id, $user_id, $action, $department, $module, $description);
$audit->execute();
$audit->close();


        echo "<div class='bg-green-100 text-green-800 p-2 mb-4 rounded'>Reservation added successfully!</div>";
    } else {
        echo "<div class='bg-red-100 text-red-800 p-2 mb-4 rounded'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}
    ?>


<script>
  const bookingChannels = <?php echo json_encode($channels); ?>;
</script>

<form method="POST" class="max-w-4xl mx-auto bg-white p-6 rounded-2xl shadow-md space-y-6">
 

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <div>
  <label class="block text-sm font-medium text-gray-700 mb-1">
    <i class="fas fa-user mr-2"></i> Guest Name
  </label>
  <input type="text" name="guest_name" required placeholder="e.g. Juan Dela Cruz" class="w-full border border-gray-300 p-2 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
</div>

<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">
    <i class="fas fa-door-open mr-2"></i> Room Number
  </label>
  <input type="text" name="room_number" required placeholder="e.g. 205" class="w-full border border-gray-300 p-2 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
</div>

 

  
    <div id="date-range-picker" date-rangepicker class="flex flex-col md:flex-row gap-4">
  

  <div class="relative w-full">
    <label for="check_in" class="block text-sm font-medium text-gray-700 mb-1 ">Check-In Date</label>
    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none pt-4">
      <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
      </svg>
    </div>
    <input id="datepicker-range-start" name="check_in" type="text"
           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5"
           placeholder="Select start date">
  </div>


  <div class="relative w-full">
    <label for="check_out" class="block text-sm font-medium text-gray-700 ">Check-Out Date</label>
    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none pt-4">
      <svg class="w-4 h-4 text-gray-500 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
        <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
      </svg>
    </div>
    <input id="datepicker-range-end" name="check_out" type="text"
           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5"
           placeholder="Select end date">
  </div>

</div>



<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">
    <i class="fas fa-users mr-2"></i> Number of Guests
  </label>
  <input 
    type="number" 
    name="no_of_guests" 
    required 
    placeholder="e.g. 2" 
    min="1" 
    max="6"
    oninput="if (this.value > 6) this.value = 6; if (this.value < 1) this.value = 1;"
    class="w-full border border-gray-300 p-2 rounded-lg focus:ring-blue-500 focus:border-blue-500" 
  />
</div>

<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">
    <i class="fas fa-tasks mr-2"></i> Reservation Status
  </label>
  <select name="status" required class=" text-center custom-select">
    <option value="">Select status</option>
    <option value="Pending">Pending</option>
    <option value="Confirmed">Confirmed</option>
        <option value="Paid">Paid</option>
  </select>
</div>

<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">
    <i class="fas fa-link mr-2"></i> Booking Channel
  </label>
  <select id="channel-id" name="booking_channel_id" class="text-center  custom-select">
    <option value="">Select a Channel</option>
    <?php foreach ($channels as $channel): ?>
      <option value="<?= $channel['id'] ?>">
        Channel <?= $channel['id'] ?>: <?= htmlspecialchars($channel['name']) ?>
      </option>
    <?php endforeach; ?>
  </select>
</div>

<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">
    <i class="fas fa-bookmark mr-2"></i> Channel Name
  </label>
  <input type="text" id="channel-name" name="booking_channel_name" readonly
         class="w-full bg-gray-100 border border-gray-300 p-2 rounded-lg text-gray-600" />
</div>

<div>
  <label class="block text-sm font-medium text-gray-700 mb-1">
    <i class="fas fa-heart mr-2"></i> Guest Preference
  </label>
  <select name="Guest_preference" required class="text-center  custom-select">
    <option value="">Select Preference</option>
    <option value="Room">Room</option>
    <option value="Food">Food</option>
    <option value="Services">Services</option>
    <option value="Other">Other</option>
  </select>
</div>

<div class="md:col-span-2">
  <label class="block text-sm font-medium text-gray-700 mb-1">
    <i class="fas fa-info-circle mr-2"></i> Preference Details
  </label>
  <input type="text" name="preference_details" placeholder="e.g.vegetarian meals, Room Place, Extra..." class="w-full border border-gray-300 p-2 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
</div>

<div class="md:col-span-2">
  <label class="block text-sm font-medium text-gray-700 mb-1">
    <i class="fas fa-sticky-note mr-2"></i> Special Requests
  </label>
  <input type="text" name="special_requests" placeholder="e.g. Late check-in, Deliver meals..." class="w-full border border-gray-300 p-2 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
</div>
</div>

<div class="text-left mt-4">
  <button type="submit" name="create" class="uppercase bg-[#F7E6CA] hover:bg-[#FFF6E8] text-[#594423] px-6 py-2 rounded-lg font-medium flex items-center justify-center gap-2">
    <i class="fas fa-plus"></i> Add Reservation
  </button>
</div>

</form>
            </main>
        </div>
<script>

 const channelMap = <?php
    $jsMap = [];
    foreach ($channels as $channel) {
        $jsMap[$channel['id']] = $channel['name'];
    }
    echo json_encode($jsMap);
  ?>;

  document.getElementById('channel-id').addEventListener('change', function () {
    const selectedId = this.value;
    document.getElementById('channel-name').value = channelMap[selectedId] || '';
  });
</script>

</body>
</html>
