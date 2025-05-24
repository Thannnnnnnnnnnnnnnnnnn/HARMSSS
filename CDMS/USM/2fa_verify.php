<?php
session_start();
include("../connection.php");

define('MAX_ATTEMPTS', 5);
define('MAX_OTP_ATTEMPTS', 3);
define('COOLDOWN_SECONDS', 3600);

<<<<<<< HEAD
// Only process OTP if this is a POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $User_ID = $_SESSION["User_ID"] ?? null;
    $Role = $_SESSION["Role"] ?? null;
    $Department_ID = trim($_SESSION["Department_ID"] ?? '');
    $otpInput = trim($_POST["otp"] ?? '');

$connectionsList = [
    $connections["logs2_usm"],
    $connections["logs1_usm"],
    $connections["hr_1&2_usm"],
    $connections["fin_usm"],
    $connections["cr1_usm"],
    $connections["user_management"],
    $connections["hr34_usm"] ?? ''
];

// === Function: Resolve User Name Across Databases ===
function resolveName($User_ID, $connectionsList) {
    foreach ($connectionsList as $conn) {
        if (!$conn) continue;
        $stmt = mysqli_prepare($conn, "SELECT Name FROM department_accounts WHERE User_ID = ?");
        mysqli_stmt_bind_param($stmt, "s", $User_ID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            return $row["Name"];
        }
    }
    return null;
}

