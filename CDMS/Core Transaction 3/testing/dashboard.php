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
include $_SERVER['DOCUMENT_ROOT'] . "/cr3/partials/admin/sidebar.php";
require '../partials/admin/navbar.php';
include $_SERVER['DOCUMENT_ROOT'] . "/cr3/partials/admin/footer.php";

?>
   <main class="px-8 py-8">
<?php switch ($user['role']):
    case 'admin': ?>


       
            <!-- DASHBOARD TITLE -->
<div class="text-center mb-10">
  <h1 class="text-3xl font-bold text-[#594423] bg-[#F7E6CA] py-3 rounded-lg shadow-xl">DASHBOARD</h1>
</div>

<!-- STATS CARDS GRID -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
  <!-- Reservation Status -->
  <div class="bg-[#F7E6CA] p-6 rounded-2xl shadow-xl">
    <div class="flex items-center justify-between mb-2">
      <h2 class="text-xl font-bold text-[#594423]">Reservation Status</h2>
      <i class="fa-solid fa-calendar-check text-2xl text-[#594423]"></i>
    </div>
    <p class="text-3xl font-semibold text-[#4E3B2A]"><?= count($reservations) ?></p>
    <a href="../cr3re/rs.php" class="mt-4 block w-full py-2 text-center text-white bg-[#594423] rounded-lg hover:bg-[#4E3B2A] transition">
  View
</a>

  </div>

  <!-- Rooms Status -->
  <div class="bg-[#F7E6CA] p-6 rounded-2xl shadow-xl">
    <div class="flex items-center justify-between mb-2">
      <h2 class="text-xl font-bold text-[#594423]">Rooms Status</h2>
      <i class="fa-solid fa-bed text-2xl text-[#594423]"></i>
    </div>
    <p class="text-3xl font-semibold text-[#4E3B2A]"><?= count($status) ?></p>
    <a href="../cr3re/rooms.php" class="block mt-4 w-full text-center py-2 text-white bg-[#594423] rounded-lg hover:bg-[#4E3B2A] transition">View</a>
  </div>

  <!-- Total Reservations -->
  <div class="bg-[#F7E6CA] p-6 rounded-2xl shadow-xl">
    <div class="flex items-center justify-between mb-2">
      <h2 class="text-xl font-bold text-[#594423]">Total Reservations</h2>
      <i class="fa-solid fa-calendar text-2xl text-[#594423]"></i>
    </div>
    <p class="text-3xl font-semibold text-[#4E3B2A]"><?= count($reservations) ?></p>
    <a href="../cr3re/rs.php" class="mt-4 block w-full py-2 text-center text-white bg-[#594423] rounded-lg hover:bg-[#4E3B2A] transition">
  View
</a>
  </div>
</div>

