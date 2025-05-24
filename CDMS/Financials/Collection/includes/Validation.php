<?php
include('../config/controller.php');

if (isset($_POST['validate_invoice_id'])) {
    $invoiceID = $_POST['invoice_id'];
    $data = new Data();
    $payment = $data->getPaymentByInvoiceID($invoiceID);
    if ($payment) {
        echo json_encode(['status' => 'success', 'guest_name' => $payment['GuestName']]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Invoice ID']);
    }
    exit;
}
?>