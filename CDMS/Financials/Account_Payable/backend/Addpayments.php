<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tite'])) {
    require_once('../includes/config.php');

    $payment_date = $_POST['payment_date'] ?? '';
    $amount_paid = floatval($_POST['amount_paid'] ?? 0);
    $payment_method = $_POST['payment_method'] ?? '';

    if (!$payment_date || !$amount_paid || !$payment_method) {
        header('Location: ../PayableInvoices.php?error=missing_fields');
        exit();
    }

    $conn = new mysqli($host, $username, $password, "fin_accounts_payable");
    $conn_gl = new mysqli($host, $username, $password, "fin_general_ledger");

    if ($conn->connect_error || $conn_gl->connect_error) {
        die("Connection error: " . $conn->connect_error . $conn_gl->connect_error);
    }

    $conn->begin_transaction();

    try {
        // Insert payment record
        $stmt = $conn->prepare("
            INSERT INTO vendorpayments 
            ( PaymentStatus, PaymentDate, AmountPaid, PaymentMethod) 
            VALUES (1, 'Completed', ?, ?, ?)
        ");
        $stmt->bind_param("sds", $payment_date, $amount_paid, $payment_method);
        $stmt->execute();
        $payment_id = $stmt->insert_id;
        $stmt->close();

        // Get invoice details
        $stmt = $conn->prepare("
            SELECT Amount, Types, BudgetName, Department 
            FROM payableinvoices 
            WHERE PayableInvoiceID = ?
        ");
        $stmt->bind_param("i", $invoice_id);
        $stmt->execute();
        $stmt->bind_result($invoice_amount, $types, $budget_name, $department);
        $stmt->fetch();
        $stmt->close();

        // Insert into general ledger
        $stmt_gl = $conn_gl->prepare("
            INSERT INTO transactions 
            (PayablePaymentID, TransactionFrom, BudgetName, Allocated_Department, BudgetAllocated, PaymentMethod) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt_gl->bind_param("isssds", $payment_id, $types, $budget_name, $department, $amount_paid, $payment_method);
        $stmt_gl->execute();
        $stmt_gl->close();

        // Update invoice status (optional redundancy)
        $update = $conn->prepare("
            UPDATE payableinvoices 
            SET Status = 'Paid' 
            WHERE PayableInvoiceID = ?
        ");
        $update->bind_param("i", $invoice_id);
        $update->execute();
        $update->close();

        $conn->commit();
        header('Location: ../PayableInvoices.php?payment_success=1');
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Payment Error: " . $e->getMessage());
        header('Location: ../PayableInvoices.php?error=payment_failed&message=' . urlencode($e->getMessage()));
        exit();
    } finally {
        $conn->close();
        $conn_gl->close();
    }
} else {
    header('Location: ../PayableInvoices.php');
    exit();
}
?>
