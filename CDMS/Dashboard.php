<?php
// Check your directoryyyyyyyyyyyyyyyyyyyyyyy
include("connection.php");

// Define the database name
// just rename the DB name according to your DBV
$db_name = "hr_1&2_competency_management";

if (!isset($connections[$db_name])) {
    die("Database connection not found for $db_name");
}

$connection = $connections[$db_name];

// define the TB of your DB
$query = "SELECT * FROM `competencies`";
$result = mysqli_query($connection, $query);

// Check for errors
if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}
$logs1 = $connection->query("SELECT * FROM assets");
$logs2 = $connection->query("SELECT * FROM logs");
$core1 = $connection->query("SELECT * FROM #");
$core2 = $connection->query("SELECT * FROM #");
$hr1 = $connection->query("SELECT * FROM #");
$hr2 = $connection->query("SELECT * FROM #");
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Databases</title>
    <link rel="shortcut icon" href="emsaa.png" type="image/x-icon">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="buttons.css">

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.7/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="ddashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>

<body>
    <div class="flex min-h-screen w-full">
        <!--Sidebar-->
        <aside class="sidebar sidebar-expanded sidebar-transition h-screen bg-white border-r border-[#F7E6CA] flex flex-col hidden lg:flex">
            <div class="h-16 border-b border-[#F7E6CA] flex items-center px-2 space-x-2">
                <h1 class="text-xl font-bold text-black bg-[#D9D9D9] p-2 rounded-xl">LOGO</h1>
                <h1 class="text-xl font-bold text-[#4E3B2A]"><i class="bx bxs-server"></i>Centralized Database</h1>
            </div>
            <div class="side-menu px-4 py-6">
                <ul class="space-y-4">
                    <!-- Dashboard Item -->
                    <div class="menu-option">
                        <a href="#" class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <i class="bx bxs-dashboard"></i>
                                <span class="text-sm font-medium">Dashboard</span>
                            </div>

                        </a>
                    </div>


                    <!--- HR part 1 - 2 --->

                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('Databases- HR part 1 - 2 - dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="bx bxs-server"></i>
                                <span class="text-sm font-medium">HR Part 1 - 2</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="Databases- HR part 1 - 2 - dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">



                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium"><i class="bx bx-medal text-lg"></i> Competency</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>



                                    <!--competency---->
                                    <div id="nested-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">

                                            <li>
                                                <a href="../../HR part 1 - 2/competency/competencies.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-medal text-lg"></i> <span>Competencies</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 1 - 2/competency/competency_level.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-bar-chart-alt text-lg"></i> <span>Competency Levels</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 1 - 2/competency/employee_competencies.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-user-check text-lg"></i> <span>Employee Competencies</span>
                                                </a>
                                            </li>

                                        </ul>
                                    </div>
                                </div>
                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested1-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-book-open text-lg"></i> Learning & training
                                        </span> <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested1-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="../../HR part 1 - 2/learning & training management/assessments.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-task text-lg"></i> <span>Assessments</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 1 - 2/learning & training management/enrollments.php " class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-list-check text-lg"></i> <span>Enrollments</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 1 - 2/learning & training management/trainers.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-user text-lg"></i> <span>Trainers</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 1 - 2/learning & training management/trainingmaterials.php" class="text-sm text-gray-800  hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-book text-lg"></i> <span>Training Materials</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 1 - 2/learning & training management/trainingprograms.php" class="text-sm text-gray-800  hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-calendar-check text-lg"></i> <span>Training Programs</span>
                                                </a>
                                            </li>

                                        </ul>
                                    </div>
                                </div>
                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested2-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-user-plus text-lg"></i> NHOES
                                        </span> <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested2-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="../../HR part 1 - 2/new hire onboarding/departments.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-buildings text-lg"></i> <span>Departments</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 1 - 2/new hire onboarding/employees.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-group text-lg"></i> <span>Employees</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 1 - 2/new hire onboarding/jobroles.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-briefcase text-lg"></i> <span>Job Roles</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 1 - 2/new hire onboarding/onboardingtasks.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-task text-lg"></i> <span>Onboarding Tasks</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 1 - 2/new hire onboarding/selfservicerequest.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-user-check text-lg"></i> <span>Self-Service Request</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>
                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested3-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-chart text-lg"></i> Performance Management
                                        </span> <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested3-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="../../HR part 1 - 2/performance management/appraisals.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-bar-chart-alt text-lg"></i> <span>Appraisals</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 1 - 2/performance management/feedback.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-message-rounded-dots text-lg"></i> <span>Feedback</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 1 - 2/performance management/goals.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-target-lock text-lg"></i> <span>Goals</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 1 - 2/performance management/kpis.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-line-chart text-lg"></i> <span>KPIs</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 1 - 2/performance management/performancereviews.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-trophy text-lg"></i> <span>Performance Review</span>
                                                </a>
                                            </li>

                                        </ul>
                                    </div>
                                </div>

                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested4-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-user-check text-lg"></i> Recruitment and Applicant
                                        </span> <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested4-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-user-pin text-lg"></i> <span>Applicants</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-briefcase text-lg"></i> <span>Job Posting</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>

                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested5-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-heart text-lg"></i> Social Recognition
                                        </span> <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested5-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="../../HR part 1 - 2/social recognition/awards.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-trophy text-lg"></i> <span>Awards</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 1 - 2/social recognition/employeerecognition.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-star text-lg"></i> <span>Employee Recognition</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 1 - 2/social recognition/recognitioncategories.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-category text-lg"></i> <span>Recognition Categories</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>

                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested6-dropdown', this)">

                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-network-chart text-lg"></i> Succession Planning
                                        </span> <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested6-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="../../HR part 1 - 2/succession planning/keypositions.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-briefcase text-lg"></i> <span>Key Positions</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 1 - 2/succession planning/potentialsuccessors.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-user-plus text-lg"></i> <span>Potential Successors</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 1 - 2/succession planning/sucessionplans.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-network-chart text-lg"></i> <span>Succession Plans</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>

                            </ul>
                        </div>
                    </div>

















                    <!--HR part 3 - 4 --->

                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('Databases-HR part 3 - 4 -dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="bx bxs-server"></i>
                                <span class="text-sm font-medium">HR Part 3 - 4</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="Databases-HR part 3 - 4 -dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <div class="nested-menu">

                                    <!----dashboard--->

                                    <!--- Claims and reimbursement --->
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nestedd-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium"><i class="bx bx-wallet text-lg"></i> Claims and reimbursement</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nestedd-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="../../HR part 3 - 4/Claims and reimbursement/claimapprovals.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-shield text-lg"></i> <span>Claim approvals</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 3 - 4/Claims and reimbursement/claims.ph" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-receipt text-alt text-lg"></i> <span>Claims</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 3 - 4/Claims and reimbursement/claimtypes.ph" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-list-check text-lg"></i> <span>Claim types</span>
                                                </a>
                                            </li>

                                        </ul>
                                    </div>
                                </div>



                                <!---- compensation planning and administration --->
                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested11-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-calculator text-lg"></i> Compensation planning & administration</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested11-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="../../HR part 3 - 4/Compensation planning and administration/compensationplans.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-money text-lg"></i> <span>Compensation plans</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 3 - 4/Compensation planning and administration/incentives.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-gift text-lg"></i> <span>Incentives</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 3 - 4/Compensation planning and administration/salaryadjustments.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-dollar text-lg"></i> <span>Salary adjustments</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>



                                <!---- core human capital maangement--->

                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested22-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-user-circle text-lg"></i> Core human capital </span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested22-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="../../HR part 3 - 4/Core human capital management/employeedocuments.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-file text-lg"></i> <span>Emlpoyee documents</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 3 - 4/Core human capital management/employees.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-id-card text-lg"></i> <span>Employees</span>
                                                </a>
                                            </li>

                                            <li>
                                                <a href="../../HR part 3 - 4/Core human capital management/organizationstructure.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-sitemap text-lg"></i> <span>Organizational structure</span>
                                                </a>
                                            </li>



                                        </ul>
                                    </div>
                                </div>


                                <!---HR analytics --->
                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested33-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-bar-chart-square text-lg"></i> HR analytics</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested33-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="../../HR part 3 - 4/HR analytics/dashboards.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-line-chart text-lg"></i> <span>Dashboards</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 3 - 4/HR analytics/hrreports.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-file text-lg"></i> <span>HR reports</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 3 - 4/HR analytics/metrics.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-bullseye text-lg"></i> <span>Metrics</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>



                                <!---- leave managemet---->

                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested44-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-calendar-check text-lg"></i> Leave management</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested44-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="../../HR part 3 - 4/Leave management/leavebalances.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-id-card text-lg"></i> <span>Leave balances</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 3 - 4/Leave management/leaverequests.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-envelope text-lg"></i> <span>Leave requests</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 3 - 4/Leave management/leavetypes.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-category text-lg"></i> <span>Leave types</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>


                                <!---- payroll ---->
                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested55-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-wallet text-lg"></i> Payroll</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested55-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="../../HR part 3 - 4/Payroll/bonuses.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-gift text-lg"></i> <span>Bonuses</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 3 - 4/Payroll/deductions.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-minus-circle text-lg"></i> <span>Deduction</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 3 - 4/Payroll/employeesalaries.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-money text-lg"></i> <span>Employee salaries</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 3 - 4/Payroll/payrollruns.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-refresh text-lg"></i> <span>Payroll runs</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>


                                <!----time and attendance --->
                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested66-dropdown', this)">

                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-network-chart text-lg"></i> Time and attendance</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested66-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="../../HR part 3 - 4/Time and attendance/attendancerecords.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-calendar-check text-lg"></i> <span>Attendance records</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 3 - 4/Time and attendance/schedules.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-calendar-event text-lg"></i> <span>Schedules</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 3 - 4/Time and attendance/shifts.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-time text-lg"></i> <span>Shifts</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="../../HR part 3 - 4/Time and attendance/timesheets.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-clipboard text-lg"></i> <span>Timesheets</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>
                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested7-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-shield text-lg"></i> Admin Account
                                        </span> <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested7-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-user text-lg"></i> <span>Accounts</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>
                            </ul>
                        </div>
                    </div>




























                    <!--- logistics 1---->



                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('Logistic 1 -dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="bx bxs-server"></i>
                                <span class="text-sm font-medium">Logistics 1</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="Logistic 1 -dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <div class="nested-menu">


                                    <!--- asset management --->
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nesteddd-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium"><i class="bx bx-wallet text-lg"></i> Asset management</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nesteddd-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-box text-lg"></i> <span>Asset categories</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-package text-alt text-lg"></i> <span>Assets</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-user-check text-lg"></i> <span>Asset assignments</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-wrench text-lg"></i> <span>Maintenance schedules</span>
                                                </a>
                                            </li>

                                        </ul>
                                    </div>
                                </div>

                                <!---- procurement--->
                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested111-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-calculator text-lg"></i> Procurement</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested111-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-check-circle text-lg"></i> <span>Procurement approvals</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-cart text-lg"></i> <span>Purchase orders</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-file text-lg"></i> <span>RFQS</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-store text-lg"></i> <span>Suppliers</span>
                                                </a>
                                            </li>



                                        </ul>
                                    </div>
                                </div>



                                <!---- Project management--->

                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested222-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-user-circle text-lg"></i> Project management</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested222-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-file text-lg"></i> <span>Milestones</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-folder text-lg"></i> <span>Projects</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-task text-lg"></i> <span>Project task</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-group text-lg"></i> <span>Project team</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-time text-lg"></i> <span>Time sheets</span>
                                                </a>
                                            </li>



                                        </ul>
                                    </div>
                                </div>


                                <!---Warehousing --->
                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested333-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-store text-lg"></i>Warehousing</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested333-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-package text-lg"></i> <span>Inventory levels</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-box text-lg"></i> <span>Stock items</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-transfer text-lg"></i> <span>Stock movements</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-buildings text-lg"></i> <span>Warehouses</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>



                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested7-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-shield text-lg"></i> Admin Account</span><i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested7-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-user text-lg"></i> <span>Accounts</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>
                            </ul>
                        </div>
                    </div>





















                    <!-----logistics 2--->



                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('Logistic 2 -dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="bx bxs-server"></i>
                                <span class="text-sm font-medium">Logistics 2</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="Logistic 2 -dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <div class="nested-menu">


                                    <!--- Audit management --->
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nestedddd-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-wallet text-lg"></i> Audit management</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nestedddd-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-shield text-lg"></i> <span>Audit logs</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-calendar text-alt text-lg"></i> <span>Audit plans</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-list-check text-lg"></i> <span>Corrective actions</span>
                                                </a>
                                            </li>

                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-search-alt text-lg"></i> <span>Findings</span>
                                                </a>
                                            </li>

                                        </ul>
                                    </div>
                                </div>

                                <!---- Document tracking system --->
                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested1111-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-calculator text-lg"></i> Document tracking </span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested1111-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-check-shield text-lg"></i> <span>Approvals</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-folder text-lg"></i> <span>Document categories</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-file text-lg"></i> <span>Documents</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>



                                <!---- Fleet management--->

                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested2222-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-car text-lg"></i> Fleet management</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested2222-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-file text-lg"></i> <span>Fleet</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-gas-pump text-lg"></i> <span>Fuel logs</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-map text-lg"></i> <span>Mileage Logs</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-user-check text-lg"></i> <span>Vehicle assignments</span>
                                                </a>
                                            </li>



                                        </ul>
                                    </div>
                                </div>


                                <!---vehicle reservation --->
                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested3333-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-calendar-check text-lg"></i> Vehicle reservation</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested3333-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="Logistics 2/Vehicle reservation/VRS/drivers.php" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-user text-lg"></i> <span>Drivers</span>
                                                </a>
                                            </li>

                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-calendar-event text-lg"></i> <span>Reservation</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-car text-lg"></i> <span>Vehicles</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>



                                <!---- Vendor portal---->

                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested4444-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-store text-lg"></i> Vendor portal</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested4444-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-receipt text-lg"></i> <span>Vendor invoices</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-box text-lg"></i> <span>Vendor products</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-star text-lg"></i> <span>Vendor ratings</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-user-check text-lg"></i> <span>Vendors</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>


                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested7-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-shield text-lg"></i> Admin Account
                                        </span> <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested7-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-user text-lg"></i> <span>Accounts</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>
                            </ul>
                        </div>
                    </div>

















                    <!------core transaction 1----->

                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('CR1-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="bx bxs-server"></i>
                                <span class="text-sm font-medium">Core transaction 1</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="CR1-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <div class="nested-menu">


                                    <!--- food and beverage costing --->
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nesteddddd-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium"><i class="bx bx-wallet text-lg"></i> Food and beverage costing</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nesteddddd-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-shield text-lg"></i> <span>Costing repots</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-receipt text-alt text-lg"></i> <span>Costing details</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-list-check text-lg"></i> <span>Menu ingredients</span>
                                                </a>
                                            </li>

                                        </ul>
                                    </div>
                                </div>

                                <!---- inventory management --->
                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested11111-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-archive text-lg"></i> Inventory management</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested11111-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-cube text-lg"></i> <span>Inventory</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-sort text-lg"></i> <span>Reorder levels</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-transfer text-lg"></i> <span>Stock movements</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>



                                <!---- Kitchen/bar module--->

                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested22222-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-restaurant text-lg"></i> Kitchen/ bar module</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested22222-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-dollar-circle text-lg"></i> <span>Food costing</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-basket text-lg"></i> <span>Ingredients</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-book-open text-lg"></i> <span>Menu items</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-receipt text-lg"></i> <span>Recipes</span>
                                                </a>
                                            </li>



                                        </ul>
                                    </div>
                                </div>


                                <!---Order management with POS --->
                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested33333-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-store-alt text-lg"></i> Order management with POS</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested33333-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-list-ul text-lg"></i> <span>Order items</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-receipt text-lg"></i> <span>Order</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-credit-card text-lg"></i> <span>Payment transaction</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-calculator text-lg"></i> <span>POS</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>



                                <!---- Restaurant analytics---->

                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested44444-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-bar-chart text-lg"></i> Restaurant analytics</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested44444-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-user-check text-lg"></i> <span>Customer preferences</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-bowl-hot text-lg"></i> <span>Popular dishes</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-chart text-lg"></i> <span>Sale reports</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>


                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested7-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-shield text-lg"></i> Admin Account
                                        </span> <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested7-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-user text-lg"></i> <span>Accounts</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>
                            </ul>
                        </div>
                    </div>

























                    <!----core transation 2----->

                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('CR2-dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="bx bxs-server"></i>
                                <span class="text-sm font-medium">Core transaction 2</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="CR2-dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <div class="nested-menu">


                                    <!---Billing --->
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nestedddddd-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-wallet text-lg"></i> Billing</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nestedddddd-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-cart text-lg"></i> <span>Billing items</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-receipt text-alt text-lg"></i> <span>Invoices</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-credit-card text-lg"></i> <span>Payments</span>
                                                </a>
                                            </li>
                                            <li>


                                        </ul>
                                    </div>
                                </div>

                                <!---- front office --->
                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested111111-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-building text-lg"></i> Front office</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested111111-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-user text-lg"></i> <span>Guest</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-calendar-check text-lg"></i> <span>Room bookings</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-bed text-lg"></i> <span>Rooms</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-bed text-lg"></i> <span>Room type</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>



                                <!---- Housekeeping/laundry management--->

                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested222222-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-brush text-lg"></i> Housekeeping/laundry </span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested222222-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-calendar text-lg"></i> <span>Cleaning schedules</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-check-circle text-lg"></i> <span>Housekeeping tasks</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-basket text-lg"></i> <span>laundry request</span>
                                                </a>
                                            </li>



                                        </ul>
                                    </div>
                                </div>


                                <!---Room facilites --->
                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested333333-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-hotel text-lg"></i> Room facilities</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested333333-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-bed text-lg"></i> <span>Facilities</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-message-square-add text-lg"></i> <span>Facility request</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-wrench text-lg"></i> <span>Maintenance schedules</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>



                                <!---- Supplier management---->

                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested444444-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-package text-lg"></i> Supplier management</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested444444-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-calendar-event text-lg"></i> <span>Delivery schedules</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-building text-lg"></i> <span>Suppliers</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-clipboard text-lg"></i> <span>Supply orders</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>


                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested7-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-shield text-lg"></i> Admin Account
                                        </span> <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested7-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-user text-lg"></i> <span>Accounts</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>
                            </ul>
                        </div>
                    </div>















                    <!-------core transation 3----->

                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('CR3 - dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="bx bxs-server"></i>
                                <span class="text-sm font-medium">Core transaction 3</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="CR3 - dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <div class="nested-menu">


                                    <!--- Booking --->
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nesteddddddd-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-calendar-edit text-lg"> </i> Booking</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nesteddddddd-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-user text-lg"></i> <span>Booking channels</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-user-voice text-alt text-lg"></i> <span>Guest preferences</span>
                                                </a>
                                            </li>

                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-calendar-event text-lg"></i> <span>Reservation</span>
                                                </a>
                                            </li>

                                        </ul>
                                    </div>
                                </div>

                                <!---- Customer/guest management --->
                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested1111111-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-calculator text-lg"></i> Customer & Guest </span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested1111111-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-comment-detail text-lg"></i> <span>feedback</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-user text-lg"></i> <span>Guests</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-message-square-dots text-lg"></i> <span>Interactions</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>



                                <!---- customer relationship--->

                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested2222222-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-user-circle text-lg"></i> Customer relationship</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested2222222-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-user text-lg"></i> <span>Customers</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-medal text-lg"></i> <span>Loyalty programs</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-volume-full text-lg"></i> <span>Promotion</span>
                                                </a>
                                            </li>




                                        </ul>
                                    </div>
                                </div>


                                <!---Facilities management --->
                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested3333333-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-buildings text-lg"></i> Facilities management</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested3333333-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-building-house text-lg"></i> <span>Facilities</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-wrench text-lg"></i> <span>Maintinance request</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-clipboard text-lg"></i> <span>Usage logs</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>



                                <!---- Reservation---->

                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested4444444-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-calendar-star text-lg"></i> Reservation</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested4444444-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-door-open text-lg"></i> <span>Facilities reservation</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-info-circle text-lg"></i> <span>Reservation status</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-hotel text-lg"></i> <span>Room reservation</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>



                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested7-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-shield text-lg"></i> Admin Account
                                        </span> <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested7-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-user text-lg"></i> <span>Accounts</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>
                            </ul>
                        </div>
                    </div>
















                    <!------financials-------->


                    <div class="menu-option">
                        <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('Financials - dropdown', this)">
                            <div class="flex items-center space-x-2">
                                <i class="bx bxs-server"></i>
                                <span class="text-sm font-medium">Financials</span>
                            </div>
                            <div class="arrow">
                                <i class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"></i>
                            </div>
                        </div>
                        <div id="Financials - dropdown" class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2">
                            <ul class="space-y-1">
                                <div class="nested-menu">

                                    <!--- Dashboard --->
                                    <a href="#" class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer">
                                        <div class="flex items-center space-x-2">
                                            <i class="bx bxs-dashboard"></i>
                                            <span class="text-sm font-medium">Dashboard</span>
                                        </div>

                                    </a>

                                    <!--- Accounts payable/receivables --->
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nestedddddddd-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium"><i class="bx bx-money-withdraw text-lg">
                                            </i> Accounts payable/receivables</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nestedddddddd-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-file text-lg"></i> <span>Payable invoices</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-calendar-check text-alt text-lg"></i> <span>Payment schedules</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-credit-card text-lg"></i> <span>Vendor payments</span>
                                                </a>
                                            </li>

                                        </ul>
                                    </div>
                                </div>

                                <!---- budget management-->
                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested11111111-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-pie-chart-alt-2 text-lg"></i> Budget management</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested11111111-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-edit text-lg"></i> <span>budget adjustments</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-bar-chart text-lg"></i> <span>Budget allocations</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-wallet text-lg"></i> <span>Budgets</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>



                                <!---- Collection--->

                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested22222222-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-wallet-alt text-lg"></i> Collection</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested22222222-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-receipt text-lg"></i> <span>Invoices</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-credit-card text-lg"></i> <span>Payment methods</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-money-withdraw text-lg"></i> <span>Payment</span>
                                                </a>
                                            </li>



                                        </ul>
                                    </div>
                                </div>


                                <!---Disbursement --->
                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested33333333-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-wallet text-lg"></i> Disbursement</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested33333333-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-check-shield text-lg"></i> <span>Approvals</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-money text-lg"></i> <span>Disbursement payments</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-send text-lg"></i> <span>Disbursement request</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>



                                <!---- General ledger---->

                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested44444444-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-book text-lg"></i> General ledger</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested44444444-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-user-circle text-lg"></i> <span>Accounts</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-notepad text-lg"></i> <span>Journal entries</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-transfer-alt text-lg"></i> <span>Transaction</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>

                                <!---USM--->
                                <div class="nested-menu">
                                    <div class="menu-name flex justify-between items-center space-x-3 hover:bg-[#EBD8B6] px-3 py-2 rounded-lg transition duration-300 ease-in-out cursor-pointer" onclick="toggleDropdown('nested-usm-dropdown', this)">
                                        <span class="flex items-center space-x-2 text-sm text-gray-800 font-medium">
                                            <i class="bx bx-book text-lg"></i> User management</span>
                                        <i class="bx bx-chevron-right text-[16px] font-semibold arrow-icon"></i>
                                    </div>
                                    <div id="nested-usm-dropdown" class="menu-drop hidden flex-col w-full bg-[#EBD8B6] rounded-lg p-3 space-y-1 mt-1">
                                        <ul class="space-y-1">
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-user-circle text-lg"></i> <span>Department accouunts</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-notepad text-lg"></i> <span>Department audit trail</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-transfer-alt text-lg"></i> <span>Department log history</span>
                                                </a>
                                            </li>

                                            <li>
                                                <a href="#" class="text-sm text-gray-800 hover:bg-[#F7E6CA] flex items-center space-x-2 p-2 rounded-lg">
                                                    <i class="bx bx-transfer-alt text-lg"></i> <span>Department transaction</span>
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>




                            </ul>
                        </div>
                    </div>
                </ul>
            </div>
        </aside>
        <!--End of Sidebar-->

        <!--Main + Navbar-->
        <div class="main w-full bg-[#FFF6E8]">
            <!--Navbar-->
            <nav class="h-16 w-full bg-white border-b border-[#F7E6CA] flex justify-between items-center px-6 py-4">
                <!-- Left Navigation Section -->
                <div class="left-nav flex items-center space-x-4 max-w-96 w-full">
                    <!-- Menu Button -->


                    <button aria-label="Toggle menu" class="menu-btn text-[#4E3B2A] focus:outline-none hover:bg-[#F7E6CA] hover:rounded-full  ">
                        <i class="fa-solid fa-bars text-[#594423] text-xl w-11 py-2"></i>
                    </button>



                </div>
                <div>
                    <i class="fa-regular fa-user bg-[#594423] text-white px-4 py-2 rounded-lg cursor-pointer text-lg lg:hidden" aria-label="User profile"></i>
                </div>
                <a href="#" class="nav_link" onclick="logout()"> <i class='bx bx-log-out nav_icon'></i> <span class="nav_name">log out</span> </a>

                <!-- Right Navigation Section -->

            </nav>
            <!--End of Navbar-->

            <!--Main Content-->

            <main class="px-8 py-8">


                <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <svg class="w-7 h-7 text-gray-500 dark:text-gray-400 mb-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M18 5h-.7c.229-.467.349-.98.351-1.5a3.5 3.5 0 0 0-3.5-3.5c-1.717 0-3.215 1.2-4.331 2.481C8.4.842 6.949 0 5.5 0A3.5 3.5 0 0 0 2 3.5c.003.52.123 1.033.351 1.5H2a2 2 0 0 0-2 2v3a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1V7a2 2 0 0 0-2-2ZM8.058 5H5.5a1.5 1.5 0 0 1 0-3c.9 0 2 .754 3.092 2.122-.219.337-.392.635-.534.878Zm6.1 0h-3.742c.933-1.368 2.371-3 3.739-3a1.5 1.5 0 0 1 0 3h.003ZM11 13H9v7h2v-7Zm-4 0H2v5a2 2 0 0 0 2 2h3v-7Zm6 0v7h3a2 2 0 0 0 2-2v-5h-5Z" />
                    </svg>
                    <a href="#">
                        <h5 class="mb-2 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Need a help in Claim?</h5>
                    </a>
                    <p class="mb-3 font-normal text-gray-500 dark:text-gray-400"><?= count($logs1) ?></p>
                    <a href="#" class="inline-flex font-medium items-center text-blue-600 hover:underline">
                        See our guideline
                        <svg class="w-3 h-3 ms-2.5 rtl:rotate-[270deg]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11v4.833A1.166 1.166 0 0 1 13.833 17H2.167A1.167 1.167 0 0 1 1 15.833V4.167A1.166 1.166 0 0 1 2.167 3h4.618m4.447-2H17v5.768M9.111 8.889l7.778-7.778" />
                        </svg>
                    </a>
                </div>
                <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <svg class="w-7 h-7 text-gray-500 dark:text-gray-400 mb-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M18 5h-.7c.229-.467.349-.98.351-1.5a3.5 3.5 0 0 0-3.5-3.5c-1.717 0-3.215 1.2-4.331 2.481C8.4.842 6.949 0 5.5 0A3.5 3.5 0 0 0 2 3.5c.003.52.123 1.033.351 1.5H2a2 2 0 0 0-2 2v3a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1V7a2 2 0 0 0-2-2ZM8.058 5H5.5a1.5 1.5 0 0 1 0-3c.9 0 2 .754 3.092 2.122-.219.337-.392.635-.534.878Zm6.1 0h-3.742c.933-1.368 2.371-3 3.739-3a1.5 1.5 0 0 1 0 3h.003ZM11 13H9v7h2v-7Zm-4 0H2v5a2 2 0 0 0 2 2h3v-7Zm6 0v7h3a2 2 0 0 0 2-2v-5h-5Z" />
                    </svg>
                    <a href="#">
                        <h5 class="mb-2 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Need a help in Claim?</h5>
                    </a>
                    <p class="mb-3 font-normal text-gray-500 dark:text-gray-400"><?= count($logs2) ?></p>
                    <a href="#" class="inline-flex font-medium items-center text-blue-600 hover:underline">
                        See our guideline
                        <svg class="w-3 h-3 ms-2.5 rtl:rotate-[270deg]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11v4.833A1.166 1.166 0 0 1 13.833 17H2.167A1.167 1.167 0 0 1 1 15.833V4.167A1.166 1.166 0 0 1 2.167 3h4.618m4.447-2H17v5.768M9.111 8.889l7.778-7.778" />
                        </svg>
                    </a>
                </div>
                <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <svg class="w-7 h-7 text-gray-500 dark:text-gray-400 mb-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M18 5h-.7c.229-.467.349-.98.351-1.5a3.5 3.5 0 0 0-3.5-3.5c-1.717 0-3.215 1.2-4.331 2.481C8.4.842 6.949 0 5.5 0A3.5 3.5 0 0 0 2 3.5c.003.52.123 1.033.351 1.5H2a2 2 0 0 0-2 2v3a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1V7a2 2 0 0 0-2-2ZM8.058 5H5.5a1.5 1.5 0 0 1 0-3c.9 0 2 .754 3.092 2.122-.219.337-.392.635-.534.878Zm6.1 0h-3.742c.933-1.368 2.371-3 3.739-3a1.5 1.5 0 0 1 0 3h.003ZM11 13H9v7h2v-7Zm-4 0H2v5a2 2 0 0 0 2 2h3v-7Zm6 0v7h3a2 2 0 0 0 2-2v-5h-5Z" />
                    </svg>
                    <a href="#">
                        <h5 class="mb-2 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Need a help in Claim?</h5>
                    </a>
                    <p class="mb-3 font-normal text-gray-500 dark:text-gray-400">Go to this step by step guideline process on how to certify for your weekly benefits:</p>
                    <a href="#" class="inline-flex font-medium items-center text-blue-600 hover:underline">
                        See our guideline
                        <svg class="w-3 h-3 ms-2.5 rtl:rotate-[270deg]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11v4.833A1.166 1.166 0 0 1 13.833 17H2.167A1.167 1.167 0 0 1 1 15.833V4.167A1.166 1.166 0 0 1 2.167 3h4.618m4.447-2H17v5.768M9.111 8.889l7.778-7.778" />
                        </svg>
                    </a>
                </div>



                <br>

                <!-- Insert Button -->
                <button type="button" id="openModal"
                    class="text-[#4E3B2A] bg-[#EBD8B6] hover:bg-[#DFC5A6] focus:ring-4 focus:ring-[#EBD8B6] 
                                    font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 focus:outline-none 
                                    shadow-[0px_5px_15px_rgba(0,0,0,0.35)]">
                    Insert
                </button>



                <!--All Content Put Here-->
                <?php if ($result && mysqli_num_rows($result) > 0) : ?>
                    <table class="styled-table">

                        <thead>
                            <tr>
                                <th>Competency ID</th>
                                <th>Competency name</th>
                                <th>Description</th>
                                <th>Operations</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                <tr>
                                    <td><?php echo $row['CompetencyID']; ?></td>
                                    <td><?php echo $row['CompetencyName']; ?></td>
                                    <td><?php echo $row['Description']; ?></td>

                                    <td>


                                        <!-- View Button -->
                                        <button
                                            class="view-button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#viewModal"
                                            data-competency-id="<?php echo htmlspecialchars($row['CompetencyID']); ?>">
                                            <i class='bx bx-show tw-text-xl'></i>
                                        </button> <b> | </b>


                                        <!-- Edit Button -->
                                        <button
                                            class="edit-button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal"
                                            data-competency-id="<?php echo htmlspecialchars($row['CompetencyID']); ?>">
                                            <i class="bx bx-edit"></i>
                                        </button> <b> | </b>

                                        <!-- Delete Button -->
                                        <form id="delete-form-<?php echo $row['CompetencyID']; ?>" action="Table_1_delete.php?id=<?php echo $row['CompetencyID']; ?>" method="POST" class="inline-block">
                                            <button type="button" class="delete-btn" data-id="<?php echo $row['CompetencyID']; ?>">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>

        </div>
        </td>
        </tr>
    <?php endwhile; ?>
    </table>
<?php else : ?>
    <p>No records found</p>
<?php endif; ?>
    </div>
    </div>
    </div>
    </div>
    </div>

    <!-- Modal Structure -->
    <div id="insertModal" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3 relative">
            <h2 class="text-lg font-bold mb-4">Insert New Competency</h2>

            <!-- Form to Insert Data (Fixed form ID & Removed direct action) -->
            <form id="insertForm" method="POST">
                <label class="block mb-2">Competency Name:</label>
                <input type="text" name="competency_name" class="w-full p-2 border rounded mb-3" required>

                <label class="block mb-2">Description:</label>
                <textarea name="description" class="w-full p-2 border rounded mb-3" required></textarea>

                <div class="flex justify-end">
                    <button type="button" id="closeModal" class="bg-gray-400 text-white px-4 py-2 rounded mr-2">Cancel</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Insert</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalTitle">Competencies details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="caseDetails">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="edit-case-form">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>




    </main>
    <!--End of Main Content-->


    </div>
    <!--End of Main + Navbar-->
    </div>

    <script src="actions_functions.js"></script>
    <script src="dashboard.js"></script>




    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const deleteButtons = document.querySelectorAll(".delete-btn");

            deleteButtons.forEach(button => {
                button.addEventListener("click", function() {
                    const competencyId = this.getAttribute("data-id");
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You won't be able to revert this!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Yes, delete it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById("delete-form-" + competencyId).submit();
                        }
                    });
                });
            });
        });
    </script>

</body>

</html>