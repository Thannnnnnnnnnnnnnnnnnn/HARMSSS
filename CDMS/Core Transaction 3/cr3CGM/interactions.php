<?php
session_start();
$user = $_SESSION['user'];
$_SESSION['User_ID'] = $user['User_ID'];
?>


<!DOCTYPE html>
<html lang="en">
<head>                                               
<?php include 'header.php';?>
</head>
<body>

  
 <!-- For Manager Staff and Admin  --> 
   <div class="flex min-h-screen w-full">


<?php include __DIR__ . '/../partials/admin/sidebar.php';?>
<?php include __DIR__ . '/../partials/admin/navbar.php';?>


 <!-- Main Content -->
            <main class="px-8 py-8">
<?php if ( $user['role'] === 'admin' || $user['role'] === 'manager' || $user['role'] === 'staff' ): ?>

            <h2 class="text-center text-2xl font-bold text-gray-800 mb-4">Interaction Table</h2>

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
<?php if ( $user['role'] === 'staff' ): ?>
            <?php
include("php/connection.php");

$sql = "
    SELECT 
        interactions.InteractionID, 
        interactions.interaction_type, 
        interactions.description, 
        interactions.interaction_date, 
        interactions.interaction_status AS interaction_status,
        guests.*
    FROM interactions
    INNER JOIN guests ON interactions.GuestID = guests.GuestID
    WHERE interactions.interaction_status NOT IN ('Completed', 'Resolved')
";


$stmt = $conn->prepare($sql);
$stmt->execute();
$interaction = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php endif; ?>


<?php if ( $user['role'] === 'admin' || $user['role'] === 'manager' ): ?>
            <?php
include("php/connection.php");

$sql = "
    SELECT 
        interactions.InteractionID, 
        interactions.interaction_type, 
        interactions.description, 
        interactions.interaction_date, 
        interactions.interaction_status AS interaction_status,
        guests.*
    FROM interactions
    INNER JOIN guests ON interactions.GuestID = guests.GuestID
  
";


$stmt = $conn->prepare($sql);
$stmt->execute();
$interaction = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php endif; ?>

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
        <th class="px-6 py-1">Guest Name</th>
        <th class="px-6 py-1">Interaction Type</th>
        <th class="px-6 py-1">Description</th>
        <th class="px-6 py-1">Interaction Date</th>
        <th class="px-6 py-1">Status</th>
        <th class="px-6 py-1">Action</th>
      </tr>
    </thead>

    <tbody id="guestTbody" class="text-gray-800 divide-y divide-gray-200">
      <?php foreach ($interaction as $interactions): ?>
                    
                    <tr>
                        <td class="px-6 py-1"><?php echo htmlspecialchars($interactions['guest_name']); ?></td>
                        <td class="px-6 py-1"><?php echo htmlspecialchars($interactions['interaction_type']); ?></td>
                        <td class="px-6 py-1"><?php echo htmlspecialchars($interactions['description']); ?></td>
                        <td class="px-6 py-1"><?php echo htmlspecialchars($interactions['interaction_date']); ?></td>
                        <td class="px-6 py-1"><?php echo htmlspecialchars($interactions['interaction_status']); ?></td>
                        <td class="px-6 py-1 space-x-1 flex flex-wrap gap-1">

 <div class="flex justify-center gap-1 mb-6">

 <?php if ( $user['role'] === 'admin' ): ?>
                    
                               <!-- View Button -->
<a href="#" class="bg-blue-600 text-white w-[70px] p-2 rounded-lg inline-block text-center view-btn"
   data-bs-toggle="modal"
   data-bs-target="#viewModal"
   data-id="<?php echo $interactions['InteractionID']; ?>"
   data-interactionId="<?php echo htmlspecialchars($interactions['InteractionID']); ?>"
   data-guestName="<?php echo htmlspecialchars($interactions['guest_name']); ?>"
   data-interactionType="<?php echo htmlspecialchars($interactions['interaction_type']); ?>"
   data-description="<?php echo htmlspecialchars($interactions['description']); ?>"
   data-interactionDate="<?php echo htmlspecialchars($interactions['interaction_date']); ?>">
   <i class="bx bx-show"></i>
