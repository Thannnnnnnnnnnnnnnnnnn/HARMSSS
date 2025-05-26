<?php
session_start();
include("../../connection.php");

$conn = $connections["logs1_asset"];
$usm_conn = $connections["logs1_usm"]; // Connection for notifications

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asset_id = $_POST['asset_id'] ?? '';
    $action = $_POST['action'] ?? '';

    if (!$asset_id || !$action) {
        echo json_encode(['success' => false, 'message' => 'Missing parameters.']);
        exit;
    }

    $approval = ($action === 'approve') ? "Asset added permitted" : "Asset denied";
    $status = ($action === 'approve') ? "Asset successfully added" : null;

    // Get asset details for notification
    $check = $conn->prepare("SELECT asset_id, asset_name, submitted_by, User_ID FROM assets WHERE asset_id = ?");
    $check->bind_param("i", $asset_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Asset not found.']);
        exit;
    }

    $asset = $result->fetch_assoc();
    $name = $asset['asset_name'];
    $sent_by = $asset['submitted_by'];
    $user_id = $asset['User_ID'];

    // Update approval and status
    if ($status) {
        $stmt = $conn->prepare("UPDATE assets SET Asset successfully added = ?, status = ? WHERE asset_id = ?");
        $stmt->bind_param("ssi", $approval, $status, $asset_id);
    } else {
        $stmt = $conn->prepare("UPDATE assets SET approval = ? WHERE asset_id = ?");
        $stmt->bind_param("si", $approval, $asset_id);
    }

    if ($stmt->execute()) {
        // Only send notification if approved
        if ($action === 'approve') {
            $notification_title = "New Asset Approved";
            $notification_message = "<strong>{$sent_by}</strong>'s asset <strong>\"$name\"</strong> has been approved.";
            $notification_status = "Unread";
            $date_sent = date("Y-m-d H:i:s");
            $module = "Asset Management";
            $recipient_role = "Asset Requester"; // optional — adjust this if you have roles

            $notifStmt = $usm_conn->prepare("
                INSERT INTO notification_at (title, message, status, date_sent, sent_by, User_ID, recipient_role, module)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $notifStmt->bind_param("ssssssss", $notification_title, $notification_message, $notification_status, $date_sent, $sent_by, $user_id, $recipient_role, $module);

            if (!$notifStmt->execute()) {
                echo json_encode(['success' => false, 'message' => 'Notification insert failed.']);
                exit;
            }

            $notifStmt->close();
        }

        echo json_encode(['success' => true, 'message' => "Asset $approval successfully."]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
    }

    $stmt->close();
}
?>
