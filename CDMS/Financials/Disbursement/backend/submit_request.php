<?php

$host = '127.0.0.1';
$db = 'fin_disbursement';
$user = '3206_CENTRALIZED_DATABASE'; 
$pass = '4562526'; 


$conn = new mysqli($host, $user, $pass, $db);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$employeeId = $_POST['employeeId'];
$budgetId = $_POST['budgetId'];
$amount = $_POST['amount'];


$stmt = $conn->prepare("INSERT INTO disbursementrequests (EmployeeID, AllocationID, Amount, Status, DateOfRequest) VALUES (?, ?, ?, 'Pending', NOW())");
$stmt->bind_param("iid", $employeeId, $budgetId, $amount);
$stmt->execute();
$requestId = $stmt->insert_id;


$stmt = $conn->prepare("INSERT INTO approvals (AllocationID, RequestID, Amount, Status) VALUES (?, ?, ?, 'Pending')");
$stmt->bind_param("iid", $budgetId, $requestId, $amount);
$stmt->execute();


  
header("Location: ../Disbursement_Request.php");
exit();

$stmt->close();
$conn->close();
?>