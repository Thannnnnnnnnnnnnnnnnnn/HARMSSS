<?php
// No whitespace or output before this
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

require '../Database.php';
require '../functions.php';
$config = require '../config.php';

$db = new Database($config['database']);

$user = $_SESSION['user'];


$reservations = $db->query('SELECT * FROM reservations')->fetchAll();
$status = $db->query('SELECT * FROM reservationstatus')->fetchAll();

$col = $db->query('SELECT reservations.reservation_id, reservations.first_name, reservations.last_name, reservations.room_id, reservations.guests, reservations.created_at, reservationstatus.status
FROM reservations JOIN reservationstatus ON reservations.reservation_id = reservationstatus.reservation_id')->fetchAll();

// Include partials after all logic
require '../partials/admin/head.php';
require '../partials/admin/sidebar.php';
require '../partials/admin/navbar.php';
?>

<?php switch ($user['role']):
    case 'admin': ?>


        <main class="px-8 py-8">
            <div class="text-center p-1 text-lg font-bold rounded-lg mb-6 bg-[#F7E6CA] shadow-2xl ">
                <h1>DASHBOARD</h1>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">

                <div class="max-w-sm p-6 bg-[#F7E6CA] shadow-2xl sm:rounded-lg rounded-lg ">
                    <h5 class=" mb-2  text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Reservation Status<i class="ml-14 fa-solid fa-calendar-check"></i></h5>
                    <p class="mb-3 font-Georgia text-2xl">
                        <?= count($reservations) ?>
                    </p>
                    <button class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-[#594423] rounded-lg hover:bg-[#4E3B2A] focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" id="reservation">
                        View
                    </button>
                </div>
                <div class="max-w-sm p-6 bg-[#F7E6CA] shadow-2xl sm:rounded-lg rounded-lg ">
                    <h5 class="mb-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Rooms Status<i class="fa-solid fa-bed ml-20"></i></h5>
                    <p class="mb-3 font-normal text-2xl">
                        <?= count($status) ?>
                    </p>
                    <a href="cr3re/rooms.php" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-[#594423] rounded-lg hover:bg-[#4E3B2A] focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        View
                    </a>
                </div>
                <div class="max-w-sm p-6 bg-[#F7E6CA] shadow-2xl sm:rounded-lg rounded-lg ">
                    <h5 class="mb-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Total Reservation<i class="fa-solid fa-calendar ml-14"></i></h5>
                    <p class="mb-3 font-normal text-2xl">
                        <?= count($reservations) ?>
                    </p>
                    <a href="#" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-[#594423] rounded-lg hover:bg-[#4E3B2A]  focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        View
                    </a>
                </div>
            </div>
            <br>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">

                <div class="max-w-sm p-6 bg-[#F7E6CA] shadow-2xl sm:rounded-lg rounded-lg ">
                    <h5 class=" mb-2  text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Reservation Status<i class="ml-14 fa-solid fa-calendar-check"></i></h5>
                    <p class="mb-3 font-Georgia text-2xl">
                        <?= count($reservations) ?>
                    </p>
                    <a href="rs.php" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-[#594423] rounded-lg hover:bg-[#4E3B2A] focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        View
                    </a>
                </div>
                <div class="max-w-sm p-6 bg-[#F7E6CA] shadow-2xl sm:rounded-lg rounded-lg ">
                    <h5 class="mb-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Rooms Status<i class="fa-solid fa-bed ml-20"></i></h5>
                    <p class="mb-3 font-normal text-2xl">
                        <?= count($status) ?>
                    </p>
                    <a href="rooms.php" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-[#594423] rounded-lg hover:bg-[#4E3B2A] focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        View
                    </a>
                </div>
                <div class="max-w-sm p-6 bg-[#F7E6CA] shadow-2xl sm:rounded-lg rounded-lg ">
                    <h5 class="mb-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Total Reservation<i class="fa-solid fa-calendar ml-14"></i></h5>
                    <p class="mb-3 font-normal text-2xl">
                        <?= count($reservations) ?>
                    </p>
                    <a href="#" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-[#594423] rounded-lg hover:bg-[#4E3B2A]  focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        View
                    </a>
                </div>
            </div>
            <br>

            <!--MAIN TABLE FORMAT0-->
            <br>
            <div class="relative overflow-x-auto shadow-2xl sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-[#F7E6CA] dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Reservation ID
                            </th>
                            <th scope="col" class="px-6 py-3">
                                First Name
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Last Name
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Room ID
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Guest(s)
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3">
                                created
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($col as $cols):
                        ?>



                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <?= $cols['reservation_id'] ?>
                                </th>
                                <td class="px-6 py-4">
                                    <?= $cols['first_name'] ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $cols['last_name'] ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $cols['room_id'] ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $cols['guests'] ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $cols['status'] ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $cols['created_at'] ?>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <br>
            <div class="relative overflow-x-auto shadow-2xl sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-[#F7E6CA] dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Reservation ID
                            </th>
                            <th scope="col" class="px-6 py-3">
                                First Name
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Last Name
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Room ID
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Guest(s)
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3">
                                created
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($col as $cols):
                        ?>



                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <?= $cols['reservation_id'] ?>
                                </th>
                                <td class="px-6 py-4">
                                    <?= $cols['first_name'] ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $cols['last_name'] ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $cols['room_id'] ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $cols['guests'] ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $cols['status'] ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $cols['created_at'] ?>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <br>

            <!--------------------------------------------------------------manager-------------------------------------------------------------->
        <?php break;
    case 'manager': ?>
            <h3 class="font-semibold">Manager Dashboard</h3>
            <ul class="list-disc ml-6 mt-2">
                <li>Manage Staff</li>
                <li>Assign Tasks</li>
            </ul>
            <!-------------------------------------------------------------------STAFF-------------------------------------------------------------->
        <?php break;
    case 'staff': ?>
            <h3 class="font-semibold">Staff Dashboard</h3>
            <ul class="list-disc ml-6 mt-2">
                <li>Daily Tasks</li>
            </ul>
            <!-------------------------------------------------------------------GUEST-------------------------------------------------------------->
        <?php break;
    case 'guest': ?>

            <div class="text-center mt-5 p-1 text-lg font-bold rounded-lg mb-6 bg-[#F7E6CA] shadow-2xl ">
                <h1>DASHBOARD</h1>
            </div>

            <!-- Guest Dashboard Header -->
<section class="w-full max-w-7xl mx-auto px-4 py-6 bg-white rounded-[16px] shadow-lg border border-[#594423] mt-4">
  <h2 class="text-2xl font-bold text-gray-800 mb-4">Welcome to Your Dashboard</h2>
  <p class="text-sm text-gray-600">
    Here you can select a room, leave feedback, and stay updated with important guest information. If you need assistance, our staff is always here to help.
  </p>
</section>


            <?php
// Fetch all rooms
$rooms = $db->query("SELECT room_id, room_name, description, price, image, status FROM rooms")->fetchAll();
?>

<<!-- Room Selection Wrapper Matching Guest Feedback Style -->
<section class="w-full max-w-7xl mx-auto px-4 py-6 bg-white rounded-[16px] shadow-lg border border-[#594423] mt-4">
  

  <!-- Room Selection - One Row -->
  <div class="md:col-span-2 overflow-x-auto">
    <label class="block font-medium mb-4"></label>
    <div class="flex space-x-4 min-w-max">
      <?php foreach ($rooms as $room): 
        $status = $room['status'];
        $isAvailable = ($status === 'Available');

        // Set styling based on status
        if ($isAvailable) {
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
          $borderColor = "border-gray-400 cursor-not-allowed opacity-60";
          $textColor = "text-gray-600";
          $bgColor = "bg-white";
          $cursor = "cursor-not-allowed";
          $disabled = "disabled";
        }
      ?>
      <label class="relative block w-60 rounded-[10px] overflow-hidden transition border <?= $borderColor ?> <?= $cursor ?>">
        <input 
          type="radio" 
          name="room" 
          value="<?= htmlspecialchars($room['room_id']) ?>" 
          <?= $disabled ?> 
          required
          class="absolute inset-0 w-full h-full opacity-0 z-10 <?= $isAvailable ? 'cursor-pointer peer' : '' ?>" 
        />
        <img 
          src="../cr3CGM/roomPhoto/<?= htmlspecialchars($room['image']) ?>" 
          alt="<?= htmlspecialchars($room['room_name']) ?>" 
          class="w-full h-40 object-cover"
        >
        <div class="p-3 bg-white <?= $bgColor ?>">
          <h3 class="font-semibold"><?= htmlspecialchars($room['room_name']) ?></h3>
          <p class="text-sm text-gray-600"><?= htmlspecialchars($room['description']) ?></p>
          <p class="text-sm font-bold <?= $textColor ?>">
            ₱<?= number_format($room['price'], 2) ?> - <?= htmlspecialchars($status) ?>
          </p>
        </div>
        <?php if (!$isAvailable): ?>
        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center text-white font-bold text-lg select-none">
          <?= htmlspecialchars($status) ?>
        </div>
        <?php endif; ?>
      </label>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Guest Quick Info Cards -->
<section class="w-full max-w-7xl mx-auto px-4 py-6 bg-white rounded-[16px] shadow-lg border border-[#594423] mt-9">
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    
    <!-- Stay Duration Card -->
    <div class="border border-[#594423] rounded-[12px] p-4 bg-[#F7E6CA]">
      <h3 class="text-lg font-semibold text-[#4E3B2A] mb-1">Stay Duration</h3>
      <p class="text-sm text-gray-700">Your current stay is from <span class="font-medium">May 18</span> to <span class="font-medium">May 20</span>. Enjoy your stay!</p>
    </div>

    <!-- Room Policy Reminder Card -->
    <div class="border border-[#594423] rounded-[12px] p-4 bg-[#F7E6CA]">
      <h3 class="text-lg font-semibold text-[#4E3B2A] mb-1">Room Policies</h3>
      <p class="text-sm text-gray-700">No smoking, no loud noises after 10 PM, and kindly report any damages immediately.</p>
    </div>

    <!-- Guest Services Card -->
    <div class="border border-[#594423] rounded-[12px] p-4 bg-[#F7E6CA]">
      <h3 class="text-lg font-semibold text-[#4E3B2A] mb-1">Guest Services</h3>
      <p class="text-sm text-gray-700">Need help? Contact reception by dialing <span class="font-medium">101</span> or visit the front desk anytime.</p>
    </div>

  </div>
</section>

<!-- Why Guests Love Our Hotel Section -->
<section class="w-full max-w-7xl mx-auto px-4 py-6 bg-white rounded-[16px] shadow-lg border border-[#594423] mt-9">
  <h2 class="text-xl font-bold text-gray-800 mb-6">Why Guests Love Staying With Us</h2>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    <!-- Comfort and Cleanliness -->
    <div class="border border-[#594423] bg-[#F7E6CA] rounded-[12px] p-4">
      <h3 class="text-lg font-semibold text-[#4E3B2A] mb-1">Comfort & Cleanliness</h3>
      <p class="text-sm text-gray-700">
        Experience cozy rooms with hotel-grade linens and pristine cleanliness in every corner of the hotel.
      </p>
    </div>

    <!-- Prime Location -->
    <div class="border border-[#594423] bg-[#F7E6CA] rounded-[12px] p-4">
      <h3 class="text-lg font-semibold text-[#4E3B2A] mb-1">Prime Location</h3>
      <p class="text-sm text-gray-700">
        Located near city attractions, business centers, and transport hubs — convenience is right outside your door.
      </p>
    </div>

    <!-- 24/7 Service -->
    <div class="border border-[#594423] bg-[#F7E6CA] rounded-[12px] p-4">
      <h3 class="text-lg font-semibold text-[#4E3B2A] mb-1">24/7 Friendly Service</h3>
      <p class="text-sm text-gray-700">
        Our staff is always ready to assist you with anything you need — day or night.
      </p>
    </div>

    <!-- In-House Dining -->
    <div class="border border-[#594423] bg-[#F7E6CA] rounded-[12px] p-4">
      <h3 class="text-lg font-semibold text-[#4E3B2A] mb-1">Delicious In-House Dining</h3>
      <p class="text-sm text-gray-700">
        Enjoy local and international dishes made fresh by our experienced chefs, available right inside the hotel.
      </p>
    </div>

    <!-- Secure & Safe -->
    <div class="border border-[#594423] bg-[#F7E6CA] rounded-[12px] p-4">
      <h3 class="text-lg font-semibold text-[#4E3B2A] mb-1">Safe & Secure</h3>
      <p class="text-sm text-gray-700">
        Your safety is our priority — with round-the-clock security and monitored premises.
      </p>
    </div>

    <!-- Relaxation Amenities -->
    <div class="border border-[#594423] bg-[#F7E6CA] rounded-[12px] p-4">
      <h3 class="text-lg font-semibold text-[#4E3B2A] mb-1">Relaxation Amenities</h3>
      <p class="text-sm text-gray-700">
        Take a break at our spa, lounge, or open garden — perfect for unwinding during your stay.
      </p>
    </div>

  </div>
</section>




        <?php break;
    default: ?>
            <h3 class="font-semibold">Unknown Role</h3>
            <p>Please contact the administrator.</p>




        </main>

<?php endswitch; ?>
<?= require '../partials/admin/footer.php'; ?>