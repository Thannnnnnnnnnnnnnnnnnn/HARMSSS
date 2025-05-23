<?php
require_once '../../includes/Database.php';

header('Content-Type: application/json');

$db = new Database();
$conn = $db->connect('usm');

if (!isset($_GET['account_id']) || !is_numeric($_GET['account_id'])) {
    echo json_encode(['error' => 'Invalid account ID']);
    exit;
}

$accountId = (int)$_GET['account_id'];
$query = "SELECT * FROM department_accounts WHERE Dept_Accounts_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $accountId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $account = $result->fetch_assoc();
    // Remove or mask password for security
    unset($account['Password']);
    echo json_encode($account);
} else {
    echo json_encode(['error' => 'Account not found']);
}

$stmt->close();
$conn->close();
?>