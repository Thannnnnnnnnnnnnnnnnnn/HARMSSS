<?php
require('fpdf/fpdf.php');

// Database connection
$mysqli = new mysqli("localhost", "root", "", "cr1_order_management_with_pos", 3307);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get latest order
$result = $mysqli->query("SELECT * FROM orders ORDER BY Order_ID DESC LIMIT 1");
$order = $result->fetch_assoc();
$orderID = $order['Order_ID'];
$customerName = $order['CustomerName'];
$totalAmount = $order['TotalAmount'];
$orderDate = $order['OrderDate'];

// Get Order Type from orderitems table
$typeResult = $mysqli->query("SELECT DISTINCT OrderType FROM orderitems WHERE OrderID = $orderID LIMIT 1");
$orderTypeRow = $typeResult->fetch_assoc();
$orderType = $orderTypeRow ? $orderTypeRow['OrderType'] : 'N/A';

// Get location (Table or Room number)
$locationQuery = $mysqli->query("SELECT Location FROM orderitems WHERE OrderID = $orderID LIMIT 1");
$locationRow = $locationQuery->fetch_assoc();
$location = $locationRow ? $locationRow['Location'] : 'N/A';

// Get order items
$items = $mysqli->query("SELECT MenuItemID, MenuName, Location, Quantity, Price, SubTotal, OrderDate FROM orderitems WHERE OrderID = $orderID");

// PDF setup
$pdf = new FPDF();
$pdf->AddPage();

// Set the image (Logo placed on the right side)
$logoWidth = 40; // Width of the logo
$logoHeight = 40; // Height of the logo
$pdf->Image('../../image/Logo.png', $pdf->GetPageWidth() - $logoWidth - 10, 10, $logoWidth, $logoHeight); // Set the logo to the right side with margin



// Order Info
$pdf->SetFont('Arial', 'B', 20);
$pdf->Cell(0, 10, 'Restaurant Order Receipt', 0, 1, 'C'); // Change title to "Restaurant Order Receipt"
$pdf->SetFont('Arial', '', 12);
$pdf->Ln(10);

// Order Info
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 8, "Order ID: $orderID", 0, 1);
$pdf->Cell(0, 8, "Customer Name: $customerName", 0, 1);
$pdf->Cell(0, 8, "Order Type: $orderType", 0, 1);
$pdf->Cell(0, 8, "Location: $location", 0, 1); // Display Location
$pdf->Cell(0, 8, "Order Date: $orderDate", 0, 1);
$pdf->Ln(5);

// Table Header
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(247, 230, 202); // Light beige
$pdf->Cell(25, 10, 'Item ID', 1, 0, 'C', true);
$pdf->Cell(60, 10, 'Menu Name', 1, 0, 'C', true);
$pdf->Cell(20, 10, 'Qty', 1, 0, 'C', true);
$pdf->Cell(25, 10, 'Price', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Subtotal', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Date', 1, 1, 'C', true);

// Table Rows
$pdf->SetFont('Arial', '', 11);
$rowColor = false; // Flag to toggle row color
while ($row = $items->fetch_assoc()) {
    $fill = $rowColor ? true : false;
    $rowColor = !$rowColor; // Toggle the flag for the next row
    $pdf->Cell(25, 10, $row['MenuItemID'], 1, 0, 'C', $fill);
    $pdf->Cell(60, 10, $row['MenuName'], 1, 0);
    $pdf->Cell(20, 10, $row['Quantity'], 1, 0, 'C', $fill);
    $pdf->Cell(25, 10, '' . number_format($row['Price'], 2), 1, 0, 'R');
    $pdf->Cell(30, 10, '' . number_format($row['SubTotal'], 2), 1, 0, 'R');
    $pdf->Cell(30, 10, $row['OrderDate'], 1, 1, 'C');
}

// Total
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(130); // Move to the right
$pdf->Cell(30, 10, 'Total:', 1, 0, 'R');
$pdf->Cell(30, 10, '' . number_format($totalAmount, 2), 1, 1, 'R');

// Thank you message
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, 'Thank you for your order!', 0, 1, 'C');

// Output the PDF
$pdf->Output('I', 'receipt.pdf');

?>
