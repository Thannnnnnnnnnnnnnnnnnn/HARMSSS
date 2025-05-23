<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

require_once __DIR__ . "/../../includes/Database.php";

$db = new Database();
$conn = $db->connect("orders");

// Decode incoming JSON
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

// Extract and sanitize inputs
$customerName = $conn->real_escape_string($data['customerName'] ?? '');
$totalAmount = floatval($data['totalAmount'] ?? 0);
$cartItems = $data['cartItems'] ?? [];
$orderDate = date('Y-m-d H:i:s');

// Validate required data
if (empty($customerName) || empty($cartItems) || $totalAmount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Insert into orders
$insertOrder = $conn->prepare("
    INSERT INTO orders (posid, CustomerName, TotalAmount, OrderDate)
    VALUES (1, ?, ?, ?)
");
if (!$insertOrder) {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare order insert statement']);
    exit;
}

$insertOrder->bind_param("sds", $customerName, $totalAmount, $orderDate);
if (!$insertOrder->execute()) {
    echo json_encode(['success' => false, 'message' => 'Failed to insert order']);
    exit;
}
$orderId = $conn->insert_id;

// Prepare orderitems insert
$stmt = $conn->prepare("INSERT INTO orderitems 
(OrderID, MenuItemID, OrderType, Location, MenuName, Quantity, Price, SubTotal, OrderDate) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare orderitems insert statement']);
    exit;
}

foreach ($cartItems as $item) {
    $menuItemId = intval($item['menuItemId'] ?? 0);
    $menuName = $conn->real_escape_string($item['menuName'] ?? '');
    $quantity = intval($item['quantity'] ?? 0);
    $orderType = $conn->real_escape_string($item['orderType'] ?? '');
    $price = floatval($item['price'] ?? 0);
    $subTotal = floatval($item['subTotal'] ?? 0);
    $itemOrderDateRaw = trim($item['orderDate'] ?? '');
    $itemOrderDate = (strtotime($itemOrderDateRaw) !== false) 
        ? date('Y-m-d H:i:s', strtotime($itemOrderDateRaw)) 
        : $orderDate;
    $orderLocation = $conn->real_escape_string($item['orderLocation'] ?? ''); // Table/Room number

    // Bind and insert
    $stmt->bind_param("iisssidds", 
        $orderId, 
        $menuItemId, 
        $orderType, 
        $orderLocation,  // Table/Room number here
        $menuName, 
        $quantity, 
        $price, 
        $subTotal, 
        $itemOrderDate
    );

    if (!$stmt->execute()) {
        echo json_encode([ 
            'success' => false, 
            'message' => 'Insert item failed: ' . $stmt->error 
        ]);
        exit;
    }
}
$stmt->close();

// Insert into payment_transactions
$insertPayment = $conn->prepare("
    INSERT INTO payment_transactions (OrderID, Location, Amount, TransactionDate)
    VALUES (?, ?, ?, ?)
");
if (!$insertPayment) {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare payment transaction insert statement']);
    exit;
}

// Use the last cart item location for now (you can change this to something else if needed)
$orderLocation = $cartItems[0]['orderLocation']; // Or choose any other logic to determine the location
$insertPayment->bind_param("isds", $orderId, $orderLocation, $totalAmount, $orderDate);
if (!$insertPayment->execute()) {
    echo json_encode(['success' => false, 'message' => 'Failed to insert payment transaction']);
    exit;
}

$insertPayment->close();

// Return response
echo json_encode([
    'success' => true,
    'message' => 'Order saved successfully!',
    'orderId' => $orderId,
    'totalAmount' => $totalAmount
]);

?>
