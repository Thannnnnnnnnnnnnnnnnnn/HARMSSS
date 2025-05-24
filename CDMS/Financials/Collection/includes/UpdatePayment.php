<?php
include('../config/controller.php');

header('Content-Type: application/json'); // Ensure JSON response

$data = new Data();

if (isset($_POST['update'])) {
    $paymentID = (int)$_POST['paymentID'];
    $guestName = $_POST['guestName'];
    $totalAmount = (float)$_POST['totalAmount'];
    $amountPay = (float)$_POST['amountPay'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $paymentType = $_POST['paymentType'];

    // Determine status based on amount paid vs total
    $status = ($amountPay < $totalAmount) ? 'Downpayment' : 'Fully Paid';

    // Update the payment
    $result = $data->UpdatePayment($paymentID, $guestName, $totalAmount, $amountPay, $startDate, $endDate, $paymentType, $status);

    if ($result === true) {
        $updatedData = [
            'PaymentID' => $paymentID,
            'InvoiceID' => $data->getPaymentById($paymentID)['InvoiceID'],
            'TotalAmount' => $totalAmount,
            'AmountPay' => $amountPay,
            'GuestName' => $guestName,
            'StartDate' => $startDate,
            'EndDate' => $endDate,
            'PaymentType' => $paymentType,
            'Status' => $status
        ];
        echo json_encode([
            'status' => 'success',
            'message' => 'Payment updated successfully',
            'data' => $updatedData
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update payment in database: ' . $result
        ]);
    }
} elseif (isset($_POST['settle']) && $_POST['settle'] == 1) {
    $paymentID = (int)$_POST['paymentID'];
    $totalAmount = (float)$_POST['totalAmount'];
    $amountPay = (float)$_POST['amountPay'];
    $paymentType = $_POST['paymentType'];
    $status = $_POST['status']; // Should be "Settled"

    // Validate settlement
    if ($amountPay < $totalAmount) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Amount paid must equal or exceed total amount to settle'
        ]);
        exit;
    }

    // Update the payment with "Settled" status
    $result = $data->UpdatePayment($paymentID, null, $totalAmount, $amountPay, null, null, $paymentType, $status);

    if ($result === true) {
        // Fetch payment details to get GuestName and InvoiceID
        $paymentDetails = $data->getPaymentById($paymentID);
        if ($paymentDetails) {
            $invoiceID = $paymentDetails['InvoiceID'];
            $guestName = $paymentDetails['GuestName'];
            $totalAmount = $paymentDetails['TotalAmount']; // Ensure consistency

            // Debug: Log the fetched GuestName
            error_log("Settling PaymentID: $paymentID, GuestName: $guestName, TotalAmount: $totalAmount");

            // Connect to fin_general_ledger database
            $glConnection = mysqli_connect("localhost:3307", "root", "", "fin_general_ledger");
            if (!$glConnection) {
                error_log("GL DB Connection Failed: " . mysqli_connect_error());
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to connect to General Ledger database: ' . mysqli_connect_error()
                ]);
                exit;
            }

            // Insert transaction into transactions table
            $transactionFrom = 'Guest';
            $transactionDate = date('Y-m-d H:i:s');
            $entryID = 0; // Adjust based on your logic

            $insertQuery = "INSERT INTO transactions (EntryID, PaymentID, TransactionFrom, TransactionDate, GuestName, TotalAmount) 
                            VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($glConnection, $insertQuery);
            if (!$stmt) {
                error_log("Prepare Failed: " . mysqli_error($glConnection));
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to prepare transaction insert: ' . mysqli_error($glConnection)
                ]);
                mysqli_close($glConnection);
                exit;
            }

            mysqli_stmt_bind_param($stmt, "iissds", $entryID, $paymentID, $transactionFrom, $transactionDate, $guestName, $totalAmount);
            $insertResult = mysqli_stmt_execute($stmt);
            if (!$insertResult) {
                error_log("Insert Failed: " . mysqli_error($glConnection));
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to insert transaction into General Ledger: ' . mysqli_error($glConnection)
                ]);
                mysqli_stmt_close($stmt);
                mysqli_close($glConnection);
                exit;
            }

            mysqli_stmt_close($stmt);
            mysqli_close($glConnection);

            $updatedData = [
                'PaymentID' => $paymentID,
                'InvoiceID' => $invoiceID,
                'TotalAmount' => $totalAmount,
                'AmountPay' => $amountPay,
                'PaymentType' => $paymentType,
                'Status' => $status,
                'GuestName' => $guestName
            ];
            echo json_encode([
                'status' => 'success',
                'message' => 'Payment settled successfully and transaction recorded',
                'data' => $updatedData
            ]);
        } else {
            error_log("Failed to fetch payment details for PaymentID: $paymentID");
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to fetch payment details'
            ]);
        }
    } else {
        error_log("UpdatePayment Failed: $result");
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to settle payment in database: ' . $result
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request'
    ]);
}

exit;
?>