<!-- MAIN TABLE -->
<div class="max-h-[400px] overflow-y-auto overflow-x-auto rounded-2xl mb-9">
  <table class="w-full text-sm text-left text-gray-700">
    <thead class="text-xs uppercase bg-[#F7E6CA] text-[#594423]">
      <tr>
        <th class="px-6 py-3">Reservation ID</th>
        <th class="px-6 py-3">First Name</th>
        <th class="px-6 py-3">Last Name</th>
        <th class="px-6 py-3">Room ID</th>
        <th class="px-6 py-3">Guest(s)</th>
        <th class="px-6 py-3">Status</th>
        <th class="px-6 py-3">Created</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($col as $cols): ?>
        <tr class="bg-white even:bg-[#FAF3E0] border-b border-[#EAD5B0]">
          <td class="px-6 py-4 font-medium"><?= $cols['reservation_id'] ?></td>
          <td class="px-6 py-4"><?= $cols['first_name'] ?></td>
          <td class="px-6 py-4"><?= $cols['last_name'] ?></td>
          <td class="px-6 py-4"><?= $cols['room_id'] ?></td>
          <td class="px-6 py-4"><?= $cols['guests'] ?></td>
          <td class="px-6 py-4"><?= $cols['status'] ?></td>
          <td class="px-6 py-4"><?= $cols['created_at'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- STATS CARDS -->
 <?php
$servername = "localhost:3307"; // Change if using a different host
$username = "root"; // Change to your database username
$password = ""; // Change to your database password
$dbname = "cr3_customer_guest_management"; // Change to your database name

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>

  <?php
    // Count total checked-out guests
    $stmt = $conn->query("SELECT COUNT(*) FROM guests WHERE check_out IS NOT NULL");
    $totalGuests = $stmt->fetchColumn();

    // Count total interactions
    $stmt = $conn->query("SELECT COUNT(*) FROM interactions");
    $totalInteractions = $stmt->fetchColumn();

    // Count total feedbacks
    $stmt = $conn->query("SELECT COUNT(*) FROM feedback");
    $totalFeedbacks = $stmt->fetchColumn();

    // Count total active users (edit table/condition if needed)
    $stmt = $conn->query("SELECT COUNT(*) FROM guests WHERE status = 'active'");
    $totalActiveUsers = $stmt->fetchColumn();

 
        // Fetch latest 5 interactions with guest names
        $stmt = $conn->prepare("
          SELECT i.interaction_type, i.description, i.interaction_date, g.guest_name
          FROM interactions i
          JOIN guests g ON i.GuestID = g.GuestID
          ORDER BY i.interaction_date DESC
          LIMIT 5
        ");
        $stmt->execute();
        $interactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
  ?>
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
  <?php
    $icons = ['bx-user', 'bx-message-square-detail', 'bx-star', 'bx-user-check'];
    $labels = ['Total Guests', 'Interactions', 'Feedbacks', 'Active Users'];
    $values = [$totalGuests, $totalInteractions, $totalFeedbacks, $totalActiveUsers];
    foreach ($values as $i => $val):
  ?>
  <div class="bg-white p-6 rounded-2xl border border-[#594423] shadow-md flex items-center gap-4">
    <div class="bg-[#F7E6CA] p-3 rounded-full">
      <i class="bx <?= $icons[$i] ?> text-2xl text-[#594423]"></i>
    </div>
    <div>
      <p class="text-sm text-[#6B4F38]"><?= $labels[$i] ?></p>
      <p class="text-2xl font-bold text-[#4E3B2A]"><?= $val ?></p>
    </div>
  </div>
  <?php endforeach; ?>
</section>

<!-- RECENT INTERACTIONS -->
<section class="bg-white p-6 rounded-2xl border border-[#594423] shadow-md">
  <div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-bold text-[#594423]">Recent Interactions</h2>
    <a href="../cr3CGM/interactions.php" class="text-sm text-[#594423] hover:underline">View All</a>
  </div>
  <div class="omax-h-[400px] overflow-y-auto overflow-x-auto rounded-2xl mb-9">
    <table class="min-w-full text-sm text-left">
      <thead class="bg-[#F7E6CA] text-[#4E3B2A]">
        <tr>
          <th class="p-3 font-medium">Guest Name</th>
          <th class="p-3 font-medium">Type</th>
          <th class="p-3 font-medium">Description</th>
          <th class="p-3 font-medium">Date</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#EAD5B0]">
        <?php if ($interactions): foreach ($interactions as $interaction): ?>
          <tr>
            <td class="p-3"><?= htmlspecialchars($interaction['guest_name'] ?? '') ?></td>
            <td class="p-3"><?= htmlspecialchars($interaction['interaction_type'] ?? '') ?></td>
            <td class="p-3"><?= htmlspecialchars($interaction['description'] ?? '') ?></td>
            <td class="p-3"><?= htmlspecialchars($interaction['interaction_date'] ?? '') ?></td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="4" class="p-3 text-center text-red-500">No recent interactions found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>


          
            <br>

            <!--------------------------------------------------------------manager-------------------------------------------------------------->
        <?php break;
    case 'manager': ?>
             <!-- DASHBOARD TITLE -->
<div class="text-center mb-10">
  <h1 class="text-3xl font-bold text-[#594423] bg-[#F7E6CA] py-3 rounded-lg shadow-xl">DASHBOARD</h1>
</div>

<!-- STATS CARDS GRID -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
  <!-- Reservation Status -->
  <div class="bg-[#F7E6CA] p-6 rounded-2xl shadow-xl">
    <div class="flex items-center justify-between mb-2">
      <h2 class="text-xl font-bold text-[#594423]">Reservation Status</h2>
      <i class="fa-solid fa-calendar-check text-2xl text-[#594423]"></i>
    </div>
    <p class="text-3xl font-semibold text-[#4E3B2A]"><?= count($reservations) ?></p>
    <a href="../cr3re/rs.php" class="mt-4 block w-full py-2 text-center text-white bg-[#594423] rounded-lg hover:bg-[#4E3B2A] transition">
  View
</a>

  </div>

  <!-- Rooms Status -->
  <div class="bg-[#F7E6CA] p-6 rounded-2xl shadow-xl">
    <div class="flex items-center justify-between mb-2">
      <h2 class="text-xl font-bold text-[#594423]">Rooms Status</h2>
      <i class="fa-solid fa-bed text-2xl text-[#594423]"></i>
    </div>
    <p class="text-3xl font-semibold text-[#4E3B2A]"><?= count($status) ?></p>
    <a href="../cr3re/rooms.php" class="block mt-4 w-full text-center py-2 text-white bg-[#594423] rounded-lg hover:bg-[#4E3B2A] transition">View</a>
  </div>

  <!-- Total Reservations -->
  <div class="bg-[#F7E6CA] p-6 rounded-2xl shadow-xl">
    <div class="flex items-center justify-between mb-2">
      <h2 class="text-xl font-bold text-[#594423]">Total Reservations</h2>
      <i class="fa-solid fa-calendar text-2xl text-[#594423]"></i>
    </div>
    <p class="text-3xl font-semibold text-[#4E3B2A]"><?= count($reservations) ?></p>
    <a href="../cr3re/rs.php" class="mt-4 block w-full py-2 text-center text-white bg-[#594423] rounded-lg hover:bg-[#4E3B2A] transition">
  View
</a>
  </div>
</div>

<!-- MAIN TABLE -->
<div class="max-h-[400px] overflow-y-auto overflow-x-auto rounded-2xl mb-9">
  <table class="w-full text-sm text-left text-gray-700">
    <thead class="text-xs uppercase bg-[#F7E6CA] text-[#594423]">
      <tr>
        <th class="px-6 py-3">Reservation ID</th>
        <th class="px-6 py-3">First Name</th>
        <th class="px-6 py-3">Last Name</th>
        <th class="px-6 py-3">Room ID</th>
        <th class="px-6 py-3">Guest(s)</th>
        <th class="px-6 py-3">Status</th>
        <th class="px-6 py-3">Created</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($col as $cols): ?>
        <tr class="bg-white even:bg-[#FAF3E0] border-b border-[#EAD5B0]">
          <td class="px-6 py-4 font-medium"><?= $cols['reservation_id'] ?></td>
          <td class="px-6 py-4"><?= $cols['first_name'] ?></td>
          <td class="px-6 py-4"><?= $cols['last_name'] ?></td>
          <td class="px-6 py-4"><?= $cols['room_id'] ?></td>
          <td class="px-6 py-4"><?= $cols['guests'] ?></td>
          <td class="px-6 py-4"><?= $cols['status'] ?></td>
          <td class="px-6 py-4"><?= $cols['created_at'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- STATS CARDS -->
 <?php
$servername = "localhost:3307"; // Change if using a different host
$username = "root"; // Change to your database username
$password = ""; // Change to your database password
$dbname = "cr3_customer_guest_management"; // Change to your database name

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>

  <?php
    // Count total checked-out guests
    $stmt = $conn->query("SELECT COUNT(*) FROM guests WHERE check_out IS NOT NULL");
    $totalGuests = $stmt->fetchColumn();

    // Count total interactions
    $stmt = $conn->query("SELECT COUNT(*) FROM interactions");
    $totalInteractions = $stmt->fetchColumn();

    // Count total feedbacks
    $stmt = $conn->query("SELECT COUNT(*) FROM feedback");
    $totalFeedbacks = $stmt->fetchColumn();

    // Count total active users (edit table/condition if needed)
    $stmt = $conn->query("SELECT COUNT(*) FROM guests WHERE status = 'active'");
    $totalActiveUsers = $stmt->fetchColumn();

 
        // Fetch latest 5 interactions with guest names
        $stmt = $conn->prepare("
          SELECT i.interaction_type, i.description, i.interaction_date, g.guest_name
          FROM interactions i
          JOIN guests g ON i.GuestID = g.GuestID
          ORDER BY i.interaction_date DESC
          LIMIT 5
        ");
        $stmt->execute();
        $interactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
  ?>
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
  <?php
    $icons = ['bx-user', 'bx-message-square-detail', 'bx-star', 'bx-user-check'];
    $labels = ['Total Guests', 'Interactions', 'Feedbacks', 'Active Users'];
    $values = [$totalGuests, $totalInteractions, $totalFeedbacks, $totalActiveUsers];
    foreach ($values as $i => $val):
  ?>
  <div class="bg-white p-6 rounded-2xl border border-[#594423] shadow-md flex items-center gap-4">
    <div class="bg-[#F7E6CA] p-3 rounded-full">
      <i class="bx <?= $icons[$i] ?> text-2xl text-[#594423]"></i>
    </div>
    <div>
      <p class="text-sm text-[#6B4F38]"><?= $labels[$i] ?></p>
      <p class="text-2xl font-bold text-[#4E3B2A]"><?= $val ?></p>
    </div>
  </div>
  <?php endforeach; ?>
</section>

<!-- RECENT INTERACTIONS -->
<section class="bg-white p-6 rounded-2xl border border-[#594423] shadow-md">
  <div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-bold text-[#594423]">Recent Interactions</h2>
    <a href="../cr3CGM/interactions.php" class="text-sm text-[#594423] hover:underline">View All</a>
  </div>
  <div class="omax-h-[400px] overflow-y-auto overflow-x-auto rounded-2xl mb-9">
    <table class="min-w-full text-sm text-left">
      <thead class="bg-[#F7E6CA] text-[#4E3B2A]">
        <tr>
          <th class="p-3 font-medium">Guest Name</th>
          <th class="p-3 font-medium">Type</th>
          <th class="p-3 font-medium">Description</th>
          <th class="p-3 font-medium">Date</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#EAD5B0]">
        <?php if ($interactions): foreach ($interactions as $interaction): ?>
          <tr>
            <td class="p-3"><?= htmlspecialchars($interaction['guest_name'] ?? '') ?></td>
            <td class="p-3"><?= htmlspecialchars($interaction['interaction_type'] ?? '') ?></td>
            <td class="p-3"><?= htmlspecialchars($interaction['description'] ?? '') ?></td>
            <td class="p-3"><?= htmlspecialchars($interaction['interaction_date'] ?? '') ?></td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="4" class="p-3 text-center text-red-500">No recent interactions found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>
            <!-------------------------------------------------------------------STAFF-------------------------------------------------------------->
        <?php break;
    case 'staff': ?>
            <!-- DASHBOARD TITLE -->
<div class="text-center mb-10">
  <h1 class="text-3xl font-bold text-[#594423] bg-[#F7E6CA] py-3 rounded-lg shadow-xl">DASHBOARD</h1>
</div>

<!-- STATS CARDS GRID -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
  <!-- Reservation Status -->
  <div class="bg-[#F7E6CA] p-6 rounded-2xl shadow-xl">
    <div class="flex items-center justify-between mb-2">
      <h2 class="text-xl font-bold text-[#594423]">Reservation Status</h2>
      <i class="fa-solid fa-calendar-check text-2xl text-[#594423]"></i>
    </div>
    <p class="text-3xl font-semibold text-[#4E3B2A]"><?= count($reservations) ?></p>
    <a href="../cr3re/rs.php" class="mt-4 block w-full py-2 text-center text-white bg-[#594423] rounded-lg hover:bg-[#4E3B2A] transition">
  View
</a>

  </div>

  <!-- Rooms Status -->
  <div class="bg-[#F7E6CA] p-6 rounded-2xl shadow-xl">
    <div class="flex items-center justify-between mb-2">
      <h2 class="text-xl font-bold text-[#594423]">Rooms Status</h2>
      <i class="fa-solid fa-bed text-2xl text-[#594423]"></i>
    </div>
    <p class="text-3xl font-semibold text-[#4E3B2A]"><?= count($status) ?></p>
    <a href="../cr3re/rooms.php" class="block mt-4 w-full text-center py-2 text-white bg-[#594423] rounded-lg hover:bg-[#4E3B2A] transition">View</a>
  </div>

  <!-- Total Reservations -->
  <div class="bg-[#F7E6CA] p-6 rounded-2xl shadow-xl">
    <div class="flex items-center justify-between mb-2">
      <h2 class="text-xl font-bold text-[#594423]">Total Reservations</h2>
      <i class="fa-solid fa-calendar text-2xl text-[#594423]"></i>
    </div>
    <p class="text-3xl font-semibold text-[#4E3B2A]"><?= count($reservations) ?></p>
    <a href="../cr3re/rs.php" class="mt-4 block w-full py-2 text-center text-white bg-[#594423] rounded-lg hover:bg-[#4E3B2A] transition">
  View
</a>
  </div>
</div>

<!-- MAIN TABLE -->
<div class="max-h-[400px] overflow-y-auto overflow-x-auto rounded-2xl mb-9">
  <table class="w-full text-sm text-left text-gray-700">
    <thead class="text-xs uppercase bg-[#F7E6CA] text-[#594423]">
      <tr>
        <th class="px-6 py-3">Reservation ID</th>
        <th class="px-6 py-3">First Name</th>
        <th class="px-6 py-3">Last Name</th>
        <th class="px-6 py-3">Room ID</th>
        <th class="px-6 py-3">Guest(s)</th>
        <th class="px-6 py-3">Status</th>
        <th class="px-6 py-3">Created</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($col as $cols): ?>
        <tr class="bg-white even:bg-[#FAF3E0] border-b border-[#EAD5B0]">
          <td class="px-6 py-4 font-medium"><?= $cols['reservation_id'] ?></td>
          <td class="px-6 py-4"><?= $cols['first_name'] ?></td>
          <td class="px-6 py-4"><?= $cols['last_name'] ?></td>
          <td class="px-6 py-4"><?= $cols['room_id'] ?></td>
          <td class="px-6 py-4"><?= $cols['guests'] ?></td>
          <td class="px-6 py-4"><?= $cols['status'] ?></td>
          <td class="px-6 py-4"><?= $cols['created_at'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- STATS CARDS -->
 <?php
$servername = "localhost:3307"; // Change if using a different host
$username = "root"; // Change to your database username
$password = ""; // Change to your database password
$dbname = "cr3_customer_guest_management"; // Change to your database name

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>

  <?php
    // Count total checked-out guests
    $stmt = $conn->query("SELECT COUNT(*) FROM guests WHERE check_out IS NOT NULL");
    $totalGuests = $stmt->fetchColumn();

    // Count total interactions
    $stmt = $conn->query("SELECT COUNT(*) FROM interactions");
    $totalInteractions = $stmt->fetchColumn();

    // Count total feedbacks
    $stmt = $conn->query("SELECT COUNT(*) FROM feedback");
    $totalFeedbacks = $stmt->fetchColumn();

    // Count total active users (edit table/condition if needed)
    $stmt = $conn->query("SELECT COUNT(*) FROM guests WHERE status = 'active'");
    $totalActiveUsers = $stmt->fetchColumn();

 
        // Fetch latest 5 interactions with guest names
        $stmt = $conn->prepare("
          SELECT i.interaction_type, i.description, i.interaction_date, g.guest_name
          FROM interactions i
          JOIN guests g ON i.GuestID = g.GuestID
          ORDER BY i.interaction_date DESC
          LIMIT 5
        ");
        $stmt->execute();
        $interactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
  ?>
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
  <?php
    $icons = ['bx-user', 'bx-message-square-detail', 'bx-star', 'bx-user-check'];
    $labels = ['Total Guests', 'Interactions', 'Feedbacks', 'Active Users'];
    $values = [$totalGuests, $totalInteractions, $totalFeedbacks, $totalActiveUsers];
    foreach ($values as $i => $val):
  ?>
  <div class="bg-white p-6 rounded-2xl border border-[#594423] shadow-md flex items-center gap-4">
    <div class="bg-[#F7E6CA] p-3 rounded-full">
      <i class="bx <?= $icons[$i] ?> text-2xl text-[#594423]"></i>
    </div>
    <div>
      <p class="text-sm text-[#6B4F38]"><?= $labels[$i] ?></p>
      <p class="text-2xl font-bold text-[#4E3B2A]"><?= $val ?></p>
    </div>
  </div>
  <?php endforeach; ?>
</section>

<!-- RECENT INTERACTIONS -->
<section class="bg-white p-6 rounded-2xl border border-[#594423] shadow-md">
  <div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-bold text-[#594423]">Recent Interactions</h2>
    <a href="../cr3CGM/interactions.php" class="text-sm text-[#594423] hover:underline">View All</a>
  </div>
  <div class="omax-h-[400px] overflow-y-auto overflow-x-auto rounded-2xl mb-9">
    <table class="min-w-full text-sm text-left">
      <thead class="bg-[#F7E6CA] text-[#4E3B2A]">
        <tr>
          <th class="p-3 font-medium">Guest Name</th>
          <th class="p-3 font-medium">Type</th>
          <th class="p-3 font-medium">Description</th>
          <th class="p-3 font-medium">Date</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#EAD5B0]">
        <?php if ($interactions): foreach ($interactions as $interaction): ?>
          <tr>
            <td class="p-3"><?= htmlspecialchars($interaction['guest_name'] ?? '') ?></td>
            <td class="p-3"><?= htmlspecialchars($interaction['interaction_type'] ?? '') ?></td>
            <td class="p-3"><?= htmlspecialchars($interaction['description'] ?? '') ?></td>
            <td class="p-3"><?= htmlspecialchars($interaction['interaction_date'] ?? '') ?></td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="4" class="p-3 text-center text-red-500">No recent interactions found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>
            <!-------------------------------------------------------------------GUEST-------------------------------------------------------------->
        <?php break;
    case 'guest': ?>
 <div class="container mx-auto max-w-3xl px-4 py-4">


  <!-- All your dashboard content goes here -->


           <!-- Guest Dashboard Title -->
<div class="text-center mt-5 p-1 text-2xl font-bold rounded-lg mb-6 bg-[#F7E6CA] shadow-xl text-[#4E3B2A] tracking-wide">
  DASHBOARD
</div>

<!-- Welcome Message -->
<section class="w-full max-w-7xl mx-auto px-6 py-6 bg-white rounded-[16px] shadow-lg border border-[#594423]">
  <h2 class="text-2xl font-bold text-[#4E3B2A] mb-2">Welcome to Your Dashboard</h2>
  <p class="text-sm text-gray-600">Select a room, leave feedback, and stay updated with guest info. For assistance, our staff is always here to help.</p>
</section>

<?php
$rooms = [
  ['room_id' => 1, 'room_name' => 'Deluxe Suite', 'description' => 'Spacious room with sea view', 'price' => 4500, 'status' => 'Check it now!', 'image' => 'junior_suite.jpg'],
  ['room_id' => 2, 'room_name' => 'Standard Room', 'description' => 'Comfortable room for two', 'price' => 2500, 'status' => 'Check it now!', 'image' => 'executive_suite.jpg'],
  ['room_id' => 3, 'room_name' => 'Penthouse', 'description' => 'Perfect for solo travelers', 'price' => 1500, 'status' => 'Check it now!', 'image' => 'penthouse_suite.jpg'],
  ['room_id' => 4, 'room_name' => 'Presidential Suite', 'description' => 'Luxurious top-tier room', 'price' => 7500, 'status' => 'Check it now!', 'image' => 'presidential_suite.jpg'],
];
?>

<!-- Room Selection Section -->
<section class="w-full max-w-7xl mx-auto px-6 py-6 bg-white rounded-[16px] shadow-lg border border-[#594423] mt-4">
  <h2 class="text-xl font-semibold text-[#4E3B2A] mb-4">Available Rooms</h2>
  <div class="overflow-x-auto h-[290px]">
    <div class="flex space-x-4 min-w-max">
      <?php foreach ($rooms as $room): ?>
        <label class="relative block w-64 rounded-[12px] overflow-hidden transition border border-[#594423] shadow hover:shadow-xl cursor-pointer">
          <img src="../cr3CGM/roomPhoto/<?= htmlspecialchars($room['image']) ?>" alt="<?= htmlspecialchars($room['room_name']) ?>" class="w-full h-40 object-cover">
          <div class="p-3 bg-white">
            <h3 class="font-semibold text-[#4E3B2A]"><?= htmlspecialchars($room['room_name']) ?></h3>
            <p class="text-sm text-gray-600 mb-1"><?= htmlspecialchars($room['description']) ?></p>
            <p class="text-sm font-bold text-[#594423]">
              ₱<?= number_format($room['price'], 2) ?> - <?= htmlspecialchars($room['status']) ?>
            </p>
            <a href="../cr3CGM/guest.php" class="mt-2 inline-block w-full text-center py-2 text-white bg-[#594423] rounded-lg hover:bg-[#4E3B2A] transition duration-300 font-semibold shadow-md">View</a>
          </div>
        </label>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Quick Info Section -->
<section class="w-full max-w-7xl mx-auto px-6 py-6 bg-white rounded-[16px] shadow-lg border border-[#594423] mt-6">
  <h2 class="text-xl font-semibold text-[#4E3B2A] mb-4">Quick Info</h2>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="border border-[#594423] rounded-[12px] p-4 bg-[#F7E6CA]">
      <h3 class="text-lg font-semibold text-[#4E3B2A] mb-1">Stay Duration</h3>
      <p class="text-sm text-gray-700">Your current stay is from <span class="font-medium">May 18</span> to <span class="font-medium">May 20</span>.</p>
    </div>
    <div class="border border-[#594423] rounded-[12px] p-4 bg-[#F7E6CA]">
      <h3 class="text-lg font-semibold text-[#4E3B2A] mb-1">Room Policies</h3>
      <p class="text-sm text-gray-700">No smoking, no loud noises after 10 PM, and kindly report any damages immediately.</p>
    </div>
    <div class="border border-[#594423] rounded-[12px] p-4 bg-[#F7E6CA]">
      <h3 class="text-lg font-semibold text-[#4E3B2A] mb-1">Guest Services</h3>
      <p class="text-sm text-gray-700">Need help? Contact reception by dialing <span class="font-medium">101</span>.</p>
    </div>
  </div>
</section>

<!-- Why Guests Love Us Section -->
<section class="w-full max-w-7xl mx-auto px-6 py-6 bg-white rounded-[16px] shadow-lg border border-[#594423] mt-6">
  <h2 class="text-xl font-bold text-[#4E3B2A] mb-6">Why Guests Love Staying With Us</h2>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="border border-[#594423] bg-[#F7E6CA] rounded-[12px] p-4">
      <h3 class="text-lg font-semibold text-[#4E3B2A] mb-1">Comfort & Cleanliness</h3>
      <p class="text-sm text-gray-700">Hotel-grade linens and pristine rooms.</p>
    </div>
    <div class="border border-[#594423] bg-[#F7E6CA] rounded-[12px] p-4">
      <h3 class="text-lg font-semibold text-[#4E3B2A] mb-1">Prime Location</h3>
      <p class="text-sm text-gray-700">Close to city attractions and transport hubs.</p>
    </div>
    <div class="border border-[#594423] bg-[#F7E6CA] rounded-[12px] p-4">
      <h3 class="text-lg font-semibold text-[#4E3B2A] mb-1">24/7 Service</h3>
      <p class="text-sm text-gray-700">Always ready to assist, day or night.</p>
    </div>
    <div class="border border-[#594423] bg-[#F7E6CA] rounded-[12px] p-4">
      <h3 class="text-lg font-semibold text-[#4E3B2A] mb-1">In-House Dining</h3>
      <p class="text-sm text-gray-700">Enjoy fresh meals prepared by our chefs.</p>
    </div>
    <div class="border border-[#594423] bg-[#F7E6CA] rounded-[12px] p-4">
      <h3 class="text-lg font-semibold text-[#4E3B2A] mb-1">Safe & Secure</h3>
      <p class="text-sm text-gray-700">Monitored premises and 24/7 security.</p>
    </div>
    <div class="border border-[#594423] bg-[#F7E6CA] rounded-[12px] p-4">
      <h3 class="text-lg font-semibold text-[#4E3B2A] mb-1">Relaxation Amenities</h3>
      <p class="text-sm text-gray-700">Spa, lounge, and garden for your leisure.</p>
    </div>
  </div>
</section>




        <?php break;
    default: ?>
            <h3 class="font-semibold">Unknown Role</h3>
            <p>Please contact the administrator.</p>


</div>


<?php endswitch; ?>
   </main>
<?= require '../partials/admin/footer.php'; ?>