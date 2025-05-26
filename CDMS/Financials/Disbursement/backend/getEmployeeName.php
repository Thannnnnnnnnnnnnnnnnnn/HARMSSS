<?php


include_once __DIR__ . '/../../Database/connection.php';

$db = new Database();
$conn = $db->connect('fin_disbursement'); 

$employeeId = $_GET['employeeId'];

$stmt = $conn->prepare("SELECT FirstName, Types FROM employees WHERE EmployeeID = ?");
$stmt->bind_param("i", $employeeId);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

if ($employee) {
    echo json_encode(['success' => true, 'employeeName' => $employee['FirstName'],'type' => $employee['Types'] ]);
} else {
    echo json_encode(['success' => false]);
}

$stmt->close();
$conn->close();
?>
