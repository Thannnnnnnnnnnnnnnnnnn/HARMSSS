<?php
session_start(); 
include("connection.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['GuestID'])) {
    $guestID = $_POST['GuestID'];

    try {
        // Start transaction
        $conn->beginTransaction();

        // 1. Delete from interactions table
        $stmt1 = $conn->prepare("DELETE FROM interactions WHERE GuestID = :GuestID");
        $stmt1->execute([':GuestID' => $guestID]);

        // 2. Delete from feedback table
        $stmt2 = $conn->prepare("DELETE FROM feedback WHERE GuestID = :GuestID");
        $stmt2->execute([':GuestID' => $guestID]);

        // 3. Delete from guests table
        $stmt3 = $conn->prepare("DELETE FROM guests WHERE GuestID = :GuestID");
        $stmt3->execute([':GuestID' => $guestID]);

        // Commit changes
        $conn->commit();

        // --- Insert Notification ---
        $notifType = 'delete';
        $message = "A guest deleted '$guestID' has been deleted. by user id:'$user_id'";
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
        $description = "Delete Guests ID:  '$guestID'";
        $department_affected = 'Core 3';
        $module_affected = 'Guests';

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
        header("Location: ../guest.php");
        exit();

    } catch (PDOException $e) {
        // Rollback if error occurs
        $conn->rollBack();
        echo "Error deleting guest: " . $e->getMessage();
    }
} else {
    header("Location: ../guest.php?msg=invalid_request");
    exit;
}
?>