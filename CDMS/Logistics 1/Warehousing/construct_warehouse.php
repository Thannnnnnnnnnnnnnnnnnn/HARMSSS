<?php
session_start();
include("../../connection.php");



$db_name = "logs1_warehousing"; // Warehousing database
$log2_dt = "logs2_document_tracking";
$usm_db = "logs1_usm"; // User database

if (!isset($connections[$db_name]) || !isset($connections[$usm_db])) {
    die("Database connection not found.");
}

$conn = $connections[$db_name];
$usm_conn = $connections[$usm_db];
$log2_conn = $connections[$log2_dt];


ini_set('display_errors', 1);
error_reporting(E_ALL);

// Make sure user is logged in
if (!isset($_SESSION['User_ID'])) {
    die("Access denied: user not logged in.");
}

$user_id = $_SESSION['User_ID'];

// Get user info from logs1_usm
$usm_conn = $connections['logs1_usm'];

$userQuery = $usm_conn->prepare("SELECT Name, Role FROM department_accounts WHERE User_ID = ?");
$userQuery->bind_param("i", $user_id);
$userQuery->execute();
$userResult = $userQuery->get_result();

if ($userResult->num_rows === 0) {
    die("User not found.");
}

$userData = $userResult->fetch_assoc();
$sent_by = $userData['Name'];
$sender_role = $userData['Role'];
$userQuery->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        empty($_POST['warehouse_name']) ||
        empty($_POST['warehouse_location']) ||
        empty($_POST['warehouse_for'])
    ) {
        die("All fields are required.");
    }

    $name = trim($_POST['warehouse_name']);
    $location = trim($_POST['warehouse_location']);
    $purpose = trim($_POST['warehouse_for']);
    $status = "For construction permit approval";
    $created_at = date("Y-m-d H:i:s");

    // Insert warehouse into logs1_warehousing
    $conn = $connections['logs1_warehousing'];

    $stmt = $conn->prepare("
        INSERT INTO warehouses (warehouse_name, warehouse_location, warehouse_for, warehouse_status, User_ID, submitted_by)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssssis", $name, $location, $purpose, $status, $user_id, $created_at);

    if ($stmt->execute()) {
        $stmt->close();

        // Insert notification into logs1_usm
        $notification_title = "New Warehouse Construct Request";
        $notification_message = "<strong>{$sent_by}</strong> submitted a new warehouse for construction: <strong>\"$name\"</strong>.";
        $notification_status = "Unread";
        $date_sent = date("Y-m-d H:i:s");
        $module = "Warehousing";

        $notifStmt = $usm_conn->prepare("
            INSERT INTO notification_wr (title, message, status, date_sent, sent_by, User_ID, recipient_role, module)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $notifStmt->bind_param("ssssssss", $notification_title, $notification_message, $notification_status, $date_sent, $sent_by, $user_id, $sender_role, $module);

        if (!$notifStmt->execute()) {
            die("Notification insert failed: " . $notifStmt->error);
        }

        $notifStmt->close();

        echo "<script>
            alert('New warehouse submitted for construction approval.');
            window.location.href = 'warehouses.php'; // Change this to your actual page
        </script>";
    } else {
        die("Insert failed: " . $stmt->error);
    }
} else {
    die("Invalid request method.");
}
?>
