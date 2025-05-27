<?php
function dd($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once('../includes/config.php');
    // Sanitize and validate inputs
    $amount_paid = filter_input(INPUT_POST, 'amount_paid', FILTER_VALIDATE_FLOAT);
    $payment_method = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_STRING);
    $invoice_id = filter_input(INPUT_POST, 'invoice_id', FILTER_VALIDATE_INT);

    // Check for valid inputs
    if ($amount_paid === false || $amount_paid <= 0 || empty($payment_method) || $invoice_id === false || $invoice_id <= 0) {
        error_log("Validation failed: amount_paid=$amount_paid, payment_method=$payment_method, invoice_id=$invoice_id");
        header('Location: ../PayableInvoices.php?error=invalid_input');
        exit();
    }

    // Initialize database connections
    $conn = new mysqli($host, $username, $password, "fin_accounts_payable");
    $conn_gl = new mysqli($host, $username, $password, "fin_general_ledger");

    // Check connection errors
    if ($conn->connect_error) {
        error_log("Accounts Payable DB Connection Error: " . $conn->connect_error);
        die("Connection failed. Please try again later.");
    }
    if ($conn_gl->connect_error) {
        error_log("General Ledger DB Connection Error: " . $conn_gl->connect_error);
        die("Connection failed. Please try again later.");
    }

    // Start transaction
    $conn->begin_transaction();
    $conn_gl->begin_transaction(); // Ensure GL database is also part of the transaction

    try {
        // Insert payment record
        $stmt = $conn->prepare("
            INSERT INTO vendorpayments 
            (PayableInvoiceID, PaymentStatus, AmountPaid, PaymentMethod) 
            VALUES (?, 'Completed', ?, ?)
        ");
        if (!$stmt) {
            dd('test1');
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ids", $invoice_id, $amount_paid, $payment_method);
        if (!$stmt->execute()) {
            dd('test2');
            throw new Exception("Insert vendor payment failed: " . $stmt->error);
        }
        $payment_id = $stmt->insert_id;
        $stmt->close();

        // Fetch invoice details
        $stmt_amount = $conn->prepare("
            SELECT Amount, Types, BudgetName, Department 
            FROM payableinvoices 
            WHERE PayableInvoiceID = ?
        ");
        if (!$stmt_amount) {
            dd('test3');
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt_amount->bind_param("i", $invoice_id);
        if (!$stmt_amount->execute()) {
            dd('test4');
            throw new Exception("Fetch invoice details failed: " . $stmt_amount->error);
        }
        $result = $stmt_amount->get_result();
        if ($result->num_rows === 0) {
            throw new Exception("Invoice not found for ID: $invoice_id");
        }
        $invoice = $result->fetch_assoc();
        $stmt_amount->close();

        $invoice_amount = $invoice['Amount'];
        $types = $invoice['Types'];
        $budget_name = $invoice['BudgetName'];
        $department = $invoice['Department'];

        // Check total payments
        $total_paid_stmt = $conn->prepare("
            SELECT COALESCE(SUM(AmountPaid), 0) as TotalPaid 
            FROM vendorpayments 
            WHERE PayableInvoiceID = ?
        ");
        if (!$total_paid_stmt) {
            dd('test5');
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $total_paid_stmt->bind_param("i", $invoice_id);
        if (!$total_paid_stmt->execute()) {
            dd('test6');
            throw new Exception("Fetch total paid failed: " . $total_paid_stmt->error);
        }
        $total_paid = $total_paid_stmt->get_result()->fetch_assoc()['TotalPaid'];
        $total_paid_stmt->close();

        $status = ($total_paid >= $invoice_amount) ? 'Paid' : 'Partially Paid';

        // Insert into general ledger
        $stmt_gl = $conn_gl->prepare("
            INSERT INTO transactions 
            (PayablePaymentID, TransactionFrom, BudgetName, Allocated_Department, BudgetAllocated, PaymentMethod) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        if (!$stmt_gl) {
            dd('test7');
            throw new Exception("Prepare failed: " . $conn_gl->error);
        }
        $stmt_gl->bind_param("isssds", $payment_id, $types, $budget_name, $department, $amount_paid, $payment_method);
        if (!$stmt_gl->execute()) {
            dd('test8');
            throw new Exception("Insert general ledger transaction failed: " . $stmt_gl->error);
        }
        $stmt_gl->close();
        dd('this is d end');
        // Update invoice status
        $update = $conn->prepare("
            UPDATE payableinvoices 
            SET Status = ? 
            WHERE PayableInvoiceID = ?
        ");
        if (!$update) {
            dd('test9');
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $update->bind_param("si", $status, $invoice_id);
        if (!$update->execute()) {
            dd('test10');
            throw new Exception("Update invoice status failed: " . $update->error);
        }
        $update->close();

        // Commit transactions
        $conn->commit();
        $conn_gl->commit();

        // Close connections
        $conn->close();
        $conn_gl->close();

        // Redirect with success
        header('Location: ../PayableInvoices.php?success=payment_processed');
        exit();

    } catch (Exception $e) {
        // Rollback transactions
        $conn->rollback();
        $conn_gl->rollback();

        // Log detailed error
        error_log("Payment Insertion Error: " . $e->getMessage() . " | InvoiceID: $invoice_id | Amount: $amount_paid | Method: $payment_method");

        // Close connections
        $conn->close();
        $conn_gl->close();

        // Redirect with detailed error
        header('Location: ../PayableInvoices.php?error=payment_failed&message=' . urlencode($e->getMessage()));
        exit();
    }
} else {
    // Invalid request method
    header('Location: ../PayableInvoices.php?error=invalid_request');
    exit();
}
?>