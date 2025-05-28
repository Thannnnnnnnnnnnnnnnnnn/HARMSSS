<?php
include('../config/controller.php');
require('../fpdf/fpdf.php');

// Function to determine payment status
function determinePaymentStatus($amountPay, $totalAmount, $existingStatus) {
    if ($existingStatus === 'Settled') {
        return 'Settled';
    }
    if ($amountPay < $totalAmount) {
        return 'Downpayment';
    }
    return 'Fully Paid';
}

// Generate Invoice (Preview)
if (isset($_POST['generate_invoice'])) {
    $invoiceID = $_POST['invoiceID'];
    $data = new Data();
    $payment = $data->getPaymentByInvoiceID($invoiceID);

    if ($payment) {
        $pdf = new FPDF('P', 'pt', [226, 500]);
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 10);

        $pdf->SetFillColor(245, 231, 200);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(209, 213, 219);

        $pdf->Image('../../images/Logo.png', 83, 10, 60, 60);
        $pdf->Ln(70);

        $pdf->Image('../../images/Logo-Name.png', 63, 80, 100, 20);
        $pdf->Ln(30);

        $pdf->SetFont('Helvetica', '', 8);
        $pdf->Cell(0, 8, '123 Business City, Country', 0, 1, 'C');
        $pdf->Cell(0, 8, 'Tel: +1 (555) 123-4567 | Email: avalon@gmail.com', 0, 1, 'C');
        $pdf->Ln(10);

        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->Cell(0, 15, 'OFFICIAL INVOICE', 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->Cell(0, 12, 'Payment Details', 1, 1, 'C', true);
        $pdf->SetFont('Helvetica', '', 7);

        $colWidth = 84.8;
        $pdf->Cell($colWidth, 12, 'Invoice ID:', 1, 0, 'L');
        $pdf->Cell($colWidth, 12, $payment['InvoiceID'], 1, 1, 'R');
        $pdf->Cell($colWidth, 12, 'Guest Name:', 1, 0, 'L');
        $pdf->Cell($colWidth, 12, $payment['GuestName'], 1, 1, 'R');
        $pdf->Cell($colWidth, 12, 'Check-In:', 1, 0, 'L');
        $pdf->Cell($colWidth, 12, date('Y-m-d H:i', strtotime($payment['StartDate'])), 1, 1, 'R');
        $pdf->Cell($colWidth, 12, 'Check-Out:', 1, 0, 'L');
        $pdf->Cell($colWidth, 12, date('Y-m-d H:i', strtotime($payment['EndDate'])), 1, 1, 'R');
        $pdf->Cell($colWidth, 12, 'Payment Method:', 1, 0, 'L');
        $pdf->Cell($colWidth, 12, $payment['PaymentType'], 1, 1, 'R');
        $pdf->Ln(10);

        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->Cell(0, 12, 'Financial Summary', 1, 1, 'C', true);
        $pdf->SetFont('Helvetica', '', 7);

        $pdf->Cell($colWidth, 12, 'Description', 1, 0, 'L');
        $pdf->Cell($colWidth, 12, 'Amount', 1, 1, 'R');
        $pdf->Cell($colWidth, 12, 'Total Amount', 1, 0, 'L');
        $pdf->Cell($colWidth, 12, 'PHP ' . number_format($payment['TotalAmount'], 2), 1, 1, 'R');
        $pdf->Cell($colWidth, 12, 'Amount Paid', 1, 0, 'L');
        $pdf->Cell($colWidth, 12, 'PHP ' . number_format($payment['AmountPay'], 2), 1, 1, 'R');
        $pdf->Cell($colWidth, 12, 'Status', 1, 0, 'L');
        
        // Determine status dynamically
        $status = determinePaymentStatus($payment['AmountPay'], $payment['TotalAmount'], $payment['Status']);
        $pdf->Cell($colWidth, 12, $status, 1, 1, 'R');
        
        $pdf->Ln(10);

        $pdf->SetFont('Helvetica', 'I', 6);
        $pdf->Cell(0, 10, 'This is a computer-generated invoice. Please retain for your records.', 0, 1, 'C');

        $pdfContent = $pdf->Output('S');
        $pdfBase64 = base64_encode($pdfContent);
        $invoiceID = htmlspecialchars($invoiceID);

        ob_start();
        include('modals/InvoicePreviewModal.php');
        $modalContent = ob_get_clean();

        header('Content-Type: text/html');
        echo $modalContent;
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => "No payment found for Invoice ID: $invoiceID"]);
    }
    exit;
}

