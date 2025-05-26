<?php 
include('includes/config.php'); 

// Update payment status based on due dates and payment completion
$statusUpdateQuery = "
    UPDATE fin_accounts_payable.payableinvoices pi
    LEFT JOIN fin_accounts_payable.paymentschedules ps ON pi.PayableInvoiceID = ps.PayableInvoiceID
    LEFT JOIN fin_accounts_payable.vendorpayments vp ON pi.PayableInvoiceID = vp.PayableInvoiceID
    SET pi.Status = CASE 
        WHEN vp.PayablePaymentID IS NOT NULL THEN 'Paid'
        WHEN ps.PaymentSchedule < CURDATE() AND vp.PayablePaymentID IS NULL THEN 'Overdue'
        ELSE 'Pending'
    END;
";

if (!$conn_budget->query($statusUpdateQuery)) {
    die("Error updating payment status: " . $conn_budget->error);
}

// Fetch invoice data with payment schedule and payment status
$sql = "
 SELECT 
    pi.PayableInvoiceID, 
    MAX(pi.AccountID) AS AccountID, 
    MAX(pi.BudgetName) AS BudgetName, 
    MAX(pi.Department) AS Department, 
    MAX(pi.Amount) AS Amount, 
    MAX(pi.StartDate) AS StartDate, 
    MAX(vp.PaymentStatus) AS PaymentStatus, 
    MAX(ps.PaymentSchedule) AS PaymentSchedule,
    MAX(vp.PaymentMethod) AS PaymentMethod
FROM fin_accounts_payable.payableinvoices pi
LEFT JOIN fin_accounts_payable.paymentschedules ps ON pi.PayableInvoiceID = ps.PayableInvoiceID
LEFT JOIN fin_accounts_payable.vendorpayments vp ON pi.PayableInvoiceID = vp.PayableInvoiceID
GROUP BY pi.PayableInvoiceID;

";
$result = $conn_budget->query($sql);

