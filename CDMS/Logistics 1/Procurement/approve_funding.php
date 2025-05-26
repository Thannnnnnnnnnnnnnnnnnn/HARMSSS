<?php
session_start();
include("../../connection.php");

// ✅ Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ✅ Define DB aliases
$db_name = "logs1_procurement";
$fin_ds = "fin_disbursement";
$log2_dt = "logs2_document_tracking";
$usm_db = "logs1_usm";

// ✅ Validate DB connections
if (!isset($connections[$db_name]) || !isset($connections[$usm_db]) || !isset($connections[$fin_ds])) {
    die("One or more database connections are missing.");
}

$conn = $connections[$db_name];
$usm_conn = $connections[$usm_db];
$fin_conn = $connections[$fin_ds];
$log2_conn = $connections[$log2_dt]; // Optional

// ✅ Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['funding_id'])) {
    $funding_id = $_POST['funding_id'];

    // 🔍 Fetch funding details
    $stmt = $conn->prepare("SELECT User_ID, item_name, purpose, estimated_budget, requested_date FROM for_funding WHERE funding_id = ?");
    if (!$stmt) {
        die("Funding prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $funding_id);
    $stmt->execute();
    $stmt->bind_result($user_id, $item_name, $purpose, $estimated_budget, $requested_date);
    $stmt->fetch();
    $stmt->close();

    if (!$user_id || !$item_name) {
        die("Invalid or incomplete funding record.");
    }

    // ✅ Update status in for_funding
    $updateStmt = $conn->prepare("UPDATE for_funding SET status = 'Funds successfully requested' WHERE funding_id = ?");
    $updateStmt->bind_param("s", $funding_id);
    $updateStmt->execute();
    $updateStmt->close();

    // ✅ Clean and prepare amount
    $estimated_budget = floatval(str_replace(',', '', $estimated_budget));

    // ✅ Insert into disbursementrequests table
    $disbStmt = $fin_conn->prepare("
        INSERT INTO disbursementrequests (funding_id, Amount, DateOfRequest) 
        VALUES (?, ?, ?)
    ");

    if (!$disbStmt) {
        die("Disbursement prepare failed: " . $fin_conn->error);
    }

    $disbStmt->bind_param("sds", $funding_id, $estimated_budget, $requested_date);

    if (!$disbStmt->execute()) {
        error_log("Insert Error (disbursementrequests): " . $disbStmt->error);
        die("Disbursement insertion failed: " . $disbStmt->error);
    }

    $request_id = $fin_conn->insert_id;
    $disbStmt->close();

    // ✅ Get AllocationID (FK from budgetallocations table)
    $allocQuery = $fin_conn->query("SELECT AllocationID FROM fin_budget_management.budgetallocations ORDER BY RAND() LIMIT 1");

    if ($allocQuery && $allocQuery->num_rows > 0) {
        $row = $allocQuery->fetch_assoc();
        $allocation_id = $row['AllocationID'];
    } else {
        die("No valid AllocationID found in budgetallocations table.");
    }

    // ✅ Insert into approvals table
    $status = 'Pending';
    $approver_id = $_SESSION['User_ID'];

    $approvalStmt = $fin_conn->prepare("
        INSERT INTO approvals 
        (Amount, Status, DateOfApproval, title, purpose, funding_id, User_ID, AllocationID, RequestID, ApproverID)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$approvalStmt) {
        die("Approval prepare failed: " . $fin_conn->error);
    }

    $approvalStmt->bind_param(
        "dsssssssss",
        $estimated_budget,
        $status,
        $requested_date,
        $item_name,
        $purpose,
        $funding_id,
        $user_id,
        $allocation_id,
        $request_id,
        $approver_id
    );

    if (!$approvalStmt->execute()) {
        error_log("MySQL Insert Error (approvals): " . $approvalStmt->error);
        die("Approval insertion failed: " . $approvalStmt->error);
    }

    $approvalStmt->close();

    // ✅ Fetch user's name and role from department_accounts (logs1_usm)
    $userDetailsStmt = $usm_conn->prepare("SELECT Name, Role FROM department_accounts WHERE User_ID = ?");
    $userDetailsStmt->bind_param("s", $user_id);
    $userDetailsStmt->execute();
    $userDetailsStmt->bind_result($name, $Role);
    $userDetailsStmt->fetch();
    $userDetailsStmt->close();

    if (!$name || !$Role) {
        die("User details not found in department_accounts.");
    }

    // ✅ Create notification
    $notification_title = "Funds requisition was requested";
    $notification_message = "<strong>$name</strong> has submitted a fund requisition request for: <strong>\"$item_name\"</strong>.";
    $notification_status = "Unread";
    $date_sent = date("Y-m-d H:i:s");
    $recipient_role = $Role;
    $module = "Procurement";

    $notifStmt = $conn->prepare("
        INSERT INTO notification_pr 
        (title, message, status, date_sent, sent_by, User_ID, recipient_role, module) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$notifStmt) {
        die("Notification prepare failed: " . $conn->error);
    }

    $notifStmt->bind_param("ssssssss", $notification_title, $notification_message, $notification_status, $date_sent, $name, $user_id, $recipient_role, $module);

    if (!$notifStmt->execute()) {
        error_log("Notification Insert Error: " . $notifStmt->error);
        die("Notification insertion failed: " . $notifStmt->error);
    }

    $notifStmt->close();

    // ✅ Done
    echo "<script>
        alert('Funding approved, disbursement logged, and notification sent successfully.');
        window.location.href = 'For_funding.php';
    </script>";
}
?>
