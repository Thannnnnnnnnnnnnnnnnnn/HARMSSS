<?php
session_start();
$user = $_SESSION['user'];
$_SESSION['user_id'] = $user['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>                                                
      <?php include 'header.php';?>
</head>
<body>
  <div class="flex min-h-screen w-full">

<?php include __DIR__ . '/../partials/admin/sidebar.php';?>
<?php include __DIR__ . '/../partials/admin/navbar.php';?>
   

            <!-- Main Content -->
            <main class="px-8 py-8">


            <?php if ( $user['role'] === 'admin' || $user['role'] === 'manager' || $user['role'] === 'staff' ): ?>
            <h2 class="text-center text-2xl font-bold text-gray-800 mb-4">Feedback History Table</h2>

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

<?php 
   include("php/connection.php");

   $sql = "
    SELECT 
        guests.*, 
        feedback.FeedbackID, 
        feedback.rating, 
        feedback.comment, 
        feedback.feedback_date
    FROM guests
    LEFT JOIN feedback ON guests.GuestID = feedback.GuestID
    WHERE feedback.FeedbackID IS NOT NULL
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$guests = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="overflow-x-auto rounded-2xl shadow-md bg-white p-4">
    <input type="text" 
    class="bg-[#FFF6E8] h-10 rounded-lg grow w-full pl-10 pr-4 focus:ring-2 focus:ring-[#F7E6CA] focus:outline-none" 
    placeholder="Search something..." 
    aria-label="Search input"
    id="searchInput"/>
    <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-[#4E3B2A]"></i>
  <table id="customerTable" class="min-w-full text-sm text-left text-gray-700">
  <thead class="bg-white text-[#4E3B2A] text-base border-b divide-gray-200">
      <tr>
        <th class="px-6 py-1">Feedback ID</th>
        <th class="px-6 py-1">Guest Name</th>
        <th class="px-6 py-1">Rate</th>
        <th class="px-6 py-1">Comment</th>
        <th class="px-6 py-1">Action</th>
      </tr>
    </thead>
    <tbody id="guestTbody" class="text-gray-800 divide-y divide-gray-200">
      <?php foreach ($guests as $guest): ?>
        <tr class="hover:bg-gray-100 transition">
          <td class="px-6 py-1"><?php echo htmlspecialchars($guest['GuestID']); ?></td>
          <td class="px-6 py-1"><?php echo htmlspecialchars($guest['guest_name']); ?></td>
          <td class="px-6 py-1"><?php echo htmlspecialchars($guest['rating']); ?></td>
          <td class="px-6 py-1"><?php echo htmlspecialchars($guest['comment']); ?></td>
          <td class="px-6 py-1 space-x-1 flex flex-wrap gap-1">

            <div class="flex justify-center gap-1 mb-6">
    
            <!-- View Button -->
<a href="#" class="bg-blue-600 text-white w-[70px] p-2 rounded-lg inline-block text-center view-btn"
   data-bs-toggle="modal"
   data-bs-target="#viewModal"
   data-id="<?php echo $guest['GuestID']; ?>"
   data-name="<?php echo htmlspecialchars($guest['guest_name']); ?>"
   data-email="<?php echo htmlspecialchars($guest['email']); ?>"
   data-phone="<?php echo htmlspecialchars($guest['phone']); ?>"
   data-address="<?php echo htmlspecialchars($guest['address']); ?>"
   data-birthday="<?php echo htmlspecialchars($guest['date_of_birth']); ?>"
   data-gender="<?php echo htmlspecialchars($guest['gender']); ?>"
   data-nationality="<?php echo htmlspecialchars($guest['nationality']); ?>"
   data-status="<?php echo htmlspecialchars($guest['status']); ?>"

   data-rating="<?php echo htmlspecialchars($guest['rating'] ?? ''); ?>"
   data-comment="<?php echo htmlspecialchars($guest['comment'] ?? ''); ?>"
   data-feedback-date="<?php echo htmlspecialchars($guest['feedback_date'] ?? ''); ?>">

   <i class="bx bx-show"></i>

</a>

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
                <p><strong>Status:</strong> <span id="guestStatus"></span></p>
                <br>


            </div>
        </div>
    </div>
</div>

<?php if ( $user['role'] === 'admin'  ): ?>
  


 <!-- Delete Feedback Button -->
<?php if (!empty($guest['FeedbackID'])): ?>
    <button type="button"
    onclick="<?php echo !empty($guest['FeedbackID']) 
        ? "openFeedbackModal('{$guest['FeedbackID']}', '" . htmlspecialchars($guest['guest_name']) . "')" 
        : "alert('This guest has no feedback yet.')" ?>"
    class="bg-red-500 text-white w-[70px] p-2 rounded-lg text-center">
    <i class="bx bx-trash"></i>
</button>
<?php else: ?>
    <button type="button"
        onclick="alert('This guest has no feedback yet.')"
        class="bg-gray-400 text-white w-[70px] p-2 rounded-lg text-center cursor-not-allowed">
        <i class="bx bx-trash"></i>
    </button>
<?php endif; ?>


<!-- Delete Confirmation Modal -->
<div id="feedbackDeleteModal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-[350px] text-center">
        <h2 class="text-xl font-bold mb-4">Confirm Deletion</h2>
        <p class="mb-4">
            Are you sure you want to delete feedback from
            <strong id="deleteFeedbackName"></strong>
            (ID: <strong id="deleteFeedbackID"></strong>)?
        </p>

        <form id="feedbackDeleteForm" action="php/delete-feedback.php" method="POST">
            <input type="hidden" name="FeedbackID" id="FeedbackID">
            <div class="flex justify-center space-x-4">
                <button type="button" onclick="closeFeedbackModal()"
                    class="bg-gray-400 text-white px-4 py-2 rounded-lg">Cancel</button>
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg">Delete</button>
            </div>
        </form>
    </div>
</div>


<?php endif; ?>
      </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- No Guests Message -->
  <div id="noGuestsMessage" class="text-center text-gray-500 py-4 hidden">
    No guests Record.
  </div>
</div>

 <?php endif; ?>   


  <?php if ($user['role'] === 'guest'): ?>   

    <!-- for Guests only -->
    <div class="bg-white p-5 rounded-[16px] shadow-lg border border-[#594423] w-[700px] mx-auto">

    <div class="flex justify-center mb-3">
      <img src="Logo.png" alt="Hotel Logo" class="h-16 object-contain">
    </div>
    <div class="flex justify-center mb-5">
      <img src="Logo-Name.png" alt="Hotel Logo Name" class="h-8 object-contain">
    </div>

    <form class="space-y-4 text-sm" method="POST" action="php/create_feedback.php">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">


        <!-- Hidden Guest ID -->
         <?php 
     $userId = $_SESSION['user_id'];

// Include DB connection
include('php/connection.php');

// Prepare the SQL query with a placeholder
$sqlGuestInfo = "SELECT * FROM guests WHERE user_id = ?";

$stmtGuestInfo = $conn->prepare($sqlGuestInfo);
$stmtGuestInfo->bindParam(1, $userId, PDO::PARAM_INT);
$stmtGuestInfo->execute();
$guest = $stmtGuestInfo->fetch(PDO::FETCH_ASSOC);
$guestId = $guest ? $guest['GuestID'] : null;

      ?>
      <!-- Hidden Guest ID -->
      <input type="hidden" name="guest_id" value="<?php echo $guestId ?>">

        <!-- Rating -->
        <div class="md:col-span-2">
          <label class="block font-medium mb-1">Rating</label>
          <select name="rating" class="w-full border border-[#594423] rounded-[10px] p-2 focus:outline-none focus:ring-2 focus:ring-[#F7E6CA]" required>
            <option disabled selected>Select Rating</option>
            <option value="5">⭐⭐⭐⭐⭐ - Excellent</option>
            <option value="4">⭐⭐⭐⭐ - Very Good</option>
            <option value="3">⭐⭐⭐ - Good</option>
            <option value="2">⭐⭐ - Fair</option>
            <option value="1">⭐ - Poor</option>
          </select>
        </div>

        <!-- Comment -->
        <div class="md:col-span-2">
          <label class="block font-medium mb-1">Comment</label>
          <textarea name="comment" required
  class="w-full h-32 border border-[#594423] rounded-[10px] p-3 resize-none focus:outline-none focus:ring-2 focus:ring-[#F7E6CA]"
  placeholder="Write your feedback here..."></textarea>

        </div>
      </div>

      <!-- Submit Button -->
      <div class="flex justify-center pt-2">
        <button type="submit" class="bg-[#F7E6CA] text-[#4E3B2A] px-5 py-2 rounded-[8px] border border-[#594423] transition hover:bg-[#e4d3b4] active:scale-95">
          Submit Feedback
        </button>
      </div>
    </form>
  </div>
  <?php endif; ?>   

            </main>
        </div>
    </div>

    

    <?php include 'footer.php';?>

</body>
</html>
