<?php
session_start();
// The connection.php is in the parent directory relative to this login.php
include("../connection.php");

// Load PHPMailer classes using Composer's autoloader
// Assuming Composer's autoload.php is at C:\xampp\htdocs\HARMS\CDMS\vendor\autoload.php
require __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Define specific connections needed for login process from the global $connections array
$usm_connection = $connections["hr34_usm"]; // Assuming hr34_usm contains department_accounts
$logs2_usm = $connections["logs2_usm"]; // For logging user_log_history and department_log_history
$hr1_2_usm = $connections["hr_1_2_new_hire_onboarding_and_employee_self-service"]; // For HR1-2 data mapping
// Add other connections as needed from your connection.php, e.g., $fin_usm_connection, $cr1_usm, $cr3_re_usm

// Constants for login attempt limits and cooldown
define('MAX_LOGIN_ATTEMPTS', 5);
define('COOLDOWN_SECONDS', 3600); // 1 hour

$User_ID = trim($_POST["User_ID"] ?? '');
$password = trim($_POST["Password"] ?? '');
$loginAttemptsKey = "login_attempts_$User_ID";
$Log_Date_Time = date('Y-m-d H:i:s');

// --- Function: Log user login attempts (using mysqli for the provided connection type) ===
// Modified to accept a specific connection resource, as connection.php returns multiple
function logAttempt($conn, $User_ID, $User_name, $Role, $Log_Status, $Attempt_Type, $Attempt_Count, $Failure_reason, $Cooldown_Until) {
    $Log_Date_Time = date('Y-m-d H:i:s');
    // Ensure the connection is valid before preparing the statement
    if (!$conn) {
        error_log("Attempt to log with invalid connection for user_log_history. User_ID: $User_ID");
        return false;
    }
    $sql = "
        INSERT INTO user_log_history 
        (User_ID, Name, Role, Log_Status, Attempt_Type, Attempt_Count, Failure_reason, Cooldown_Until, `Log_Date_Time`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssssisss", // User_ID, Name, Role, Log_Status, Attempt_Type are strings, Attempt_Count is int
            $User_ID, $User_name, $Role, $Log_Status, $Attempt_Type, 
            $Attempt_Count, $Failure_reason, $Cooldown_Until, $Log_Date_Time);
        mysqli_stmt_execute($stmt);
        return true;
    } else {
        error_log("Failed to prepare statement for user_log_history: " . mysqli_error($conn));
        return false;
    }
}

// === Function: Log department login attempts ===
function logDepartmentAttempt($conn, $Department_ID, $User_ID, $Name, $Role, $Log_Status, $Attempt_type, $Attempt_Count, $Failure_reason, $Cooldown_Until) {
    $Log_Date_Time = date('Y-m-d H:i:s');
     // Ensure the connection is valid before preparing the statement
    if (!$conn) {
        error_log("Attempt to log with invalid connection for department_log_history. User_ID: $User_ID");
        return false;
    }
    $sql = "
        INSERT INTO department_log_history 
        (Department_ID, User_ID, Name, Role, Log_Status, Attempt_type, Attempt_count, Failure_reason, Cooldown_until, Log_Date_Time)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssssissss", // Department_ID, User_ID, Name, Role, Log_Status, Attempt_type are strings, Attempt_count is int
            $Department_ID, $User_ID, $Name, $Role, $Log_Status, $Attempt_type, 
            $Attempt_Count, $Failure_reason, $Cooldown_Until, $Log_Date_Time);
        mysqli_stmt_execute($stmt);
        return true;
    } else {
        error_log("Failed to prepare statement for department_log_history: " . mysqli_error($conn));
        return false;
    }
}


// === Function: Increment login attempts ===
function incrementLoginAttempts($User_ID) {
    $key = "login_attempts_$User_ID";
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 1, 'last' => time()];
    } else {
        $_SESSION[$key]['count']++;
        $_SESSION[$key]['last'] = time();
    }
}

// === Cooldown enforcement ===
if (isset($_SESSION[$loginAttemptsKey]) && $_SESSION[$loginAttemptsKey]['count'] >= MAX_LOGIN_ATTEMPTS) {
    $lastAttempt = $_SESSION[$loginAttemptsKey]['last'];
    $remaining = COOLDOWN_SECONDS - (time() - $lastAttempt);
    if ($remaining > 0) {
        $minutes = ceil($remaining / 60);
        $cooldownUntil = date('Y-m-d H:i:s', $lastAttempt + COOLDOWN_SECONDS);
        // Use logs2_usm for logging cooldown
        logAttempt($logs2_usm, $User_ID, $User_ID, 'Unknown', 'Failed', 'Login', $_SESSION[$loginAttemptsKey]['count'], 'Account banned (cooldown)', $cooldownUntil);
        $_SESSION["loginError"] = "Your account is temporarily banned. Try again in $minutes minute(s).";
        header("Location: login.php");
        exit();
    } else {
        unset($_SESSION[$loginAttemptsKey]);
    }
}

