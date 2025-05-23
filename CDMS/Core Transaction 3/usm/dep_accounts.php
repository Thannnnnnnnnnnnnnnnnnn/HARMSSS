<?php
session_start();
require 'connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    // Redirect to login page
    header("Location: ../testing/login.php");
    exit;
}

$user = $_SESSION['user'];
$_SESSION['User_ID'] = $user['User_ID'];

// --- 2. HANDLE INSERT ---------------------------------------------------------------------------------------
if (isset($_POST['action']) && $_POST['action'] == 'insert') {
    $department_id = $_POST['department_id'];
    $user_id = $_POST['user_id'];
    $first_name = $_POST['first_name'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $conn->query("INSERT INTO dept_accounts (department_id, user_id, first_name, password, role)
                  VALUES ('$department_id', '$user_id', '$first_name', '$password', '$role')");
    header("Location: dept_accounts.php");
    exit;
}

// --- 3. HANDLE UPDATE ACCOUNT --------------------------------------------------------------------------------------------
if (isset($_GET['update_account'])) {
    $user_id = $_POST['user_id'];
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $username = $_POST['username'] ?? '';

    // Validate password match if provided
    if (!empty($new_password) || !empty($confirm_password)) {
        if ($new_password !== $confirm_password) {
            echo "Passwords do not match.";
            exit;
        }
        $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
    } else {
        // Password not updated: get current password from DB
        $stmt = $conn->prepare("SELECT password FROM user_account WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $hashedPassword = $stmt->fetchColumn();
    }

    // Prepare and execute update
    $sql = "UPDATE user_account 
            SET password = :password,
            username = :username
            WHERE user_id = :user_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: dep_accounts.php?status=updated");
        exit;
    } else {
        echo "Error updating account.";
    }
}

// --- 4. HANDLE ACTIVATION ----------------------------------------------------------------------------------------
if (isset($_GET['edit_status'])) {

$id = $_POST['dept_accounts_id'];
$status = $_POST['status'];

  $sql = "UPDATE Department_Accounts SET status = :status WHERE dept_accounts_id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: dep_accounts.php");
        exit;
    } else {
        echo "Error updating record.";
    }
}


// --- 5. HANDLE EDIT INFO. ----------------------------------------------------------------------------------------
if (isset($_GET['update_info'])) {

  
$user_id = $_POST['User_ID'];
$fname = $_POST['first_name'];
$lname = $_POST['last_name'];
$role = $_POST['role'];
$email = $_POST['email'];

  $sql = "UPDATE user_account SET first_name = :first_name, last_name = :last_name, email = :email, role = :role WHERE User_ID = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':first_name', $fname);
    $stmt->bindParam(':last_name', $lname);
    $stmt->bindParam(':role', $role);
     $stmt->bindParam(':email', $email);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: dep_accounts.php");
        exit;
    } else {
        echo "Error updating record.";
        header("Location: dep_accounts.php");
    }
}

// --- 6. HANDLE Delete ---------------------------------------------------------------------------------------
if (isset($_GET['delete_account'])) {
 
    $user_id = $_POST['User_ID'];
    $dept_accounts_id = $_POST['dept_accounts_id'];

    try {
        // Start transaction
        $conn->beginTransaction();

        // Delete from dept_accounts
        $stmt1 = $conn->prepare("DELETE FROM department_accounts WHERE dept_accounts_id = :dept_id");
        $stmt1->bindParam(':dept_id', $dept_accounts_id, PDO::PARAM_INT);
        $stmt1->execute();

        // Delete from user_account
        $stmt2 = $conn->prepare("DELETE FROM user_account WHERE User_ID = :user_id");
        $stmt2->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt2->execute();

        // Commit both deletions
        $conn->commit();

        header("Location: dep_accounts.php");
        exit;
    } catch (PDOException $e) {
        $conn->rollBack(); // Rollback if there's an error
        echo "Error deleting records: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>                                               
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department accounts</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body{
 font-family: "cinzel",Georgia;   
}
td{
            padding: 7px;
         }
        .sidebar-collapsed {
            width: 85px;
        }
        .sidebar-expanded {
            width: 320px;
        }
       
        .sidebar-collapsed .menu-name span,
        .sidebar-collapsed .menu-name .arrow {
            display: none;
        }
        
        .sidebar-collapsed .menu-name i {
            margin-right: 0;
        }
        
        .sidebar-collapsed .menu-drop {
            display: none;
        }

        .sidebar-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            position: fixed;
            inset: 0;
            z-index: 40;
            display: none;
        }

        .sidebar-overlay.active {
            display: block;
        }
        .close-sidebar-btn{
                display: none;
            }
        @media (max-width: 968px) {
            .sidebar {
                position: fixed;
                left: -100%;
                transition: left 0.3s ease-in-out;
            }

            .sidebar.mobile-active {
                left: 0;
            }

            .main {
                margin-left: 0 !important;
            }
            .close-sidebar-btn{
                display: block;
            }
        }
        .menu-name {
            position: relative;
            overflow: hidden;
        }

        .menu-name::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            height: 2px;
            width: 0;
            background-color: #4E3B2A;
            transition: width 0.3s ease;
        }

        .menu-name:hover::after {
            width: 100%;
        }
    </style>
