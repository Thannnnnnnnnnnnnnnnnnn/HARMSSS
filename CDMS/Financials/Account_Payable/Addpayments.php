<?php  
// function dd($value){     
//     echo "<pre>";     
//     var_dump($value);     
//     echo "</pre>";       
//     die(); 
// }  

include('includes/config.php');  

// Connect to the databases 
$conn = new mysqli($host, $username, $password, "fin_accounts_payable"); 
$conn_general_ledger = new mysqli($host, $username, $password, "fin_general_ledger");  

if ($conn->connect_error) {      
    die("Connection failed: " . $conn->connect_error); 
} 
if ($conn_general_ledger->connect_error) {     
    die("Connection failed: " . $conn_general_ledger->connect_error); 
}  

if ($_SERVER['REQUEST_METHOD'] === 'POST') {     
    $invoice_id = isset($_POST['invoice_id']) ? (int) $_POST['invoice_id'] : 0;     
    $payment_date = $_POST['payment_date'] ?? '';     
    $amount_paid = isset($_POST['amount_paid']) ? (float) $_POST['amount_paid'] : 0.00;     
    $payment_method = $_POST['payment_method'] ?? '';      
    
    if (empty($invoice_id) || empty($payment_date) || empty($amount_paid) || empty($payment_method)) {         
        header('Location: ../PayableInvoices.php?error=missing_fields');
        exit();
    }      

    // Start transaction     
    $conn->begin_transaction();         
    
    try {              
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
        
        $stmt_amount = $conn->prepare("             
            SELECT Amount, Types, BudgetName, Department FROM payableinvoices              
            WHERE PayableInvoiceID = ?         
        ");         
        $stmt_amount->bind_param("i", $invoice_id);         
        $stmt_amount->execute();         
        $stmt_amount->bind_result($invoice_amount, $types, $budget_name, $department);                  
        
        if (!$stmt_amount->fetch()) {             
            throw new Exception("Error: Invoice not found.");         
        }         
        $stmt_amount->close();        
        
        // If the status is completed, insert into the general ledger the transaction         
        if ($payment_status === "Completed") {             
            $stmt_ledger = $conn_general_ledger->prepare("                 
                INSERT INTO transactions                  
                (PayablePaymentID, TransactionFrom, BudgetName, Allocated_Department, BudgetAllocated, PaymentMethod)                  
                VALUES (?, ?, ?, ?, ?, ?)             
            ");             
            $stmt_ledger->bind_param("isssds", $payment_id, $types, $budget_name, $department, $amount_paid, $payment_method);              
            
            if (!$stmt_ledger->execute()) {                 
                throw new Exception("Error inserting into transactions: " . $stmt_ledger->error);             
            }             
            $stmt_ledger->close();         
        }
        
        // Commit transaction         
        $conn->commit();          
        
        // Redirect with success message         
        header('Location: PayableInvoices.php?payment_success=1');         
        exit();     
        
    } catch (Exception $e) {         
        // Rollback transaction         
        $conn->rollback();          
        
        // Log error         
        error_log("Payment Insertion Error: " . $e->getMessage());
        
        // Redirect with error message instead of leaving blank page
        header('Location: PayableInvoices.php?error=payment_failed&message=' . urlencode($e->getMessage()));
        exit();
    } finally {
        // Close database connections in finally block to ensure they're always closed
        $conn->close();     
        $conn_general_ledger->close();
    }
} else {
    // If not POST request, redirect back to main page
    header('Location: ../PayableInvoices.php');
    exit();
}
?>