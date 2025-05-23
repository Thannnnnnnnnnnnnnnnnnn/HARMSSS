<?php 
session_start();
if (!isset($_SESSION['user'])) {
    // Redirect to login page
    header("Location: ../testing/login.php");
    exit;
}

$user = $_SESSION['user'];
$user_id = $_SESSION['user_id'] = $user['user_id'];
$role = $_SESSION['user']['role'];

include $_SERVER['DOCUMENT_ROOT'] . "/cr3/cr3bo/conn.php";
$query = "
    SELECT 
        u.user_id, 
        u.first_name, 
        u.last_name, 
        CONCAT(u.first_name, ' ', u.last_name) AS Name,
        u.email, 
        u.role, 
        u.department_id , 
        d.department_id , 
        d.dept_name 
    FROM user_account u 
    INNER JOIN departments d ON u.department_id = d.department_id  
    WHERE u.user_id = ?
";

$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $user_name = $row['Name'];
        $role = $row['role'];
        $department = $row['dept_name'];
        $email = $row['email'];
        $department_id = $row['department_id'];
    }
} else {
    die("Error fetching user data: " . mysqli_error($connection));
}

mysqli_stmt_close($stmt);

$notif = $connections["cr3_re"];
$usmDb = $connections["cr3_re"];
$db_booking = "cr3_re"; 
$db_guest = "cr3_re";
$connection = $connections[$db_booking];

$itemsPerPage = isset($_GET['items'])?(int)$_GET['items']:10;
$page = isset($_GET['page'])?(int)$_GET['page']:1;
$offset = ($page - 1) * $itemsPerPage;

$totalResults = $connection->query("SELECT COUNT(*) AS total FROM guest_preferences");
$totalRow = $totalResults ->fetch_assoc();
$totalItems =$totalRow['total'];
$totalPages = ceil($totalItems / $itemsPerPage);
$query = "SELECT * FROM guest_preferences ORDER BY PreferencesID ASC LIMIT $offset, $itemsPerPage";





$query = "
    SELECT gp.*, g.guest_name AS GuestName
    FROM `$db_booking`.guest_preferences AS gp
    LEFT JOIN `$db_guest`.guests AS g
    ON gp.GuestID = g.GuestID
    ORDER BY gp.PreferencesID ASC
    LIMIT $offset, $itemsPerPage
";

$result = $connection->query($query);


