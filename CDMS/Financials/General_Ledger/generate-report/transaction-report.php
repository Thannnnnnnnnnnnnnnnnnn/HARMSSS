<?php
require('../asset/fpdf/fpdf.php'); // Include FPDF library

$connection = mysqli_connect("localhost:3307","root","","fin_general_ledger");
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch data from transactions table with relevant columns
$sql = "SELECT TransactionID, EntryID, PaymentID, TransactionFrom, TransactionDate FROM transactions ORDER BY TransactionDate DESC";
$result = mysqli_query($connection, $sql);
$totalTransactions = mysqli_num_rows($result);
$totalAmount = 0;

// Calculate Total Amount if PaymentID is applicable
$amountSql = "SELECT SUM(Amount) AS TotalAmount FROM transactions JOIN journalentries ON transactions.EntryID = journalentries.EntryID WHERE journalentries.EntryType = 'Credit'";
$amountResult = mysqli_query($connection, $amountSql);
if ($amountRow = mysqli_fetch_assoc($amountResult)) {
    $totalAmount = $amountRow['TotalAmount'] ?? 0;
}

// Initialize FPDF
$pdf = new FPDF('P', 'mm', array(210, 297)); // A4 Size (210x297mm)
$pdf->AddPage();

// name - logo
$pdf->Image('../asset/Logo.png', 18, 10, 30, 30);
$pdf->Ln(10);

$pdf->Image('../asset/Logo-Name.png', 55, 15, 100, 18);
$pdf->Ln(10);

$pdf->Image('../asset/Logo.png', 163, 10, 30, 30);
$pdf->Ln(10);

// Header
$pdf->SetFont('Arial','B',16);
$pdf->Cell(190,10,'Hotel & Restaurant',0,1,'C');
$pdf->SetFont('Arial','B',12);
$pdf->Cell(190,10,'Financial Department - General Ledger',0,1,'C');
$pdf->Cell(190,10,'General Ledger Transaction Report',0,1,'C');

// Report Period and Date
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');
$currentDate = date('Y-m-d');

$pdf->SetFont('Arial','',12);
$pdf->Cell(190,10,"Report Period: From $startDate to $endDate",0,1,'C');
$pdf->Cell(190,10,"Generated On: $currentDate",0,1,'C');

// Table Header with centered alignment
$pdf->Ln(10);
$pdf->SetFont('Arial','B',10);

// Calculate table width for centering
$tableWidth = 180;
$startX = ($pdf->GetPageWidth() - $tableWidth) / 2;
$pdf->SetX($startX);

$pdf->Cell(30,10,'Transaction ID',1);
$pdf->Cell(30,10,'Entry ID',1);
$pdf->Cell(30,10,'Payment ID',1);
$pdf->Cell(40,10,'Transaction From',1);
$pdf->Cell(50,10,'Transaction Date',1);
$pdf->Ln();

// Table Data
$pdf->SetFont('Arial','',10);
if ($totalTransactions > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $pdf->SetX($startX);
        $pdf->Cell(30,10,$row['TransactionID'],1);
        $pdf->Cell(30,10,$row['EntryID'],1);
        $pdf->Cell(30,10,$row['PaymentID'],1);
        $pdf->Cell(40,10,$row['TransactionFrom'],1);
        $pdf->Cell(50,10,$row['TransactionDate'],1);
        $pdf->Ln();
    }
} else {
    $pdf->SetX($startX);
    $pdf->Cell(180,10,'No Records Found',1,1,'C');
}

// Summary Section
$pdf->Ln(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(190,10,'Summary:',0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Cell(190,10,"Total Transactions: $totalTransactions",0,1,'C');

// Footer Section aligned to the left
$pdf->Ln(20);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(40,10,'Prepared By:',0,1);
$pdf->SetFont('Arial','',12);
$pdf->Cell(40,10,'Name: Ric Jason E. Altamante');
$pdf->Ln();
$pdf->Cell(40,10,'Position: General Ledger Manager/Staff');
$pdf->Ln();
$pdf->Cell(40,10,"Date: $currentDate");

// Adjust Page Height to Remove Extra White Space
$pdf->SetAutoPageBreak(false);
$pageHeight = $pdf->GetY() + 20;
$pdf->SetY($pageHeight);

// Output PDF
$pdf->Output();
mysqli_close($connection);

// Check if download is requested
if (isset($_GET['download']) && $_GET['download'] === 'true') {
    $pdf->Output('D', 'Transaction_Report.pdf'); // 'D' forces download
} else {
    $pdf->Output('I'); // 'I' displays PDF in browser
}
mysqli_close($connection);

?>