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
    $connections["cr3_re_usm"],
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

// === Function: Department Log ===
function logDepartmentAttempt($conn, $Dept_log_ID, $Department_ID, $User_ID, $Name, $Role, $Log_Status, $Attempt_type, $Attempt_Count, $Failure_reason, $Cooldown_Until) {
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
        'L120304' => '../Logistics 2/Vehicle reservation/VRS/vehicles.php',
        'F20309' => '../Financials/financial2/User_Management/Department_Acc.php',
        'HR120302' => '../HR part 1 - 2/recruitment_applicant_management/controllers/admin/index.php',
        'HR220303' => '../hr34/admin_landing.php',
        'C120306' => '../Core transaction 1/CoreTrans1/Dashboard.php',
        'C320308' => '../Core transaction 3/testing/dashboard.php'
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
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>2FA Verification</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="w-full h-dvh flex items-center justify-center bg-cover bg-center relative" style="background-image: url('left.png');">
        <div class="absolute inset-0 bg-black bg-opacity-40 z-0"></div>
        <div class="relative z-10 bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md mx-4">
            <h3 class="text-center text-4xl font-semibold text-gray-800 mb-6">🔐 2FA Verification</h3>
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
                        aria-label="One Time Password"
                    />
                </div>
                <button 
                    type="submit"
                    class="w-full py-3 bg-blue-600 text-white rounded-lg text-lg font-semibold hover:bg-blue-700 transition focus:outline-none focus:ring-2 focus:ring-blue-400"
                >
                    ✅ Verify OTP
                </button>
            </form>
            <div class="text-center text-sm mt-4 text-gray-500">
                Didn't receive the code?
                <a href="resend_otp.php" class="text-blue-600 hover:underline">Resend</a>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION["loginError"])): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Verification Failed',
                text: "<?= htmlspecialchars($_SESSION['loginError'], ENT_QUOTES); ?>",
                confirmButtonColor: '#3085d6',
                background: '#fefefe'
            });
        </script>
        <?php unset($_SESSION["loginError"]); ?>
    <?php endif; ?>
</body>
</html>