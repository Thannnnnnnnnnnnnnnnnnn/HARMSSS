<?php
require_once __DIR__ . "/../../includes/Database.php";

header('Content-Type: text/html');

$db = new Database();
$conn = $db->connect("orders");

$orderId = intval($_GET['orderId'] ?? 0);
$tableNumber = $_GET['tableNumber'] ?? "—";
$roomNumber = $_GET['roomNumber'] ?? "—";

if ($orderId <= 0) {
    echo "Invalid order ID.";
    exit;
}

// Fetch main order
$orderStmt = $conn->prepare("SELECT CustomerName, TotalAmount, OrderDate FROM orders WHERE OrderID = ?");
$orderStmt->bind_param("i", $orderId);
$orderStmt->execute();
$order = $orderStmt->get_result()->fetch_assoc();

// Fetch items
$itemStmt = $conn->prepare("SELECT MenuName, Quantity, Price, SubTotal, OrderDate, OrderType FROM orderitems WHERE OrderID = ?");
$itemStmt->bind_param("i", $orderId);
$itemStmt->execute();
$items = $itemStmt->get_result();

$orderItemsHTML = "";
$orderType = "";
foreach ($items as $item) {
    $orderType = $item['OrderType']; // Assume all items share same order type
    $orderItemsHTML .= "- {$item['MenuName']} x{$item['Quantity']} @ ₱{$item['Price']} = ₱{$item['SubTotal']} ({$item['OrderDate']})<br>";
}

$locationDetail = ($orderType === "dine-in")
    ? "<p><strong>Table Number:</strong> $tableNumber</p>"
    : (($orderType === "room service") ? "<p><strong>Room Number:</strong> $roomNumber</p>" : "—");

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Printable Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
        }
        .receipt {
            max-width: 700px;
            margin: auto;
        }
        .receipt img {
            max-width: 150px;
            display: block;
            margin: auto;
        }
        .receipt h2 {
            text-align: center;
            margin: 10px 0;
        }
        .receipt .section {
            margin-bottom: 20px;
        }
        @media print {
            body {
                margin: 0;
            }
        }
    </style>
</head>
<body onload="window.print(); window.onafterprint = () => window.close();">
<div class="receipt">
    <img src="../../image/Logo.png" alt="Logo">
    <h2>Order Receipt</h2>

    <div class="section">
        <p><strong>Order ID:</strong> <?= $orderId ?></p>
        <p><strong>Customer Name:</strong> <?= htmlspecialchars($order['CustomerName']) ?></p>
        <p><strong>Order Type:</strong> <?= htmlspecialchars($orderType) ?></p>
        <?= $locationDetail ?>
    </div>

    <hr>

    <div class="section">
        <h3>Items Ordered:</h3>
        <div style="font-size: 14px; line-height: 1.6;">
            <?= $orderItemsHTML ?>
        </div>
    </div>

    <div class="section" style="text-align: right;">
        <h3>Total: ₱<?= number_format($order['TotalAmount'], 2) ?></h3>
    </div>
</div>
</body>
</html>
