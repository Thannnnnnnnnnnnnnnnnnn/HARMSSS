<?php
session_start(); 
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $guest_id = isset($_POST['GuestID']) ? intval($_POST['GuestID']) : 0;
    $interaction_type = isset($_POST['interaction_type']) ? trim($_POST['interaction_type']) : '';
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';

    // Validation
    if ($guest_id <= 0 || empty($interaction_type) || empty($comment) || empty($status)) {
        $_SESSION['success_message'] = "Fill up the details first.";
        header("Location: ../guest.php");
        exit;
    }

    try {
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO interactions (GuestID, interaction_type, description, interaction_status, interaction_date) VALUES (?, ?, ?, ?, NOW())");

        $stmt->bindParam(1, $guest_id, PDO::PARAM_INT);
        $stmt->bindParam(2, $interaction_type, PDO::PARAM_STR);
        $stmt->bindParam(3, $comment, PDO::PARAM_STR);
        $stmt->bindParam(4, $status, PDO::PARAM_STR);

        if ($stmt->execute()) {
                       // --- Audit Trail Logging ---
        $user_id = $_SESSION['user_id'];
        $department_id = $_SESSION['department_id'];
        $action = 'Submit';
        $description = "Made a Interaction:  '$guest_id'";
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

         // --- Insert Notification ---
        $notifType = 'create';
        $message = "A new guest ID '$guest_id' made interaction.";
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
        
        } else {
            // Display error message
            echo "Error inserting data: " . implode(" | ", $stmt->errorInfo());
            header("Location: ../interactions.php");
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
        header("Location: ../interactions.php");
    }
} else {
    echo "Invalid request method.";
    header("Location: ../interactions.php");
}
?>
