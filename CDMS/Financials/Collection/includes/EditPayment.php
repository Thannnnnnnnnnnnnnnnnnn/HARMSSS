<?php
include('../config/controller.php');

if (isset($_POST['paymentID'])) {
    $paymentID = (int)$_POST['paymentID'];
    $data = new Data();
    $payment = $data->getPaymentById($paymentID);

    if ($payment) {
        ?>
        <form id="editPaymentForm" method="POST" action="./includes/UpdatePayment.php" class="space-y-4">
            <input type="hidden" name="paymentID" id="editPaymentID" value="<?= $payment['PaymentID'] ?>">
            <div class="flex flex-col">
                <label class="text-sm font-semibold text-gray-800 mb-1"><i class="fa-solid fa-user mr-2 text-green-500"></i>Guest Name</label>
                <input type="text" name="guestName" id="editGuestName" value="<?= htmlspecialchars($payment['GuestName']) ?>" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 bg-gray-50" required>
            </div>
            <div class="flex flex-col">
                <label class="text-sm font-semibold text-gray-800 mb-1"><i class="fa-solid fa-peso-sign mr-2 text-yellow-500"></i>Total Amount</label>
                <input type="number" step="0.01" name="totalAmount" id="editTotalAmount" value="<?= $payment['TotalAmount'] ?>" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-gray-50" required>
            </div>
            <div class="flex flex-col">
                <label class="text-sm font-semibold text-gray-800 mb-1"><i class="fa-solid fa-money-bill mr-2 text-green-500"></i>Amount Paid</label>
                <input type="number" step="0.01" name="amountPay" id="editAmountPay" value="<?= $payment['AmountPay'] ?>" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 bg-gray-50" required>
            </div>
            <div class="flex flex-col">
                <label class="text-sm font-semibold text-gray-800 mb-1"><i class="fa-solid fa-calendar-day mr-2 text-blue-500"></i>Check-In Date & Time</label>
                <input type="datetime-local" name="startDate" id="editStartDate" value="<?= date('Y-m-d\TH:i', strtotime($payment['StartDate'])) ?>" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50" required>
            </div>
            <div class="flex flex-col">
                <label class="text-sm font-semibold text-gray-800 mb-1"><i class="fa-solid fa-calendar-check mr-2 text-blue-500"></i>Check-Out Date & Time</label>
                <input type="datetime-local" name="endDate" id="editEndDate" value="<?= date('Y-m-d\TH:i', strtotime($payment['EndDate'])) ?>" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50" required>
            </div>
            <div class="flex flex-col">
                <label class="text-sm font-semibold text-gray-800 mb-1"><i class="fa-solid fa-credit-card mr-2 text-purple-500"></i>Payment Method</label>
                <select name="paymentType" id="editPaymentType" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-50" required>
                    <option value="Credit/Debit" <?= $payment['PaymentType'] === 'Credit/Debit' ? 'selected' : '' ?>>Credit/Debit</option>
                    <option value="Cash" <?= $payment['PaymentType'] === 'Cash' ? 'selected' : '' ?>>Cash</option>
                </select>
            </div>
            <div class="flex flex-col sm:flex-row justify-end gap-2 pt-2">
                <button type="button" id="close-edit-btn" class="w-full sm:w-auto px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 transition-colors flex items-center justify-center">
                    <i class="fa-solid fa-times mr-2 text-gray-600"></i>Cancel
                </button>
                <button type="submit" name="update" class="w-full sm:w-auto px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors flex items-center justify-center">
                    <i class="fa-solid fa-check mr-2"></i>Save
                </button>
            </div>
        </form>
        <script>
            document.getElementById('close-edit-btn').addEventListener('click', () => {
                document.getElementById('editPaymentModal').classList.add('hidden');
            });
        </script>
        <?php
    } else {
        echo "Payment not found.";
    }
} else {
    echo "No payment ID provided.";
}
?>