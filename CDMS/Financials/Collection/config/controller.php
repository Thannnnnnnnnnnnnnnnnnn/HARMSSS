<?php
// Start output buffering to prevent stray output
ob_start();


include_once __DIR__ . '/../../Database/connection.php';

class Data {
    public $conn; 

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect("fin_collection");
        if (!$this->conn) {
            throw new Exception("Database connection failed: " . $db->getLastError());
        }
    }

    // Fetch all payments with joined data, including IsViewed
    public function ViewCollectionPayments() {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    cp.PaymentID,
                    cp.InvoiceID,
                    cp.TotalAmount,
                    cp.AmountPay,
                    i.GuestName,
                    rs.StartDate,
                    rs.EndDate,
                    pm.PaymentType,
                    ar.Status,
                    ar.IsViewed
                FROM collection_payments cp
                INNER JOIN invoices i ON cp.InvoiceID = i.InvoiceID
                INNER JOIN receivableschedule rs ON cp.InvoiceID = rs.InvoiceID
                INNER JOIN paymentmethods pm ON cp.InvoiceID = pm.InvoiceID
                INNER JOIN acct_receivable ar ON cp.InvoiceID = ar.InvoiceID
            ");
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $data;
        } catch (Exception $e) {
            error_log("ViewCollectionPayments failed: " . $e->getMessage());
            return [];
        }
    }

    // Create a new payment with transaction safety, including IsViewed
    public function CreateCollectionPayment($guestName, $totalAmount, $amountPay, $startDate, $endDate, $paymentType, $status) {
        $this->conn->begin_transaction();
        try {
            // Validate inputs
            if (empty($guestName) || $totalAmount <= 0 || $amountPay < 0 || empty($startDate) || empty($endDate) || empty($paymentType) || empty($status)) {
                throw new Exception("Invalid input data provided.");
            }

            // Determine IsViewed based on status
            $isViewed = ($status === 'Reservation') ? 0 : 1; // Unseen for reservations

            // Insert into invoices
            $stmt = $this->conn->prepare("INSERT INTO invoices (GuestName) VALUES (?)");
            $stmt->bind_param("s", $guestName);
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert into invoices: " . $stmt->error);
            }
            $invoiceID = $this->conn->insert_id;

            // Insert into collection_payments
            $stmt = $this->conn->prepare("
                INSERT INTO collection_payments (InvoiceID, TotalAmount, AmountPay) 
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("idd", $invoiceID, $totalAmount, $amountPay);
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert into collection_payments: " . $stmt->error);
            }
            $paymentID = $this->conn->insert_id;

            // Insert into receivableschedule
            $stmt = $this->conn->prepare("INSERT INTO receivableschedule (InvoiceID, StartDate, EndDate) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $invoiceID, $startDate, $endDate);
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert into receivableschedule: " . $stmt->error);
            }

            // Insert into paymentmethods
            $stmt = $this->conn->prepare("INSERT INTO paymentmethods (InvoiceID, PaymentType) VALUES (?, ?)");
            $stmt->bind_param("is", $invoiceID, $paymentType);
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert into paymentmethods: " . $stmt->error);
            }

            // Insert into acct_receivable with IsViewed
            $stmt = $this->conn->prepare("INSERT INTO acct_receivable (InvoiceID, Status, IsViewed) VALUES (?, ?, ?)");
            $stmt->bind_param("isi", $invoiceID, $status, $isViewed);
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert into acct_receivable: " . $stmt->error);
            }

            $this->conn->commit();
            return ['PaymentID' => $paymentID, 'InvoiceID' => $invoiceID, 'Status' => $status, 'IsViewed' => $isViewed];
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("CreateCollectionPayment failed: " . $e->getMessage());
            return $e->getMessage();
        }
    }

    // Fetch payment by PaymentID
    public function getPaymentById($id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    cp.PaymentID,
                    cp.InvoiceID,
                    cp.TotalAmount,
                    cp.AmountPay,
                    i.GuestName,
                    rs.StartDate,
                    rs.EndDate,
                    pm.PaymentType,
                    ar.Status,
                    ar.IsViewed
                FROM collection_payments cp 
                INNER JOIN invoices i ON cp.InvoiceID = i.InvoiceID 
                INNER JOIN receivableschedule rs ON cp.InvoiceID = rs.InvoiceID 
                INNER JOIN paymentmethods pm ON cp.InvoiceID = pm.InvoiceID 
                INNER JOIN acct_receivable ar ON cp.InvoiceID = ar.InvoiceID 
                WHERE cp.PaymentID = ?
            ");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $result ?: false;
        } catch (Exception $e) {
            error_log("getPaymentById failed: " . $e->getMessage());
            return false;
        }
    }

    // Fetch payment by InvoiceID
    public function getPaymentByInvoiceID($invoiceID) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    cp.PaymentID,
                    cp.InvoiceID,
                    cp.TotalAmount,
                    cp.AmountPay,
                    i.GuestName,
                    rs.StartDate,
                    rs.EndDate,
                    pm.PaymentType,
                    ar.Status,
                    ar.IsViewed
                FROM collection_payments cp 
                INNER JOIN invoices i ON cp.InvoiceID = i.InvoiceID 
                INNER JOIN receivableschedule rs ON cp.InvoiceID = rs.InvoiceID 
                INNER JOIN paymentmethods pm ON cp.InvoiceID = pm.InvoiceID 
                INNER JOIN acct_receivable ar ON cp.InvoiceID = ar.InvoiceID 
                WHERE cp.InvoiceID = ?
            ");
            $stmt->bind_param("i", $invoiceID);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $result ?: false;
        } catch (Exception $e) {
            error_log("getPaymentByInvoiceID failed: " . $e->getMessage());
            return false;
        }
    }

    // Update payment with optional fields and status recalculation
    public function UpdatePayment($paymentID, $guestName = null, $totalAmount = null, $amountPay = null, $startDate = null, $endDate = null, $paymentType = null, $status = null) {
        $this->conn->begin_transaction();
        try {
            $payment = $this->getPaymentById($paymentID);
            if (!$payment) {
                throw new Exception("PaymentID $paymentID not found.");
            }
            $invoiceID = $payment['InvoiceID'];
    
            // Update collection_payments
            $totalAmount = $totalAmount ?? $payment['TotalAmount'];
            $amountPay = $amountPay ?? $payment['AmountPay'];
            if ($totalAmount <= 0 || $amountPay < 0) {
                throw new Exception("Invalid total amount or amount paid.");
            }
            $stmt = $this->conn->prepare("
                UPDATE collection_payments 
                SET TotalAmount = ?, AmountPay = ?
                WHERE PaymentID = ?
            ");
            $stmt->bind_param("ddi", $totalAmount, $amountPay, $paymentID);
            if (!$stmt->execute()) {
                throw new Exception("Failed to update collection_payments: " . $stmt->error);
            }
    
            // Update invoices
            if ($guestName !== null) {
                $stmt = $this->conn->prepare("UPDATE invoices SET GuestName = ? WHERE InvoiceID = ?");
                $stmt->bind_param("si", $guestName, $invoiceID);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to update invoices: " . $stmt->error);
                }
            }
    
            // Update receivableschedule
            if ($startDate !== null && $endDate !== null) {
                $startDateTime = new DateTime($startDate);
                $endDateTime = new DateTime($endDate);
                if ($endDateTime < $startDateTime) {
                    throw new Exception("Check-Out cannot be before Check-In.");
                }
                $stmt = $this->conn->prepare("UPDATE receivableschedule SET StartDate = ?, EndDate = ? WHERE InvoiceID = ?");
                $stmt->bind_param("ssi", $startDate, $endDate, $invoiceID);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to update receivableschedule: " . $stmt->error);
                }
            }
    
            // Update paymentmethods
            if ($paymentType !== null) {
                $stmt = $this->conn->prepare("UPDATE paymentmethods SET PaymentType = ? WHERE InvoiceID = ?");
                $stmt->bind_param("si", $paymentType, $invoiceID);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to update paymentmethods: " . $stmt->error);
                }
            }
    
            // Update acct_receivable with provided status
            if ($status !== null) {
                $isViewed = ($status === 'Reservation') ? 0 : 1; // Reset IsViewed for new reservations
                $stmt = $this->conn->prepare("UPDATE acct_receivable SET Status = ?, IsViewed = ? WHERE InvoiceID = ?");
                $stmt->bind_param("sii", $status, $isViewed, $invoiceID);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to update acct_receivable: " . $stmt->error);
                }
            } else {
                // If no status provided, calculate based on amounts (unless settled)
                $currentStatus = $payment['Status'];
                if ($currentStatus !== 'Settled') {
                    $newStatus = ($amountPay < $totalAmount) ? 'Downpayment' : 'Fully Paid';
                    $stmt = $this->conn->prepare("UPDATE acct_receivable SET Status = ? WHERE InvoiceID = ?");
                    $stmt->bind_param("si", $newStatus, $invoiceID);
                    if (!$stmt->execute()) {
                        throw new Exception("Failed to update acct_receivable status: " . $stmt->error);
                    }
                }
            }
    
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("UpdatePayment failed: " . $e->getMessage());
            return $e->getMessage();
        }
    }

    // Delete payment with transaction safety
    public function DeleteCollectionPayment($id) {
        $this->conn->begin_transaction();
        try {
            $payment = $this->getPaymentById($id);
            if (!$payment) {
                throw new Exception("PaymentID $id not found.");
            }
            $invoiceID = $payment['InvoiceID'];

            $tables = [
                'collection_payments' => "DELETE FROM collection_payments WHERE PaymentID = ?",
                'receivableschedule' => "DELETE FROM receivableschedule WHERE InvoiceID = ?",
                'paymentmethods' => "DELETE FROM paymentmethods WHERE InvoiceID = ?",
                'acct_receivable' => "DELETE FROM acct_receivable WHERE InvoiceID = ?",
                'invoices' => "DELETE FROM invoices WHERE InvoiceID = ?"
            ];

            foreach ($tables as $table => $query) {
                $stmt = $this->conn->prepare($query);
                $param = ($table === 'collection_payments') ? $id : $invoiceID;
                $stmt->bind_param("i", $param);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to delete from $table: " . $stmt->error);
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("DeleteCollectionPayment failed: " . $e->getMessage());
            return $e->getMessage();
        }
    }
}

// Clear buffer to ensure no stray output
ob_end_clean();
?>