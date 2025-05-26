<?php
// Database connection
$host = "localhost";
$username = "3206_CENTRALIZED_DATABASE";
$password = "4562526";
$database = "fin_accounts_payable";
// $host = "localhost:3307";  
// $username = "root";   
// $password = "";       

$conn = new mysqli($host, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $invoice_id = $_POST['invoice_id'];
    $due_date = $_POST['due_date'];


    if (!empty($invoice_id) && !empty($due_date)) {
        $stmtCheck = $conn->prepare("SELECT ScheduleID FROM paymentschedules WHERE PayableInvoiceID = ?");
        $stmtCheck->bind_param("i", $invoice_id);
        $stmtCheck->execute();
        $stmtCheck->store_result();

        if ($stmtCheck->num_rows > 0) {
            echo "<script>alert('Payment schedule already exists for this invoice!'); window.location.href='../PayableInvoices.php';</script>";
        } else {
            $stmtInsert = $conn->prepare("INSERT INTO paymentschedules (PayableInvoiceID, PaymentSchedule) VALUES (?, ?)");
            $stmtInsert->bind_param("is", $invoice_id, $due_date);

            if ($stmtInsert->execute()) {
                header('Location: ../PayableInvoices.php');
                
                exit();
            } else {
                echo $stmtInsert->error;
            }
            $stmtInsert->close();
        }
        $stmtCheck->close();
    } else {
        echo "All fields are required.";
    }
}

$conn->close();
?>
