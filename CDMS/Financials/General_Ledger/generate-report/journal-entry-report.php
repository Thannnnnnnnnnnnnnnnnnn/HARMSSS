<?php
require('../asset/fpdf/fpdf.php');

// Database connection
$connection = mysqli_connect("localhost:3307","root","","fin_general_ledger");
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch data from journalentries table with relevant columns
$sql = "SELECT journalentries.EntryID, accounts.AccountName, journalentries.EntryType, journalentries.Amount, journalentries.Description, journalentries.EntryDate FROM journalentries INNER JOIN accounts ON journalentries.AccountID = accounts.AccountID ORDER BY journalentries.EntryDate DESC";
$result = mysqli_query($connection, $sql);

$totalDebits = 0;
$totalCredits = 0;

// Calculate total debits and credits
while ($row = mysqli_fetch_assoc($result)) {
    if ($row['EntryType'] === 'Debit') {
        $totalDebits += $row['Amount'];
    } else {
        $totalCredits += $row['Amount'];
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
$pdf->Cell(190,10,'General Ledger Journal Entry Report',0,1,'C');

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

// Adjust column sizes to fit within the 190mm page width
$pdf->SetX(10); // Adjust starting position to center the table
$pdf->Cell(20,10,'Entry ID',1);
$pdf->Cell(40,10,'Account Name',1);
$pdf->Cell(30,10,'Entry Type',1);
$pdf->Cell(30,10,'Amount (Php)',1);
$pdf->Cell(40,10,'Description',1);
$pdf->Cell(30,10,'Entry Date',1);
$pdf->Ln();

// Table Data
$pdf->SetFont('Arial','',10);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $formattedDate = date('Y-m-d', strtotime($row['EntryDate']));
        $pdf->SetX(10);
        $pdf->Cell(20,10,$row['EntryID'],1);
        $pdf->Cell(40,10,$row['AccountName'],1);
        $pdf->Cell(30,10,$row['EntryType'],1);
        $pdf->Cell(30,10,'Php '.number_format($row['Amount'], 2),1);
        $pdf->Cell(40,10,$row['Description'],1);
        $pdf->Cell(30,10,$formattedDate,1);
        $pdf->Ln();
    }
} else {
    $pdf->SetX(10);
    $pdf->Cell(190,10,'No Records Found',1,1,'C');
}

// Summary Section
$pdf->Ln(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(190,10,'Summary:',0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Cell(190,10,"Total Debits: Php ".number_format($totalDebits, 2),0,1,'C');
$pdf->Cell(190,10,"Total Credits: Php ".number_format($totalCredits, 2),0,1,'C');

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

// Output PDF
$pdf->Output();
mysqli_close($connection);
?>
