<?php
session_start();

// Prevent caching of the page to avoid browser back button issues
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Past date to prevent caching

// Set session timeout duration (in seconds)
$timeout_duration = 900; // 15 minutes

// Check for inactivity timeout
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();     // Unset $_SESSION variables
    session_destroy();   // Destroy session
    header("Location: login.php?timeout=true");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity time

// Check if user is logged in and is a staff
if (!isset($_SESSION['Role']) || $_SESSION['Role'] !== 'manager') {
    header("Location: login.php?unauthorized=true");
    exit();
}

include '../connection.php';

// Fetch the logged-in staff's name from the database
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $query = "SELECT Name FROM staff WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($name);
    if ($stmt->fetch()) {
        $_SESSION['Name'] = $name; // Store name in session
    }
    $stmt->close();
}
?>

<script>
// This script forces a page refresh when the back button is clicked
window.onpageshow = function(event) {
    if (event.persisted) {
        location.reload();
    }
};
</script>



<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
      href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
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
        content: "";
        position: absolute;
        left: 0;
        bottom: 0;
        height: 2px;
        width: 0;
        background-color: #4e3b2a;
        transition: width 0.3s ease;
      }

      .menu-name:hover::after {
        width: 100%;
      }
    </style>
  </head>

  <body>
    <div class="flex min-h-screen w-full">
      <!-- Overlay -->
      <div class="sidebar-overlay" id="sidebar-overlay"></div>

      <!-- Sidebar -->
      <div
        class="sidebar sidebar-expanded fixed z-50 h-screen bg-white border-r border-[#F7E6CA] flex flex-col overflow-y-auto"
      >
        <div
          class="h-16 border-b border-[#F7E6CA] flex items-center px-2 space-x-2"
        >
        <h1>
          <img src="../img/Logo.png" alt="Logo" class="h-8 md:h-10 mr-3">
          </h1>
          <h1 class="text-xl font-bold text-[#4E3B2A]"><img src="../img/Logo-Name.png" alt="Logo" class="h-8 md:h-10 mr-3"></h1>
          <!--Close Button-->
          <i
            id="close-sidebar-btn"
            class="fa-solid fa-x close-sidebar-btn transform translate-x-20 font-bold text-xl"
          ></i>
        </div>
        <div class="side-menu px-4 py-6">
          <ul class="space-y-4">
            <!-- Dashboard Item -->
            <div class="menu-option">
              <a
                href="dashboard.php"
                class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer"
              >
                <div class="flex items-center space-x-2">
                  <i class="fa-solid fa-house text-lg pr-4"></i>
                  <span class="text-sm font-medium">Dashboard</span>
                </div>
              </a>
            </div>

            <!-- Disbursement Item  -->
            <div class="menu-option">
              <div
                class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer"
                onclick="toggleDropdown('NHOES-dropdown', this)"
              >
                <div class="flex items-center space-x-2">
                  <i class="fa-solid fa-wallet text-lg pr-4"></i>
                  <span class="text-sm font-medium"
                    >New Hire Onboarding and Employee Self-Service
                  </span>
                </div>
                <div class="arrow">
                  <i
                    class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"
                  ></i>
                </div>
              </div>
              <div
                id="NHOES-dropdown"
                class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2"
              >
                <ul class="space-y-1">
                  <li>
                    <a
                      href="#"
                      class="text-sm text-gray-800 hover:text-blue-600 hover:ms-2 duration-300 ease-out pr-5 py-2"
                      >Employees</a
                    >
                  </li>
                  <li>
                    <a
                      href="#"
                      class="text-sm text-gray-800 hover:text-blue-600 hover:ms-2 duration-300 ease-out pr-5 py-2"
                      >JobRoles</a
                    >
                  </li>
                  <li>
                    <a
                      href="#"
                      class="text-sm text-gray-800 hover:text-blue-600 hover:ms-2 duration-300 ease-out pr-5 py-2"
                      >Departments</a
                    >
                  </li>
                  <li>
                    <a
                      href="#"
                      class="text-sm text-gray-800 hover:text-blue-600 hover:ms-2 duration-300 ease-out pr-5 py-2"
                      >OnboardingTasks</a
                    >
                  </li>
                  <li>
                    <a
                      href="#"
                      class="text-sm text-gray-800 hover:text-blue-600 hover:ms-2 duration-300 ease-out pr-5 py-2"
                      >SelfServiceRequests</a
                    >
                  </li>
                </ul>
              </div>
            </div>

            <!-- Budget Management Item  -->
            <div class="menu-option">
              <div
                class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer"
                onclick="toggleDropdown('pms-dropdown', this)"
              >
                <div class="flex items-center space-x-2">
                  <i class="fa-solid fa-chart-pie text-lg pr-4"></i>
                  <span class="text-sm font-medium"
                    >Performance Management System</span
                  >
                </div>
                <div class="arrow">
                  <i
                    class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"
                  ></i>
                </div>
              </div>
              <div
                id="pms-dropdown"
                class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2"
              >
                <ul class="space-y-1">
                  <li>
                    <a
                      href="Performance.php"
                      class="text-sm text-gray-800 hover:text-blue-600 hover:ms-2 duration-300 ease-out pr-5 py-2"
                      >PerformanceReviews</a
                    >
                  </li>
                  <li>
                    <a
                      href="KPIs.php"
                      class="text-sm text-gray-800 hover:text-blue-600 hover:ms-2 duration-300 ease-out pr-5 py-2"
                      >KPIs</a
                    >
                  </li>
                  <li>
                    <a
                      href="Goals.php"
                      class="text-sm text-gray-800 hover:text-blue-600 hover:ms-2 duration-300 ease-out pr-5 py-2"
                      >Goals</a
                    >
                  </li>
                  <li>
                    <a
                      href="feedback.php"
                      class="text-sm text-gray-800 hover:text-blue-600 hover:ms-2 duration-300 ease-out pr-5 py-2"
                      >Feedback</a
                    >
                  </li>
                  <li>
                    <a
                      href="appraisals.php"
                      class="text-sm text-gray-800 hover:text-blue-600 hover:ms-2 duration-300 ease-out pr-5 py-2"
                      >Appraisals</a
                    >
                  </li>
                </ul>
              </div>
            </div>

            <!-- Collection Item  -->
            <div class="menu-option">
              <div
                class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer"
                onclick="toggleDropdown('ram-dropdown', this)"
              >
                <div class="flex items-center space-x-2">
                  <i class="fa-solid fa-folder-open text-lg pr-4"></i>
                  <span class="text-sm font-medium"
                    >Recruitment and Applicant Management</span
                  >
                </div>
                <div class="arrow">
                  <i
                    class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"
                  ></i>
                </div>
              </div>
              <div
                id="ram-dropdown"
                class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2"
              >
                <ul class="space-y-1">
                  <li>
                    <a
                      href="#"
                      class="text-sm text-gray-800 hover:text-blue-600 hover:ms-2 duration-300 ease-out pr-5 py-2"
                    >
                      applicants</a
                    >
                  </li>
                  <li>
                    <a
                      href="#"
                      class="text-sm text-gray-800 hover:text-blue-600 hover:ms-2 duration-300 ease-out pr-5 py-2"
                      >applicationstatus</a
                    >
                  </li>
                  <li>
                    <a
                      href="#"
                      class="text-sm text-gray-800 hover:text-blue-600 hover:ms-2 duration-300 ease-out pr-5 py-2"
                    >
                      interviewers</a
                    >
                  </li>
                  <li>
                    <a
                      href="#"
                      class="text-sm text-gray-800 hover:text-blue-600 hover:ms-2 duration-300 ease-out pr-5 py-2"
                    >
                      interviewschedules</a
                    >
                  </li>
                  <li>
                    <a
                      href="#"
                      class="text-sm text-gray-800 hover:text-blue-600 hover:ms-2 duration-300 ease-out pr-5 py-2"
                      >jobpostings</a
                    >
                  </li>
                </ul>
              </div>
            </div>

            <!-- General Ledger Item  -->
            <div class="menu-option">
              <div
                class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer"
                onclick="toggleDropdown('general-ledger-dropdown', this)"
              >
                <div class="flex items-center space-x-2">
                  <i class="fa-solid fa-money-bills text-lg pr-4"></i>
                  <span class="text-sm font-medium">Social Recognition</span>
                </div>
                <div class="arrow">
                  <i
                    class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"
                  ></i>
                </div>
              </div>
              <div
                id="general-ledger-dropdown"
                class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2"
              >
                <ul class="space-y-1">
                  <li>
                    <a
                      href="#"
                      class="text-sm text-gray-800 hover:text-blue-600 hover:ms-2 duration-300 ease-out pr-5 py-2"
                      >EmployeeRecognition</a
                    >
                  </li>
                </ul>
              </div>
            </div>

            <!-- Account Payable/Receiver Item  -->
            <div class="menu-option">
              <div
                class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer"
                onclick="toggleDropdown('competency-dropdown', this)"
              >
                <div class="flex items-center space-x-2">
                  <i class="fa-solid fa-file-invoice-dollar text-lg pr-4"></i>
                  <span class="text-sm font-medium">Competency Management</span>
                </div>
                <div class="arrow">
                  <i
                    class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"
                  ></i>
                </div>
              </div>
              <div
                id="competency-dropdown"
                class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2"
              >
                <ul class="space-y-1">
                  <li>
                    <a
                      href="#"
                      class="text-sm text-gray-800 hover:text-blue-600 hover:ms-2 duration-300 ease-out pr-5 py-2"
                      >Competencies</a
                    >
                  </li>
                  <li>
                    <a
                      href="#"
                      class="text-sm text-gray-800 hover:text-blue-600 hover:ms-2 duration-300 ease-out pr-5 py-2"
                      >EmployeeCompetencies</a
                    >
                  </li>
                </ul>
              </div>
            </div>

            <div class="menu-option">
              <div
                class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer"
                onclick="toggleDropdown('succession-dropdown', this)"
              >
                <div class="flex items-center space-x-2">
                  <i class="fa-solid fa-file-invoice-dollar text-lg pr-4"></i>
                  <span class="text-sm font-medium">Succession Planning</span>
                </div>
                <div class="arrow">
                  <i
                    class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"
                  ></i>
                </div>
              </div>
              <div
                id="succession-dropdown"
                class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2"
              >
                <ul class="space-y-1">
                  <li>
                    <a
                      href="#"
                      class="text-sm text-gray-800 hover:text-blue-600 hover:ms-2 duration-300 ease-out pr-5 py-2"
                      >SuccessionPlans</a
                    >
                  </li>
                  <li>
                    <a
                      href="#"
                      class="text-sm text-gray-800 hover:text-blue-600 hover:ms-2 duration-300 ease-out pr-5 py-2"
                      >KeyPositions</a
                    >
                  </li>
                  <li>
                    <a
                      href="#"
                      class="text-sm text-gray-800 hover:text-blue-600 hover:ms-2 duration-300 ease-out pr-5 py-2"
                      >PotentialSuccessors</a
                    >
                  </li>
                </ul>
              </div>
            </div>

            <div class="menu-option">
              <div
                class="menu-name flex justify-between items-center space-x-3 hover:bg-[#F7E6CA] px-4 py-3 rounded-lg transition duration-300 ease-in-out cursor-pointer"
                onclick="toggleDropdown('ltm-dropdown', this)"
              >
                <div class="flex items-center space-x-2">
                  <i class="fa-solid fa-file-invoice-dollar text-lg pr-4"></i>
                  <span class="text-sm font-medium"
                    >Learning and Training Management</span
                  >
                </div>
                <div class="arrow">
                  <i
                    class="bx bx-chevron-right text-[18px] font-semibold arrow-icon"
                  ></i>
                </div>
              </div>
              <div
                id="ltm-dropdown"
                class="menu-drop hidden flex-col w-full bg-[#F7E6CA] rounded-lg p-4 space-y-2 mt-2"
              >
                <ul class="space-y-1">
                  <li>
                    <a
                      href="#"
                      class="text-sm text-gray-800 hover:text-blue-600 hover:ms-2 duration-300 ease-out pr-5 py-2"
                      >TrainingPrograms</a
                    >
                  </li>
                  <li>
                    <a
                      href="#"
                      class="text-sm text-gray-800 hover:text-blue-600 hover:ms-2 duration-300 ease-out pr-5 py-2"
                      >Enrollments</a
                    >
                  </li>
                  <li>
                    <a
                      href="#"
                      class="text-sm text-gray-800 hover:text-blue-600 hover:ms-2 duration-300 ease-out pr-5 py-2"
                      >Trainers</a
                    >
                  </li>
                  <li>
                    <a
                      href="#"
                      class="text-sm text-gray-800 hover:text-blue-600 hover:ms-2 duration-300 ease-out pr-5 py-2"
                      >Assessments</a
                    >
                  </li>
                </ul>
              </div>
            </div>
          </ul>
        </div>
      </div>

      <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
        <!-- Navbar -->
        <nav
          class="h-16 w-full bg-white border-b border-[#F7E6CA] flex justify-between items-center px-6 py-4"
        >
          <!-- Left Navigation Section -->
          <div class="left-nav flex items-center space-x-4 max-w-96 w-full">
            <!-- Toggle Menu Button-->
            <button
              aria-label="Toggle menu"
              class="menu-btn text-[#4E3B2A] focus:outline-none hover:bg-[#F7E6CA] hover:rounded-full"
            >
              <i class="fa-solid fa-bars text-[#594423] text-xl w-11 py-2"></i>
            </button>

          </div>

          <div>
            <i
              class="fa-regular fa-user bg-[#594423] text-white px-4 py-2 rounded-lg cursor-pointer text-lg lg:hidden"
              aria-label="User profile"
            ></i>
          </div>

          <!-- Profile Dropdown (Click to Open) -->
