<?php
session_start(); 
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and assign variables
    $guestId = $_POST['GuestID'] ?? null;
    $roomId = $_POST['room'] ?? null;
    $checkIn = $_POST['check_in'] ?? null;
    $checkOut = $_POST['check_out'] ?? null;

    // Validate inputs
    if (!$guestId) {
        $_SESSION['error_message'] = "⚠️ Please fill out the form above first to select a guest.";
        header("Location: ../guest.php");
        exit();
    }

    if (!$roomId || !$checkIn || !$checkOut) {
        $_SESSION['error_message'] = "⚠️ Please fill all required fields: room, check-in, and check-out dates.";
        header("Location: ../guest.php");
        exit();
    }

    try {
        // Check for existing reservation with same room and check-in date
        $checkDuplicate = $conn->prepare("SELECT * FROM reservations WHERE room_id = ? AND checkin_date = ?");
        $checkDuplicate->execute([$roomId, $checkIn]);
        if ($checkDuplicate->rowCount() > 0) {
            $_SESSION['error_message'] = "⚠️ This room is already reserved for the selected check-in date.";
            header("Location: ../guest.php");
            exit();
        }

        // Fetch guest info
        $guestStmt = $conn->prepare("SELECT guest_name, phone FROM guests WHERE GuestID = ?");
        $guestStmt->execute([$guestId]);
        $guest = $guestStmt->fetch(PDO::FETCH_ASSOC);

        if (!$guest) {
            $_SESSION['error_message'] = "⚠️ Guest not found.";
            header("Location: ../guest.php");
            exit();
        }

        // Insert new reservation
        $sql = "INSERT INTO reservations 
                (first_name, room_id, checkin_date, checkout_date, guests, created_at, phone)
                VALUES 
                (:first_name, :room_id, :checkin_date, :checkout_date, :guests, NOW(), :phone)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':first_name' => $guest['guest_name'],
            ':room_id' => $roomId,
            ':checkin_date' => $checkIn,
            ':checkout_date' => $checkOut,
            ':guests' => $guestId,
            ':phone' => $guest['phone']
        ]);

        // Get the last inserted reservation ID
$reservationId = $conn->lastInsertId();

// Insert initial reservation status
$statusSql = "INSERT INTO reservationstatus (reservation_id, status, updated_at) 
              VALUES (:reservation_id, :status, NOW())";
$statusStmt = $conn->prepare($statusSql);
$statusStmt->execute([
    ':reservation_id' => $reservationId,
    ':status' => 'Pending'
]);

       // Assume you have these values from your reservation creation or form input
$depart_transc_id = null; // Usually auto-increment, so leave null or omit in insert
$department_id = $_SESSION['department_id']; // from session
$user_id = $_SESSION['user_id']; // from session
$transaction_type = 'Create, Reservation for guest ID: ';
$description = "Created a reservation for guest";
$timestamp = date('Y-m-d H:i:s'); // current timestamp

// Insert into department_transaction table
$insertTranscQuery = "INSERT INTO department_transaction 
    (department_id, user_id, transaction_type, description, timestamp) 
    VALUES 
    (:department_id, :user_id, :transaction_type, :description, :timestamp)";
$insertTranscStmt = $conn->prepare($insertTranscQuery);
$insertTranscStmt->execute([
    ':department_id' => $department_id,
    ':user_id' => $user_id,
    ':transaction_type' => $transaction_type,
    ':description' => $description,
    ':timestamp' => $timestamp
]);


// --- Audit Trail Logging ---
$action = 'Create';
$department_affected = 'Core 3';
$module_affected = 'Guests and Reservation';

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

$_SESSION['success_message'] = "✅ Reservation created and audit logged successfully.";
header("Location: ../guest.php");
exit();

    } catch (PDOException $e) {
        $_SESSION['error_message'] = "❌ Database error: " . $e->getMessage();
        header("Location: ../guest.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "❌ Invalid request method.";
    header("Location: ../guest.php");
    exit();
}
