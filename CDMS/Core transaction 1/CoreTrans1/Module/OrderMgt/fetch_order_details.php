<?php
header('Content-Type: application/json');
require_once __DIR__ . "/../../includes/Database.php";

$dbOrders = new Database();
$connOrders = $dbOrders->connect("orders");

if (!$connOrders) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$orderId = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($orderId <= 0) {
    echo json_encode(['error' => 'Invalid order ID']);
    exit;
}

$stmt = $connOrders->prepare("SELECT Order_ID, CustomerName, TotalAmount, OrderDate FROM orders WHERE Order_ID = ?");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmt = $connOrders->prepare("SELECT ItemName, Quantity, Price FROM orderitems WHERE OrderID = ?");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$orderItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$stmt = $connOrders->prepare("SELECT TransactionID, Amount, TransactionDate FROM payment_transactions WHERE OrderID = ?");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$paymentTransactions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

echo json_encode([
    'order' => $order ?: [],
    'orderItems' => $orderItems,
    'paymentTransactions' => $paymentTransactions
]);
?>