</a>

                           <!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Interaction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Interaction ID:</strong> <span id="interactionId"></span></p>
                <p><strong>Guest:</strong> <span id="guestName"></span></p>
                <p><strong>Interaction type:</strong> <span id="interactionType"></span></p>
                <p><strong>Description:</strong> <span id="description"></span></p>
                <p><strong>Interaction Date:</strong> <span id="interactionDate"></span></p>
                
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ( $user['role'] === 'admin' || $user['role'] === 'manager' || $user['role'] === 'staff' ): ?>

<!-- Edit Button -->
<a href="#" 
   class="bg-green-500 text-white w-[70px] p-2 rounded-lg inline-block text-center edit-btn"
    data-bs-toggle="modal" 
    data-bs-target="#editModal"
    data-interactionId="<?php echo $interactions['InteractionID']; ?>"
    data-interactionType="<?php echo $interactions['interaction_type']; ?>"
    data-description="<?php echo $interactions['description']; ?>"
    data-interactionDate="<?php echo $interactions['interaction_date']; ?>"
     data-status="<?php echo htmlspecialchars($interactions['interaction_status']); ?>">
    <i class="bx bx-edit"></i>
</a>

                          <!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Guest Interaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="php/update_interaction.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- Hidden Field for ID -->
                    <input type="hidden" id="editInteractionId" name="InteractionID">

                    <div class="mb-3">
                        <label for="editInteractionType" class="form-label">Interaction Type</label>
                        <input type="text" class="form-control" id="editInteractionType" name="interactionType" readonly>
                    </div>

                    <div class="mb-3">
    <label for="editDescription" class="form-label">Description</label>
    <textarea class="form-control" id="editDescription" name="description" rows="4" readonly></textarea>
</div>


                    <div class="mb-3">
                        <label for="editInteractionDate" class="form-label">Interaction Date</label>
                        <input type="date" class="form-control" id="editInteractionDate" name="interactionDate" rows="3" readonly>
                    </div>

                    <!-- Status Selection -->
                    <div class="mb-3">
                        <label for="editStatus" class="form-label">Status</label>
                        <select class="form-control" id="editStatus" name="status">
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="no_show">No Show</option>
                            <option value="follow_up_required">Follow-up Required</option>
                            <option value="resolved">Resolved</option>
                            <option value="escalated">Escalated</option>
                            <option value="under_review">Under Review</option>
                        </select>
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
      // Get values from data attributes
      const interactionId = this.getAttribute('data-interactionId');
      const interactionType = this.getAttribute('data-interactionType');
      const description = this.getAttribute('data-description');
      const interactionDate = this.getAttribute('data-interactionDate');
      const status = this.getAttribute('data-status');

      // Populate the modal fields
      document.getElementById('editInteractionId').value = interactionId;
      document.getElementById('editInteractionType').value = interactionType;
      document.getElementById('editDescription').value = description;
      document.getElementById('editInteractionDate').value = interactionDate;
      document.getElementById('editStatus').value = status;
    });
  });
});
</script>

<?php endif; ?>


<?php if ( $user['role'] === 'admin' ): ?>
 <!-- Delete Button -->
<button type="button"
    onclick="openModal('<?php echo $interactions['InteractionID']; ?>', '<?php echo htmlspecialchars($interactions['guest_name']); ?>')"
    class="bg-red-500 text-white w-[70px] p-2 rounded-lg text-center">
    <i class="bx bx-trash"></i>
</button>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-[350px] text-center">
        <h2 class="text-xl font-bold mb-4">Confirm Deletion</h2>
        <p class="mb-4">
            Are you sure you want to delete interaction for guest
            <strong id="deleteCustomerName"></strong> (ID:
            <strong id="deleteCustomerID"></strong>)?
        </p>

        <!-- Delete Form -->
        <form id="deleteForm" action="php/delete-interaction.php" method="POST">
            <input type="hidden" name="interaction_id" id="customerID">
            <div class="flex justify-center space-x-4">
                <button type="button" onclick="closeModal()" class="bg-gray-400 text-white px-4 py-2 rounded-lg">
                    Cancel
                </button>
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg">
                    Delete
                </button>
            </div>
        </form>
    </div>
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
    No guests are currently checked in.
  </div>

  </div>

<?php endif; ?>
   

   <?php if ($user['role'] === 'guest'): ?>

<!-- For Guest  --> 

