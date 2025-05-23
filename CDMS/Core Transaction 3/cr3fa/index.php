<?php
// Database connection
session_start();
$role = $_SESSION['role'] ?? null;
$user = $_SESSION['user'] ?? null;

require '../Database.php';
require '../functions.php';

$config = require '../config.php';

$db = new Database($config['database']);
$conn = $db->getConnection(); // Now $conn is a mysqli object

$query = "SELECT * FROM facilities";
$result = mysqli_query($conn, $query);





?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facility</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />


</head>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }

    button {
        padding: 10px 20px;
    }



    .btn {
        padding: 5px 10px;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        color: white;
    }

    .btn-view {
        background: #3b82f6;
    }

    .btn-edit {
        background: #10b981;
    }

    .btn-delete {
        background: #ef4444;
    }

    .btn-receipt {
        background: rgb(30, 17, 17);
    }

    .modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 20px;
        box-shadow: 0px 0px 10px gray;
        border-radius: 5px;
    }

    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th,
    td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background: #1d2939;
        color: white;
    }

    .btn {
        padding: 5px 10px;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        color: white;
    }

    .btn-view {
        background: #3b82f6;
    }

    .btn-edit {
        background: #10b981;
    }

    .btn-delete {
        background: #ef4444;
    }

    .btn-receipt {
        background: rgb(30, 17, 17);
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

    .close-sidebar-btn {
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

        .close-sidebar-btn {
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

    .svdata input {
        border: 1px solid #4E3B2A;
        margin-bottom: 10px;

    }

    .svdata label {
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }

    .modal form {
        display: flex;
        flex-direction: column;
        width: 90%;
        align-items: center;
    }

    .modal label {
        margin-top: 5px;
        font-weight: bold;
    }

    .modal input {
        width: 80%;
        padding: 8px;
        margin-bottom: 5px;
        border-radius: 6px;

        border: 1px solid #4E3B2A;
    }

    .btn-group {
        display: flex;
        gap: 10px;
        margin-top: px;
    }

    .modal {
        align-items: center;
        justify-content: center;
        margin: 30px auto;
        width: 25%;

    }

    .modal h3 {
        font-size: 18px;
        font-weight: bold;
        text-align: center;
        margin-top: 20px;
    }

    .savebtn {
        font-size: 16px;
        font-weight: bold;
        padding: 10px 20px;
        background-color: #fff;
        color: #4E3B2A;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 1px solid #4E3B2A;
    }

    .savebtn:hover {
        background-color: #4E3B2A;
        color: #fff;


    }
</style>
</head>

<body>
    <?php include __DIR__ . '/../partials/admin/sidebar.php'; ?>
    <?php include __DIR__ . '/../partials/admin/navbar.php'; ?>

    <?php if ($user['role'] === 'admin' || $user['role'] === 'manager' || $user['role'] === 'staff'): ?>
    <div class="flex min-h-screen w-full">

        <div class="sidebar-overlay" id="sidebar-overlay"></div>


        <div class="sidebar sidebar-expanded fixed z-50 overflow-hidden h-screen bg-white border-r border-[#F7E6CA] flex flex-col">
            <div class="h-16 border-b border-[#F7E6CA] flex items-center">
                <img src="img/Logo.png" alt="Logo" class="w-20 h-20 py-2 pl-2" />
                <img src="img/Logo-Name.png" alt="Logo" class="w-30 h-12 p-3" />


                <i id="close-sidebar-btn" class="fa-solid fa-x close-sidebar-btn transform translate-x-[50px] font-bold text-xl cursor-pointer"></i>
            </div>
            <div class="side-menu px-4 py-6">
                <ul class="space-y-4">

                    <div class="menu-option">
                        <a href="finalTemplate.html" class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-house text-lg pr-4"></i>
                                <span class="text-sm font-medium">Facilities</span>
                            </div>
                        </a>
                    </div>

                    <div class="menu-option">
                        <a href="#" class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-folder-open text-lg pr-4"></i>
                                <span class="text-sm font-medium">Maintenance Request</span>
                            </div>
                        </a>
                    </div>


                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('account-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-chart-pie text-lg pr-4"></i>
                                <span class="text-sm font-medium">Usage Logs</span>
                            </div>

                        </div>

                    </div>
                </ul>

            </div>
        </div>

        <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">

            <nav class="h-16 w-full bg-white border-b border-[#F7E6CA] flex justify-between items-center px-6 py-4">

                <div class="left-nav flex items-center space-x-4 max-w-96 w-full">

                    <button aria-label="Toggle menu" class="menu-btn text-[#4E3B2A] focus:outline-none hover:bg-[#F7E6CA] hover:rounded-full">
                        <i class="fa-solid fa-bars text-[#594423] text-xl w-11 py-2"></i>
                    </button>

                    <div class="relative w-full flex pr-2">
                        <input type="text"
                            class="bg-[#FFF6E8] h-10 rounded-lg grow w-full pl-10 pr-4 focus:ring-2 focus:ring-[#F7E6CA] focus:outline-none"
                            placeholder="Search something..."
                            aria-label="Search input" />
                        <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-[#4E3B2A]"></i>
                    </div>
                </div>

                <div>
                    <i class="fa-regular fa-user bg-[#594423] text-white px-4 py-2 rounded-lg cursor-pointer text-lg lg:hidden" aria-label="User profile"></i>
                </div>

                <div class="right-nav items-center space-x-6 hidden lg:flex">
                    <button aria-label="Notifications" class="text-[#4E3B2A] focus:outline-none border-r border-[#F7E6CA] pr-6 relative">
                        <i class="fa-regular fa-bell text-xl"></i>
                        <span class="absolute top-0.5 right-5 block w-2.5 h-2.5 bg-[#594423] rounded-full"></span>
                    </button>

                    <div class="flex items-center space-x-2">
                        <i class="fa-regular fa-user bg-[#594423] text-white px-4 py-2 rounded-lg text-lg" aria-label="User profile"></i>
                        <div class="info flex flex-col py-2">
                            <h1 class="text-[#4E3B2A] font-semibold font-serif text-sm">Madelyn Cline</h1>
                            <p class="text-[#594423] text-sm pl-2">Administrator</p>
                        </div>
                    </div>
                </div>
            </nav>

            <main class="px-8 py-8">
                <div class="max-w mx-auto">
                    <header class="mb-8">
                    </header>

                    <div class="text-left mb-8">
                        <button onclick="openModal()" class="bg-[#1F2937] text-white px-6 py-3 rounded-lg hover:bg-[#4E3B2A] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors shadow-md">
                            Request Maintenance
                        </button>

                        <div id="overlay" class="modal-overlay" onclick="closeModal()">

                        </div>

                        <div id="modal" class="modal">
                            <h3>Enter Maintenance Request Details</h3>
                            <form action="save_facilities.php" method="post" class="svdata">

                                <label>Asset Repairs</label>
                                <input type="text" name="Asset_Repairs" required><br>

                                <label>Room No.</label>
                                <input type="number" name="Room_No" required><br>

                                <label>Date:</label>
                                <input type="date" name="date" required><br>


                                <div class="btn-group">
                                    <button type="submit" class="savebtn">Save</button>
                                    <button type="button" class="savebtn" onclick="closeModal()">Cancel</button>
                                </div>

                            </form>
                        </div>
                    </div>


                    <div class="bg-white p-8 rounded-xl shadow-md mb-12">
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-100 uppercase tracking-wider rounded-tl-lg" style="background-color: #1F2937;">Facility ID</th>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-100 uppercase tracking-wider" style="background-color: #1F2937;">Asset Repairs</th>

                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-100 uppercase tracking-wider" style="background-color: #1F2937;">Room No. </th>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-100 uppercase tracking-wider" style="background-color: #1F2937;">Date</th>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-100 uppercase tracking-wider" style="background-color: #1F2937;">Action</th>

                                    </tr>
                                <tbody class="divide-y divide-gray-200">
                                     

                                     <?php while ($row = mysqli_fetch_assoc($result)) : ?>
    <tr>
        <td><?php echo htmlspecialchars($row['FacilityID']); ?></td>
        <td><?php echo htmlspecialchars($row['Asset_Repairs']); ?></td>
        <td><?php echo htmlspecialchars($row['Room_No']); ?></td>
        <td><?php echo htmlspecialchars($row['Date']); ?></td>
        <td>
            <button class='btn btn-view' onclick="viewfacilities(<?php echo $row['FacilityID']; ?>)">👁</button>
            <button class='btn btn-edit' onclick="editfacilities(<?php echo $row['FacilityID']; ?>)">✏</button>
            <button class="delete-btn btn btn-delete" data-id="<?php echo $row['FacilityID']; ?>">🗑</button>
        </td>
    </tr>
<?php endwhile; ?>
                                </tbody>
                                </thead>

                            </table>
                        </div>
                    </div>



                    <script>
                        function viewFacilities(id) {
                            alert("Viewing facilities " + id);
                        }

                        function editfacilities(id, currentName = '', roomNo = '', date = '') {
                            document.getElementById('FacilityID').value = id;
                            document.getElementById('Asset_Repairs').value = currentName;
                            document.getElementById('Room_No').value = roomNo;
                            document.getElementById('Date').value = date;

                            document.getElementById('modal-edit').style.display = 'block';
                        }

                        function closeModal() {
                            document.getElementById('modal-edit').style.display = 'none';
                        }




                        $(document).on('click', '.delete-btn', function() {
                            var facilities = $(this).data('id');
                            Swal.fire({
                                title: "Are you sure?",
                                text: "You won't be able to revert this!",
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#6a5acd",
                                cancelButtonColor: "#6c757d",
                                confirmButtonText: "Yes, delete it!"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "delete_facilities.php?id=" + facilities;
                                }
                            });
                        });



                        function openModal() {
                            document.getElementById('modal').style.display = 'block';
                            document.getElementById('overlay').style.display = 'block';
                        }

                        function closeModal() {
                            document.getElementById('modal').style.display = 'none';
                            document.getElementById('overlay').style.display = 'none';
                        }
                    </script>
<?php endif; ?>
</body>

</html>