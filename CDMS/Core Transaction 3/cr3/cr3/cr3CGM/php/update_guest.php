<?php
session_start(); 
require 'connection.php';

if (isset($_GET['GuestID'])) {
    $id = intval($_GET['GuestID']);
    $stmt = $conn->prepare("SELECT * FROM guests WHERE GuestID = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $guest = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$guest) {
        echo "Guest not found.";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate GuestsID exists and is not empty
    if (!isset($_POST['GuestID']) || empty($_POST['GuestID'])) {
        echo "Error: Missing or invalid Guest ID.";
        exit;
    }

    $id = intval($_POST['GuestID']);
    $name = $_POST['guest_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $birthday = $_POST['date_of_birth'] ?? '';
    $email = $_POST['email'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $nationality = $_POST['nationality'] ?? '';
    $reservation = $_POST['reservation'] ?? '';
    $checkin = $_POST['check_in'] ?? '';
    $checkout = $_POST['check_out'] ?? '';
    $status = $_POST['status'] ?? '';

    // Update guest details in the database
    try {
        $stmt = $conn->prepare("UPDATE guests 
            SET guest_name = :guest_name, phone = :phone, address = :address, 
                date_of_birth = :birthday, email = :email, gender = :gender, 
                nationality = :nationality, reservation = :reservation, 
                check_in = :check_in, check_out = :check_out, status = :status 
            WHERE GuestID = :id");
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':guest_name', $name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':birthday', $birthday);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':nationality', $nationality);
        $stmt->bindParam(':reservation', $reservation);
        $stmt->bindParam(':check_in', $checkin);
        $stmt->bindParam(':check_out', $checkout);
        $stmt->bindParam(':status', $status);
        
        $stmt->execute();

          // --- Audit Trail Logging ---
        $user_id = $_SESSION['user_id'];
        $department_id = $_SESSION['department_id'];
        $action = 'Update';
        $description = "Update Guests ID:  '$id'";
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
        echo "Error updating guest: " . $e->getMessage();
    }
}
?>
