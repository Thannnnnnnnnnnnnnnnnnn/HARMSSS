<?php

session_start();
if (!isset($_SESSION['user'])) {
    // Redirect to login page
    header("Location: ../testing/login.php");
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'];
$user_id = $_SESSION['user_id'] = $user['user_id'];


include $_SERVER['DOCUMENT_ROOT'] . "/cr3/cr3bo/conn.php";

?>
  
<?php 
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
     $db_guest = "cr3_re";
  $db_name = "cr3_re"; 
if (!isset($connections[$db_name]) || !$connections[$db_name]) {
    die("Database connection failed.");
  }

  $connection = $connections[$db_name];
  

  $query = "SELECT * FROM booking_channels"; 
  $result = mysqli_query($connection , $query);    
  
  $itemsPerPage = isset($_GET['items'])?(int)$_GET['items']:10;
$page = isset($_GET['page'])?(int)$_GET['page']:1;
$offset = ($page - 1) * $itemsPerPage;

$totalResults = $connection->query("SELECT COUNT(*) AS total FROM booking_channels");
$totalRow = $totalResults ->fetch_assoc();
$totalItems =$totalRow['total'];
$totalPages = ceil($totalItems / $itemsPerPage);
$query = "SELECT * FROM booking_channels ORDER BY ChannelID ASC LIMIT $offset, $itemsPerPage";
$result =$connection->query($query);


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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['create_channel'])) {
    $channel_name = trim($_POST['channel_name'] ?? '');
    $channel_type = trim($_POST['channel_type'] ?? '');
    $description = trim($_POST['description'] ?? '');

    function is_probably_real_word($word) {
        static $dictionary = null;
        if ($dictionary === null) {
            $dictionary = file('english-words-master/words.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $dictionary = array_map('strtolower', $dictionary);
        }
    
        $word = strtolower($word);
    
        if (in_array($word, $dictionary)) return true;
    
        if (preg_match('/^[a-z]{3,}$/', $word)) {
            $vowels = preg_match_all('/[aeiou]/', $word);
            if ($vowels >= 2) return true;
        }
    
        return false;
    }
    
    $words = explode(" ", $channel_name);
    $valid_real_word = false;
    
    foreach ($words as $word) {
        if (strlen($word) >= 3 && is_probably_real_word($word)) {
            $valid_real_word = true;
            break;
        }
    }
    
    if (!$valid_real_word) {
        $_SESSION['error_message'] = "❌ Channel name must be a real or valid word (min 3 letters, 2+ vowels, no gibberish).";
        $_SESSION['show_insert_modal'] = true;
        header("Location: Channel.php");
        exit();
    }

    $stmt = $connection->prepare("INSERT INTO booking_channels (ChannelName, ChannelType, Description) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $channel_name, $channel_type, $description);

    if ($stmt->execute()) {
        $id = mysqli_insert_id($connection);
        $_SESSION['success_message'] = "Channel added successfully!";
        
        $notification = $notif->prepare("INSERT INTO notifications (User_ID, notifType, message, status, Department, date_sent) 
            VALUES (?, 'create', ?, 'Unread', ?, NOW())");
        $message = "- $role, Create New Channel $id.";
        $notification->bind_param("sss", $user_id, $message, $department);
        $notification->execute();

        logTransaction($usmDb, $department_id, $user_id, 'Insert', "$user_name Insert Booking Channel Data");
        logAudit($usmDb, $department_id, $user_id, 'Insert', $department, 'Booking', "$user_name Insert Booking Channel Data");

        header("Location: Channel.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error: " . $stmt->error;
    }

    $stmt->close();
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_channel'])) {
    $id = $_POST['update_id'];
    $name = trim($_POST['update_name']);
    $type = trim($_POST['update_type']);
    $description = trim($_POST['update_description']);

    $origboss = $connection->prepare("SELECT * FROM booking_channels WHERE ChannelID = ?");
    $origboss->bind_param("i", $id); 
    $origboss->execute();
    $resultboss = $origboss->get_result();
    $bossdata = $resultboss->fetch_assoc();
    $origboss->close();

    $binago = [];
    if ($bossdata['ChannelName'] !== $name) {
        $binago[] = "Name: '{$bossdata['ChannelName']}' : '$name'";
    }
    if ($bossdata['ChannelType'] !== $type) {
        $binago[] = "Channel Type: '{$bossdata['ChannelType']}' : '$type'";
    }
    if ($bossdata['Description'] !== $description) {
        $binago[] = "Description: '{$bossdata['Description']}' : '$description'";
    }

    if (!empty($binago)) {
        $stmt = $connection->prepare("UPDATE booking_channels SET ChannelName=?, ChannelType=?, Description=? WHERE ChannelID=?");
        $stmt->bind_param("sssi", $name, $type, $description, $id);
    
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Channel updated successfully!";
            $changeSummary = implode("; ", $binago);
            
            $notification = $notif->prepare("INSERT INTO notifications (User_ID, notifType, message, status, Department, date_sent) 
                VALUES (?, 'update', ?, 'Unread', ?, NOW())");
            $message = "- $role updated Booking Channel ID #$id. Changes: $changeSummary";
            $notification->bind_param("sss", $user_id, $message, $department);
            $notification->execute();

            logTransaction($usmDb, $department_id, $user_id, 'Update', "$user_name Update Booking Channel Data");
            logAudit($usmDb, $department_id, $user_id, 'Update', $department, 'Booking', "$user_name Update Booking Channel Data");

            header("Location: Channel.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Update Error: " . $stmt->error;
        }
    
        $stmt->close();
    }
}



if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_channel'])) {
    $channel_id = $_POST['delete_channel_id'];
  
    $stmt = $connection->prepare("DELETE FROM booking_channels WHERE ChannelID = ?");
    $stmt->bind_param("i", $channel_id);
  
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Channel deleted successfully!";
        
        $notification = $notif->prepare("INSERT INTO notifications (User_ID, notifType, message, status, Department, date_sent) 
            VALUES (?, 'delete', ?, 'Unread', ?, NOW())");
        $message = "- $role Delete Channel ID #$channel_id";
        $notification->bind_param("sss", $user_id, $message, $department);
        $notification->execute();

        logTransaction($usmDb, $department_id, $user_id, 'Delete', "$user_name Delete Booking Channel Data");
        logAudit($usmDb, $department_id, $user_id, 'Delete', $department, 'Booking', "$user_name Delete Booking Channel Data");

        header("Location: Channel.php");
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


        


           <h2 class="text-center text-2xl font-bold text-[#594423] mb-4">Booking Channels</h2>

            <div class="bg-gradient-to-b from-white to-orange-100 min-h-screen p-8">


 


          <div class="overflow-x-auto bg-white shadow-lg rounded-md">
            <?php if($role === 'admin'): ?>
  <button onclick="document.getElementById('insertModal').classList.remove('hidden')" 
  class="bg-[#f1ddbe]  text-black px-4 py-2 rounded mb-4">
  + Add Channel
  </button>
  <?php endif; ?>
  <table class="min-w-full text-[12px] text-center text-[#594423] ">
      <thead class="bg-[#F7E6CA] text-[#594423]  uppercase">
      <tr>
          <th class="px-4 py-3">Channel ID</th>
          <th class="px-4 py-3">Channel Name</th>
          <th class="px-4 py-3">Channel Type</th>
          <th class="px-4 py-3">Description</th>
          <th class="px-4 py-3">Action</th>
        </tr>
      </thead>
      <tbody>
      <tbody>
        <?php
    
       

        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            
            $channelType = $row['ChannelType'];
            $isOffline = strtolower($channelType) === 'offline';

           $bgClass = $isOffline ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800';

            echo "<tr class='hover:bg-[#FFF6E8] text-[11px]'>
              <td class='border px-4 py-2'>{$row['ChannelID']}</td>
              <td class='border px-4 py-2'>{$row['ChannelName']}</td>
               <td class='border px-4 py-2  $bgClass '>$channelType</td>
              <td class='border px-4 py-2'>{$row['Description']}</td>
             <td class='border p-2 flex justify-center space-x-2'>";

          

         
            echo "
            <button 
           onclick='openViewModal(this)' 
               data-name=\"" . htmlspecialchars($row['ChannelName']) . "\" 
              data-type=\"" . htmlspecialchars($row['ChannelType']) . "\" 
                data-description=\"" . htmlspecialchars($row['Description']) . "\" 
               class='bg-[#f1ddbe] rounded p-2 text-black'>
               <i class=\"bx bx-show\"></i>
                 </button>";
                  
                  
                  if($role === 'admin' ){ 
                       echo "
        
                    <button 
        
                   onclick='openUpdateModal(this)' 
                   data-id=\"{$row['ChannelID']}\"
                    data-name=\"" . htmlspecialchars($row['ChannelName']) . "\" 
                   data-type=\"" . htmlspecialchars($row['ChannelType']) . "\" 
                   data-description=\"" . htmlspecialchars($row['Description']) . "\" 
                   class='bg-[#f1ddbe] rounded-sm p-2 text-black'>
                    <i class=\"bx bx-edit\"></i>
                      </button>
                  
              
            
                         <button 
                  onclick='openDeleteModal(this)' 
                 data-id=\"{$row['ChannelID']}\"
                   class='bg-[#f1ddbe] rounded-sm p-2 text-black'>
                     <i class=\"bx bx-trash\"></i>
                  </button>";
                }
                echo "
                </td>

            </tr>";
          }
        } else {
          echo "<tr><td colspan='9' class='text-center py-4'>No Channel Found.</td></tr>";
        }
        ?>
      </tbody>
   
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
            </main>
        </div>
    </div>
    </div>


    <div id="insertModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
  <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg relative">
    <h2 class="text-xl font-bold mb-4">Add New Channel</h2>

    <form  method="POST" class="space-y-4">
      <div>
        <label class="block text-sm font-medium ">Channel Name</label>
        <input type="text" name="channel_name"
       pattern="^[A-Za-z]+(?: [A-Za-z]+)*$"
       title="Only valid names with letters and spaces allowed"
       required class="w-full border p-2 rounded">
      </div>

      <div>
        <label class="block text-sm font-medium">Channel Type</label>
        <select name="channel_type" required class="w-full border p-2 rounded">
          <option value="">Select Type</option>
          <option value="Online">Online</option>
          <option value="Offline">Offline</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium">Description</label>
        <textarea name="description" rows="3" class="w-full border p-2 rounded"></textarea>
      </div>

      <div class="flex justify-end space-x-2 pt-2">
        <button type="button" onclick="document.getElementById('insertModal').classList.add('hidden')" 
          class="px-4 py-2 bg-red-300 hover:bg-red-400 rounded">
          Cancel
        </button>
        <button type="submit" name="create_channel" class="px-4 py-2 bg-[#f1ddbe] text-black rounded">
          Submit
        </button>
      </div>
    </form>
  </div>
</div>


<!-- view -->



<div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50" onclick="closeViewModal()">
  <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg relative animate__animated animate__fadeInDown" onclick="event.stopPropagation()">
    

    <div class="flex items-center justify-between mb-6">
      <h2 class="text-2xl font-bold text-[#594423] flex items-center gap-2">
        <i class="fas fa-info-circle"></i> Channel Details
      </h2>
      <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
    </div>

    <div class="space-y-4 text-gray-700 text-sm">
      <div class="flex items-center">
        <span class="font-semibold w-40">Channel Name:</span>
        <span id="modalChannelName" class="text-gray-900"></span>
      </div>
      <div class="flex items-center">
        <span class="font-semibold w-40">Channel Type:</span>
        <span id="modalChannelType" class="text-gray-900"></span>
      </div>
      <div class="flex items-start">
        <span class="font-semibold w-40">Description:</span>
        <span id="modalDescription" class="text-gray-900"></span>
      </div>
    </div>


    <div class="flex justify-end mt-8">
      <button onclick="closeViewModal()" class="px-6 py-2 bg-[#594423] hover:bg-[#4a3820] text-white rounded-lg font-medium transition">
        Close
      </button>
    </div>
    
  </div>
</div>





<div id="updateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50" onclick="closeUpdateModal()">
  <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg relative animate__animated animate__fadeInDown" onclick="event.stopPropagation()">
    
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-2xl font-bold text-[#594423] flex items-center gap-2">
        <i class="fas fa-edit"></i> Update Channel
      </h2>
      <button onclick="closeUpdateModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
    </div>

    <form method="POST"  class="space-y-5 text-sm text-gray-700">
      <input type="hidden" name="update_id" id="updateChannelId">

      <div>
        <label class="block font-semibold mb-1">Channel Name</label>
        <input type="text" name="update_name" id="updateChannelName" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-[#f1ddbe] focus:border-[#f1ddbe]" required>
      </div>

      <div>
        <label class="block font-semibold mb-1">Channel Type</label>
        <select name="update_type" id="updateChannelType" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-[#f1ddbe] focus:border-[#f1ddbe]" required>
          <option value="">Select Type</option>
          <option value="Online">Online</option>
          <option value="Offline">Offline</option>
        </select>
      </div>

      <div>
        <label class="block font-semibold mb-1">Description</label>
        <textarea name="update_description" id="updateDescription" rows="3" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-[#f1ddbe] focus:border-[#f1ddbe]"></textarea>
      </div>

      <div class="flex justify-end gap-3 pt-4">
        <button type="button" onclick="closeUpdateModal()" class="px-6 py-2 bg-red-400 hover:bg-red-500 text-white rounded-lg font-semibold transition">
          Cancel
        </button>
        <button type="submit" name="update_channel" class="px-6 py-2 bg-[#594423] hover:bg-[#4a3820] text-white rounded-lg font-semibold transition">
          Update
        </button>
      </div>
    </form>
  </div>
</div>


<!-- delete -->

<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50" onclick="closeDeleteModal()">
  <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md relative" onclick="event.stopPropagation()">
    <h2 class="text-xl font-bold mb-4 text-red-600">Confirm Deletion</h2>

    <p class="mb-4">Are you sure you want to delete this channel?</p>

    <form  method="POST" class="flex justify-end space-x-2">
      <input type="hidden" name="delete_channel_id" id="deleteChannelId">
      <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded">
        Cancel
      </button>
      <button type="submit" name="delete_channel" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded">
        Delete
      </button>
    </form>
  </div>
</div>

    <script>

      
      
    function changeItemsPerPage(select) {
  const items = select.value;
  window.location.href = "?page=1&items=" + items;
  }
    



    
     
function openViewModal(button) {
  const name = button.getAttribute('data-name');
  const type = button.getAttribute('data-type');
  const description = button.getAttribute('data-description');
  document.getElementById('modalChannelName').textContent = name;
  document.getElementById('modalChannelType').textContent = type;
  document.getElementById('modalDescription').textContent = description;
  document.getElementById('viewModal').classList.remove('hidden');
}

function closeViewModal() {
  document.getElementById('viewModal').classList.add('hidden');
}

function openUpdateModal(button) {
    document.getElementById('updateChannelId').value = button.dataset.id;
    document.getElementById('updateChannelName').value = button.dataset.name;
    document.getElementById('updateChannelType').value = button.dataset.type;
    document.getElementById('updateDescription').value = button.dataset.description;

    document.getElementById('updateModal').classList.remove('hidden');
  }

  function closeUpdateModal() {
    document.getElementById('updateModal').classList.add('hidden');
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
