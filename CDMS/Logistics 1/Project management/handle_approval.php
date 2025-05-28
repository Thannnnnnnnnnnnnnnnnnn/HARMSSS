<?php
session_start();
include("../../connection.php");

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['project_id'], $_POST['action'])) {
        die("Invalid request");
    }

    $project_id = $_POST['project_id'];
    $action = $_POST['action'];
    $user_id = $_SESSION['User_ID'];

    // DB connection alias
    $conn = $connections['logs1_project_management'];
    $conn_usm_pm = $connections['logs1_usm'];

    $fin_conn = $connections['fin_disbursement'];

    // Get user info for notification
    $userStmt = $conn_usm_pm->prepare("SELECT Name, Role FROM department_accounts WHERE User_ID = ?");
    $userStmt->bind_param("i", $user_id);
    $userStmt->execute();
    $userStmt->bind_result($name, $Role);
    $userStmt->fetch();
    $userStmt->close();

    // Get project info
    $projStmt = $conn->prepare("SELECT project_est_budget, project_name FROM project WHERE project_id = ?");
    $projStmt->bind_param("i", $project_id);
    $projStmt->execute();
    $projStmt->bind_result($project_est_budget, $item_name); // Assuming project_title is the item name
    if (!$projStmt->fetch()) {
        die("Project not found.");
    }
    $projStmt->close();

    if ($action === 'approve') {
        $new_status = "Funds allocation requested";
        $pending_status = "Pending for funds allocation";
        $date_now = date("Y-m-d H:i:s");

        // 1. Update project status
        $updateStmt = $conn->prepare("UPDATE project SET project_status = ? WHERE project_id = ?");
        $updateStmt->bind_param("si", $new_status, $project_id);
        if (!$updateStmt->execute()) {
            die("Failed to update project status: " . $updateStmt->error);
        }
        $updateStmt->close();

        // 2. Insert into disbursementrequests
        $disbStmt = $fin_conn->prepare("
            INSERT INTO disbursementrequests (project_id, Amount, Status, DateOfRequest, EmployeeID)
            VALUES (?, ?, ?, ?, ?)
        ");
        $disbStmt->bind_param("idssi", $project_id, $project_est_budget, $pending_status, $date_now, $user_id);
        if (!$disbStmt->execute()) {
            die("Failed to insert disbursement request: " . $disbStmt->error);
        }
        $request_id = $fin_conn->insert_id;
        $disbStmt->close();

        // 3. Insert into approvals
        $allocResult = $fin_conn->query("SELECT AllocationID FROM fin_budget_management.budgetallocations ORDER BY RAND() LIMIT 1");
        if (!$allocResult || $allocResult->num_rows === 0) {
            die("No AllocationID found.");
        }
        $allocation_id = $allocResult->fetch_assoc()['AllocationID'];

        $title = "Project Fund Allocation";
        $purpose = "Approval of fund allocation for project";
        $funding_id = NULL;

        $approvalStmt = $fin_conn->prepare("
            INSERT INTO approvals 
            (Amount, Status, DateOfApproval, title, purpose, funding_id, User_ID, AllocationID, RequestID, ApproverID, project_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $approvalStmt->bind_param(
            "dsssssssisi",
            $project_est_budget,
            $pending_status,
            $date_now,
            $title,
            $purpose,
            $funding_id,
            $user_id,
            $allocation_id,
            $request_id,
            $user_id,
            $project_id
        );
        if (!$approvalStmt->execute()) {
            die("Failed to insert approval: " . $approvalStmt->error);
        }
        $approvalStmt->close();


        // ✅ 5. Create notification
        $notification_title = "Funds requisition was requested";
        $notification_message = "<strong>$name</strong> has submitted a fund requisition request for: <strong>\"$item_name\"</strong>.";
        $notification_status = "Unread";
        $recipient_role = $Role;
        $module = "Project Management";

        $notifStmt = $conn->prepare("
            INSERT INTO notification_pm 
            (title, message, status, date_sent, sent_by, User_ID, recipient_role, module) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $notifStmt->bind_param("ssssssss", $notification_title, $notification_message, $notification_status, $date_now, $name, $user_id, $recipient_role, $module);

        if (!$notifStmt->execute()) {
            error_log("Notification Insert Error: " . $notifStmt->error);
            die("Notification insertion failed: " . $notifStmt->error);
        }
        $notifStmt->close();

        echo "<script>
            alert('Project approved and fund allocation requested successfully.');
            window.location.href = 'project.php'; 
        </script>";
    } else {
        // Deny project
        $new_status = "Denied";

        $updateStmt = $conn->prepare("UPDATE project SET project_status = ? WHERE project_id = ?");
        $updateStmt->bind_param("si", $new_status, $project_id);
        if (!$updateStmt->execute()) {
            die("Failed to update project status: " . $updateStmt->error);
        }
        $updateStmt->close();

        echo "<script>
            alert('Project denied.');
            window.location.href = 'project.php'; 
        </script>";
    }
} else {
    die("Invalid request method.");
}
?>
