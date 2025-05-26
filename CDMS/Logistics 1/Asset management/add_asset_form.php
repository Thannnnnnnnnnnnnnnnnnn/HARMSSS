<?php
session_start();
include '../../connection.php'; // adjust path as needed

// Define the database names
$db_name = "logs1_asset";
$db_logs1_usm = "logs1_usm";
$db_logs2_doc = "logs2_document_tracking";

if (!isset($connections[$db_name]) || !isset($connections[$db_logs1_usm]) || !isset($connections[$db_logs2_doc])) {
    die("One or more database connections are missing.");
}

$conn = $connections[$db_name];
$logs1_usm_conn = $connections[$db_logs1_usm];
$logs2_doc_conn = $connections[$db_logs2_doc];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['asset_name'];
    $type = $_POST['asset_type'];
    $quantity = $_POST['asset_quantity'];
    $date_added = $_POST['date_created'];
    $asset_status = "Pending for permit approval";

    // Get current user info
    $user_id = $_SESSION['User_ID'];
    $account_query = $logs1_usm_conn->prepare("SELECT Name, Role FROM department_accounts WHERE User_ID = ?");
    $account_query->bind_param("s", $user_id);
    $account_query->execute();
    $account_result = $account_query->get_result();

    if ($account_result->num_rows > 0) {
        $user_info = $account_result->fetch_assoc();
        $sent_by = $user_info['Name'];
        $sender_role = $user_info['Role'];
    } else {
        die("User info not found in department_accounts.");
    }
    $account_query->close();

    // Insert asset into logs1_asset
    $stmt = $conn->prepare("
        INSERT INTO assets (asset_name, asset_type, asset_quantity, date_created, asset_status, User_ID, submitted_by)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssissss", $name, $type, $quantity, $date_added, $asset_status, $user_id, $sent_by);

    if ($stmt->execute()) {
        $asset_id = $stmt->insert_id;
        $stmt->close();

        // Insert notification into logs1_usm
        $notification_title = "New Asset Added";
        $notification_message = "<strong>{$sent_by}</strong> added a new asset: <strong>\"$name\"</strong>.";
        $notification_status = "Unread";
        $date_sent = date("Y-m-d H:i:s");
        $module = "Asset Management";

        $notifStmt = $conn->prepare("
            INSERT INTO notification_at (title, message, status, date_sent, sent_by, User_ID, recipient_role, module)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $notifStmt->bind_param("ssssssss", $notification_title, $notification_message, $notification_status, $date_sent, $sent_by, $user_id, $sender_role, $module);

        if (!$notifStmt->execute()) {
            die("Notification insert failed: " . $notifStmt->error);
        }
        $notifStmt->close();

        $type_1 = "For added asset approval";
        $permit_status = "For permit approval"; 
        $insert_permit = $logs2_doc_conn->prepare("
            INSERT INTO permits_approval (asset_id, item_name, type_of_item, purpose, status, submitted_by, requested_date, User_ID)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $insert_permit->bind_param("issssssi", $asset_id, $name, $type, $type_1, $permit_status, $sent_by, $date_sent, $user_id);

        if (!$insert_permit->execute()) {
            die("Permit approval insert failed: " . $insert_permit->error);
        }
        $insert_permit->close();

        header("Location: add_asset.php?success=1");
        exit();
    } else {
        header("Location: add_asset.php?error=1");
        exit();
    }
}
?>