// === Function: Central Log ===
function logAttempt($conn, $User_ID, $Name, $Role, $Log_Status, $Attempt_Type, $Attempt_Count, $Failure_reason, $Cooldown_Until) {
=======
function dd($value)
{
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
    die();
}
$User_ID = $_SESSION["User_ID"];
$otpInput = trim($_POST["otp"] ?? '');
$Log_Date_Time = date('Y-m-d H:i:s');

// === Function: Log user 2FA attempts ===
function logAttempt($conn, $User_ID, $Name, $Role, $Log_Status, $Attempt_Type, $Attempt_Count, $Failure_reason, $Cooldown_Until)
{
>>>>>>> e7efff534a5dad81579b5b4b4ebd9edaa7e0cd47
    $Log_Date_Time = date('Y-m-d H:i:s');
    $sql = "
        INSERT INTO user_log_history 
        (User_ID, Name, Role, Log_Status, Attempt_Type, Attempt_Count, Failure_reason, Cooldown_Until, `Log_Date_Time`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param(
        $stmt,
        "sssssssss",
        $User_ID,
        $Name,
        $Role,
        $Log_Status,
        $Attempt_Type,
        $Attempt_Count,
        $Failure_reason,
        $Cooldown_Until,
        $Log_Date_Time
    );
    mysqli_stmt_execute($stmt);
}

<<<<<<< HEAD
// === Function: Department Log ===
function logDepartmentAttempt($conn, $Dept_log_ID, $Department_ID, $User_ID, $Name, $Role, $Log_Status, $Attempt_type, $Attempt_Count, $Failure_reason, $Cooldown_Until) {
=======
// === Function: Log department 2FA attempts ===
function logDepartmentAttempt($conn, $Dept_log_ID, $Department_ID, $User_ID, $Name, $Role, $Log_Status, $Attempt_type, $Attempt_Count, $Failure_reason, $Cooldown_Until)
{
>>>>>>> e7efff534a5dad81579b5b4b4ebd9edaa7e0cd47
    $Log_Date_Time = date('Y-m-d H:i:s');
    $sql = "
        INSERT INTO department_log_history 
        (Dept_log_ID, Department_ID, User_ID, Name, Role, Log_Status, Attempt_type, Attempt_count, Failure_reason, Cooldown_until, Log_Date_Time)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param(
        $stmt,
        "issssssisss",
        $Dept_log_ID,
        $Department_ID,
        $User_ID,
        $Name,
        $Role,
        $Log_Status,
        $Attempt_type,
        $Attempt_Count,
        $Failure_reason,
        $Cooldown_Until,
        $Log_Date_Time
    );
    mysqli_stmt_execute($stmt);
}

<<<<<<< HEAD
function incrementOTPAttempts() {
=======
$Name = null; // default

// Try logistic 2 first
$stmt = mysqli_prepare($logs2_usm, "SELECT Name FROM department_accounts WHERE User_ID = ?");
mysqli_stmt_bind_param($stmt, "s", $User_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if ($row = mysqli_fetch_assoc($result)) {
    $Name = $row["Name"];
}


// Try logistic 2 first
$stmt = mysqli_prepare($hr1_2_usm, "SELECT Name FROM department_accounts WHERE User_ID = ?");
mysqli_stmt_bind_param($stmt, "s", $User_ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if ($row = mysqli_fetch_assoc($result)) {
    $Name = $row["Name"];
}

// If not found, try Financial USM
if (!$Name) {
    $stmt = mysqli_prepare($fin_usm_connection, "SELECT Name FROM department_accounts WHERE User_ID = ?");
    mysqli_stmt_bind_param($stmt, "s", $User_ID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $Name = $row["Name"];
    }
}

// If not found, try Core 1 USM
if (!$Name) {
    $stmt = mysqli_prepare($cr1_usm, "SELECT Name FROM department_accounts WHERE User_ID = ?");
    mysqli_stmt_bind_param($stmt, "s", $User_ID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $Name = $row["Name"];
    }
}


// If still not found, try Department USM
if (!$Name) {
    $stmt = mysqli_prepare($usm_connection, "SELECT Name FROM department_accounts WHERE User_ID = ?");
    mysqli_stmt_bind_param($stmt, "s", $User_ID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $Name = $row["Name"];
    }
}
// If still not found, try Department USM
if (!$Name) {
    $stmt = mysqli_prepare($hr3_4_usm, "SELECT Name FROM department_accounts WHERE User_ID = ?");
    mysqli_stmt_bind_param($stmt, "s", $User_ID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $Name = $row["Name"];
    }
}

// === Function: Increment OTP attempts ===
function incrementOTPAttempts()
{
>>>>>>> e7efff534a5dad81579b5b4b4ebd9edaa7e0cd47
    if (!isset($_SESSION["otp_attempts"])) {
        $_SESSION["otp_attempts"] = 1;
    } else {
        $_SESSION["otp_attempts"]++;
    }
}

// === Guard Clause for Invalid Access ===
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !$otpInput) {
    $_SESSION["loginError"] = "Invalid OTP submission.";
    header("Location: 2fa_verify.php");
    exit();
}

$Name = resolveName($User_ID, $connectionsList);

// === Check Cooldown Ban ===
$loginAttemptsKey = "login_attempts_$User_ID";
if (isset($_SESSION[$loginAttemptsKey]) && $_SESSION[$loginAttemptsKey]['count'] >= MAX_ATTEMPTS) {
    $lastAttempt = $_SESSION[$loginAttemptsKey]['last'];
    $remaining = COOLDOWN_SECONDS - (time() - $lastAttempt);
    if ($remaining > 0) {
        $minutes = ceil($remaining / 60);
        $cooldownUntil = date('Y-m-d H:i:s', $lastAttempt + COOLDOWN_SECONDS);
        logAttempt($connections["fin_usm"], $User_ID, $Name, $Role, 'Failed', '2FA', $_SESSION[$loginAttemptsKey]['count'], 'Account banned (cooldown)', $cooldownUntil);
        $_SESSION["loginError"] = "Your account is temporarily banned. Try again in $minutes minute(s).";
        header("Location: 2fa_verify.php");
        exit();
    } else {
        unset($_SESSION[$loginAttemptsKey]);
    }
}

// === OTP Check ===
$storedOtp = $_SESSION["otp"];
if ($otpInput === (string)$storedOtp) {
    // ✅ Success
    logAttempt($connections["logs2_usm"], $User_ID, $Name, $Role, 'Success', '2FA', 0, '2FA Successful', '');
    logDepartmentAttempt($connections["logs2_usm"], $User_ID, $Department_ID, $User_ID, $Name, $Role, 'Success', '2FA', 0, '2FA Successful', '');

<<<<<<< HEAD
    $redirectMap = [
        'L220305' => '../Logistics 2/Vehicle reservation/VRS/vehicles.php',
        'L120304' => '../Logistics 2/Vehicle reservation/VRS/vehicles.php',
        'F20309' => '../Financials/financial2/User_Management/Department_Acc.php',
        'HR120302' => '../HR part 1 - 2/recruitment_applicant_management/controllers/admin/index.php',
        'HR220303' => '../hr34/admin_landing.php',
        'C120306' => '../Core transaction 1/CoreTrans1/Dashboard.php'
    ];
    $redirectUrl = $redirectMap[$Department_ID] ?? 'login.php';
    header("Location: $redirectUrl");
    exit();
} else {
    // ❌ Fail
    incrementOTPAttempts();
    $otpAttempt = $_SESSION["otp_attempts"];
    logAttempt($connections["logs2_usm"], $User_ID, $Name, $Role, 'Failed', '2FA', $otpAttempt, 'Incorrect OTP', '');
    logDepartmentAttempt($connections["logs2_usm"], $User_ID, $Department_ID, $User_ID, $Name, $Role, 'Failed', '2FA', $otpAttempt, 'Incorrect OTP', '');

    if ($otpAttempt >= MAX_OTP_ATTEMPTS) {
        $_SESSION["loginError"] = "Too many incorrect OTP attempts. Please try again later.";
        header("Location: login.php");
        exit();
    }    $_SESSION["loginError"] = "Incorrect OTP.";
    header("Location: 2fa_verify.php");
    exit();
}
} // End of POST request processing

?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
=======
        logAttempt($logs2_usm, $User_ID, $Name, $Role, 'Success', '2FA', 0, '2FA Successful', '');
        logDepartmentAttempt($logs2_usm, $User_ID, $Department_ID, $User_ID, $Name, $Role, 'Success', '2FA', 0, '2FA Successful', '');
        // dd('Success');
        // === Role-based Department Redirects ===
        if ($Department_ID == 'L220305') {
            dd('L220305');
            switch ($User_ID) {
                case 'S225178160504':  // Vehicle Reservation
                    header("Location: ../Logistics 2/Vehicle reservation/VRS/vehicles.php");
                    exit();

                case 'S225186490504':  // Audit Management
                    header("Location: audit_dashboard.php");
                    exit();

                case 'S225210110504':  // Fleet Management
                    header("Location: fleet_dashboard.php");
                    exit();

                case 'S225101320504':  // Vendor Portal
                    header("Location: vendor_dashboard.php");
                    exit();

                case 'S225112233504':  // Document Tracking System
                    header("Location: document_tracking_dashboard.php");
                    exit();

                default:
                    header("Location: login.php");
                    exit();
            }

            //Financials
        } elseif ($Department_ID == 'F20309') {
            dd('F20309');
            switch ($User_ID) {
                case 's254225000904':  // John Mark Balacy
                    header("Location: ../Financials/financial2/User_Management/Department_Acc.php");
                    exit();

                case 's254223290904':  // Audit Management
                    header("Location: ../Financials/financial2/User_Management/Department_Acc.php");
                    exit();

                case 's254124910904':  // Fleet Management
                    header("Location: ../Financials/financial2/User_Management/Department_Acc.php");
                    exit();

                case 's254191860904':  // Vendor Portal
                    header("Location: ../Financials/financial2/User_Management/Department_Acc.php");
                    exit();

                case 's254105470904':  // Document Tracking System
                    header("Location: ../Financials/financial2/User_Management/Department_Acc.php");
                    exit();

                case 's254166290904':  // Document Tracking System
                    header("Location: ../Financials/financial2/User_Management/Department_Acc.php");
                    exit();

                default:
                    header("Location: login.php");
                    exit();
            }

            //hr 1- 2 1
        } elseif ($Department_ID == 'HR120302') {
            dd('HR120302');
            switch ($User_ID) {
                case 'S225206660204':  // John Mark Balacy
                    dd('TEST');
                    header("Location: HR part 1 - 2/recruitment_applicant_management/controllers/admin/index.php");
                    exit();
                default:
                    header("Location: login.php");
                    exit();
            }

            //hr 34
        } elseif ($Department_ID == 'HR220303') {
            dd('HR220303');
            switch ($User_ID) {
                case 'SA22501830301':  // John Mark Balacy
                    header("Location:   ../hr34/admin_landing.php");
                    exit();



                default:
                    header("Location: login.php");
                    exit();
            }


            //Core 1
        } elseif ($Department_ID == 'C120306') {
            dd('C120306');
            switch ($User_ID) {
                case 'A225224220602':  // bert
                    header("Location: ../Core transaction 1/CoreTrans1/Dashboard.php");
                    exit();

                case 'M2250190810603':  // thei
                    header("Location: ../Core transaction 1/CoreTrans1/Dashboard.php");
                    exit();

                case '#':  //
                    header("Location: #");
                    exit();

                case '#2':  // 
                    header("Location: #");
                    exit();

                case '#1':  // 
                    header("Location: #");
                    exit();

                default:
                    header("Location: #");
                    exit();
            }
        } else {
            // fallback
            dd('Unknown Department_ID');
            header("Location: login.php");
            exit();
        }
    } else {
        // OTP is incorrectx`
        incrementOTPAttempts();

        $Role = $_SESSION["Role"]; // Use Role from session
        $Department_ID = $_SESSION["Department_ID"]; // Use Department_ID from session

        logAttempt($logs2_usm, $User_ID, $Name, $Role, 'Failed', '2FA', $_SESSION["otp_attempts"], 'Incorrect OTP', '');
        logDepartmentAttempt($logs2_usm, $User_ID, $Department_ID, $User_ID, $Name, $Role, 'Failed', '2FA', $_SESSION["otp_attempts"], 'Incorrect OTP', '');

        if ($_SESSION["otp_attempts"] >= 3) {
            $_SESSION["loginError"] = "Too many incorrect OTP attempts. Please try again later.";
            header("Location: login.php");
            exit();
        }

        $_SESSION["loginError"] = "Incorrect OTP.";
        header("Location: 2fa_verify.php");
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

>>>>>>> e7efff534a5dad81579b5b4b4ebd9edaa7e0cd47
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>2FA Verification</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
    }
    /* Soft fade-in animation */
    .fade-in {
      animation: fadeIn 0.8s ease forwards;
      opacity: 0;
    }
    @keyframes fadeIn {
      to {
        opacity: 1;
      }
    }
  </style>
</head>
<<<<<<< HEAD

<body class="flex items-center justify-center p-6">
  <div class="fade-in max-w-md w-full bg-white/90 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/30 p-10 text-center">

    <div class="mb-8">
      <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-tr from-purple-600 to-indigo-600 rounded-full shadow-lg mx-auto mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c1.104 0 2-.672 2-1.5S13.104 8 12 8s-2 .672-2 1.5S10.896 11 12 11z" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c-5.523 0-10-3.134-10-7 0-1.72 1.283-3.299 3.492-4.34M12 21c5.523 0 10-3.134 10-7 0-1.72-1.283-3.299-3.492-4.34" />
        </svg>
      </div>
      <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Two-Factor Authentication</h1>
      <p class="mt-2 text-gray-600">Enter the one-time passcode sent to your device</p>
    </div>
=======

<body class="bg-gray-100">
    <div class="w-full h-dvh flex items-center justify-center bg-cover bg-center relative" style="background-image: url('left.png');">
        <!-- Dark overlay for readability -->
        <div class="absolute inset-0 bg-black bg-opacity-40 z-0"></div>

        <!-- 2FA Container -->
        <div class="relative z-10 bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md mx-4">
            <h3 class="text-center text-4xl font-semibold text-gray-800 mb-6 animate-fade-in-down">🔐 2FA Verification</h3>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="space-y-6">
                <div>
                    <label for="otp" class="block text-gray-700 text-lg font-medium mb-2">Enter OTP:</label>
                    <input
                        type="text"
                        id="otp"
                        name="otp"
                        required
                        maxlength="6"
                        placeholder="6-digit code"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                        aria-label="One Time Password" />
                </div>

                <button
                    type="submit"
                    class="w-full py-3 bg-blue-600 text-white rounded-lg text-lg font-semibold hover:bg-blue-700 transition focus:outline-none focus:ring-2 focus:ring-blue-400">
                    ✅ Verify OTP
                </button>
            </form>

            <!-- Optional: add resend option -->
            <div class="text-center text-sm mt-4 text-gray-500">
                Didn't receive the code?
                <a href="resend_otp.php" class="text-blue-600 hover:underline">Resend</a>
            </div>
        </div>
    </div>

>>>>>>> e7efff534a5dad81579b5b4b4ebd9edaa7e0cd47

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="space-y-6">
      <label for="otp" class="sr-only">OTP Code</label>
      <input 
        type="text" 
        id="otp" 
        name="otp" 
        maxlength="6" 
        required 
        autocomplete="one-time-code"
        pattern="[0-9]{6}"
        placeholder="123456"
        inputmode="numeric"
        class="w-full px-6 py-4 text-center text-2xl tracking-widest font-semibold rounded-xl border border-gray-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-400 transition outline-none"
        aria-label="One Time Password"
      />
      
      <button 
        type="submit"
        class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-lg transition focus:ring-4 focus:ring-indigo-400 focus:outline-none"
      >
        Verify <span class="ml-2">🔐</span>
      </button>
    </form>

    <p class="mt-6 text-sm text-gray-500">
      Didn’t get the code?&nbsp;
      <a href="resend_otp.php" class="font-semibold text-indigo-600 hover:underline">Resend</a>
    </p>

  </div>

  <?php if (isset($_SESSION["loginError"])): ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Verification Failed',
        text: "<?= htmlspecialchars($_SESSION['loginError'], ENT_QUOTES); ?>",
        confirmButtonColor: '#5a67d8',
        background: '#ffffff',
        timer: 3500,
        timerProgressBar: true,
      });
    </script>
    <?php unset($_SESSION["loginError"]); ?>
  <?php endif; ?>
</body>
<<<<<<< HEAD
</html>
=======

</html>
>>>>>>> e7efff534a5dad81579b5b4b4ebd9edaa7e0cd47