</head>
<body>

    <div class="flex min-h-screen w-full">
    <?php include __DIR__ . '/../partials/admin/sidebar.php'; ?>
   <?php include __DIR__ . '/../partials/admin/navbar.php'; ?>
    <?php include __DIR__ . '/../partials/admin/head.php'; ?>
   
            <!-- Main Content -->
          
            <main class="px-4 py-4">


   <!-- Admin ------------------------------------------------------------------------------------------------------------------->
     <?php if ($user['role'] === 'admin' || $user['role'] === 'manager'): ?> 
  
 
            <h2 class="text-center text-2xl font-bold text-gray-800 mb-1">Department accounts</h2>
            <div class="bg-gradient-to-b to-orange-100 min-h-screen p-8">

        


  <div class="grid grid-flow-col bg-white auto-cols-max gap-5">
  <table class="min-w-full h-50 text-[12px] text-left">
      <thead class="bg-[#f1ddbe] text-black uppercase">
      <tr>
      <th class="px-4 py-3">Account ID</th>
      <th class="px-4 py-3">Dept ID</th>
      <th class="px-4 py-3">User ID</th>
      <th class="px-4 py-3">Name</th>
      <th class="px-4 py-3">Role</th>
      <th class="px-4 py-3">Status</th>
      <th class="px-4 py-3">Email</th>
      <th class="px-4 py-3">Action</th>
    </tr>
  </thead>

  <tbody>
  <?php 


$sql = "SELECT * FROM department_accounts";
$result = $conn->query($sql);


$stmt = $conn->prepare($sql); 
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<?php foreach ($result as $dept_accounts): ?>

<tr>
  <td class="px-4 py-2"><?php echo htmlspecialchars($dept_accounts['Dept_Accounts_ID']); ?></td>
  <td class="px-4 py-2"><?php echo htmlspecialchars($dept_accounts['Department_ID']); ?></td>
  <td class="px-4 py-2"><?php echo htmlspecialchars($dept_accounts['User_ID']); ?></td>
  <td class="px-4 py-2"><?php echo htmlspecialchars($dept_accounts['Name']); ?></td>
  <td class="px-4 py-2"><?php echo htmlspecialchars($dept_accounts['role']); ?></td>
  <td class="px-4 py-2">
  </td>

  <td>

<div x-data="{ open: false }" class="relative inline-block text-left">
  <!-- Main Action Button -->
  <button @click="open = !open" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md">
    Action
  </button>

  <!-- Dropdown Menu -->
  <div x-show="open" @click.away="open = false" 
       class="absolute right-0 mt-2 bg-white border rounded-lg shadow-lg z-50 p-2 space-y-2">

    <!-- Action Buttons as Icons -->
    <div class="flex justify-center space-x-2">

      <!-- View Button -->
      <button data-modal-target="modalView-<?php echo $dept_accounts['Dept_Accounts_ID']; ?>"
              class="open-modal bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-xl shadow-md"
              title="View">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </button>

      <!-- Edit Button -->
      <button data-modal-target="modalEdit-<?php echo $dept_accounts['Dept_Accounts_ID']; ?>"
              class="open-modal bg-green-600 hover:bg-green-700 text-white p-2 rounded-xl shadow-md"
              title="Edit">
            Account
      </button>

      <!-- Status Button -->
<button data-modal-target="modal-status-<?php echo $dept_accounts['Dept_Accounts_ID']; ?>"
        class="open-modal bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl shadow-md"
        title="Change Status">
  Status
</button>

 <!-- Edit Button -->
      <button data-modal-target="modalInfo-<?php echo $dept_accounts['Dept_Accounts_ID']; ?>"
              class="open-modal bg-green-600 hover:bg-green-700 text-white p-2 rounded-xl shadow-md"
              title="Edit">
            Edit
      </button>

      <!-- Delete Button -->
