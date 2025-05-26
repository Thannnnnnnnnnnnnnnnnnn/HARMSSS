<?php
session_start();
include '../../connection.php'; // or adjust path as needed

// Define the database name
$db_name = "logs1_asset";
$db_logs1_usm = "logs1_usm";

if (!isset($connections[$db_name])) {
    die("Database connection not found for $db_name");
}
$conn = $connections[$db_name];
$logs1_usm_conn = $connections[$db_logs1_usm];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['asset_name'];
    $type = $_POST['asset_type'];
    $quantity = $_POST['asset_quantity'];
    $date_added = $_POST['date_added'];
    $asset_status = "Pending for permit approval"; // 🔧 New status value

    // Fetch Name and Role from logs1_usm.department_accounts
    $user_id = $_SESSION['User_ID'];
    $account_query = $logs1_usm_conn->prepare("SELECT Name, Role FROM department_accounts WHERE User_ID = ?");
    $account_query->bind_param("s", $user_id);
    $account_query->execute();
    $account_result = $account_query->get_result();

    if ($account_result->num_rows > 0) {
        $user_info = $account_result->fetch_assoc();
        $sent_by = $user_info['Name'];
        $recipient_role = $user_info['Role'];
    } else {
        die("User info not found in department_accounts.");
    }
    $account_query->close();

    // Insert new asset
    $stmt = $conn->prepare("INSERT INTO assets (asset_name, asset_type, asset_quantity, date_created, asset_status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $name, $type, $quantity, $date_added, $asset_status);

    if ($stmt->execute()) {
        // Insert Notification
        $notification_title = "New Asset Added";
        $notification_message = "<strong>{$sent_by}</strong> added a new asset: <strong>\"$name\"</strong>.";
        $notification_status = "Unread";
        $date_sent = date("Y-m-d H:i:s");
        $module = "Asset Management";

        $notifStmt = $logs1_usm_conn->prepare("INSERT INTO notification_at (title, message, status, date_sent, sent_by, User_ID, recipient_role, module) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$notifStmt) {
            die("Notification prepare failed: " . $logs1_usm_conn->error);
        }

        $notifStmt->bind_param("ssssssss", $notification_title, $notification_message, $notification_status, $date_sent, $sent_by, $user_id, $recipient_role, $module);

        if (!$notifStmt->execute()) {
            die("Notification execute failed: " . $notifStmt->error);
        }
        $notifStmt->close();

        header("Location: add_asset.php?success=1");
        exit();
    } else {
        header("Location: add_asset.php?error=1");
        exit();
    }
}
?>
