<?php
include 'db_connection.php';

if (isset($_POST['add_enrollment'])) {
    $role = $_POST['role'];
    $programID = $_POST['programID'];
    $status = $_POST['status'] ?? 'Enrolled';
    if ($role === 'employee') {
        $employeeID = $_POST['employeeID'];
        $stmt = $conn->prepare("INSERT INTO `Enrollments` (`EmployeeID`, `TrainerID`, `ProgramID`, `Status`) VALUES (?, NULL, ?, ?)");
        $stmt->bind_param('iis', $employeeID, $programID, $status);
        // Get employee name for notification
        $empRes = $conn->prepare("SELECT FullName FROM employees WHERE EmployeeID = ?");
        $empRes->bind_param('i', $employeeID);
        $empRes->execute();
        $empRes->bind_result($enrolleeName);
        $empRes->fetch();
        $empRes->close();
    } elseif ($role === 'trainer') {
        $trainerID = $_POST['trainerID'];
        $stmt = $conn->prepare("INSERT INTO `Enrollments` (`EmployeeID`, `TrainerID`, `ProgramID`, `Status`) VALUES (NULL, ?, ?, ?)");
        $stmt->bind_param('iis', $trainerID, $programID, $status);
        // Get trainer name for notification
        $trRes = $conn->prepare("SELECT FullName FROM trainers WHERE TrainerID = ?");
        $trRes->bind_param('i', $trainerID);
        $trRes->execute();
        $trRes->bind_result($enrolleeName);
        $trRes->fetch();
        $trRes->close();
    } else {
        echo "<script>alert('Please select a valid role.');</script>";
        return;
    }
    $stmt->execute();
    $stmt->close();
    // Add notification
    // addNotification($conn, 'A new enrollment has been added for: ' . $enrolleeName);
    // header('Location: enrollments.php');
    // exit;
}