<!-- Page Wrapper -->
<div class="min-h-screen bg-[#FFF6E8] font-[Georgia] text-[#4E3B2A] flex items-center justify-center p-6">
 <div class="w-full mx-auto mt-0.2 bg-white border border-[#594423] rounded-[12px] shadow-md p-6">

    
    <!-- Title -->
    <div class="mb-6 text-center">
      <h2 class="text-2xl font-[Cinzel] font-bold">Submit Guest Interaction</h2>
      <p class="text-sm mt-1">Fill out the form below to record your interaction.</p>
    </div>

    <!-- Form Start -->
    <form class="space-y-6 text-sm" method="POST" action="php/create_interaction.php">
      <?php 
     $userId = $_SESSION['User_ID'];

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
      <input type="hidden" name="GuestID" value="<?php echo $guestId ?>">


      <!-- Interaction Type -->
      <div>
        <label class="block font-medium mb-1">Interaction Type</label>
        <select name="interaction_type"
                class="w-full border border-[#594423] rounded-[10px] p-2 focus:outline-none focus:ring-2 focus:ring-[#F7E6CA]"
                required>
          <option value="" disabled selected>Select type</option>
          <option value="Service Request">Service Request</option>
          <option value="Inquiry">Inquiry</option>
          <option value="Complaint">Complaint</option>
          <option value="Other">Other</option>
        </select>
      </div>

      <!-- Description -->
      <div>
        <label class="block font-medium mb-1">Description / Comment</label>
        <textarea name="comment" rows="4" required
                  class="w-full border border-[#594423] rounded-[10px] p-2 focus:outline-none focus:ring-2 focus:ring-[#F7E6CA] resize-none"
                  placeholder="Write your interaction here..."></textarea>
      </div>
       

      <!-- Status -->
      <div>

        <input type="hidden" name="status" value="Pending"
               class="w-full border border-[#594423] rounded-[10px] p-2 focus:outline-none focus:ring-2 focus:ring-[#F7E6CA] bg-gray-100">
      </div>
      

      <!-- Submit Button -->
      <div class="flex justify-center pt-2">
        <button type="submit"
                class="bg-[#F7E6CA] text-[#4E3B2A] px-6 py-2 rounded-[8px] border border-[#594423] transition hover:bg-[#e4d3b4] active:scale-95 flex items-center gap-2">
          <i class='bx bx-send'></i> Submit Interaction
        </button>
      </div>
    </form>
  </div>
</div>


<div class="mt-10">
  <h2 class="text-xl font-[Cinzel] font-bold text-[#4E3B2A] mb-4">Guest Interaction History</h2>

  <div class="overflow-x-auto rounded-[16px] shadow-[0_0_4px_8px_rgba(0,0,0,0.01)]">
    <table class="min-w-full bg-white text-[#4E3B2A] border-2 border-[#594423] rounded-[12px]">
      <thead class="bg-[#F7E6CA] text-left">
        <tr>
          <th class="px-6 py-4 border-b-2 border-[#594423]">Date</th>
          <th class="px-6 py-4 border-b-2 border-[#594423]">Interaction Type</th>
          <th class="px-6 py-4 border-b-2 border-[#594423]">Comment</th>
          <th class="px-6 py-4 border-b-2 border-[#594423]">Status</th>
        </tr>
      </thead>
      <tbody>
<?php

include("php/connection.php");

// Make sure $guestId is properly defined before this block
// For example:
// $guestId = 123; // or fetch from the database based on user info

// Prepare the SQL query with a placeholder for GuestID
$sqlGuest = "
    SELECT interaction_date, interaction_type, description, interaction_status 
    FROM interactions 
    WHERE GuestID = ?
";

$stmtGuest = $conn->prepare($sqlGuest);
$stmtGuest->bindParam(1, $guestId, PDO::PARAM_INT);
$stmtGuest->execute();
$interactionsGuest = $stmtGuest->fetchAll(PDO::FETCH_ASSOC);

?>


<?php foreach ($interactionsGuest as $Guest): ?>
    <!-- Example row -->
    <tr>
        <td class="px-6 py-1"><?php echo htmlspecialchars($Guest['interaction_date']); ?></td>
        <td class="px-6 py-1"><?php echo htmlspecialchars($Guest['interaction_type']); ?></td>
        <td class="px-6 py-1"><?php echo htmlspecialchars($Guest['description']); ?></td>
        <td class="px-6 py-1"><?php echo htmlspecialchars($Guest['interaction_status']); ?></td>
    </tr>
<?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php endif; ?>


         </main>
    </div>




 <?php include 'footer.php';?>   
   
</body>
</html>
