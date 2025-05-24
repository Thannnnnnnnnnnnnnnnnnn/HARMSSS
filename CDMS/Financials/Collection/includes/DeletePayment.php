<?php
// Start output buffering to catch stray output
ob_start();

// Set JSON header
header('Content-Type: application/json');

// Include controller
$controllerPath = __DIR__ . '/controller.php';
if (!file_exists($controllerPath)) {
    echo json_encode(['status' => 'error', 'message' => 'Controller file not found at ' . $controllerPath]);
    exit;
}
include($controllerPath);

// Clear buffer before JSON output
ob_clean();

if (isset($_POST['paymentID'])) {
    try {
        $data = new Data();
        $paymentID = intval($_POST['paymentID']);

        // Verify database connection
        if (!$data->conn || $data->conn->connect_error) {
            echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . ($data->conn->connect_error ?? 'No connection object')]);
            exit;
        }

        // Call DeleteCollectionPayment
        $result = $data->DeleteCollectionPayment($paymentID);

        if ($result === true) {
            error_log("PaymentID $paymentID deleted successfully on " . date('Y-m-d H:i:s'));
            echo json_encode(['status' => 'success', 'message' => 'Payment has been deleted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete payment: ' . $result]);
        }
    } catch (Exception $e) {
        error_log("DeletePayment error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request: Missing paymentID.']);
}

exit;
?>