if (isset($_POST['edit_enrollment'])) {
    $enrollmentID = $_POST['enrollmentID'];
    $programID = $_POST['programID'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE `Enrollments` SET `ProgramID` = ?, `Status` = ? WHERE `EnrollmentID` = ?");
    $stmt->bind_param('isi', $programID, $status, $enrollmentID);
    $stmt->execute();
    // header('Location: enrollments.php');
    // exit;
}

if (isset($_GET['delete_enrollment'])) {
    $enrollmentID = $_GET['delete_enrollment'];
    $stmt = $conn->prepare("DELETE FROM `Enrollments` WHERE `EnrollmentID` = ?");
    $stmt->bind_param('i', $enrollmentID);
    $stmt->execute();
    // header('Location: enrollments.php');
    // exit;
}

$enrollments = $conn->query("SELECT e.`EnrollmentID`, e.`EnrollmentDate`, e.`Status`, 
            IFNULL(CONCAT(emp.`FullName`, ' (Employee)'), CONCAT(t.`FullName`, ' (Trainer)')) AS Name, 
            tp.`ProgramName` 
     FROM `Enrollments` e 
     LEFT JOIN `employees` emp ON e.`EmployeeID` = emp.`EmployeeID` 
     LEFT JOIN `trainers` t ON e.`TrainerID` = t.`TrainerID` 
     LEFT JOIN `trainingprograms` tp ON e.`ProgramID` = tp.`ProgramID`");

$employees = $conn->query("SELECT `EmployeeID`, `FullName`, `Email` FROM `employees`");
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
            <?php
            include 'db_connection.php';

            // Handle Adding Enrollment
            if (isset($_POST['add_enrollment'])) {
                $role = $_POST['role'];
                $programID = $_POST['programID'];
                $status = $_POST['status'] ?? 'Enrolled';

                if ($role === 'employee') {
                    $employeeID = $_POST['employeeID'];
                    $stmt = $conn->prepare("INSERT INTO `Enrollments` (`EmployeeID`, `TrainerID`, `ProgramID`, `Status`) VALUES (?, NULL, ?, ?)");
                    $stmt->bind_param('iis', $employeeID, $programID, $status);
                } elseif ($role === 'trainer') {
                    $trainerID = $_POST['trainerID'];
                    $stmt = $conn->prepare("INSERT INTO `Enrollments` (`EmployeeID`, `TrainerID`, `ProgramID`, `Status`) VALUES (NULL, ?, ?, ?)");
                    $stmt->bind_param('iis', $trainerID, $programID, $status);
                } else {
                    echo "<script>alert('Please select a valid role.');</script>";
                    return;
                }

                $stmt->execute();
                // header('Location: enrollments.php');
                // exit;
            }

            // Handle Editing Enrollment
            if (isset($_POST['edit_enrollment'])) {
                $enrollmentID = $_POST['enrollmentID'];
                $programID = $_POST['programID'];
                $status = $_POST['status'];

                $stmt = $conn->prepare("UPDATE `Enrollments` SET `ProgramID` = ?, `Status` = ? WHERE `EnrollmentID` = ?");
                $stmt->bind_param('isi', $programID, $status, $enrollmentID);
                $stmt->execute();
                // header('Location: enrollments.php');
                // exit;
            }

            // Handle Deleting Enrollment
            if (isset($_GET['delete_enrollment'])) {
                $enrollmentID = $_GET['delete_enrollment'];
                $stmt = $conn->prepare("DELETE FROM `Enrollments` WHERE `EnrollmentID` = ?");
                $stmt->bind_param('i', $enrollmentID);
                $stmt->execute();
                // header('Location: enrollments.php');
                // exit;
            }

            // Fetch Enrollments
            $enrollments = $conn->query("SELECT e.`EnrollmentID`, e.`EnrollmentDate`, e.`Status`, 
            IFNULL(CONCAT(emp.`FullName`, ' (Employee)'), CONCAT(t.`FullName`, ' (Trainer)')) AS Name, 
            tp.`ProgramName` 
     FROM `Enrollments` e 
     LEFT JOIN `employees` emp ON e.`EmployeeID` = emp.`EmployeeID` 
     LEFT JOIN `trainers` t ON e.`TrainerID` = t.`TrainerID` 
     LEFT JOIN `trainingprograms` tp ON e.`ProgramID` = tp.`ProgramID`");

            $employees = $conn->query("SELECT `EmployeeID`, `FullName`, `Email` FROM `employees`");
            ?>

            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Manage Enrollments</title>
                <script src="https://cdn.tailwindcss.com"></script>
                <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Georgia&display=swap" rel="stylesheet">
                <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
                <style>
                    body {
                        font-family: 'Georgia', serif;
                    }

                    h1 {
                        font-family: 'Cinzel', serif;
                    }
                </style>
            </head>

            <body class="bg-white text-[#4E3B2A] min-h-screen">
                <div class="main-content px-4 md:px-10 py-8">
                    <h1 class="text-3xl font-bold mb-10 text-center">Manage Enrollments</h1>
                    <button class="bg-[#F7E6CA] text-[#4E3B2A] border-2 border-[#594423] rounded-lg px-6 py-3 font-semibold hover:bg-[#594423] hover:text-white transition mb-6" onclick="openModal('addModal')">
                        <i class='bx bx-plus'></i> Add Enrollment
                    </button>
                    <div class="overflow-x-auto">
                        <table class="w-full border-separate border-spacing-y-3">
                            <thead>
                                <tr class="bg-[#F7E6CA]">
                                    <th class="py-4 px-6 text-left text-base font-semibold uppercase border-2 border-[#594423]">ID</th>
                                    <th class="py-4 px-6 text-left text-base font-semibold uppercase border-2 border-[#594423]">Status</th>
                                    <th class="py-4 px-6 text-left text-base font-semibold uppercase border-2 border-[#594423]">Name</th>
                                    <th class="py-4 px-6 text-left text-base font-semibold uppercase border-2 border-[#594423]">Program</th>
                                    <th class="py-4 px-6 text-left text-base font-semibold uppercase border-2 border-[#594423]">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $enrollments->fetch_assoc()): ?>
                                    <tr class="bg-[#FFF6E8] hover:bg-[#F7E6CA] transition">
                                        <td class="py-4 px-6 rounded-l-lg"><?= $row['EnrollmentID'] ?></td>
                                        <td class="py-4 px-6"><?= $row['Status'] ?></td>
                                        <td class="py-4 px-6"><?= $row['Name'] ?></td>
                                        <td class="py-4 px-6"><?= $row['ProgramName'] ?></td>
                                        <td class="py-4 px-6 flex gap-2 rounded-r-lg">
                                            <button class="bg-[#FFF6E8] text-[#594423] border-2 border-[#594423] rounded-md px-4 py-2 hover:bg-[#F7E6CA] transition" onclick="openEditModal(<?= $row['EnrollmentID'] ?>, '<?= $row['Status'] ?>', '<?= $row['ProgramName'] ?>')">Edit</button>
                                            <a href="?delete_enrollment=<?= $row['EnrollmentID'] ?>" class="bg-[#594423] text-white border-2 border-[#594423] rounded-md px-4 py-2 hover:bg-[#402f1c] transition" onclick="return confirm('Are you sure?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Add Enrollment Modal -->
                <div id="addModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
                    <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-2">
                        <form method="POST" class="p-6">
                            <div class="flex justify-between items-center border-b pb-3 mb-4">
                                <h2 class="text-xl font-bold">Add Enrollment</h2>
                                <button type="button" class="text-2xl" onclick="closeModal('addModal')">&times;</button>
                            </div>
                            <div class="mb-4">
                                <label for="role" class="block font-semibold mb-1">Role:</label>
                                <select name="role" id="role" required onchange="toggleRoleSelection()" class="w-full border-2 border-[#594423] rounded px-3 py-2 focus:outline-none focus:border-[#4E3B2A]">
                                    <option value="">Select Role</option>
                                    <option value="employee">Employee</option>
                                    <option value="trainer">Trainer</option>
                                </select>
                            </div>
                            <div id="employeeSelection" class="mb-4 hidden">
                                <label for="employeeID" class="block font-semibold mb-1">Select Employee:</label>
                                <select name="employeeID" id="employeeID" class="w-full border-2 border-[#594423] rounded px-3 py-2 focus:outline-none focus:border-[#4E3B2A]">
                                    <option value="">Select an Employee</option>
                                    <?php while ($employee = $employees->fetch_assoc()): ?>
                                        <option value="<?= $employee['EmployeeID'] ?>">
                                            <?= $employee['FullName'] ?> (<?= $employee['Email'] ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div id="trainerSelection" class="mb-4 hidden">
                                <label for="trainerID" class="block font-semibold mb-1">Select Trainer:</label>
                                <select name="trainerID" id="trainerID" class="w-full border-2 border-[#594423] rounded px-3 py-2 focus:outline-none focus:border-[#4E3B2A]">
                                    <option value="">Select a Trainer</option>
                                    <?php
                                    $trainers = $conn->query("SELECT `TrainerID`, `FullName`, `Email` FROM `trainers`");
                                    while ($trainer = $trainers->fetch_assoc()): ?>
                                        <option value="<?= $trainer['TrainerID'] ?>">
                                            <?= $trainer['FullName'] ?> (<?= $trainer['Email'] ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="programID" class="block font-semibold mb-1">Program:</label>
                                <select name="programID" id="programID" required class="w-full border-2 border-[#594423] rounded px-3 py-2 focus:outline-none focus:border-[#4E3B2A]">
                                    <?php
                                    $programs = $conn->query("SELECT `ProgramID`, `ProgramName` FROM `trainingprograms`");
                                    while ($program = $programs->fetch_assoc()): ?>
                                        <option value="<?= $program['ProgramID'] ?>">
                                            <?= $program['ProgramName'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="status" class="block font-semibold mb-1">Status:</label>
                                <select name="status" id="status" required class="w-full border-2 border-[#594423] rounded px-3 py-2 focus:outline-none focus:border-[#4E3B2A]">
                                    <option value="Enrolled">Enrolled</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                            <div class="flex justify-end gap-2 mt-6">
                                <button type="button" class="bg-gray-200 text-[#4E3B2A] rounded px-4 py-2" onclick="closeModal('addModal')">Close</button>
                                <button type="submit" name="add_enrollment" class="bg-[#F7E6CA] text-[#4E3B2A] border-2 border-[#594423] rounded px-4 py-2 hover:bg-[#594423] hover:text-white transition">Add Enrollment</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Edit Enrollment Modal -->
                <div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
                    <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-2">
                        <form method="POST" class="p-6">
                            <div class="flex justify-between items-center border-b pb-3 mb-4">
                                <h2 class="text-xl font-bold">Edit Enrollment</h2>
                                <button type="button" class="text-2xl" onclick="closeModal('editModal')">&times;</button>
                            </div>
                            <input type="hidden" name="enrollmentID" id="edit_enrollmentID">
                            <div class="mb-4">
                                <label for="edit_programID" class="block font-semibold mb-1">Program:</label>
                                <select name="programID" id="edit_programID" class="w-full border-2 border-[#594423] rounded px-3 py-2 focus:outline-none focus:border-[#4E3B2A]">
                                    <?php
                                    $programs = $conn->query("SELECT `ProgramID`, `ProgramName` FROM `trainingprograms`");
                                    while ($program = $programs->fetch_assoc()): ?>
                                        <option value="<?= $program['ProgramID'] ?>">
                                            <?= $program['ProgramName'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="edit_status" class="block font-semibold mb-1">Status:</label>
                                <select name="status" id="edit_status" class="w-full border-2 border-[#594423] rounded px-3 py-2 focus:outline-none focus:border-[#4E3B2A]">
                                    <option value="Enrolled">Enrolled</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                            <div class="flex justify-end gap-2 mt-6">
                                <button type="button" class="bg-gray-200 text-[#4E3B2A] rounded px-4 py-2" onclick="closeModal('editModal')">Close</button>
                                <button type="submit" name="edit_enrollment" class="bg-[#FFF6E8] text-[#594423] border-2 border-[#594423] rounded px-4 py-2 hover:bg-[#F7E6CA] transition">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    function toggleRoleSelection() {
                        const role = document.getElementById('role').value;
                        document.getElementById('employeeSelection').classList.toggle('hidden', role !== 'employee');
                        document.getElementById('trainerSelection').classList.toggle('hidden', role !== 'trainer');
                    }

                    function openModal(modalId) {
                        document.getElementById(modalId).classList.remove('hidden');
                    }

                    function closeModal(modalId) {
                        document.getElementById(modalId).classList.add('hidden');
                    }

                    function openEditModal(enrollmentID, status, programName) {
                        document.getElementById('edit_enrollmentID').value = enrollmentID;
                        document.getElementById('edit_programID').value = ""; // Adjust accordingly
                        document.getElementById('edit_status').value = status;
                        openModal('editModal');
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