if (!$result) {
    die("Error retrieving data: " . $conn_budget->error);
}
?>
<?php include('../includes/head.php'); ?>
<body>
    <div class="flex min-h-screen w-full">
        <!-- Sidebar -->
        <?php include('../includes/sidebar.php'); ?>
        <!-- Main + Navbar -->
        <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
            <!-- Navbar -->
            <?php include('../includes/navbar.php'); ?>
            <!-- Main Content -->
            <main class="px-8 py-8">
                <div class="w-full">
                    <div class="mt-2 overflow-x-auto">
                        <table class="min-w-full bg-white rounded-lg overflow-hidden shadow-md border">
                            <thead class="bg-[#4E3B2A] text-white">
                                <tr>
                                    <th class="py-3 px-2 md:px-4 text-left">Payable Invoice ID</th>
                                    <th class="py-3 px-2 md:px-4 text-left">Budget Name</th>
                                    <th class="py-3 px-2 md:px-4 text-left">Department</th>
                                    <th class="py-3 px-2 md:px-4 text-left">Amount</th>
                                    <th class="py-3 px-2 md:px-4 text-left">Date Created</th>
                                    <th class="py-3 px-2 md:px-4 text-left">Due Date</th>
                                    <th class="py-3 px-2 md:px-4 text-left">Payment Status</th>
                                    <th class="py-3 px-2 md:px-4 text-left">Payment Details</th>
                                    <th class="py-3 px-2 md:px-4 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="dataRows" class="text-gray-700">
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="py-3 px-2 md:px-4"><?= $row['PayableInvoiceID']; ?></td>
                                        <td class="py-3 px-2 md:px-4"><?= $row['BudgetName']; ?></td>
                                        <td class="py-3 px-2 md:px-4"><?= $row['Department']; ?></td>
                                        <td class="py-3 px-2 md:px-4">₱<?= number_format($row['Amount'], 2); ?></td>
                                        <td class="py-3 px-2 md:px-4"><?= $row['StartDate']; ?></td>
                                        <td class="py-3 px-2 md:px-4"><?= $row['PaymentSchedule'] ?? 'Not Scheduled'; ?></td>
                                        <td class="py-3 px-2 md:px-4 
                                            <?php 
                                            switch($row['PaymentStatus']) {
                                                case 'Completed': echo 'text-green-600'; break;
                                                case 'Overdue': echo 'text-red-600'; break;
                                                default: echo 'text-yellow-600'; 
                                            }
                                            ?>">
                                            <?= $row['PaymentStatus']?? 'No Status yet'; ?>
                                        </td>
                                        <td class="py-3 px-2 md:px-4"><?= $row['PaymentMethod'] ?? 'No Payments yet'; ?></td>
                                        <td class="py-3 px-2 md:px-4 text-center">
                                            <a href="invoice/generatePdf.php?id=<?= $row['PayableInvoiceID']; ?>" target="_blank" class="px-2 md:px-4 py-2 bg-red-500 text-xs md:text-lg text-white rounded"><i class="fa-solid fa-file-invoice"></i></a>
                                            <button onclick="openPaymentScheduleModal(<?= $row['PayableInvoiceID'] ?>)" class="px-2 md:px-4 py-2 bg-green-500 text-xs md:text-lg text-white rounded"><i class="fa-solid fa-calendar-days"></i></button>
                                            <button onclick="openPaymentModal(<?= $row['PayableInvoiceID'] ?>)" class="px-2 md:px-4 py-2 bg-blue-500 text-xs md:text-lg text-white rounded"><i class="fa-solid fa-money-check-dollar"></i></button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Payment Schedule Modal -->
                    <div id="paymentScheduleModal" class="fixed inset-0 bg-gray-700 bg-opacity-50 flex items-center justify-center z-50 overflow-auto hidden">
                        <div class="bg-white p-6 shadow-lg rounded-lg w-full max-w-[90%] sm:max-w-[600px]">
                            <div class="flex justify-between items-center mb-4">
                                <h1 class="text-2xl font-semibold">Payment Schedule</h1>
                                <button id="close-modal-btn" class="text-2xl cursor-pointer">
                                    <i class="fa-solid fa-xmark text-2xl"></i>
                                </button>
                            </div>
                            <div class="modal-body-viewAdjust">
                                <form id="paymentForm" action="backend/payment_schedule.php" method="POST" class="flex flex-col gap-4">
                                    <input type="hidden" name="invoice_id" id="schedule_invoice_id">
                                    <label class="block">
                                        <span class="text-gray-700">Due Date:</span>
                                        <input type="date" name="due_date" required class="w-full border p-2 rounded">
                                    </label>
                                    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Save Payment Schedule</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Modal -->
                    <div id="paymentModal" class="fixed inset-0 bg-gray-700 bg-opacity-50 flex items-center justify-center z-50 overflow-auto hidden">
                        <div class="bg-white p-6 shadow-lg rounded-lg w-full max-w-[90%] sm:max-w-[600px]">
                            <div class="flex justify-between items-center mb-4">
                                <h1 class="text-2xl font-semibold">Record Payment</h1>
                                <button id="close-modal-btn" class="text-2xl cursor-pointer">
                                    <i class="fa-solid fa-xmark text-2xl"></i>
                                </button>
                            </div>
                            <div class="modal-body-viewAdjust">
                                <form id="recordPaymentForm" action="backend/payments.php" method="POST" class="flex flex-col gap-4">
                                    <input type="hidden" name="invoice_id" id="payment_invoice_id">
                                    <div class="flex flex-col gap-2">
                                        <label class="block">
                                            <span class="text-gray-700">Payment Date:</span>
                                            <input type="date" name="payment_date" required class="w-full border p-2 rounded">
                                        </label>
                                        <label class="block">
                                            <span class="text-gray-700">Amount Paid:</span>
                                            <input type="number" step="0.01" name="amount_paid" required class="w-full border p-2 rounded">
                                        </label>
                                        <label class="block">
                                            <span class="text-gray-700">Payment Method:</span>
                                            <select name="payment_method" class="w-full border p-2 rounded">
                                                <option value="Cash">Cash</option>
                                                <option value="Bank Transfer">Bank Transfer</option>
                                                <option value="Check">Check</option>
                                            </select>
                                        </label>
                                    </div>
                                    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Record Payment</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="../assets/js.js"></script>
    <script>
        function openPaymentScheduleModal(invoiceId) {
            document.getElementById('schedule_invoice_id').value = invoiceId;
            document.getElementById('paymentScheduleModal').classList.remove('hidden');
        }

        function openPaymentModal(invoiceId) {
            document.getElementById('payment_invoice_id').value = invoiceId;
            document.getElementById('paymentModal').classList.remove('hidden');
        }

        document.querySelectorAll('#close-modal-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.fixed').classList.add('hidden');
            });
        });
    </script>
</body>
</html>