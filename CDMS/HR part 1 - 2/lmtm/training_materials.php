<?php
include 'db_connection.php';

if (isset($_POST['add_material'])) {
    $programID = $_POST['programID'];
    $fileName = $_POST['fileName'];
    $filePath = $_POST['filePath'];

    $stmt = $conn->prepare("INSERT INTO `trainingmaterials` (`ProgramID`, `FileName`, `FilePath`) VALUES (?, ?, ?)");
    $stmt->bind_param('iss', $programID, $fileName, $filePath);
    $stmt->execute();
    // header('Location: training_materials.php');
    // exit();
}

if (isset($_POST['edit_material'])) {
    $materialID = $_POST['materialID'];
    $programID = $_POST['programID'];
    $fileName = $_POST['fileName'];
    $filePath = $_POST['filePath'];

    $stmt = $conn->prepare("UPDATE `trainingmaterials` SET `ProgramID` = ?, `FileName` = ?, `FilePath` = ? WHERE `MaterialID` = ?");
    $stmt->bind_param('issi', $programID, $fileName, $filePath, $materialID);
    $stmt->execute();
    // header('Location: training_materials.php');
    // exit();
}

if (isset($_GET['delete_material'])) {
    $materialID = $_GET['delete_material'];
    $stmt = $conn->prepare("DELETE FROM `trainingmaterials` WHERE `MaterialID` = ?");
    $stmt->bind_param('i', $materialID);
    $stmt->execute();
    // header('Location: training_materials.php');
    // exit();
}

