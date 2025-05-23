<?php
session_start(); 
require 'connection.php'; // Your PDO connection setup

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get submitted data safely
    $guestId = intval($_POST['GuestID'] ?? 0);
    $userId = intval($_POST['user_id'] ?? 0);

    $name = $_POST['guest_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $birthday = $_POST['date_of_birth'] ?? '';
    $email = $_POST['email'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $nationality = $_POST['nationality'] ?? '';

    // You might want to add validation here before proceeding

   try {
    if ($guestId > 0) {
        // Update guest
        $stmt = $conn->prepare("UPDATE guests 
            SET guest_name = :guest_name, phone = :phone, address = :address,
                date_of_birth = :date_of_birth, email = :email, gender = :gender,
                nationality = :nationality
            WHERE GuestID = :guestId");

        $stmt->bindParam(':guestId', $guestId, PDO::PARAM_INT);
    } else {
        // Insert new guest
        $stmt = $conn->prepare("INSERT INTO guests 
            (user_id, guest_name, phone, address, date_of_birth, email, gender, nationality)
            VALUES
            (:user_id, :guest_name, :phone, :address, :date_of_birth, :email, :gender, :nationality)");

        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    }

    // Common bindings
    $stmt->bindParam(':guest_name', $name);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':date_of_birth', $birthday);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':nationality', $nationality);

    $stmt->execute();
  // --- Audit Trail Logging ---
        $user_id = $_SESSION['user_id'];
        $department_id = $_SESSION['department_id'];
        $action = 'Update';
        $description = "Update Guests ID:  '$guestid'";
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
    echo "Database error: " . $e->getMessage();
    header("Location: ../guest.php");
}
}
