<?php
session_start();
require_once __DIR__ . "/includes/Database.php";

$db = new Database();
$conn = $db->connect("usm");

function record_login_attempt($conn, $departmentId, $userId, $name, $role, $status, $failureReason = null, $attemptType = 'Login') {
    $stmt_log_hist = $conn->prepare("INSERT INTO department_log_history (Department_ID, User_ID, Name, Role, Log_Status, Log_Date_Time, Failure_reason, Attempt_type) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)");
    if (!$stmt_log_hist) {
        error_log("Prepare failed for department_log_history: " . $conn->error);
        return;
    }
    $stmt_log_hist->bind_param("sssssss", $departmentId, $userId, $name, $role, $status, $failureReason, $attemptType);
    $stmt_log_hist->execute();
    $stmt_log_hist->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = trim($_POST['identifier']);
    $password = $_POST['password'];

    if (empty($identifier) || empty($password)) {
        header("Location: login.php?error=User ID/Email and Password are required.");
        exit();
    }

    $sql = "SELECT Dept_Accounts_ID, Department_ID, User_ID, Name, Password, Role, Status, Email FROM department_accounts WHERE (User_ID = ? OR Email = ?) AND Status = 'Active'";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        header("Location: login.php?error=Database error. Please contact support.");
        exit();
    }

    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // !!! SECURITY WARNING: PLAIN TEXT PASSWORD CHECK !!!
        // You MUST replace this with password_verify($password, $user['Password'])
        // after you start storing hashed passwords.
        if ($password === $user['Password']) {
        // if (password_verify($password, $user['Password'])) { // Use this line if passwords are hashed

            $_SESSION['user_id'] = $user['User_ID'];
            $_SESSION['user_name'] = $user['Name'];
            $_SESSION['user_role'] = $user['Role'];
            $_SESSION['dept_accounts_id'] = $user['Dept_Accounts_ID'];
            $_SESSION['department_id'] = $user['Department_ID'];

            record_login_attempt($conn, $user['Department_ID'], $user['User_ID'], $user['Name'], $user['Role'], 'Success');
            header("Location: Dashboard.php");
            exit();
        } else {
            record_login_attempt($conn, $user['Department_ID'] ?? null, $identifier, ($user['Name'] ?? 'Unknown Identifier'), ($user['Role'] ?? 'Unknown'), 'Failed', 'Invalid password');
            header("Location: login.php?error=Invalid credentials provided.");
            exit();
        }
    } else {
        record_login_attempt($conn, null, $identifier, 'Unknown Identifier', 'Unknown', 'Failed', 'User not found or inactive');
        header("Location: login.php?error=Invalid credentials provided.");
        exit();
    }

    $stmt->close();
} else {
    header("Location: login.php");
    exit();
}

$conn->close();
?>