<?php
// function dd($data) {
//     echo '<pre>';
//     var_dump($data);
//     echo '</pre>';
//     die;
// }


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once('../includes/config.php');
   
    $amount_paid = floatval($_POST['amount_paid'] ?? 0);
    $payment_method = $_POST['payment_method'] ?? '';
    $invoice_id = intval($_POST['invoice_id'] ?? 0);

    if (!$amount_paid || !$payment_method || !$invoice_id) {
        header('Location: ../PayableInvoices.php?error=missing_fields');
        exit();
    }

    $conn = new mysqli($host, $username, $password, "fin_accounts_payable");
    $conn_gl = new mysqli($host, $username, $password, "fin_general_ledger");

    if ($conn->connect_error) {
        die("Accounts Payable DB Connection Error: " . $conn->connect_error);
    }
    if ($conn_gl->connect_error) {
        die("General Ledger DB Connection Error: " . $conn_gl->connect_error);
    }

    // Start transaction
    $conn->begin_transaction();
    try {
        // Insert payment record (no PaymentDate)
        $stmt = $conn->prepare("
            INSERT INTO vendorpayments 
            (PayableInvoiceID, PaymentStatus, AmountPaid, PaymentMethod) 
            VALUES (?, 'Completed', ?, ?)
        ");
        $stmt->bind_param("ids", $invoice_id, $amount_paid, $payment_method);
        $stmt->execute();
        $payment_id = $stmt->insert_id;
        $stmt->close();

        $stmt_amount = $conn->prepare("
            SELECT Amount, Types, BudgetName,Department FROM payableinvoices 
            WHERE PayableInvoiceID = ?
        ");
        $stmt_amount->bind_param("i", $invoice_id);
        $stmt_amount->execute();
        $stmt_amount->bind_result($invoice_amount, $types, $budget_name,$department);
        
        if (!$stmt_amount->fetch()) {
            throw new Exception("Error: Invoice not found.");
        }
        $stmt_amount->close();
       //if the status is completed , insert into the general ledger the trabsaction
        if ($payment_status === "Completed") {
            $stmt_ledger = $conn_general_ledger->prepare("
                INSERT INTO transactions 
                (PayablePaymentID, TransactionFrom, BudgetName,Allocated_Department ,BudgetAllocated, PaymentMethod) 
                VALUES (?, ?, ?, ?, ?,?)
            ");
            $stmt_ledger->bind_param("isssds", $payment_id, $types, $budget_name,$department,$amount_paid, $payment_method);

        // Check total payments for this invoice
        $total_paid_stmt = $conn->prepare("
            SELECT SUM(AmountPaid) as TotalPaid 
            FROM vendorpayments 
            WHERE PayableInvoiceID = ?
        ");
        $total_paid_stmt->bind_param("i", $invoice_id);
        $total_paid_stmt->execute();
        $total_paid_stmt->bind_result($total_paid);
        $total_paid_stmt->fetch();
        $total_paid_stmt->close();

        $status = ($total_paid >= $invoice_amount) ? 'Paid' : 'Partially Paid';

        // Insert into general ledger
        $stmt_gl = $conn_gl->prepare("
            INSERT INTO transactions 
            (PayablePaymentID, TransactionFrom, BudgetName, Allocated_Department, BudgetAllocated, PaymentMethod) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt_gl->bind_param("isssds", $payment_id, $types, $budget_name, $department, $amount_paid, $payment_method);
        $stmt_gl->execute();
        $stmt_gl->close();

        // Update invoice status
        $update = $conn->prepare("
            UPDATE payableinvoices 
            SET Status = ? 
            WHERE PayableInvoiceID = ?
        ");
        $update->bind_param("si", $status, $invoice_id);
        $update->execute();
        $update->close();

        // Commit transaction
        $conn->commit();

        // Redirect with success message
        header('Location: ../PayableInvoices.php');
        exit();
    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollback();

        // Log error
        error_log("Payment Insertion Error: " . $e->getMessage());

    }

    // Close database connections
    $conn->close();
    $conn_general_ledger->close();
    
}
}
?>
