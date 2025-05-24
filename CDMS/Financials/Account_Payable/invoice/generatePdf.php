<?php
include_once(__DIR__ . '/../fpdf186/fpdf.php');
include_once(__DIR__ . '/../includes/config.php');

class InvoicePDF extends FPDF {
    private $companyName = 'Avalon';
    private $companyAddress = '123 Business Street, City, Country';
    private $companyContact = 'Tel: +1 (555) 123-4567 | Email: avalon@gmail.com';

    function Header() {
        // Logo
        $logo = __DIR__ . '/../images/Logo.png'; 
        $logoName = __DIR__ . '/../images/Logo-Name.png';
        $this->Image($logo, 10, 10, 40);
        $this->Image($logoName, 50, 25, 40);

        // Company Details
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(0, 102, 204);     
        $this->Cell(0, 10, $this->companyName, 0, 1, 'R');

        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 6, $this->companyAddress, 0, 1, 'R');
        $this->Cell(0, 6, $this->companyContact, 0, 1, 'R');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function SectionHeader($title) {
        $this->SetFillColor(0, 102, 204); 
        $this->SetTextColor(255, 255, 255); 
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, $title, 0, 1, 'L', true);
        $this->Ln(2);
        $this->SetTextColor(0, 0, 0); 
    }

   function InvoiceDetails($row) {
    // Invoice Header
    $this->SetFont('Arial', 'B', 16);
    $this->SetTextColor(0, 102, 204);
    $this->Cell(0, 12, 'OFFICIAL INVOICE', 0, 1, 'C');
    $this->SetTextColor(0, 0, 0);
    $this->Ln(5);

    // Invoice Details Section
    $this->SectionHeader('Invoice Details');

    $this->SetFont('Arial', '', 10);
    $details = [
        'Invoice ID' => $row['PayableInvoiceID'],
        'Account ID' => $row['AccountID'] ?? 'N/A',
        'Budget Name' => $row['BudgetName'],
        'Department' => $row['Department'],
        'Types' => $row['Types'],
        'Date' => $row['StartDate'],
        'Payment Status' => $row['PaymentStatus'] ?? 'No Payment Status yet',
        'Payment Schedule' => $row['PaymentSchedule'] ?? 'No Payment Schedule yet'
    ];

    foreach ($details as $label => $value) {
        $this->Cell(60, 7, $label . ':', 0, 0);
        $this->Cell(0, 7, $value, 0, 1);
    }

    function generateReferenceCode() {
        $letters = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 2);
        $numbers = rand(100000, 999999);
        $extraLetter = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 1);
        return $letters . $numbers . $extraLetter;
    }    

    $this->Ln(10);
    $this->SectionHeader('Financial Summary');
    $referenceCode = generateReferenceCode();
    
    $financials = [
        'Total Approved Amount' => 'php' . number_format($row['Amount'], 2),
        'Reference Code' => $referenceCode
    ];

    // Table Header
    $this->SetFillColor(230, 230, 230);
    $this->SetFont('Arial', 'B', 10);
    $this->Cell(90, 8, 'Description', 1, 0, 'C', true);
    $this->Cell(90, 8, 'Amount', 1, 1, 'C', true);

    // Table Data
    $this->SetFont('Arial', '', 10);
    $fill = false;
    foreach ($financials as $label => $value) {
        $this->SetFillColor(245, 245, 245); 
        $this->Cell(90, 8, $label, 1, 0, 'L', $fill);
        $this->Cell(90, 8, $value, 1, 1, 'R', $fill);
        $fill = !$fill; 
    }

    // Notes Section
    $this->Ln(10);
    $this->SetFont('Arial', 'I', 10);
    $this->MultiCell(0, 7, 'Notes: This is a computer-generated invoice. Please retain for your records.', 'T', 'L');
}

}

$invoiceID = $_GET['id'] ?? null;
if (!$invoiceID) {
    die("Invalid Invoice ID.");
}

$query = "SELECT pi.*, 
                 vp.PaymentStatus, 
                 ps.PaymentSchedule 
          FROM fin_accounts_payable.payableinvoices pi
          LEFT JOIN fin_accounts_payable.vendorpayments vp 
              ON pi.PayableInvoiceID = vp.PayableInvoiceID
          LEFT JOIN fin_accounts_payable.paymentschedules ps 
              ON pi.PayableInvoiceID = ps.PayableInvoiceID
          WHERE pi.PayableInvoiceID = ?";

$stmt = $conn_budget->prepare($query);
$stmt->bind_param("i", $invoiceID);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("Invoice not found.");
}


// Create PDF
$pdf = new InvoicePDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->InvoiceDetails($row);
$pdf->Output();
?>
 