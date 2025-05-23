<?php
session_start();
include("../connection.php");

$usm_connection = $connections["user_management"];
$fin_usm_connection = $connections["fin_usm"];
$logs2_usm = $connections["logs2_usm"];
$cr1_usm = $connections["cr1_usm"];
$hr1_2_usm = $connections["hr_1&2_usm"];
$hr34_usm = $connections["hr34_usm"];


$User_ID = trim($_POST["User_ID"] ?? '');
$password = trim($_POST["Password"] ?? '');
$loginAttemptsKey = "login_attempts_$User_ID";
$Log_Date_Time = date('Y-m-d H:i:s');


// === Function: Log user login attempts ===
function logAttempt($usm_connection, $User_ID, $User_name, $Role, $Log_Status, $Attempt_Type, $Attempt_Count, $Failure_reason, $Cooldown_Until) {
    $Log_Date_Time = date('Y-m-d H:i:s');
    $sql = "
        INSERT INTO user_log_history 
        (User_ID, Name, Role, Log_Status, Attempt_Type, Attempt_Count, Failure_reason, Cooldown_Until, `Log_Date_Time`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($usm_connection, $sql);
    mysqli_stmt_bind_param($stmt, "sssssssss", 
        $User_ID, $User_name, $Role, $Log_Status, $Attempt_Type, 
        $Attempt_Count, $Failure_reason, $Cooldown_Until,$Log_Date_Time);
    mysqli_stmt_execute($stmt);
}

// === Function: Log department login attempts ===
function logDepartmentAttempt($conn, $Dept_log_ID, $Department_ID, $User_ID, $Name, $Role, $Log_Status, $Attempt_type, $Attempt_Count, $Failure_reason, $Cooldown_Until) {
    $Log_Date_Time = date('Y-m-d H:i:s');
    $sql = "
        INSERT INTO department_log_history 
        (Dept_log_ID, Department_ID, User_ID, Name, Role, Log_Status, Attempt_type, Attempt_count, Failure_reason, Cooldown_until, Log_Date_Time)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "issssssisss", 
        $Dept_log_ID, $Department_ID, $User_ID, $Role, $Name, $Log_Status, $Attempt_type, 
        $Attempt_Count, $Failure_reason, $Cooldown_Until, $Log_Date_Time);
    mysqli_stmt_execute($stmt);
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
if (isset($_SESSION[$loginAttemptsKey]) && $_SESSION[$loginAttemptsKey]['count'] >= 5) {
    $lastAttempt = $_SESSION[$loginAttemptsKey]['last'];
    $remaining = 3600 - (time() - $lastAttempt);
    if ($remaining > 0) {
        $minutes = ceil($remaining / 60);
        $cooldownUntil = date('Y-m-d H:i:s', $lastAttempt + 3600);
        logAttempt($fin_usm_connection, $User_ID, $User_ID, 'Unknown', 'Failed', '2FA', $_SESSION[$loginAttemptsKey]['count'], 'Account banned (cooldown)', $cooldownUntil);
        $_SESSION["loginError"] = "Your account is temporarily banned. Try again in $minutes minute(s).";
        header("Location: login.php");
        exit();
    } else {
        unset($_SESSION[$loginAttemptsKey]);
    }
}

// === Function: Send OTP via email ===
function sendOTP($email, $otp) {
    require '../PHPMailer/PHPMailerAutoload.php';
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'VehicleReservationManagement@gmail.com';
    $mail->Password = 'fzja ezgo ojdu fobc'; // 
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('AVALON@gmail.com', 'AVALON System Authenticator');
    $mail->addAddress($email);
    $mail->Subject = 'Your 2FA Verification Code';
    $mail->Body = "Your login verification code is: $otp";
    return $mail->send();
}

// === Main Login Logic ===
if ($_SERVER["REQUEST_METHOD"] === "POST" && $User_ID && $password) {

     // Check in logistic 2
     $stmt = mysqli_prepare($logs2_usm, "SELECT  Email, Name, Password, Department_ID, User_ID, Role FROM department_accounts WHERE User_ID = ?");
     mysqli_stmt_bind_param($stmt, "s", $User_ID);
     mysqli_stmt_execute($stmt);
     $result = mysqli_stmt_get_result($stmt);
 
     if ($result && mysqli_num_rows($result) > 0) {
         $row = mysqli_fetch_assoc($result);
         $Department_ID = $row["Department_ID"];
        $Role = $row["Role"];
        $Name = $row["Name"];


 
         if ($password === $row["Password"]) {
             $otp = rand(100000, 999999);
             $_SESSION["otp"] = $otp;
             error_log("Session OTP: " . ($_SESSION["otp"] ?? 'not set'));
             
             $_SESSION["User_ID"] = $User_ID;
             $_SESSION["Role"] = $Role;
             $_SESSION["Department_ID"] = $row["Department_ID"];
             $_SESSION["email"] = $row["Email"];
             $_SESSION["otp_attempts"] = 0;
             $_SESSION["auth_method"] = "2FA";
 
             if (sendOTP($row["Email"], $otp)) {
                logAttempt($logs2_usm, $User_ID, $Name, $Role, 'Authenticating', 'Login', 0, 'Authenticating', '');
                logDepartmentAttempt($logs2_usm, $User_ID, $Department_ID, $User_ID, $Name, $Role, 'Authenticating', 'Login', 0, 'Authenticating', '');
                header("Location: 2fa_verify.php");
                 exit();
             } else {
                logAttempt($logs2_usm, $User_ID, $Name, $Role, 'Failed', 'Login', 0, 'Failed to send OTP email', '');
                 $_SESSION["loginError"] = "Failed to send OTP email.";
                 header("Location: login.php");
                 exit();
             }
         } else {
             incrementLoginAttempts($User_ID);
             logAttempt($logs2_usm, $User_ID, $Name, $Role, 'Failed', 'Login', 0, 'Incorrect password', '');
             $_SESSION["loginError"] = "Incorrect password.";
             header("Location: login.php");
             exit();
         }
     }
        
     // Core 2
     $stmt = mysqli_prepare($hr1_2_usm, "SELECT  Email, Name, Password, Department_ID, User_ID, Role FROM department_accounts WHERE User_ID = ?");
     mysqli_stmt_bind_param($stmt, "s", $User_ID);
     mysqli_stmt_execute($stmt);
     $result = mysqli_stmt_get_result($stmt);
 
     if ($result && mysqli_num_rows($result) > 0) {
         $row = mysqli_fetch_assoc($result);
         $Department_ID = $row["Department_ID"];
        $Role = $row["Role"];
        $Name = $row["Name"];


 
         if ($password === $row["Password"]) {
             $otp = rand(100000, 999999);
             $_SESSION["otp"] = $otp;
             error_log("Session OTP: " . ($_SESSION["otp"] ?? 'not set'));
             
             $_SESSION["User_ID"] = $User_ID;
             $_SESSION["Role"] = $Role;
             $_SESSION["Department_ID"] = $row["Department_ID"];
             $_SESSION["email"] = $row["Email"];
             $_SESSION["otp_attempts"] = 0;
             $_SESSION["auth_method"] = "2FA";
 
             if (sendOTP($row["Email"], $otp)) {
                logAttempt($hr1_2_usm, $User_ID, $Name, $Role, 'Authenticating', 'Login', 0, 'Authenticating', '');
                logDepartmentAttempt($hr1_2_usm, $User_ID, $Department_ID, $User_ID, $Name, $Role, 'Authenticating', 'Login', 0, 'Authenticating', '');
                header("Location: 2fa_verify.php");
                 exit();
             } else {
                logAttempt($hr1_2_usm, $User_ID, $Name, $Role, 'Failed', 'Login', 0, 'Failed to send OTP email', '');
                 $_SESSION["loginError"] = "Failed to send OTP email.";
                 header("Location: login.php");
                 exit();
             }
         } else {
             incrementLoginAttempts($User_ID);
             logAttempt($hr1_2_usm, $User_ID, $Name, $Role, 'Failed', 'Login', 0, 'Incorrect password', '');
             $_SESSION["loginError"] = "Incorrect password.";
             header("Location: login.php");
             exit();
         }
     }

      // Core 2
      $stmt = mysqli_prepare($cr1_usm, "SELECT  Email, Name, Password, Department_ID, User_ID, Role FROM department_accounts WHERE User_ID = ?");
      mysqli_stmt_bind_param($stmt, "s", $User_ID);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
  
      if ($result && mysqli_num_rows($result) > 0) {
          $row = mysqli_fetch_assoc($result);
          $Department_ID = $row["Department_ID"];
         $Role = $row["Role"];
         $Name = $row["Name"];
 
 
  
          if ($password === $row["Password"]) {
              $otp = rand(100000, 999999);
              $_SESSION["otp"] = $otp;
              error_log("Session OTP: " . ($_SESSION["otp"] ?? 'not set'));
              
              $_SESSION["User_ID"] = $User_ID;
              $_SESSION["Role"] = $Role;
              $_SESSION["Department_ID"] = $row["Department_ID"];
              $_SESSION["email"] = $row["Email"];
              $_SESSION["otp_attempts"] = 0;
              $_SESSION["auth_method"] = "2FA";
  
              if (sendOTP($row["Email"], $otp)) {
                 logAttempt($cr1_usm, $User_ID, $Name, $Role, 'Authenticating', 'Login', 0, 'Authenticating', '');
                 logDepartmentAttempt($cr1_usm, $User_ID, $Department_ID, $User_ID, $Name, $Role, 'Authenticating', 'Login', 0, 'Authenticating', '');
                 header("Location: 2fa_verify.php");
                  exit();
              } else {
                 logAttempt($cr1_usm, $User_ID, $Name, $Role, 'Failed', 'Login', 0, 'Failed to send OTP email', '');
                  $_SESSION["loginError"] = "Failed to send OTP email.";
                  header("Location: login.php");
                  exit();
              }
          } else {
              incrementLoginAttempts($User_ID);
              logAttempt($cr1_usm, $User_ID, $Name, $Role, 'Failed', 'Login', 0, 'Incorrect password', '');
              $_SESSION["loginError"] = "Incorrect password.";
              header("Location: login.php");
              exit();
          }
      }

    // Check in Financial USM
    $stmt = mysqli_prepare($fin_usm_connection, "SELECT Email, Name, Password, Department_ID, User_ID, Role FROM department_accounts WHERE User_ID = ?");
    mysqli_stmt_bind_param($stmt, "s", $User_ID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $Department_ID = $row["Department_ID"];
        $Role = $row["Role"];
        $Name = $row["Name"];



        if ($password === $row["Password"]) {
            $otp = rand(100000, 999999);
            $_SESSION["otp"] = $otp;
            $_SESSION["User_ID"] = $User_ID;
            $_SESSION["Role"] = $Role;
            $_SESSION["Department_ID"] = $row["Department_ID"];
            $_SESSION["email"] = $row["Email"];
            $_SESSION["otp_attempts"] = 0;
            $_SESSION["auth_method"] = "2FA";

            if (sendOTP($row["Email"], $otp)) {
                logAttempt($fin_usm_connection, $User_ID, $Name, $Role, 'Authenticating', 'Login', 0, 'Authenticating', '');
                logDepartmentAttempt($fin_usm_connection, $User_ID, $Department_ID, $User_ID, $Name, $Role, 'Success', 'Login', 0, 'Login Successful', '');
                header("Location: 2fa_verify.php");
                exit();
            } else {
                logAttempt($fin_usm_connection, $User_ID, $Name, $Role, 'Failed', 'Login', 0, 'Failed to send OTP email', '');
                $_SESSION["loginError"] = "Failed to send OTP email.";
                header("Location: login.php");
                exit();
            }
        } else {
            incrementLoginAttempts($User_ID);
            logAttempt($fin_usm_connection, $User_ID, $Name, $Role, 'Failed', 'Login', 0, 'Incorrect password', '');
            $_SESSION["loginError"] = "Incorrect password.";
            header("Location: login.php");
            exit();
        }
    }

    // Check in Department USM
    $stmt = mysqli_prepare($usm_connection, "SELECT Email, Name, Password, account_type, Department_ID, Dept_Accounts_ID, Role FROM department_accounts WHERE User_ID = ?");
    mysqli_stmt_bind_param($stmt, "s", $User_ID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $Department_ID = $row["Department_ID"];
        $Role = $row["Role"];
        $Name = $row["Name"];



        if ($password === $row["Password"]) {
            $_SESSION["User_ID"] = $User_ID;
            $_SESSION["Role"] = $role;
            $_SESSION["Department_ID"] = $row["Department_ID"];
            $_SESSION["Dept_Account_ID"] = $row["Dept_Accounts_ID"];
            $_SESSION["email"] = $row["Email"];
            header("Location: dashboard.php");
            exit();
        } else {
            incrementLoginAttempts($User_ID);
            logAttempt($usm_connection, $User_ID, $Name, $Role, 'Success', 'Login', 0, 'Login Successful', '');
            $_SESSION["loginError"] = "Incorrect password.";
            header("Location: login.php");
            exit();
        }
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