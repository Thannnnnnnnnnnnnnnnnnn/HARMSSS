<?php
session_start(); 
require 'connection.php';

$guestID = $_GET['guest_id'] ?? '';


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $guestID = $_POST['guest_id'] ?? '';
    $rating = $_POST['rating'] ?? '';
    $comment = $_POST['comment'] ?? '';

    if (!empty($guestID) && !empty($rating) && !empty($comment)) {
        try {
            $stmt = $conn->prepare("INSERT INTO feedback (GuestID, rating, comment, feedback_date) VALUES (:guestID, :rating, :comment, NOW())");
            $stmt->bindParam(':guestID', $guestID);
            $stmt->bindParam(':rating', $rating);
            $stmt->bindParam(':comment', $comment);
            $stmt->execute();

             // --- Insert Notification ---
        $notifType = 'create';
        $message = "A guest ID '$guestID' created a feedback.";
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
        $action = 'Submit';
        $description = "Create a feedback:  '$guestID'";
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

        

        // ✅ Set success message
        $_SESSION['success_message'] = "Status updated and audit logged successfully.";
        
        
            echo "<script>alert('Thank you for your feedback!');</script>";
             header("Location: ../history_feedback.php");
        } catch (PDOException $e) {
            echo "<script>alert('Error saving feedback: " . $e->getMessage() . "');</script>";
        }
    } else {
        echo "<script>alert('Please complete all fields.');</script>";
    }
}
?>