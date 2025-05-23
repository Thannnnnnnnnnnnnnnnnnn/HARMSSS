<?php
session_start(); 
// Start session if using session-based messages (optional)
// session_start();

// Include your database connection
include("connection.php");// Adjust the path if needed

// Check if the interaction ID is posted
if (isset($_POST['interaction_id'])) {
    $interactionID = $_POST['interaction_id'];

    try {
        // Prepare and execute the DELETE query using PDO
        $stmt = $conn->prepare("DELETE FROM interactions WHERE InteractionID = :interaction_id");
        $stmt->bindParam(':interaction_id', $interactionID, PDO::PARAM_INT);
        $stmt->execute();

          // --- Insert Notification ---
        $notifType = 'delete';
        $message = "deleted interaction inter-ID:'$interactioID' has been deleted. by user ID:'$user_id'";
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

        include("../../usm/connection.php");

          // --- Audit Trail Logging ---
        $user_id = $_SESSION['user_id'];
        $department_id = $_SESSION['department_id'];
        $action = 'Delete';
        $description = "Delete Interaction ID:  '$interactionID'";
        $department_affected = 'Core 3';
        $module_affected = 'Interaction';

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

       

        // ✅ Set success message
        $_SESSION['success_message'] = "Status updated and audit logged successfully.";
        header("Location: ../interactions.php");
       
        exit();
    } catch (PDOException $e) {
        echo "Error deleting interaction: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>