<?php
include 'db_connection.php';


// Handle Add Trainer
if (isset($_POST['add_trainer'])) {
    $stmt = $conn->prepare("INSERT INTO trainers (FullName, Email) VALUES (?, ?)");
    $stmt->bind_param("ss", $_POST['full_name'], $_POST['email']);
    $stmt->execute();
    $stmt->close();
    // addNotification($conn, 'A new trainer has been added: ' . $_POST['full_name']);
    // header('Location: trainers.php');
    // exit;
}

// Handle Edit Trainer
if (isset($_POST['edit_trainer'])) {
    $stmt = $conn->prepare("UPDATE trainers SET FullName=?, Email=? WHERE TrainerID=?");
    $stmt->bind_param("ssi", $_POST['full_name'], $_POST['email'], $_POST['trainer_id']);
    $stmt->execute();
    $stmt->close();
}

// Handle Delete Trainer
if (isset($_GET['delete_trainer'])) {
    $trainer_id = $_GET['delete_trainer'];

    // Get trainer name for notification before deleting
    $trRes = $conn->prepare("SELECT FullName FROM trainers WHERE TrainerID = ?");
    $trRes->bind_param("i", $trainer_id);
    $trRes->execute();
    $trRes->bind_result($trainerName);
    $trRes->fetch();
    $trRes->close();

    // Delete related enrollments first
    $stmt1 = $conn->prepare("DELETE FROM Enrollments WHERE TrainerID = ?");
    $stmt1->bind_param("i", $trainer_id);
    $stmt1->execute();
    $stmt1->close();

    // Then delete the trainer
    $stmt2 = $conn->prepare("DELETE FROM Trainers WHERE TrainerID = ?");
    $stmt2->bind_param("i", $trainer_id);
    $stmt2->execute();
    $stmt2->close();

    // if (!empty($trainerName)) {
    // }

    // header('Location: trainers.php');
    // exit;
}

// Fetch Trainers
$trainers = $conn->query("SELECT * FROM trainers");
?>
<!-- HEAD -->
<?php require 'partials/admin/head.php' ?>
<!-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
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

        .sidebar.sidebar-collapsed #logo-name {
            display: none;
        }
    </style>
</head>