<button data-modal-target="modalDelete-<?php echo $dept_accounts['Dept_Accounts_ID']; ?>"
        class="open-modal bg-red-600 hover:bg-red-700 text-white p-2 rounded-xl shadow-md"
        title="Delete">
    Delete
</button>



    </div>

  </div>
</div>

<!-- Modals ----------------------------------------------------------------------------------------------------------------------------------------------------------------------->
<!-- View Modal -->
<div id="modalView-<?php echo $dept_accounts['Dept_Accounts_ID']; ?>" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-xl p-6 shadow-xl w-full max-w-md">
    <h2 class="text-xl font-semibold mb-4">View Details</h2>

                <p><strong>Department acc ID:</strong> <?php echo htmlspecialchars($dept_accounts['Dept_Accounts_ID']); ?> </span></p>
                <p><strong>Department ID:</strong> <?php echo htmlspecialchars($dept_accounts['Department_ID']); ?></span></p>
                <p><strong>User ID:</strong><?php echo htmlspecialchars($dept_accounts['User_ID']); ?></span></p>
                <p><strong>Name:</strong><?php echo htmlspecialchars($dept_accounts['Name']); ?></span></p>
                <p><strong>Password:</strong><?php echo htmlspecialchars($dept_accounts['Password']); ?></span></p>
                <p><strong>Role:</strong><?php echo htmlspecialchars($dept_accounts['Role']); ?></span></p>
            
    <div class="text-right mt-4">
      <button class="close-modal bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Close</button>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div id="modalEdit-<?php echo $dept_accounts['Dept_Accounts_ID']; ?>" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-xl p-6 shadow-xl w-full max-w-md">

    <h2 class="text-xl font-semibold mb-4">Edit Item</h2>

    <form method="POST" action="?update_account">
  <!-- Hidden field to keep track of the ID -->
  <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($dept_accounts['User_ID']); ?>">

  <div class="mb-4">
    <label class="block font-semibold">Username:</label>
    <input type="text" name="username" value="<?php echo htmlspecialchars($dept_accounts['Email']); ?>" class="w-full border border-gray-300 p-2 rounded-md">
  </div>

  <div class="mb-4">
    <label class="block font-semibold">New Password:</label>
    <input type="password" name="new_password" value="<?php echo htmlspecialchars($dept_accounts['Password']); ?>" class="w-full border border-gray-300 p-2 rounded-md">
  </div>

  <div class="mb-4">
    <label class="block font-semibold">Confirm passowrd:</label>
    <input type="password" name="confirm_password" value="<?php echo htmlspecialchars($dept_accounts['Password']); ?>" class="w-full border border-gray-300 p-2 rounded-md">
  </div>



  <div class="text-right">
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Update</button>
  </div>
</form>

    <div class="text-right mt-4">
      <button class="close-modal bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">Close</button>
    </div>
  </div>
</div>



<!-- Edit Status Modal -->
<div id="modal-status-<?php echo $dept_accounts['Dept_Accounts_ID']; ?>" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-xl p-6 shadow-xl w-full max-w-md">
    <h2 class="text-xl font-semibold mb-4">Account Status</h2>

    <form method="POST" action="?edit_status">
      <!-- Hidden field to keep track of the ID -->
      <input type="hidden" name="dept_accounts_id" value="<?php echo htmlspecialchars($dept_accounts['Dept_Accounts_ID']); ?>">

      <div class="mb-4">
        <select name="status" class="w-full border border-gray-300 p-2 rounded-md">
          <option value="active" <?php echo $dept_accounts['Status'] === 'active' ? 'selected' : ''; ?>>Activate</option>
          <option value="inactive" <?php echo $dept_accounts['Status'] === 'inactive' ? 'selected' : ''; ?>>Deactivate</option>
        </select>
      </div>

      <div class="text-right mt-4">
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded shadow-md">
          Save
        </button>
        <button type="button" class="close-modal bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg ml-2">
          Close
        </button>
      </div>
    </form>

  </div>
</div>

