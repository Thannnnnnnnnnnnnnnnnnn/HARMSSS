<?php
include('../config/controller.php');

header('Content-Type: application/json');

if (isset($_POST['mark_viewed']) && isset($_POST['paymentID'])) {
    $data = new Data();
    $paymentID = intval($_POST['paymentID']);

    try {
        $stmt = $data->conn->prepare("
            UPDATE acct_receivable ar
            INNER JOIN collection_payments cp ON ar.InvoiceID = cp.InvoiceID
            SET ar.IsViewed = 1
            WHERE cp.PaymentID = ? AND ar.Status = 'Reservation'
        ");
        $stmt->bind_param('i', $paymentID);
        $result = $stmt->execute();

        if ($result) {
            error_log("Reservation PaymentID $paymentID marked as viewed on " . date('Y-m-d H:i:s'));
            echo json_encode(['status' => 'success', 'message' => 'Reservation marked as viewed.']);
        } else {
            error_log("Failed to execute query: " . $stmt->error);
            echo json_encode(['status' => 'error', 'message' => 'Failed to mark reservation as viewed: ' . $stmt->error]);
        }
        $stmt->close();
    } catch (Exception $e) {
        error_log("MarkReservationsViewed error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request: Missing parameters.']);
}
exit;
?>