$materials = $conn->query("
    SELECT tm.`MaterialID`, tm.`FileName`, tm.`FilePath`, tm.`ProgramID`, tp.`ProgramName`, 
           IFNULL(CONCAT(emp.`FullName`, ' (Employee)'), CONCAT(t.`FullName`, ' (Trainer)')) AS Holder 
    FROM `trainingmaterials` tm 
    JOIN `trainingprograms` tp ON tm.`ProgramID` = tp.`ProgramID` 
    LEFT JOIN `enrollments` e ON tp.`ProgramID` = e.`ProgramID` 
    LEFT JOIN `employees` emp ON e.`EmployeeID` = emp.`EmployeeID` 
    LEFT JOIN `trainers` t ON e.`TrainerID` = t.`TrainerID`
");
$programs = $conn->query("SELECT `ProgramID`, `ProgramName` FROM `trainingprograms`");
?>
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
            <?php
            // The PHP logic has been moved to the top of the file.
            ?>

            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Training Materials</title>
                <script src="https://cdn.tailwindcss.com"></script>
                <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Georgia&display=swap" rel="stylesheet">
                <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
                <style>
                    body {
                        font-family: 'Georgia', serif;
                        background-color: #FFFFFF;
                        margin: 0;
                        padding: 0;
                        color: #4E3B2A;
                    }

                    h1 {
                        font-family: 'Cinzel', serif;
                    }
                </style>
                <script>
                    function openModal(modalId) {
                        document.getElementById(modalId).classList.remove('hidden');
                    }

                    function closeModal(modalId) {
                        document.getElementById(modalId).classList.add('hidden');
                    }

                    function editMaterial(materialID, programID, fileName, filePath) {
                        document.getElementById('edit_materialID').value = materialID;
                        document.getElementById('edit_programID').value = programID;
                        document.getElementById('edit_fileName').value = fileName;
                        document.getElementById('edit_filePath').value = filePath;
                        openModal('editModal');
                    }
                </script>
            </head>

            <body class="bg-white text-[#4E3B2A] min-h-screen">
                <div class="main-content px-4 md:px-10 py-8">
                    <h1 class="text-3xl font-bold mb-10 text-center">Training Materials</h1>
                    <div class="text-left mb-6">
                        <button onclick="openModal('addModal')" class="bg-[#F7E6CA] text-[#4E3B2A] border-2 border-[#594423] rounded-lg px-6 py-3 font-semibold hover:bg-[#594423] hover:text-white transition"><i class='bx bx-plus'></i> Add Material</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full border-separate border-spacing-y-3">
                            <thead>
                                <tr class="bg-[#F7E6CA]">
                                    <th class="py-4 px-6 text-left text-base font-semibold uppercase border-2 border-[#594423]">ID</th>
                                    <th class="py-4 px-6 text-left text-base font-semibold uppercase border-2 border-[#594423]">File Name</th>
                                    <th class="py-4 px-6 text-left text-base font-semibold uppercase border-2 border-[#594423]">File Path</th>
                                    <th class="py-4 px-6 text-left text-base font-semibold uppercase border-2 border-[#594423]">Program</th>
                                    <th class="py-4 px-6 text-left text-base font-semibold uppercase border-2 border-[#594423]">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $materials->fetch_assoc()): ?>
                                    <tr class="bg-[#FFF6E8] hover:bg-[#F7E6CA] transition">
                                        <td class="py-4 px-6 rounded-l-lg"><?= $row['MaterialID'] ?></td>
                                        <td class="py-4 px-6"><?= $row['FileName'] ?></td>
                                        <td class="py-4 px-6"><a href="<?= $row['FilePath'] ?>" target="_blank" class="text-blue-600 hover:underline">Open File</a></td>
                                        <td class="py-4 px-6"><?= $row['ProgramName'] ?></td>
                                        <td class="py-4 px-6 flex gap-2 rounded-r-lg">
                                            <button onclick="editMaterial(<?= $row['MaterialID'] ?>, '<?= $row['ProgramID'] ?? '' ?>', '<?= $row['FileName'] ?>', '<?= $row['FilePath'] ?>')" class="bg-[#FFF6E8] text-[#594423] border-2 border-[#594423] rounded-md px-4 py-2 hover:bg-[#F7E6CA] transition">Edit</button>
                                            <a href="?delete_material=<?= $row['MaterialID'] ?>" onclick="return confirm('Are you sure?')" class="bg-[#594423] text-white border-2 border-[#594423] rounded-md px-4 py-2 hover:bg-[#402f1c] transition">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Add Material Modal -->
                <div id="addModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
                    <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-2">
                        <form method="POST" class="p-6">
                            <div class="flex justify-between items-center border-b pb-3 mb-4">
                                <h2 class="text-xl font-bold">Add Material</h2>
                                <button type="button" class="text-2xl" onclick="closeModal('addModal')">&times;</button>
                            </div>
                            <div class="mb-4">
                                <label for="programID" class="block font-semibold mb-1">Program:</label>
                                <select name="programID" id="programID" required class="w-full border-2 border-[#594423] rounded px-3 py-2 focus:outline-none focus:border-[#4E3B2A]">
                                    <?php while ($program = $programs->fetch_assoc()): ?>
                                        <option value="<?= $program['ProgramID'] ?>">
                                            <?= $program['ProgramName'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="fileName" class="block font-semibold mb-1">File Name:</label>
                                <input type="text" name="fileName" id="fileName" required class="w-full border-2 border-[#594423] rounded px-3 py-2 focus:outline-none focus:border-[#4E3B2A]">
                            </div>
                            <div class="mb-4">
                                <label for="filePath" class="block font-semibold mb-1">File Path:</label>
                                <input type="file" name="filePath" id="filePath" required class="w-full border-2 border-[#594423] rounded px-3 py-2 focus:outline-none focus:border-[#4E3B2A]">
                            </div>
                            <div class="flex justify-end gap-2 mt-6">
                                <button type="button" class="bg-gray-200 text-[#4E3B2A] rounded px-4 py-2" onclick="closeModal('addModal')">Close</button>
                                <button type="submit" name="add_material" class="bg-[#F7E6CA] text-[#4E3B2A] border-2 border-[#594423] rounded px-4 py-2 hover:bg-[#594423] hover:text-white transition">Add Material</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Edit Material Modal -->
                <div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
                    <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-2">
                        <form method="POST" class="p-6">
                            <div class="flex justify-between items-center border-b pb-3 mb-4">
                                <h2 class="text-xl font-bold">Edit Material</h2>
                                <button type="button" class="text-2xl" onclick="closeModal('editModal')">&times;</button>
                            </div>
                            <input type="hidden" name="materialID" id="edit_materialID">
                            <div class="mb-4">
                                <label for="edit_programID" class="block font-semibold mb-1">Program:</label>
                                <select name="programID" id="edit_programID" class="w-full border-2 border-[#594423] rounded px-3 py-2 focus:outline-none focus:border-[#4E3B2A]">
                                    <?php
                                    $programs2 = $conn->query("SELECT `ProgramID`, `ProgramName` FROM `trainingprograms`");
                                    while ($program = $programs2->fetch_assoc()): ?>
                                        <option value="<?= $program['ProgramID'] ?>">
                                            <?= $program['ProgramName'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="edit_fileName" class="block font-semibold mb-1">File Name:</label>
                                <input type="text" name="fileName" id="edit_fileName" required class="w-full border-2 border-[#594423] rounded px-3 py-2 focus:outline-none focus:border-[#4E3B2A]">
                            </div>
                            <div class="mb-4">
                                <label for="edit_filePath" class="block font-semibold mb-1">File Path:</label>
                                <input type="text" name="filePath" id="edit_filePath" required class="w-full border-2 border-[#594423] rounded px-3 py-2 focus:outline-none focus:border-[#4E3B2A]">
                            </div>
                            <div class="flex justify-end gap-2 mt-6">
                                <button type="button" class="bg-gray-200 text-[#4E3B2A] rounded px-4 py-2" onclick="closeModal('editModal')">Close</button>
                                <button type="submit" name="edit_material" class="bg-[#FFF6E8] text-[#594423] border-2 border-[#594423] rounded px-4 py-2 hover:bg-[#F7E6CA] transition">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
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