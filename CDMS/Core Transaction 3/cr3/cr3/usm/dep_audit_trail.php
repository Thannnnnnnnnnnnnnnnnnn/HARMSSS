<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    // Redirect to login page
    header("Location: ../testing/login.php");
    exit;
}

$user = $_SESSION['user'];
$_SESSION['user_id'] = $user['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>                                               
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Trail</title>
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
            <!-- Main Content -->
          
            <main class="px-4 py-4">

            <h2 class="text-center text-2xl font-bold text-gray-800 mb-1">Department Audit Trail</h2>

            <div class="bg-gradient-to-b to-orange-100 min-h-screen p-8">

        


  <div class="overflow-x-auto bg-white shadow-lg rounded-xl">
  <table class="min-w-full h-50 text-[12px] text-left">
      <thead class="bg-[#f1ddbe] text-black uppercase">
      <tr>
      <th class="px-4 py-3">Audit Trail ID</th>
      <th class="px-4 py-3">Dept ID</th>
      <th class="px-4 py-3">User ID</th>
      <th class="px-4 py-3">Action</th>
      <th class="px-4 py-3">Department affected</th>
      <th class="px-4 py-3">Module affected</th>
    </tr>
  </thead>

  <tbody>
  <?php 
require 'connection.php';

$sql = "SELECT * FROM department_audit_trail";
$stmt = $conn->prepare($sql); 
$stmt->execute(); // ✅ Don't forget to execute!
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php foreach ($result as $dept_audit): ?>
<tr class="border-t">
  <td class="px-4 py-2"><?php echo htmlspecialchars($dept_audit['dept_audit_trail_id']); ?></td>
  <td class="px-4 py-2"><?php echo htmlspecialchars($dept_audit['department_id']); ?></td>
  <td class="px-4 py-2"><?php echo htmlspecialchars($dept_audit['user_id']); ?></td>
  <td class="px-4 py-2"><?php echo htmlspecialchars($dept_audit['action']); ?></td>
  <td class="px-4 py-2"><?php echo htmlspecialchars($dept_audit['department_affected']); ?></td>
  <td class="px-4 py-2"><?php echo htmlspecialchars($dept_audit['module_affected']); ?></td>
  

  <td>
    
  </td>
</tr>
<?php endforeach; ?>
      </tbody>
    </table>
    
  </div>


    </div>
            </main>
        </div>


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
