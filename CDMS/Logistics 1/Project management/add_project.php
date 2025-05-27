<?php
session_start();

include("../../connection.php"); // Ensure this provides $connections[]

$db_name = "logs1_project_management";
$log2_dt = "logs2_document_tracking";
$usm_db = "logs1_usm";

if (!isset($connections[$db_name]) || !isset($connections[$usm_db]) || !isset($connections[$log2_dt])) {
    die("Database connection not found.");
}

$conn = $connections[$db_name];
$usm_conn = $connections[$usm_db];
$log2_conn = $connections[$log2_dt];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['User_ID'];

    // ✅ Fetch name and role from department_accounts
    $stmt = $usm_conn->prepare("SELECT Name, Role FROM department_accounts WHERE User_ID = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($submitted_by, $user_role);
    if (!$stmt->fetch()) {
        die("User not found in department_accounts.");
    }
    $stmt->close();

    $name = $_POST['project_name'] ?? '';
    $desc = $_POST['project_desc'] ?? '';
    $status = "For project permit approval";
    $budget = $_POST['estimated_budget'] ?? 0;
    $construction_date = $_POST['construction_date'] ?? null;
    $completed_date = $_POST['completed_date'] ?? null;

    $conn->begin_transaction();

    try {
        // 1. Insert into project table
        $stmt1 = $conn->prepare("INSERT INTO project 
            (User_ID, project_name, project_desc, project_status, project_date_end, project_est_budget, project_date, submitted_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt1->bind_param("isssssss", $user_id, $name, $desc, $status, $completed_date, $budget, $construction_date, $submitted_by);
        $stmt1->execute();

        $project_id = $stmt1->insert_id; // ✅ Capture the inserted project ID
        $stmt1->close();

        // 2. Insert into permits_approval table (with project_id)
        $purpose = "New Project";
        $type_of_item = "Project";
        $requested_date = date("Y-m-d H:i:s");
        $permit_status = "For project permit approval";

        $stmt2 = $log2_conn->prepare("INSERT INTO permits_approval 
            (User_ID, requested_date, status, purpose, type_of_item, submitted_by, item_name, project_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt2->bind_param("sssssssi", $user_id, $requested_date, $permit_status, $purpose, $type_of_item, $submitted_by, $name, $project_id);
        $stmt2->execute();
        $stmt2->close();

        // 3. Notification
        $notification_title = "Project Request Submitted";
        $notification_message = "<strong>$submitted_by</strong> submitted a permit for project \"<strong>$name</strong>\".";
        $notification_status = "Unread";
        $date_sent = date("Y-m-d H:i:s");
        $recipient_role = $user_role;
        $module = "Project Management";

        $notifStmt = $conn->prepare("INSERT INTO notification_pm 
            (title, message, status, date_sent, sent_by, User_ID, recipient_role, module) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $notifStmt->bind_param("ssssssss", $notification_title, $notification_message, $notification_status, $date_sent, $submitted_by, $user_id, $recipient_role, $module);
        $notifStmt->execute();
        $notifStmt->close();

        $conn->commit();
        header("Location: project.php?success=1");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "Transaction failed: " . $e->getMessage();
    }

    $conn->close();
}
?>