<body> -->
<div class="flex min-h-screen w-full">
    <!-- Overlay && Sidebar -->
    <?php require 'partials/admin/sidebar.php' ?>
    <!-- <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <div class="sidebar sidebar-expanded fixed z-50 overflow-hidden h-screen bg-white border-r border-[#F7E6CA] flex flex-col">
        <div class="h-16 border-b border-[#F7E6CA] flex items-center px-2 space-x-2">
            <img src="logo.png" alt="Logo" class="h-15 w-10 rounded-x1 object-cover" />

            <span id="logo-name" class="transition-all duration-300 overflow-hidden">
                <img src="logo-name.png" alt="Logo Text" class="h-10 w-auto object-contain" />
            </span>
            <i id="close-sidebar-btn" class="fa-solid fa-x close-sidebar-btn transform translate-x-20 font-bold text-xl"></i>
        </div>
        <div class="side-menu px-4 py-6">
            <ul class="space-y-4">
                <div class="menu-option">
                    <a href="index.php" class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-house text-lg pr-4"></i>
                            <span class="text-sm font-medium">Dashboard</span>
                        </div>

                    </a>
                </div>

                <div class="menu-option">
                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('disbursement-dropdown', this)">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-wallet text-lg pr-4"></i>
                            <span class="text-sm font-medium">New Hire Onboarding and Employee Self-Service</span>
                        </div>
                        <div class="arrow">
                            <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                        </div>
                    </div>
                    <div id="disbursement-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                        <ul class="space-y-1">
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                        </ul>
                    </div>
                </div>
                <div class="menu-option">
                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('training-dropdown', this)">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-file-invoice-dollar text-lg pr-4"></i>
                            <span class="text-sm font-medium">Learning & Training Management</span>
                        </div>
                        <div class="arrow">
                            <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                        </div>
                    </div>
                    <div id="training-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                        <ul class="space-y-1">
                            <li><a href="Employees.php" class="text-sm text-gray-800 hover:text-blue-600">Employee</a></li>
                            <li><a href="trainers.php" class="text-sm text-gray-800 hover:text-blue-600">Trainer</a></li>
                            <li><a href="enrollments.php" class="text-sm text-gray-800 hover:text-blue-600">Enrollments</a></li>
                            <li><a href="training_programs.php" class="text-sm text-gray-800 hover:text-blue-600">Training Program</a></li>
                            <li><a href="training_materials.php" class="text-sm text-gray-800 hover:text-blue-600">Training Materials</a></li>
                            <li><a href="assessments.php" class="text-sm text-gray-800 hover:text-blue-600">Assessments</a></li>
                        </ul>
                    </div>
                </div>

                <div class="menu-option">
                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('budget-dropdown', this)">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-chart-pie text-lg pr-4"></i>
                            <span class="text-sm font-medium">Performance Management</span>
                        </div>
                        <div class="arrow">
                            <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                        </div>
                    </div>
                    <div id="budget-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                        <ul class="space-y-1">
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                        </ul>
                    </div>
                </div>

                <div class="menu-option">
                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('collection-dropdown', this)">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-folder-open text-lg pr-4"></i>
                            <span class="text-sm font-medium">Recruitment and Applicant Management</span>
                        </div>
                        <div class="arrow">
                            <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                        </div>
                    </div>
                    <div id="collection-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                        <ul class="space-y-1">
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                        </ul>
                    </div>
                </div>

                <div class="menu-option">
                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('general-ledger-dropdown', this)">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-money-bills text-lg pr-4"></i>
                            <span class="text-sm font-medium ">Social Recognition</span>
                        </div>
                        <div class="arrow">
                            <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                        </div>
                    </div>
                    <div id="general-ledger-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                        <ul class="space-y-1">
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                        </ul>
                    </div>
                </div>

                <div class="menu-option">
                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('account-dropdown', this)">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-file-invoice-dollar text-lg pr-4"></i>
                            <span class="text-sm font-medium">Competency Management</span>
                        </div>
                        <div class="arrow">
                            <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                        </div>
                    </div>
                    <div id="account-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                        <ul class="space-y-1">
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                        </ul>
                    </div>
                </div>
                <div class="menu-option">
                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('succession-dropdown', this)">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-file-invoice-dollar text-lg pr-4"></i>
                            <span class="text-sm font-medium">Succession Planning</span>
                        </div>
                        <div class="arrow">
                            <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                        </div>
                    </div>
                    <div id="succession-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                        <ul class="space-y-1">
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                            <li><a href="#" class="text-sm text-gray-800 hover:text-blue-600">Sub Modules</a></li>
                        </ul>
                    </div>
                </div>

            </ul>
        </div>
    </div> -->

    <!-- Main + Navbar -->
    <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
        <!-- Navbar -->
        <?php require 'partials/admin/navbar.php' ?>
        <!-- <nav class="h-16 w-full bg-white border-b border-[#F7E6CA] flex justify-between items-center px-6 py-4">
            <div class="left-nav flex items-center space-x-4 max-w-96 w-full">
                <button aria-label="Toggle menu" class="menu-btn text-[#4E3B2A] focus:outline-none hover:bg-[#F7E6CA] hover:rounded-full">
                    <i class="fa-solid fa-bars text-[#594423] text-xl w-11 py-2"></i>
                </button>


            </div>

            <div>
                <i class="fa-regular fa-user bg-[#594423] text-white px-4 py-2 rounded-lg cursor-pointer text-lg lg:hidden" aria-label="User profile"></i>
            </div>

            <div class="right-nav  items-center space-x-6 hidden lg:flex">
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
        </nav> -->

        <!-- Main Content -->
        <main class="px-8 py-8">
            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta charset="UTF-8">
                <title>Trainers Management</title>
                <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Georgia&display=swap" rel="stylesheet">
                <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
                <script src="https://cdn.tailwindcss.com"></script>
                <style>
                    body {
                        font-family: 'Georgia', serif;
                    }

                    h1 {
                        font-family: 'Cinzel', serif;
                    }
                </style>
            </head>

            <body>
                <h1 class="text-3xl font-bold mb-10 text-center">Trainers Management</h1>
                <div class="flex justify-start mb-6 ">
                    <button class="bg-[#F7E6CA]  text-[#4E3B2A] border-2 border-[#594423] rounded-lg px-6 py-3 font-semibold hover:bg-[#594423] hover:text-white transition " onclick="openModal('addTrainerModal')">
                        <i class='bx bx-plus'></i> Add Trainer
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full border-separate border-spacing-y-3">
                        <thead>
                            <tr class="bg-[#F7E6CA]">
                                <th class="py-4 px-6 text-left text-base font-semibold uppercase border-2 border-[#594423]">ID</th>
                                <th class="py-4 px-6 text-left text-base font-semibold uppercase border-2 border-[#594423]">Full Name</th>
                                <th class="py-4 px-6 text-left text-base font-semibold uppercase border-2 border-[#594423]">Email</th>
                                <th class="py-4 px-6 text-left text-base font-semibold uppercase border-2 border-[#594423]">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $trainers->fetch_assoc()): ?>
                                <tr class="bg-[#FFF6E8] hover:bg-[#F7E6CA] transition">
                                    <td class="py-4 px-6 rounded-l-lg"><?= $row['TrainerID'] ?></td>
                                    <td class="py-4 px-6"><?= $row['FullName'] ?></td>
                                    <td class="py-4 px-6"><?= $row['Email'] ?></td>
                                    <td class="py-4 px-6 flex gap-2 rounded-r-lg">
                                        <button class="bg-[#FFF6E8] text-[#594423] border-2 border-[#594423] rounded-md px-4 py-2 hover:bg-[#F7E6CA] transition" onclick="editTrainer(<?= $row['TrainerID'] ?>, '<?= $row['FullName'] ?>', '<?= $row['Email'] ?>'); openModal('editTrainerModal')">Edit</button>
                                        <form method="get" class="inline">
                                            <input type="hidden" name="delete_trainer" value="<?= $row['TrainerID'] ?>">
                                            <button type="submit" class="bg-[#594423] text-white border-2 border-[#594423] rounded-md px-4 py-2 hover:bg-[#402f1c] transition" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                        <button class="bg-[#FFF6E8] text-[#594423] border-2 border-[#594423] rounded-md px-4 py-2 hover:bg-[#F7E6CA] transition" onclick="viewTrainer(<?= $row['TrainerID'] ?>, '<?= $row['FullName'] ?>', '<?= $row['Email'] ?>'); openModal('viewTrainerModal')">View</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Add Trainer Modal -->
                <div id="addTrainerModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
                    <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-2">
                        <form method="post" class="p-6">
                            <div class="flex justify-between items-center border-b pb-3 mb-4">
                                <h5 class="text-xl font-bold">Add Trainer</h5>
                                <button type="button" class="text-2xl" onclick="closeModal('addTrainerModal')">&times;</button>
                            </div>
                            <div class="mb-4">
                                <label for="full_name" class="block font-semibold mb-1">Full Name</label>
                                <input type="text" name="full_name" id="full_name" class="w-full border-2 border-[#594423] rounded px-3 py-2 focus:outline-none focus:border-[#4E3B2A]" required>
                            </div>
                            <div class="mb-4">
                                <label for="email" class="block font-semibold mb-1">Email</label>
                                <input type="email" name="email" id="email" class="w-full border-2 border-[#594423] rounded px-3 py-2 focus:outline-none focus:border-[#4E3B2A]" required>
                            </div>
                            <div class="flex justify-end gap-2 mt-6">
                                <button type="button" class="bg-gray-200 text-[#4E3B2A] rounded px-4 py-2" onclick="closeModal('addTrainerModal')">Close</button>
                                <button type="submit" name="add_trainer" class="bg-[#F7E6CA] text-[#4E3B2A] border-2 border-[#594423] rounded px-4 py-2 hover:bg-[#594423] hover:text-white transition">Add</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Edit Trainer Modal -->
                <div id="editTrainerModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
                    <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-2">
                        <form method="post" class="p-6">
                            <div class="flex justify-between items-center border-b pb-3 mb-4">
                                <h5 class="text-xl font-bold">Edit Trainer</h5>
                                <button type="button" class="text-2xl" onclick="closeModal('editTrainerModal')">&times;</button>
                            </div>
                            <input type="hidden" name="trainer_id" id="edit_trainer_id">
                            <div class="mb-4">
                                <label for="edit_full_name" class="block font-semibold mb-1">Full Name</label>
                                <input type="text" name="full_name" id="edit_full_name" class="w-full border-2 border-[#594423] rounded px-3 py-2 focus:outline-none focus:border-[#4E3B2A]" required>
                            </div>
                            <div class="mb-4">
                                <label for="edit_email" class="block font-semibold mb-1">Email</label>
                                <input type="email" name="email" id="edit_email" class="w-full border-2 border-[#594423] rounded px-3 py-2 focus:outline-none focus:border-[#4E3B2A]" required>
                            </div>
                            <div class="flex justify-end gap-2 mt-6">
                                <button type="button" class="bg-gray-200 text-[#4E3B2A] rounded px-4 py-2" onclick="closeModal('editTrainerModal')">Close</button>
                                <button type="submit" name="edit_trainer" class="bg-[#FFF6E8] text-[#594423] border-2 border-[#594423] rounded px-4 py-2 hover:bg-[#F7E6CA] transition">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- View Trainer Modal -->
                <div id="viewTrainerModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
                    <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-2">
                        <div class="p-6">
                            <div class="flex justify-between items-center border-b pb-3 mb-4">
                                <h5 class="text-xl font-bold">View Trainer</h5>
                                <button type="button" class="text-2xl" onclick="closeModal('viewTrainerModal')">&times;</button>
                            </div>
                            <p class="mb-2"><strong>Full Name:</strong> <span id="view_full_name"></span></p>
                            <p class="mb-2"><strong>Email:</strong> <span id="view_email"></span></p>
                            <div class="flex justify-end mt-6">
                                <button class="bg-gray-200 text-[#4E3B2A] rounded px-4 py-2" onclick="closeModal('viewTrainerModal')">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function openModal(id) {
                        document.getElementById(id).classList.remove('hidden');
                    }

                    function closeModal(id) {
                        document.getElementById(id).classList.add('hidden');
                    }

                    function editTrainer(id, fullName, email) {
                        document.getElementById('edit_trainer_id').value = id;
                        document.getElementById('edit_full_name').value = fullName;
                        document.getElementById('edit_email').value = email;
                    }

                    function viewTrainer(id, fullName, email) {
                        document.getElementById('view_full_name').textContent = fullName;
                        document.getElementById('view_email').textContent = email;
                    }
                </script>
            </body>

            </html>
        </main>
    </div>
</div>
<!-- FOOTER -->
<?php require 'partials/admin/footer.php' ?>
<!-- <script>
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

</html> -->