<?php
// Start output buffering to catch any stray output
ob_start();

// Set JSON header immediately
header('Content-Type: application/json');

// Include controller with absolute path relative to this file
$controllerPath = __DIR__ . '/controller.php';
if (!file_exists($controllerPath)) {
    echo json_encode(['status' => 'error', 'message' => 'Controller file not found at ' . $controllerPath]);
    exit;
}
include($controllerPath);

// Clear any buffered output before sending JSON
ob_clean();

if (isset($_POST['mark_viewed']) && isset($_POST['paymentID'])) {
    try {
        $data = new Data();
        $paymentID = intval($_POST['paymentID']);

        // Verify database connection
        if (!$data->conn || $data->conn->connect_error) {
            echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . ($data->conn->connect_error ?? 'No connection object')]);
            exit;
        }

        // Prepare and execute query
        $stmt = $data->conn->prepare("
            UPDATE acct_receivable ar
            INNER JOIN collection_payments cp ON ar.InvoiceID = cp.InvoiceID
            SET ar.IsViewed = 1
            WHERE cp.PaymentID = ? AND ar.Status = 'Reservation'
        ");
        if ($stmt === false) {
            echo json_encode(['status' => 'error', 'message' => 'Query preparation failed: ' . $data->conn->error]);
            exit;
        }
        $stmt->bind_param('i', $paymentID);
        $result = $stmt->execute();

        if ($result) {
            error_log("Reservation PaymentID $paymentID marked as viewed on " . date('Y-m-d H:i:s'));
            echo json_encode(['status' => 'success', 'message' => 'Reservation marked as viewed.']);
        } else {
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