// === Function: Send OTP via email (using modern PHPMailer via Composer) ===
function sendOTP($email, $otp) {
    $mail = new PHPMailer(true); // Enable exceptions
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Use your SMTP host
        $mail->SMTPAuth = true;
        // Fetch SMTP credentials from environment variables for security
        $mail->Username = getenv('GMAIL_USER'); // Your Gmail email address
        $mail->Password = getenv('GMAIL_APP_PASSWORD'); // Your Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use ENCRYPTION_SMTPS for port 465, or STARTTLS for port 587
        $mail->Port = 465; // Or 587 for STARTTLS

        $mail->setFrom(getenv('GMAIL_USER'), 'Avalon System Authenticator'); // Sender's email and name
        $mail->addAddress($email);
        $mail->Subject = 'Your 2FA Verification Code';
        $mail->Body = "Your login verification code is: $otp";

        return $mail->send();
    } catch (Exception $e) {
        error_log("Failed to send OTP email: " . $e->getMessage());
        return false;
    }
}

// === Main Login Logic ===
if ($_SERVER["REQUEST_METHOD"] === "POST" && $User_ID && $password) {
    $authenticated = false;
    $userData = null;
    $target_conn = null;

    // List of database connections to check, in order of priority (adjust as needed)
    // IMPORTANT: Make sure these database names match the keys in your $connections array from connection.php
    $database_check_order = [
        "logs2_usm" => $connections["logs2_usm"],
        "hr_1_2_new_hire_onboarding_and_employee_self-service" => $connections["hr_1_2_new_hire_onboarding_and_employee_self-service"],
        "logs1_usm" => $connections["logs1_usm"],
        "cr1_usm" => $connections["cr1_usm"],
        "hr34_usm" => $connections["hr34_usm"],
        "fin_usm" => $connections["fin_usm"],
        "cr3_re_usm" => $connections["cr3_re_usm"],
        // "user_management" => $connections["user_management"], // If user_management is a separate DB, include it here
    ];

    foreach ($database_check_order as $db_name_key => $conn) {
        if (!$conn) {
            error_log("Connection for $db_name_key is not established in connection.php.");
            continue; // Skip if connection failed
        }
        $stmt = mysqli_prepare($conn, "SELECT Email, Name, Password, Department_ID, User_ID, Role, Status FROM department_accounts WHERE User_ID = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $User_ID);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $userData = $row;
                $target_conn = $conn; // Store the connection where the user was found
                $authenticated = true; // User ID found in this DB
                break; // Found user, stop checking other databases
            }
        } else {
            error_log("Failed to prepare statement for $db_name_key: " . mysqli_error($conn));
        }
    }

    if ($authenticated && $userData) {
        // User found in one of the databases
        $Department_ID = $userData["Department_ID"];
        $Role = $userData["Role"];
        $Name = $userData["Name"];
        $Status = $userData["Status"]; // Get user status

        // Check if account is inactive
        if ($Status === 'Inactive') {
            logAttempt($logs2_usm, $User_ID, $Name, $Role, 'Failed', 'Login', 0, 'Account Inactive', '');
            $_SESSION["loginError"] = "Your account is inactive. Please contact support.";
            header("Location: login.php");
            exit();
        }

        // VERIFY PASSWORD (CRITICAL CHANGE: Use password_verify for hashed passwords)
        // If your database still stores plain text passwords, this will fail.
        // You MUST hash existing passwords and hash new ones upon creation/update.
        // For demonstration, if passwords are still plain text, you can use:
        // if ($password === $userData["Password"]) {
        // But strongly, strongly advise against this.

        // Assuming passwords are now hashed using password_hash() with PASSWORD_BCRYPT
        if (password_verify($password, $userData["Password"])) {
            // Check if 2FA is needed. For now, assume all users require 2FA for this system.
            // In a real system, you'd check a flag like IsTwoFactorEnabled from department_accounts.
            // For example: if ($userData['IsTwoFactorEnabled'] == 1) { ... }
            
            $otp = rand(100000, 999999);
            $_SESSION["otp"] = $otp;
            
            // Set session variables expected by 2fa_verify.php and the HR34 system
            $_SESSION["User_ID"] = $User_ID;
            $_SESSION["Role"] = $Role;
            $_SESSION["Department_ID"] = $Department_ID;
            $_SESSION["email"] = $userData["Email"]; // Store email for sending OTP
            $_SESSION["Name"] = $Name; // Store user name for logging

            $_SESSION["otp_attempts"] = 0;
            $_SESSION["auth_method"] = "2FA";

            if (sendOTP($userData["Email"], $otp)) {
                logAttempt($logs2_usm, $User_ID, $Name, $Role, 'Authenticating', 'Login', 0, 'Authenticating', '');
                logDepartmentAttempt($logs2_usm, $Department_ID, $User_ID, $Name, $Role, 'Authenticating', 'Login', 0, 'Authenticating', '');
                header("Location: 2fa_verify.php");
                exit();
            } else {
                logAttempt($logs2_usm, $User_ID, $Name, $Role, 'Failed', 'Login', 0, 'Failed to send OTP email', '');
                $_SESSION["loginError"] = "Failed to send OTP email.";
                header("Location: login.php");
                exit();
            }
        } else {
            // Incorrect password
            incrementLoginAttempts($User_ID);
            logAttempt($logs2_usm, $User_ID, $Name, $Role, 'Failed', 'Login', 0, 'Incorrect password', '');
            $_SESSION["loginError"] = "Incorrect password.";
            header("Location: login.php");
            exit();
        }
    } else {
        // User not found in any database
        incrementLoginAttempts($User_ID);
        logAttempt($logs2_usm, $User_ID, 'Unknown', 'Unknown', 'Failed', 'Login', 0, 'User not found', '');
        $_SESSION["loginError"] = "User ID not found.";
        header("Location: login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Cinzel&display=swap" rel="stylesheet">
	<script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
	<link rel="stylesheet" href="usm.css">
	<title>Avalon</title>
</head>
<body>
<div class="w-full h-dvh flex justify-center items-center bg-cover bg-center" style="background-image: url('background.png');">
		<div class="w-200 h-125 flex flex-row bg-clip-padding backdrop-filter bg-white border-accent rounded-xl backdrop-blur bg-opacity-10 backdrop-saturate-100 backdrop-contrast-100">
			<div class="p-3">
            <div class="text-white w-[350px] h-full flex flex-col items-center p-3 rounded-md bg-gray-300 bg-blend-multiply bg-cover bg-center shadow-[5px_5px_rgba(200,_180,_140,_0.4),_10px_10px_rgba(200,_180,_140,_0.3),_15px_15px_rgba(200,_180,_140,_0.2),_20px_20px_rgba(200,_180,_140,_0.1),_25px_25px_rgba(200,_180,_140,_0.05)]"
     style="background-image: url('left.png');">
					<div class="w-full flex flex-row flex-start">
						<img src="logo.svg" alt="" class="w-[50px]">
						<img src="logo-name.svg" alt="" class="w-[125px]">
					</div>
					<div class="relative h-full flex flex-col justify-end items-center">
						<h1 class="text-sm font">The Sanctuary of Serenity and Flavor</h1>
					</div>
				</div>
			</div>
			<div class="size-full flex flex-col gap-3 p-8 text-secondary  fill-secondary">
				<h1 class="text-2xl font-bold">Login to your Account</h1>
				<p class="text-sm mb-4 font-bold">Manage the art of excellence</p>
				<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="flex flex-col gap-6">
					<div class="flex flex-col gap-2">
						<div class="flex item-center gap-2 te">
							<box-icon name='user'></box-icon>
							<label for="User_ID">Employee ID</label>
						</div>
                            <input type="text" id="User_ID" name="User_ID" value="<?php echo $User_ID; ?>" 
                                   class="w-full border-b-2 border-gray-300 focus:outline-none focus:border-[#EDB886] transition duration-300 bg-transparent text-maroon-700" 
                                   required>
					</div>
					<div class="flex flex-col gap-2">
						<div class="flex item-center gap-2">
							<box-icon name='key' ></box-icon>
							<label for="Password">Password</label>
						</div>
                        <input type="password" id="Password" name="Password" value="<?php echo $password; ?>" 
                                   class="w-full border-b-2 border-gray-300 focus:outline-none focus:border-[#EDB886] transition duration-300 bg-transparent text-maroon-700" 
                                   required>
					</div>
					<div class="relative flex justify-center mt-8 font-bold">
                    <button type="submit" value="Login" class="w-35 bg-accent size-fit px-3 py-2 rounded-md hover:bg-secondary hover:text-white transition">Login</button>
					</div>
				</form>
				<div class=" flex justify-end items-end h-full">
					<a href="#" class="p-1 rounded hover:bg-secondary hover:text-white transition">Forgot Password?</a>
				</div>
			</div>
		</div>
        <div class="absolute left-5 bottom-5 text-white text-sm">Build By: BSIT - 3206 IM</div>
	</div>
    <?php if (isset($_SESSION["loginError"])): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Login Failed',
            text: '<?= $_SESSION["loginError"]; ?>',
            confirmButtonColor: '#3085d6'
        });
    </script>
    <?php unset($_SESSION["loginError"]); endif; ?>

</body>
</html>
