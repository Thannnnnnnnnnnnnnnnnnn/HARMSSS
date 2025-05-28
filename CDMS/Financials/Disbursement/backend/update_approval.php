<?php
include_once __DIR__ . '/../../Database/connection.php';
$db = new Database();
$conn = $db->connect('fin_disbursement');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$approvalId = $_POST['approvalId'] ?? null;
$status = $_POST['status'] ?? null;
$approverId = $_POST['approverId'] ?? null;
$allocationID = $_POST['allocationId'] ?? null;
$rejectReason = $_POST['rejectReason'] ?? null;


if (!$approvalId || !$status || !$approverId || !$allocationID) {
    die("Error: Missing required fields.");
}


$stmt = $conn->prepare("UPDATE approvals SET Status = ?, DateOfApproval = CURDATE(), ApproverID = ?, AllocationID = ?, RejectReason = ? WHERE ApprovalID = ?");
$stmt->bind_param("siisi", $status, $approverId, $allocationID, $rejectReason, $approvalId);
$stmt->execute();
$stmt->close();

// Retrieve RequestID and Amount from approvals
$stmt = $conn->prepare("SELECT RequestID, Amount ,funding_id ,project_id FROM approvals WHERE ApprovalID = ?");
$stmt->bind_param("i", $approvalId);
$stmt->execute();
$result = $stmt->get_result();
$approval = $result->fetch_assoc();
$stmt->close();

if ($approval) {
    $requestId = $approval['RequestID'];
    $amount = $approval['Amount'];
    $fundsID = $approval['funding_id'];
    $projectID = $approval['project_id'];
    
    // Update disbursement request status
    $stmt = $conn->prepare("UPDATE disbursementrequests SET Status = ? WHERE RequestID = ?");
    $stmt->bind_param("si", $status, $requestId);
    $stmt->execute();
    $stmt->close();
    
    // If approved, update budget allocations and insert into payable invoices
    if ($status === 'Approved') {
        $budgetConn = $db->connect('fin_budget_management');
        
        // Start transaction for budget update
        $budgetConn->begin_transaction();
        
        // Get BudgetName and Department from budget allocations
        $stmt = $budgetConn->prepare("SELECT BudgetName, DepartmentName FROM budgetallocations WHERE AllocationID = ?");
        $stmt->bind_param("i", $allocationID);
        $stmt->execute();
        $budgetResult = $stmt->get_result();
        $budgetData = $budgetResult->fetch_assoc();
        $stmt->close();
        
        if (!$budgetData) {
            $budgetConn->rollback();
            die("Error: Budget allocation data not found.");
        }
        
        $budgetName = $budgetData['BudgetName'];
        $department = $budgetData['DepartmentName'];
        
        // Update budget allocation
        $stmt = $budgetConn->prepare("UPDATE budgetallocations SET AllocatedAmount = AllocatedAmount - ? WHERE AllocationID = ? AND AllocatedAmount >= ?");
        $stmt->bind_param("dii", $amount, $allocationID, $amount);
        
        if (!$stmt->execute()) {
            $budgetConn->rollback();
            die("Error: Failed to update budget allocation.");
        }
        
        // Get DateOfApproval from approvals
        $stmt = $conn->prepare("SELECT DATE_FORMAT(DateOfApproval, '%Y-%m-%d') as DateOfApproval FROM approvals WHERE ApprovalID = ?");
        $stmt->bind_param("i", $approvalId);
        $stmt->execute();
        $approvalResult = $stmt->get_result();
        $approvalData = $approvalResult->fetch_assoc();
        $dateOfApproval = $approvalData['DateOfApproval'];
        $stmt->close();
        
        // Get Employee Type from employees 
        $stmt = $conn->prepare("SELECT e.DepartmentID FROM employees e 
                               JOIN disbursementrequests d ON e.EmployeeID = d.EmployeeID
                               WHERE d.RequestID = ?");
        $stmt->bind_param("i", $requestId);
        $stmt->execute();
        $employeeResult = $stmt->get_result();
        $employeeData = $employeeResult->fetch_assoc();
        $employeeType = $employeeData['Types'] ?? 'Unknown';
        $stmt->close();
        
        // Insert data  to  fin_account_payable database
        $payableConn = $db->connect('fin_accounts_payable');
        
        $stmt = $payableConn->prepare("INSERT INTO payableinvoices 
                                      (BudgetName, Department, Amount, Types, Status, AllocationID ,funding_id,project_id) 
                                      VALUES (?, ?, ?, ?, 'Pending', ?,?,?)");
        $stmt->bind_param("ssdsis", $budgetName, $department, $amount, $employeeType, $allocationID,$fundsID,$projectID);
        
        if (!$stmt->execute()) {
            $budgetConn->rollback();
            die("Error: Failed to insert into payable invoices.");
        }
        $stmt->close();
        
        $budgetConn->commit();
        $payableConn->close();
        $budgetConn->close();
    }
}

$conn->close();

header("Location: ../Approvals.php");
exit();
?>