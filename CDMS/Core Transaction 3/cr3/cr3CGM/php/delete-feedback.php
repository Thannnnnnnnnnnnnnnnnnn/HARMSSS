<?php
session_start(); 
require 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['FeedbackID'])) {
    try {
        $FeedbackID = intval($_POST['FeedbackID']);

        $stmt = $conn->prepare("DELETE FROM feedback WHERE FeedbackID = :feedback_id");
        $stmt->bindValue(':feedback_id', $FeedbackID, PDO::PARAM_INT);

        if ($stmt->execute()) {
                        // --- Audit Trail Logging ---
        $user_id = $_SESSION['user_id'];
        $department_id = $_SESSION['department_id'];
        $action = 'Delete';
        $description = "Delete a feedback:  '$FeedbackID'";
        $department_affected = 'Core 3';
        $module_affected = 'Feedback';

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
        $notifType = 'delete';
        $message = "Delete feedback. Feedback ID: '$FeedbackID' has been Deleted. by user ID '$user_id'";
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
            echo "<script>alert('Feedback deleted successfully!'); window.location.href='../history_feedback.php';</script>";
        } else {
            echo "<script>alert('Error deleting feedback!'); window.location.href='../history_feedback.php';</script>";
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: ../history_feedback.php");
    exit();
}
?>