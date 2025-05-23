<?php
session_start();
require_once __DIR__ . "/includes/Database.php";
$db = new Database();
$conn = $db->connect("usm");

if (isset($_SESSION['user_id'])) {
    $departmentId = $_SESSION['department_id'] ?? null;
    $userId = $_SESSION['user_id'];
    $name = $_SESSION['user_name'];
    $role = $_SESSION['user_role'];
    $log_status = "Logout";
    $attempt_type = "Logout";

    $stmt_log_hist = $conn->prepare("INSERT INTO department_log_history (Department_ID, User_ID, Name, Role, Log_Status, Log_Date_Time, Attempt_type) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
    if ($stmt_log_hist) {
        $stmt_log_hist->bind_param("ssssss", $departmentId, $userId, $name, $role, $log_status, $attempt_type);
        $stmt_log_hist->execute();
        $stmt_log_hist->close();
    } else {
        error_log("Prepare failed for logout log: " . $conn->error);
    }
}
$conn->close();

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

header("Location: ../../USM/login.php");
exit();
?>