<!-- Edit Info Modal ----------------------------------------------------------------->
<div id="modalInfo-<?php echo $dept_accounts['Dept_Accounts_ID']; ?>" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-xl p-6 shadow-xl w-full max-w-md">

    <h2 class="text-xl font-semibold mb-4">Edit Item</h2>

    <form method="POST" action="?update_info">
  <!-- Hidden field to keep track of the ID -->
  <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($dept_accounts['User_ID']); ?>">

  <div class="mb-4">
    <label class="block font-semibold">Name:</label>
    <input type="text" name="first_name" value="<?php echo htmlspecialchars($dept_accounts['Name']); ?>" class="w-full border border-gray-300 p-2 rounded-md" >
  </div>


  <div class="mb-4">
    <label class="block font-semibold">Role:</label>
    <input type="text" name="role" value="<?php echo htmlspecialchars($dept_accounts['Role']); ?>" class="w-full border border-gray-300 p-2 rounded-md" >
  </div>

  <div class="mb-4">
    <label class="block font-semibold">Email:</label>
    <input type="text" name="email" value="<?php echo htmlspecialchars($dept_accounts['Email']); ?>" class="w-full border border-gray-300 p-2 rounded-md" >
  </div>

  <div class="text-right">
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Update</button>
  </div>

</form>

    <div class="text-right mt-4">
      <button class="close-modal bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">Close</button>
    </div>
  </div>
</div>

<!-- Delete Status Modal -->
<div id="modalDelete-<?php echo $dept_accounts['Dept_Accounts_ID']; ?>" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-xl p-6 shadow-xl w-full max-w-md">
    <h2 class="text-xl font-semibold mb-4">Delete?</h2>

    <form method="POST" action="?delete_account">
      <!-- Hidden field to keep track of the ID -->
      <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($dept_accounts['User_ID']); ?>">
      <input type="hidden" name="dept_accounts_id" value="<?php echo htmlspecialchars($dept_accounts['Dept_Accounts_ID']); ?>">

      <h3>Are you sure You want to delete this account?<h3>
      <h3>User ID: <?php echo htmlspecialchars($dept_accounts['User_ID']); ?><h3>

      <div class="text-right mt-4">
        <button type="submit" class="bg-red-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded shadow-md">
          Save
        </button>
        <button type="button" class="close-modal bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg ml-2">
          Close
        </button>
      </div>
    </form>

  </div>
</div>


  </td>
</tr>
<?php endforeach; ?>
      </tbody>
    </table>
    
  </div>

      <?php endif; ?>      
  <!-- Manager ------------------------------------------------------------------------------------------------------------------->
   
     
    </div>
            </main>
        </div>
        <script>

        // Open modal
document.querySelectorAll('.open-modal').forEach(button => {
  button.addEventListener('click', () => {
    const targetId = button.getAttribute('data-modal-target');
    document.getElementById(targetId).classList.remove('hidden');
  });
});

// Close modal
document.querySelectorAll('.close-modal').forEach(button => {
  button.addEventListener('click', () => {
    button.closest('.modal').classList.add('hidden');
  });
});
</script>



    <script>
        const menu = document.querySelector('.menu-btn');
        const sidebar = document.querySelector('.sidebar');
        const main = document.querySelector('.main');
        const overlay = document.getElementById('sidebar-overlay');
        const close = document.getElementById('close-sidebar-btn');

        function closeSidebar() {
            sidebar.classList.remove('mobile-active');
            overlay.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function openSidebar() {
            sidebar.classList.add('mobile-active');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function toggleSidebar() {
            if (window.innerWidth <= 968) {
                sidebar.classList.add('sidebar-expanded'); 
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.classList.contains('mobile-active') ? closeSidebar() : openSidebar();
            } else {
                sidebar.classList.toggle('sidebar-collapsed');
                sidebar.classList.toggle('sidebar-expanded');
                main.classList.toggle('md:ml-[85px]');
                main.classList.toggle('md:ml-[360px]');
            }
        }

        menu.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', closeSidebar);
        close.addEventListener('click', closeSidebar);

        window.addEventListener('resize', () => {
            if (window.innerWidth > 968) {
                closeSidebar();
                sidebar.classList.remove('mobile-active');
                overlay.classList.remove('active');
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.classList.add('sidebar-expanded'); 
            } else {
                sidebar.classList.add('sidebar-expanded'); 
                sidebar.classList.remove('sidebar-collapsed');
            }
        });

         function toggleDropdown(dropdownId, element) {
            const dropdown = document.getElementById(dropdownId);
            const icon = element.querySelector('.arrow-icon');
            const allDropdowns = document.querySelectorAll('.menu-drop');
            const allIcons = document.querySelectorAll('.arrow-icon');

            allDropdowns.forEach(d => {
                if (d !== dropdown) d.classList.add('hidden');
            });

            allIcons.forEach(i => {
                if (i !== icon) {
                    i.classList.remove('bx-chevron-down');
                    i.classList.add('bx-chevron-right');
                }
            });

            dropdown.classList.toggle('hidden');
            icon.classList.toggle('bx-chevron-right');
            icon.classList.toggle('bx-chevron-down');
        }
    </script>
</body>
</html>
