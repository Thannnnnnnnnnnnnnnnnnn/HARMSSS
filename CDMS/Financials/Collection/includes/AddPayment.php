<?php
include('../config/controller.php');

if (isset($_POST['submit'])) {
    $guestName = $_POST['guestName'];
    $totalAmount = floatval($_POST['totalAmount']);
    $amountPay = floatval($_POST['amountPay']);
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $paymentType = $_POST['paymentType'];

    if ($amountPay <= 0) {
        $error = "Amount paid must be greater than 0 for downpayment.";
        include('../collection.php');
        exit;
    }

    $today = new DateTime();
    $startDateObj = new DateTime($startDate);
    $todayDate = $today->format('Y-m-d');
    $startDateOnly = $startDateObj->format('Y-m-d');

    $status = ($startDateOnly === $todayDate) 
        ? ($amountPay < $totalAmount ? 'Downpayment' : 'Fully Paid')
        : ($startDateObj > $today ? 'Reservation' : ($amountPay < $totalAmount ? 'Downpayment' : 'Fully Paid'));

    $add_data = new Data();
    if ($add_data->CreateCollectionPayment($guestName, $totalAmount, $amountPay, $startDate, $endDate, $paymentType, $status)) {
        header("Location: ../collection.php");
        exit;
    } else {
        $error = "Failed to add payment. Please try again.";
        include('../collection.php');
        exit;
    }
}
?>