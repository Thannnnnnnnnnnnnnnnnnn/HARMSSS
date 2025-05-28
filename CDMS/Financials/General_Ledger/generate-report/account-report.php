<?php
require('../asset/fpdf/fpdf.php');

// Database connection
$connection = mysqli_connect("127.0.0.1","3206_CENTRALIZED_DATABASE","4562526","fin_general_ledger");
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch data from accounts table
$sql = "SELECT AccountID, AccountName, AccountType FROM accounts";
$result = mysqli_query($connection, $sql);

$totalAccounts = 0;
$totalAssets = 0;
$totalLiabilities = 0;

// Calculate totals
while ($row = mysqli_fetch_assoc($result)) {
    $totalAccounts++;
    if ($row['AccountType'] === 'Asset') {
        $totalAssets++;
    } else {
        $totalLiabilities++;
    }
}

mysqli_data_seek($result, 0);

// Initialize FPDF
$pdf = new FPDF();
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
$pdf->Cell(190,10,'General Ledger Accounts Report',0,1,'C');

// Report Period and Date
$currentDate = date('Y-m-d');
$pdf->SetFont('Arial','',12);
$pdf->Cell(190,10,"Generated On: $currentDate",0,1,'C');

// Table Header
$pdf->Ln(10);
$pdf->SetFont('Arial','B',10);
$pdf->SetX(40);
$pdf->Cell(30,10,'Account ID',1);
$pdf->Cell(70,10,'Account Name',1);
$pdf->Cell(40,10,'Account Type',1);
$pdf->Ln();

// Table Data
$pdf->SetFont('Arial','',10);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $pdf->SetX(40);
        $pdf->Cell(30,10,$row['AccountID'],1);
        $pdf->Cell(70,10,$row['AccountName'],1);
        $pdf->Cell(40,10,$row['AccountType'],1);
        $pdf->Ln();
    }
} else {
    $pdf->SetX(40);
    $pdf->Cell(140,10,'No Records Found',1,1,'C');
}

// Summary Section
$pdf->Ln(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(190,10,'Summary:',0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Cell(190,10,"Total Accounts: $totalAccounts",0,1,'C');
$pdf->Cell(190,10,"Total Assets: $totalAssets",0,1,'C');
$pdf->Cell(190,10,"Total Liabilities: $totalLiabilities",0,1,'C');

// Footer Section
$pdf->Ln(20);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(40,10,'Prepared By:',0,1);
$pdf->SetFont('Arial','',12);
$pdf->Cell(40,10,'Name: Ric Jason E. Altamante');
$pdf->Ln();
$pdf->Cell(40,10,'Position: General Ledger Manager/Staff');
$pdf->Ln();
$pdf->Cell(40,10,"Date: $currentDate");

// Output PDF
$pdf->Output();
mysqli_close($connection);
?>
