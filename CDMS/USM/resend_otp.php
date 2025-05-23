<?php
session_start();
include("../connection.php");

// Ensure both email and user ID are set in the session
if (!isset($_SESSION['Email']) || !isset($_SESSION['User_ID'])) {
    // Redirect if email or user ID is not set
    header("Location: 2fa_verify.php");

    echo "<script>alert('Failed to send new OTP.');</script>";
    exit();
}

$usm_connection = $connections["user_management"];
$fin_usm_connection = $connections["fin_usm"];
$logs2_usm = $connections["logs2_usm"];

$User_ID = $_SESSION["User_ID"];
$Log_Date_Time = date('Y-m-d H:i:s');

// Function to generate OTP
function generateOTP() {
    return rand(100000, 999999); // Generate a 6-digit OTP
}

// Function to send OTP
function sendOTP($email, $otp) {
    require '../PHPMailer/PHPMailerAutoload.php';
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'VehicleReservationManagement@gmail.com';
    $mail->Password = getenv('SMTP_PASSWORD'); // Use environment variable for security
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('AVALON@gmail.com', 'AVALON System Authenticator');
    $mail->addAddress($email);
    $mail->Subject = 'Your New 2FA Verification Code';
    $mail->Body = "Your New login verification code is: $otp";
    
    if ($mail->send()) {
        return true;
    } else {
        return false;
    }
}

// Function to log OTP attempts
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

// OTP Resend Handling
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Generate a new OTP
    $newOtp = generateOTP();
    $_SESSION["otp"] = $newOtp;

    // Send OTP
    $email = $_SESSION["email"];  // Email is retrieved from session or DB
    if (sendOTP($email, $newOtp)) {
        // Log OTP resend action
        $Role = $_SESSION["Role"];
        $Department_ID = $_SESSION["Department_ID"];
        $Name = $_SESSION["Name"];
        logAttempt($logs2_usm, $User_ID, $Name, $Role, 'Success', 'OTP Resend', 0, 'OTP Resent', '');

        // Feedback to the user
        $_SESSION["loginError"] = "A new OTP has been sent to your email.";
        header("Location: 2fa_verify.php");
        exit();
    } else {
        // Handle OTP send failure (e.g., log failure or notify user)
        $_SESSION["loginError"] = "Failed to send OTP. Please try again.";
        header("Location: 2fa_verify.php");
        exit();
    }
}
?>
