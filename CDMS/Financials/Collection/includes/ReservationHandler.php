<?php
include('../config/controller.php');

// Verify Reservation
if (isset($_POST['verify_reservation'])) {
    $paymentID = $_POST['paymentID'];
    $data = new Data();
    $result = $data->UpdatePayment($paymentID, null, null, null, null, null, null, 'Fully Paid');
    if ($result === true) {
        echo json_encode(['status' => 'success', 'message' => 'Reservation verified successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => "Failed to verify reservation: " . $result]);
    }
    exit;
}

// Update Reservation
if (isset($_POST['update_reservation'])) {
    $paymentID = $_POST['paymentID'];
    $guestName = $_POST['guestName'];
    $totalAmount = floatval($_POST['totalAmount']);
    $amountPay = floatval($_POST['amountPay']);
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];

    if ($amountPay <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Amount paid must be greater than 0.']);
        exit;
    }

    $startDateTime = new DateTime($startDate);
    $endDateTime = new DateTime($endDate);
    if ($endDateTime < $startDateTime) {
        echo json_encode(['status' => 'error', 'message' => 'Check-Out cannot be before Check-In.']);
        exit;
    }

    $today = new DateTime();
    $startDateObj = new DateTime($startDate);
    $todayDate = $today->format('Y-m-d');
    $startDateOnly = $startDateObj->format('Y-m-d');

    $status = ($startDateOnly === $todayDate) 
        ? ($amountPay < $totalAmount ? 'Downpayment' : 'Fully Paid')
        : ($startDateObj > $today ? 'Reservation' : ($amountPay < $totalAmount ? 'Downpayment' : 'Fully Paid'));

    $data = new Data();
    $result = $data->UpdatePayment($paymentID, $guestName, $totalAmount, $amountPay, $startDate, $endDate, null, $status);
    if ($result === true) {
        echo json_encode(['status' => 'success', 'message' => 'Reservation updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update reservation: ' . $result]);
    }
    exit;
}

// Cancel Reservation
if (isset($_POST['cancel_reservation'])) {
    $paymentID = $_POST['paymentID'];
    $data = new Data();
    $result = $data->DeleteCollectionPayment($paymentID);
    if ($result === true) {
        echo json_encode(['status' => 'success', 'message' => 'Reservation canceled successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to cancel reservation']);
    }
    exit;
}
?>