<div class="relative">
  <div
    class="flex items-center space-x-2 cursor-pointer"
    onclick="toggleProfileDropdown()"
    id="profile-btn"
  >
    <i
      class="fa-regular fa-user bg-[#594423] text-white px-4 py-2 rounded-lg text-lg"
      aria-label="User profile"
    ></i>
    <div class="info flex flex-col py-2">
      <h1 class="text-[#4E3B2A] font-semibold font-serif text-sm">
        <?php echo htmlspecialchars($_SESSION['Name'] ?? ''); ?>
      </h1>
      <p class="text-[#594423] text-sm pl-2"><?php echo htmlspecialchars($_SESSION['Role'] ?? ''); ?></p>
    </div>
  </div>

  <!-- Dropdown Menu -->
  <div
    id="profile-dropdown"
    class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded shadow-lg z-50 hidden"
  >
    <a
      href="../logout.php"
      class="block px-4 py-2 text-sm text-[#4E3B2A] hover:bg-[#f5f5f5] hover:text-[#594423] hover:pl-6 transition-all duration-200"
    >
      Logout
    </a>
  </div>
</div>


        </nav>
        

        <!-- Main Content -->
        <main class="px-8 py-8">
          <!-- All Content Put Here -->
         
    <script>
      function toggleProfileDropdown() {
    document.getElementById('profile-dropdown').classList.toggle('hidden');
    document.getElementById('notif-dropdown').classList.add('hidden');
  }

  function toggleNotificationDropdown() {
    document.getElementById('notif-dropdown').classList.toggle('hidden');
    document.getElementById('profile-dropdown').classList.add('hidden');
  }

  // Close dropdowns when clicking outside
  document.addEventListener('click', function (event) {
    const profileBtn = document.getElementById('profile-btn');
    const profileDropdown = document.getElementById('profile-dropdown');
    const notifBtn = document.getElementById('notif-btn');
    const notifDropdown = document.getElementById('notif-dropdown');

    if (!profileBtn.contains(event.target)) {
      profileDropdown.classList.add('hidden');
    }
    if (!notifBtn.contains(event.target)) {
      notifDropdown.classList.add('hidden');
    }
  });
      const menu = document.querySelector(".menu-btn");
      const sidebar = document.querySelector(".sidebar");
      const main = document.querySelector(".main");
      const overlay = document.getElementById("sidebar-overlay");
      const close = document.getElementById("close-sidebar-btn");

      function closeSidebar() {
        sidebar.classList.remove("mobile-active");
        overlay.classList.remove("active");
        document.body.style.overflow = "auto";
      }

      function openSidebar() {
        sidebar.classList.add("mobile-active");
        overlay.classList.add("active");
        document.body.style.overflow = "hidden";
      }

      function toggleSidebar() {
        if (window.innerWidth <= 968) {
          sidebar.classList.add("sidebar-expanded");
          sidebar.classList.remove("sidebar-collapsed");
          sidebar.classList.contains("mobile-active")
            ? closeSidebar()
            : openSidebar();
        } else {
          sidebar.classList.toggle("sidebar-collapsed");
          sidebar.classList.toggle("sidebar-expanded");
          main.classList.toggle("md:ml-[85px]");
          main.classList.toggle("md:ml-[360px]");
        }
      }

      menu.addEventListener("click", toggleSidebar);
      overlay.addEventListener("click", closeSidebar);
      close.addEventListener("click", closeSidebar);

      window.addEventListener("resize", () => {
        if (window.innerWidth > 968) {
          closeSidebar();
          sidebar.classList.remove("mobile-active");
          overlay.classList.remove("active");
          sidebar.classList.remove("sidebar-collapsed");
          sidebar.classList.add("sidebar-expanded");
        } else {
          sidebar.classList.add("sidebar-expanded");
          sidebar.classList.remove("sidebar-collapsed");
        }
      });

      function toggleDropdown(dropdownId, element) {
        const dropdown = document.getElementById(dropdownId);
        const icon = element.querySelector(".arrow-icon");
        const allDropdowns = document.querySelectorAll(".menu-drop");
        const allIcons = document.querySelectorAll(".arrow-icon");

        allDropdowns.forEach((d) => {
          if (d !== dropdown) d.classList.add("hidden");
        });

        allIcons.forEach((i) => {
          if (i !== icon) {
            i.classList.remove("bx-chevron-down");
            i.classList.add("bx-chevron-right");
          }
        });

        dropdown.classList.toggle("hidden");
        icon.classList.toggle("bx-chevron-right");
        icon.classList.toggle("bx-chevron-down");
      }
      function toggleProfileDropdown() {
  const dropdown = document.getElementById("profile-dropdown");
  dropdown.classList.toggle("hidden");
}

// Optional: Hide dropdown when clicking outside
document.addEventListener("click", function (event) {
  const isClickInside = document.getElementById("profile-btn")?.contains(event.target) ||
                        document.getElementById("profile-dropdown")?.contains(event.target);
  if (!isClickInside) {
    document.getElementById("profile-dropdown")?.classList.add("hidden");
  }
});

    </script>
  </body>
</html>