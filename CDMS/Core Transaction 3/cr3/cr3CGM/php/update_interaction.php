<?php
session_start(); 
// Include your database connection
include('connection.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the InteractionID and new status from the POST request
    $interactionID = $_POST['InteractionID'];
    $status = $_POST['status'];

    try {
        // --- Update interaction status ---
        $query = "UPDATE interactions SET interaction_status = :status WHERE InteractionID = :interactionID";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':interactionID', $interactionID, PDO::PARAM_INT);
        $stmt->execute();

        // --- Audit Trail Logging ---
        $user_id = $_SESSION['user_id'];
        $department_id = $_SESSION['department_id'];
        $action = 'Update';
        $description = "Updated interaction status to '$status' (ID: $interactionID)";
        $department_affected = 'Core 3';
        $module_affected = 'Interaction';;

        $auditQuery = "INSERT INTO department_audit_trail 
            (department_id, user_id, action, description, department_affected, module_affected) 
            VALUES 
            (:department_id, :user_id, :action, :description, :department_affected, :module_affected)";
        $auditStmt = $conn->prepare($auditQuery);
        $auditStmt->execute([
            ':department_id' => $department_id,
            ':user_id' => $user_id,
            ':action' => $action,
            ':description' => $description,
            ':department_affected' => $department_affected,
            ':module_affected' => $module_affected
        ]);

         // --- Insert Notification ---
        $notifType = 'update';
        $message = "An interaction ID '$interactionID' has been updated. by user ID'$user_id'";
        $status = 'Unread';
        $notifDepartment = 'Core 3';
        date_default_timezone_set('Asia/Manila'); // Optional but good practice
        $date_sent = date('Y-m-d H:i:s');

        $notifQuery = "INSERT INTO notifications 
            (notifType, message, status, Department, date_sent, User_ID) 
            VALUES 
            (:notifType, :message, :status, :department, :date_sent, :user_id)";
        
        $notifStmt = $conn->prepare($notifQuery);
        $notifStmt->execute([
            ':notifType' => $notifType,
            ':message' => $message,
            ':status' => $status,
            ':department' => $notifDepartment,
            ':date_sent' => $date_sent,
            ':user_id' => $user_id

        ]);

        // ✅ Set success message
        $_SESSION['success_message'] = "Status updated and audit logged successfully.";
        header("Location: ../interactions.php");
        exit();
        
    } catch (PDOException $e) {
        // ❌ Set error message
        $_SESSION['error_message'] = "Something went wrong: " . $e->getMessage();
        header("Location: ../interactions.php");
        exit();
    }
}
?>
