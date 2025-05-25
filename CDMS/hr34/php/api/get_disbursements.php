<?php
/**
 * API Endpoint: Get Disbursements Data
 * Fetches data from disbursementrequests and approvals tables.
 */
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
// Ensure this path is writable:
// ini_set('error_log', dirname(__FILE__) . '/../../../../php-error-financials.log');


header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 

// IMPORTANT: Database Connection for fin_disbursement
// You need to establish a PDO connection to your 'fin_disbursement' database here.
// This might involve a different db_connect.php or direct connection details.
// For this example, $pdo_disbursement is assumed to be your PDO object for this DB.

/* Example Connection (replace with your actual connection logic):
$db_host_disbursement = 'localhost'; // Or your specific host
$db_name_disbursement = 'fin_disbursement'; // From your SQL dump
$db_user_disbursement = 'your_db_user';
$db_pass_disbursement = 'your_db_password';
$charset_disbursement = 'utf8mb4';

$dsn_disbursement = "mysql:host=$db_host_disbursement;dbname=$db_name_disbursement;charset=$charset_disbursement";
$options_disbursement = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
     $pdo_disbursement = new PDO($dsn_disbursement, $db_user_disbursement, $db_pass_disbursement, $options_disbursement);
} catch (\PDOException $e) {
     error_log("PDO Connection Error (fin_disbursement): " . $e->getMessage());
     http_response_code(500);
     echo json_encode(['error' => 'Database connection failed for disbursements.']);
     exit;
}
*/

// --- Placeholder for DB Connection ---
// Replace this with actual connection to 'fin_disbursement'
$pdo_disbursement = null; 
// --- End Placeholder ---

if (!$pdo_disbursement) {
    // This will be triggered if the placeholder above is not replaced
    http_response_code(500);
    echo json_encode(['error' => 'Disbursement database connection not configured in API.']);
    exit;
}


try {
    $stmt_requests = $pdo_disbursement->query("SELECT RequestID, EmployeeID, AllocationID, Amount, DATE_FORMAT(DateOfRequest, '%Y-%m-%d %H:%i:%s') AS DateOfRequest, Status FROM disbursementrequests ORDER BY DateOfRequest DESC");
    $requests = $stmt_requests->fetchAll();

    $stmt_approvals = $pdo_disbursement->query("SELECT ApprovalID, RequestID, AllocationID, Amount, ApproverID, Status, DATE_FORMAT(DateOfApproval, '%Y-%m-%d %H:%i:%s') AS DateOfApproval, RejectReason FROM approvals ORDER BY ApprovalID DESC");
    $approvals = $stmt_approvals->fetchAll();

    echo json_encode([
        'requests' => $requests,
        'approvals' => $approvals
    ]);

} catch (PDOException $e) {
    error_log("API Error (get_disbursements): " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to retrieve disbursement data. ' . $e->getMessage()]);
}
?>