// Download Invoice
if (isset($_GET['download_invoice'])) {
    $invoiceID = $_GET['invoice_id'];
    $data = new Data();
    $payment = $data->getPaymentByInvoiceID($invoiceID);

    if ($payment) {
        $pdf = new FPDF('P', 'pt', [226, 500]);
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 10);

        $pdf->SetFillColor(245, 231, 200);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(209, 213, 219);

        $pdf->Image('../../images/Logo.png', 83, 10, 60, 60);
        $pdf->Ln(70);

        $pdf->Image('../../images/Logo-Name.png', 63, 80, 100, 20);
        $pdf->Ln(30);

        $pdf->SetFont('Helvetica', '', 8);
        $pdf->Cell(0, 8, '123 Business City, Country', 0, 1, 'C');
        $pdf->Cell(0, 8, 'Tel: +1 (555) 123-4567 | Email: avalon@gmail.com', 0, 1, 'C');
        $pdf->Ln(10);

        $pdf->SetFont('Helvetica', 'B', 10);
        $pdf->Cell(0, 15, 'OFFICIAL INVOICE', 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->Cell(0, 12, 'Payment Details', 1, 1, 'C', true);
        $pdf->SetFont('Helvetica', '', 7);

        $colWidth = 84.8;
        $pdf->Cell($colWidth, 12, 'Invoice ID:', 1, 0, 'L');
        $pdf->Cell($colWidth, 12, $payment['InvoiceID'], 1, 1, 'R');
        $pdf->Cell($colWidth, 12, 'Guest Name:', 1, 0, 'L');
        $pdf->Cell($colWidth, 12, $payment['GuestName'], 1, 1, 'R');
        $pdf->Cell($colWidth, 12, 'Check-In:', 1, 0, 'L');
        $pdf->Cell($colWidth, 12, date('Y-m-d H:i', strtotime($payment['StartDate'])), 1, 1, 'R');
        $pdf->Cell($colWidth, 12, 'Check-Out:', 1, 0, 'L');
        $pdf->Cell($colWidth, 12, date('Y-m-d H:i', strtotime($payment['EndDate'])), 1, 1, 'R');
        $pdf->Cell($colWidth, 12, 'Payment Method:', 1, 0, 'L');
        $pdf->Cell($colWidth, 12, $payment['PaymentType'], 1, 1, 'R');
        $pdf->Ln(10);

        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->Cell(0, 12, 'Financial Summary', 1, 1, 'C', true);
        $pdf->SetFont('Helvetica', '', 7);
        $pdf->Cell($colWidth, 12, 'Description', 1, 0, 'L');
        $pdf->Cell($colWidth, 12, 'Amount', 1, 1, 'R');
        $pdf->Cell($colWidth, 12, 'Total Amount', 1, 0, 'L');
        $pdf->Cell($colWidth, 12, 'PHP ' . number_format($payment['TotalAmount'], 2), 1, 1, 'R');
        $pdf->Cell($colWidth, 12, 'Amount Paid', 1, 0, 'L');
        $pdf->Cell($colWidth, 12, 'PHP ' . number_format($payment['AmountPay'], 2), 1, 1, 'R');
        $pdf->Cell($colWidth, 12, 'Status', 1, 0, 'L');
        
        // Determine status dynamically
        $status = determinePaymentStatus($payment['AmountPay'], $payment['TotalAmount'], $payment['Status']);
        $pdf->Cell($colWidth, 12, $status, 1, 1, 'R');
        
        $pdf->Ln(10);

        $pdf->SetFont('Helvetica', 'I', 6);
        $pdf->Cell(0, 10, 'This is a computer-generated invoice. Please retain for your records.', 0, 1, 'C');

        $pdf->Output('D', 'invoice_' . $invoiceID . '.pdf');
        exit;
    }
}
?>