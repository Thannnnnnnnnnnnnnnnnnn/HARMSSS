<?php
include('../config/controller.php');

if (isset($_POST['paymentID'])) {
    $paymentID = $_POST['paymentID'];
    $data = new Data();
    $payment = $data->getPaymentById($paymentID);

    if ($payment) {
        // Recalculate status if not 'Settled' or 'Reservation'
        $status = $payment['Status'];
        if ($status !== 'Settled' && $status !== 'Reservation') {
            $status = ($payment['AmountPay'] < $payment['TotalAmount']) ? 'Downpayment' : 'Fully Paid';
            // Optionally update the database to persist this status
            $data->UpdatePayment($paymentID, null, $payment['TotalAmount'], $payment['AmountPay'], null, null, null, $status);
        }
        $statusIconColor = ($status === 'Settled') ? 'text-gray-500' : ($status === 'Downpayment' ? 'text-red-500' : 'text-green-500');
        $statusTextColor = ($status === 'Settled') ? 'text-gray-600' : ($status === 'Downpayment' ? 'text-red-600' : 'text-green-600');
?>
        <div class="text-gray-800 space-y-6 bg-white p-8 rounded-lg shadow-md border border-gray-200">
            <div class="grid grid-cols-2 gap-6">
                <div class="flex flex-col items-center text-center border-r border-gray-300">
                    <p class="font-medium text-base text-blue-600"><i class="fa-solid fa-file-invoice mr-2"></i>Invoice ID</p>
                    <p class="text-base text-gray-700"><?= htmlspecialchars($payment['InvoiceID']) ?></p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <p class="font-medium text-base text-green-600"><i class="fa-solid fa-user mr-2"></i>Guest Name</p>
                    <p class="text-base text-gray-700"><?= htmlspecialchars($payment['GuestName']) ?></p>
                </div>
            </div>
            <hr class="border-gray-300">
            <div class="grid grid-cols-2 gap-6">
                <div class="flex flex-col items-center text-center border-r border-gray-300">
                    <p class="font-medium text-base text-yellow-600"><i class="fa-solid fa-peso-sign mr-2"></i>Total Amount</p>
                    <p class="text-base text-gray-700 total-amount">₱<?= number_format($payment['TotalAmount'], 2) ?></p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <p class="font-medium text-base text-green-600"><i class="fa-solid fa-money-bill mr-2"></i>Amount Paid</p>
                    <p class="text-base text-gray-700 amount-paid">₱<?= number_format($payment['AmountPay'], 2) ?></p>
                </div>
            </div>
            <hr class="border-gray-300">
            <div class="grid grid-cols-2 gap-6">
                <div class="flex flex-col items-center text-center border-r border-gray-300">
                    <p class="font-medium text-base text-blue-500"><i class="fa-solid fa-calendar-day mr-2"></i>Check-In</p>
                    <p class="text-base text-gray-700"><?= date('Y-m-d H:i', strtotime($payment['StartDate'])) ?></p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <p class="font-medium text-base text-blue-500"><i class="fa-solid fa-calendar-check mr-2"></i>Check-Out</p>
                    <p class="text-base text-gray-700"><?= date('Y-m-d H:i', strtotime($payment['EndDate'])) ?></p>
                </div>
            </div>
            <hr class="border-gray-300">
            <div class="grid grid-cols-2 gap-6">
                <div class="flex flex-col items-center text-center border-r border-gray-300">
                    <p class="font-medium text-base text-purple-600"><i class="fa-solid fa-credit-card mr-2"></i>Payment Method</p>
                    <p class="text-base text-gray-700"><?= htmlspecialchars($payment['PaymentType']) ?></p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <p class="font-medium text-base <?= $statusIconColor ?>"><i class="fa-solid fa-circle-check mr-2"></i>Status</p>
                    <p class="text-base <?= $statusTextColor ?> status-value"><?= htmlspecialchars($status) ?></p>
                </div>
            </div>
        </div>
<?php
    } else {
        echo '<p class="text-red-600 font-medium text-base p-4 bg-red-50 rounded-md border border-red-200 text-center"><i class="fa-solid fa-exclamation-circle mr-2"></i>Payment not found.</p>';
    }
} else {
    echo '<p class="text-red-600 font-medium text-base p-4 bg-red-50 rounded-md border border-red-200 text-center"><i class="fa-solid fa-exclamation-circle mr-2"></i>Invalid request.</p>';
}
?>