function logTransaction($usmDb, $department_id, $user_id, $type, $description) {
    $stmt = $usmDb->prepare("INSERT INTO department_transaction (department_id, user_id, transaction_type, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $department_id, $user_id, $type, $description);
    return $stmt->execute();
}

function logAudit($usmDb, $department_id, $user_id, $action, $department, $module, $description) {
    $stmt = $usmDb->prepare("INSERT INTO department_audit_trail (department_id, user_id, action, department_affected, module_affected, description) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissss", $department_id, $user_id, $action, $department, $module, $description);
    return $stmt->execute();
}



if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_guest'])) {
    $types = trim($_POST['update_type']);
    $descriptions = trim($_POST['update_description']);
    $preferenceID = $_POST['update_id'];

    $origboss = $connection->prepare("SELECT * FROM guest_preferences WHERE PreferencesID = ?");
    $origboss->bind_param("i", $preferenceID); 
    $origboss->execute();
    $resultboss = $origboss->get_result();
    $bossdata = $resultboss->fetch_assoc();
    $origboss->close();

    $binago = [];
    if ($bossdata['PreferenceType'] !== $types) {
        $binago[] = "Preference Type: '{$bossdata['PreferenceType']}' : '$types'";
    }
    if ($bossdata['PreferenceDetail'] !== $descriptions) {
        $binago[] = "Details: '{$bossdata['PreferenceDetail']}' : '$descriptions'";
    }

    if (!empty($binago)) {
        $stmt = $connection->prepare("UPDATE guest_preferences SET PreferenceType=?, PreferenceDetail=? WHERE PreferencesID=?");
        $stmt->bind_param("ssi", $types, $descriptions, $preferenceID);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Guest Preference updated successfully!";
            $changeSummary = implode("; ", $binago);
            
            $notification = $notif->prepare("INSERT INTO notifications (User_ID, notifType, message, status, Department, date_sent) 
                VALUES (?, 'update', ?, 'Unread', ?, NOW())");
            $message = "- $role updated Guest Preference ID #$preferenceID. Changes: $changeSummary";
            $notification->bind_param("sss", $user_id, $message, $department);
            $notification->execute();

            logTransaction($usmDb, $department_id, $user_id, 'Update', "$user_name Update Guest Preference Data");
            logAudit($usmDb, $department_id, $user_id, 'Update', $department, 'Booking', "$user_name Update Guest Preference Data");

            header("Location: guest.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Update Error: " . $stmt->error;
        }
        $stmt->close();
    }
}



if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_preference'])) {
    $prefere_id = $_POST['delete_preference_id'];

    $stmt = $connection->prepare("DELETE FROM guest_preferences WHERE PreferencesID = ?");
    $stmt->bind_param("i", $prefere_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Guest Preference deleted successfully!";
        
        $notification = $notif->prepare("INSERT INTO notifications (User_ID, notifType, message, status, Department, date_sent) 
            VALUES (?, 'delete', ?, 'Unread', ?, NOW())");
        $message = "- $role Delete Guest Preference ID #$prefere_id";
        $notification->bind_param("sss", $user_id, $message, $department);
        $notification->execute();

        logTransaction($usmDb, $department_id, $user_id, 'Delete', "$email Delete Guest Preference Data");
        logAudit($usmDb, $department_id, $user_id, 'Delete', $department, 'Booking', "$user_name Delete Guest Preference Data");

        header("Location: guest.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error deleting record: " . $stmt->error;
    }
    $stmt->close();
}

include "../partials/admin/sidebar.php";
include "../partials/admin/head.php";
include "../partials/admin/navbar.php";
include "../partials/admin/footer.php";
?>


            <!-- Main Content -->
            <main class="px-4 py-4">
 <h2 class="text-center text-2xl font-bold text-[#594423] mb-4">Guest Preference</h2>

            <div class="bg-gradient-to-b from-white to-orange-100 min-h-screen p-8">


 


  <div class="overflow-x-auto bg-white shadow-lg rounded-xl">
  <table class="min-w-full text-[12px] text-center text-[#594423]">
      <thead class="bg-[#F7E6CA] text-[#594423]  uppercase">
      <tr>
          <th class="px-4 py-3">Preferences ID</th>
          <th class="px-4 py-3">Guest Name</th>
          <th class="px-4 py-3">Preference Type</th>
          <th class="px-4 py-3">Preference Detail</th>
          <th class="px-4 py-3">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        
       
       

        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            


            echo "<tr class='class='hover:bg-[#FFF6E8] text-[11px] '>
              <td class='border '>{$row['PreferencesID']}</td> 
               <td class='border '>{$row['GuestName']}</td>
              <td class='border '>{$row['PreferenceType']}</td>
              <td class='border '>{$row['PreferenceDetail']}</td>
             
               <td class='border p-2 flex justify-center space-x-2'>";
               echo "
            <button 
           onclick='openViewModal(this)' 
               data-id=\"" . htmlspecialchars($row['PreferencesID']) . "\" 
              data-type=\"" . htmlspecialchars($row['PreferenceType']) . "\" 
                data-description=\"" . htmlspecialchars($row['PreferenceDetail']) . "\" 
                     data-gue=\"" . htmlspecialchars($row['GuestName']) . "\" 
               class='bg-[#f1ddbe] rounded p-2 text-black'>
               <i class=\"bx bx-show\"></i>
                 </button>";
                 if($role === 'admin' || $role ==='staff'){
                  echo "
                       <button 
              onclick='openUpdateModal(this)' 
             data-id=\"{$row['PreferencesID']}\"
            data-type=\"" . htmlspecialchars($row['PreferenceType']) . "\" 
               data-description=\"" . htmlspecialchars($row['PreferenceDetail']) . "\" 
                 class='bg-[#f1ddbe] rounded-sm p-2 text-black'>
                 <i class=\"bx bx-edit\"></i>
                      </button>";
                 }
                 if($role === 'admin'){
         echo "
                         <button 
                  onclick='openDeleteModal(this)' 
                 data-id=\"{$row['PreferencesID']}\"
                   class='bg-[#f1ddbe] rounded-sm p-2 text-black'>
                     <i class=\"bx bx-trash\"></i>
                  </button>
                ";}
                echo "
                  </td>
                       
            </tr>";
          }
        } else {
          echo "<tr><td colspan='9' class='text-center py-4'>No Channel Found.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
  <div class="flex items-center justify-between mt-4">
      <div class="text-sm text-gray-700">
       Showing <?= min($itemsPerPage,$totalPages)?> to <?= $itemsPerPage?> Reservations
      </div>
      
      <div class="flex items-center space-x-1">
   
    <?php if ($page > 1): ?>
      <a href="?page=<?= $page-1 ?>&items=<?= $itemsPerPage ?>" 
         class="px-3 py-1 bg-[#f1ddbe] hover:bg-gray-300 rounded-md">&lt;</a>
    <?php endif; ?>

    
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <a href="?page=<?= $i ?>&items=<?= $itemsPerPage ?>" 
         class="px-3 py-1 <?= $i == $page ? 'bg-[#f1ddbe]' : 'bg-[#f1ddbe]' ?> hover:bg-gray-300 rounded-md"><?= $i ?></a>
    <?php endfor; ?>

 
    <?php if ($page < $totalPages): ?>
      <a href="?page=<?= $page+1 ?>&items=<?= $itemsPerPage ?>" 
         class="px-3 py-1 bg-[#f1ddbe] hover:bg-gray-300 rounded-md">&gt;</a>
    <?php endif; ?>
  </div>

  <div class="text-sm text-gray-700 flex items-center">
    Items per page:
    <select onchange="changeItemsPerPage(this)" class="bg-[#f1ddbe] border border-gray-300 rounded-md py-1 px-2 ml-2">
      <option value="10" <?= $itemsPerPage == 10 ? 'selected' : '' ?>>10</option>
      <option value="20" <?= $itemsPerPage == 20 ? 'selected' : '' ?>>20</option>
      <option value="50" <?= $itemsPerPage == 50 ? 'selected' : '' ?>>50</option>
    </select>
  </div>
</div>  


<!-- update -->

<div id="updateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50" onclick="closeUpdateModal()">
  <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg relative animate__animated animate__fadeInDown" onclick="event.stopPropagation()">
    
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-2xl font-bold text-[#594423] flex items-center gap-2">
        <i class="fas fa-edit"></i> Update Guest Preference
      </h2>
      <button onclick="closeUpdateModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
    </div>

    <form method="POST" class="space-y-5 text-sm text-gray-700">
      <input type="hidden" name="update_id" id="updateChannelId">

  

      <div>
        <label class="block font-semibold mb-1">Preference Type</label>
        <select name="update_type" id="updateChannelType" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-[#f1ddbe] focus:border-[#f1ddbe]" required>
          <option value="">Select Type</option>
          <option value="Food">Food</option>
          <option value="Room">Room</option>
          <option value="Services">Services</option>
          <option value="Other">Other</option>
        </select>
      </div>

      <div>
        <label class="block font-semibold mb-1">Preference Details</label>
        <textarea name="update_description" id="updateDescription" rows="3" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-[#f1ddbe] focus:border-[#f1ddbe]"></textarea>
      </div>

      <div class="flex justify-end gap-3 pt-4">
        <button type="button" onclick="closeUpdateModal()" class="px-6 py-2 bg-red-400 hover:bg-red-500 text-white rounded-lg font-semibold transition">
          Cancel
        </button>
        <button type="submit" name="update_guest" class="px-6 py-2 bg-[#594423] hover:bg-[#4a3820] text-white rounded-lg font-semibold transition">
          Update
        </button>
      </div>
    </form>
  </div>
</div>

<div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50" onclick="closeViewModal()">
  <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md relative animate__animated animate__fadeInDown" onclick="event.stopPropagation()">
    

    <div class="flex items-center justify-between mb-6">
      <h2 class="text-2xl font-bold text-[#594423] flex items-center gap-2">
        <i class="fas fa-cogs"></i> Preference Details
      </h2>
      <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
    </div>


    <div class="space-y-4 text-gray-700 text-sm">
      <div class="flex items-center">
        <span class="font-semibold w-40">Preferences ID:</span>
        <span id="modalid" class="text-gray-900"></span>
      </div>
      <div class="flex items-center">
        <span class="font-semibold w-40">Preference Type:</span>
        <span id="modalChannelType" class="text-gray-900"></span>
      </div>
      <div class="flex items-start">
        <span class="font-semibold w-40">Preference Detail:</span>
        <span id="modalDescription" class="text-gray-900"></span>
      </div>
      <div class="flex items-center">
  <span class="font-semibold w-40">Guest Name:</span>
  <span id="modalgus" class="text-gray-900"></span> 
</div>

 
</div>



    <div class="flex justify-end mt-8">
      <button onclick="closeViewModal()" class="px-6 py-2 bg-[#594423] hover:bg-[#4a3820] text-white rounded-lg font-medium transition">
        Close
      </button>
    </div>

  </div>
</div>


<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50" onclick="closeDeleteModal()">
  <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md relative" onclick="event.stopPropagation()">
    <h2 class="text-xl font-bold mb-4 text-red-600">Confirm Deletion</h2>

    <p class="mb-4">Are you sure you want to delete this channel?</p>

    <form  method="POST" class="flex justify-end space-x-2">
      <input type="hidden" name="delete_preference_id" id="deleteChannelId">
      <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded">
        Cancel
      </button>
      <button type="submit" name="delete_preference" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded">
        Delete
      </button>
    </form>
  </div>
</div>

            </main>
        </div>
    </div>
    </div>

    <script>
        
    function changeItemsPerPage(select) {
  const items = select.value;
  window.location.href = "?page=1&items=" + items;
  }
  function openUpdateModal(button) {
    document.getElementById('updateChannelId').value = button.dataset.id;
    document.getElementById('updateChannelType').value = button.dataset.type;
    document.getElementById('updateDescription').value = button.dataset.description;

    document.getElementById('updateModal').classList.remove('hidden');
  }

  function closeUpdateModal() {
    document.getElementById('updateModal').classList.add('hidden');
  }
function openViewModal(button) {
  const name = button.getAttribute('data-id');
  const type = button.getAttribute('data-type');
  const description = button.getAttribute('data-description');
  const gues = button.getAttribute('data-gue');

  document.getElementById('modalgus').textContent = gues;
  
  document.getElementById('modalid').textContent = name;
  document.getElementById('modalChannelType').textContent = type;
  document.getElementById('modalDescription').textContent = description;

  document.getElementById('viewModal').classList.remove('hidden');
}

function closeViewModal() {
  document.getElementById('viewModal').classList.add('hidden');
}

function openDeleteModal(button) {
    const id = button.dataset.id;
    document.getElementById('deleteChannelId').value = id;
    document.getElementById('deleteModal').classList.remove('hidden');
  }

  function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
  }

      
    </script>
</body>
</html>
