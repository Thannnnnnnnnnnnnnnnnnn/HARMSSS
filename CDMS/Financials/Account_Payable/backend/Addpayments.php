<?php
include('../includes/config.php');

function dd($data) {
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
    die();
}

// Connect to the databases
$conn = new mysqli($host, $username, $password, "fin_accounts_payable");
$conn_general_ledger = new mysqli($host, $username, $password, "fin_general_ledger");
$conn_procurement = new mysqli($host, $username, $password, "logs1_procurement");
$conn_projectManage = new mysqli($host, $username, $password, "logs1_project_management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($conn_general_ledger->connect_error) {
    die("Connection failed: " . $conn_general_ledger->connect_error);
}
if ($conn_procurement->connect_error) {
    die("Connection failed: " . $conn_procurement->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invoice_id = isset($_POST['invoice_id']) ? (int) $_POST['invoice_id'] : 0;
    $amount_paid = isset($_POST['amount_paid']) ? (float) $_POST['amount_paid'] : 0.00;
    $payment_method = $_POST['payment_method'] ?? '';
    
    if (empty($invoice_id) || empty($amount_paid) || empty($payment_method)) {
        die("Error: Missing required fields.");
    }

    // Start transaction
    $conn->begin_transaction();
   
    try {
        // Insert into vendorpayments
        $stmt = $conn->prepare("
            INSERT INTO vendorpayments (PayableInvoiceID, PaymentStatus, AmountPaid, PaymentMethod) 
            VALUES (?, ?, ?, ?)
        ");
        
        $payment_status = "Completed";  
        $stmt->bind_param("isds", $invoice_id, $payment_status, $amount_paid, $payment_method);

        if (!$stmt->execute()) {
            throw new Exception("Error inserting into vendorpayments: " . $stmt->error);
        }

        $payment_id = $stmt->insert_id;
        $stmt->close();

        // Fetch invoice details
        $stmt_amount = $conn->prepare("   
            SELECT Amount, Types, BudgetName, Department, funding_id , project_id
            FROM payableinvoices 
            WHERE PayableInvoiceID = ?
        ");
        $stmt_amount->bind_param("i", $invoice_id);
        $stmt_amount->execute();
        $stmt_amount->bind_result($invoice_amount, $types, $budget_name, $department, $funding_id,$project_id);
        
        if (!$stmt_amount->fetch()) {
            throw new Exception("Error: Invoice not found.");
        }
        $stmt_amount->close();

        // If payment is completed, insert into general ledger transactions
        if ($payment_status === "Completed") {
            $stmt_ledger = $conn_general_ledger->prepare("
                INSERT INTO transactions 
                (PayablePaymentID, TransactionFrom, BudgetName, Allocated_Department, BudgetAllocated, PaymentMethod) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
    
            $stmt_ledger->bind_param("isssds", $payment_id, $types, $budget_name, $department, $amount_paid, $payment_method);

            // if (!$stmt_ledger->execute()) {
            //     throw new Exception("Error inserting into transactions: " . $stmt_ledger->error);
            // }
            $stmt_ledger->close();
           
            // Update funding status in logs_procurement
            $stmt_funding = $conn_procurement->prepare("
                UPDATE for_funding 
                SET status = 'Funds Successfully Allocated' 
                WHERE funding_id = ?
            "); 
            $stmt_funding->bind_param("s", $funding_id);

            if (!$stmt_funding->execute()) {
                throw new Exception("Error updating logs_procureme~nt: " . $stmt_funding->error);
            }
            $stmt_funding->close();
            $stmt_project = $conn_projectManage->prepare("
                UPDATE project 
                SET project_status = 'Funds ETITS Allocated' 
                WHERE project_id = ?
            "); 
            $stmt_project->bind_param("s", $project_id);

            if (!$stmt_project->execute()) {
                throw new Exception("Error updating logs_procurement: " . $stmt_project->error);
            }
            $stmt_project->close();
        }

        // Commit transactiong
        $conn->commit();

        // Redirect with success message
        header('Location: ../PayableInvoices.php');
        exit();
    } catch (Exception $e) {
        // Rollback transaction 
        $conn->rollback();

        // Log error
        error_log("Payment Insertion Error: " . $e->getMessage());

        // Redirect with error message (optional, adjust as needed)
        header('Location: ../PayableInvoices.php?error=' . urlencode($e->getMessage()));
        exit();
    }
}

// Close database connections
$conn->close();
$conn_general_ledger->close();
?>