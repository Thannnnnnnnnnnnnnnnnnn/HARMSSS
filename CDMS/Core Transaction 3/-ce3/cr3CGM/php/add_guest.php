<?php
session_start(); 

include("connection.php");

if($_SERVER['REQUEST_METHOD'] =="POST") {

    $guest_name = $_POST['guest_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];
    $nationality = $_POST['nationality'] ?? '';
   
    try {
    
        // SQL query to insert guest data
        $sql = "INSERT INTO guests (guest_name, email, phone, address, date_of_birth, gender, nationality)
                VALUES (:guest_name, :email, :phone, :address, :birthday, :gender, :nationality)";
    
        // Prepare the query
        $stmt = $conn->prepare($sql);
    
        // Bind parameters to the prepared statement
        $stmt->bindParam(':guest_name', $guest_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':birthday', $birthday);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':nationality', $nationality);
       
    
        // Execute the query
        $stmt->execute();
    
         // --- Audit Trail Logging ---
        $user_id = $_SESSION['user_id'];
        $department_id = $_SESSION['department_id'];
        $action = 'Add';
        $description = "Add Guests named:  '$guest_name'";
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
        echo "Error: " . $e->getMessage();
    }
}
?>