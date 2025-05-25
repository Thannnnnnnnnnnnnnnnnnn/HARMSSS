<?php
session_start();
include("../connection.php");

define('MAX_ATTEMPTS', 5);
define('MAX_OTP_ATTEMPTS', 3);
define('COOLDOWN_SECONDS', 3600);

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
    $connections["hr34_usm"],
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
    $Log_Date_Time = date('Y-m-d H:i:s');
    $sql = "
        INSERT INTO user_log_history 
        (User_ID, Name, Role, Log_Status, Attempt_Type, Attempt_Count, Failure_reason, Cooldown_Until, `Log_Date_Time`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssssss", 
        $User_ID, $Name, $Role, $Log_Status, $Attempt_Type, 
        $Attempt_Count, $Failure_reason, $Cooldown_Until, $Log_Date_Time);
    mysqli_stmt_execute($stmt);
}

// === Function: Department Log ===
function logDepartmentAttempt($conn, $Dept_log_ID, $Department_ID, $User_ID, $Name, $Role, $Log_Status, $Attempt_type, $Attempt_Count, $Failure_reason, $Cooldown_Until) {
    $Log_Date_Time = date('Y-m-d H:i:s');
    $sql = "
        INSERT INTO department_log_history 
        (Dept_log_ID, Department_ID, User_ID, Name, Role, Log_Status, Attempt_type, Attempt_count, Failure_reason, Cooldown_until, Log_Date_Time)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "issssssisss", 
        $Dept_log_ID, $Department_ID, $User_ID, $Name, $Role, $Log_Status, $Attempt_type, 
        $Attempt_Count, $Failure_reason, $Cooldown_Until, $Log_Date_Time);
    mysqli_stmt_execute($stmt);
}

function incrementOTPAttempts() {
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

    $redirectMap = [
        'L220305' => '../Logistics 2/Vehicle reservation/VRS/vehicles.php',
        'L120304' => '../Logistics 1/Procurement/submit_request.php',
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
</html>
