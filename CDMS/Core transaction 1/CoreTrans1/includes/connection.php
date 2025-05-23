<?php
require_once __DIR__ . "/../../includes/Database.php"; // adjust path if needed

$db = new Database();
$conn = $db->connect("orders"); // use "kitchen", "inventory", etc. based on what you need

$result = $conn->query("SELECT * FROM orders");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Order ID: " . $row["Order_ID"] . " | Total: " . $row["TotalAmount"] . "<br>";
    }
} else {
    echo "No orders found.";
}
?>
