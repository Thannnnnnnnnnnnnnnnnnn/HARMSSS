<?php
include('config/controller.php');
require('./fpdf/fpdf.php');

$data = new Data();
$collections = $data->ViewCollectionPayments();
$dataController = $data;

if (isset($error)) {
    echo "<div class='mt-2 p-2 bg-red-100 text-red-700 rounded'>$error</div>";
}
if (isset($invoice_error)) {
    echo "<div class='mt-2 p-2 bg-red-100 text-red-700 rounded'>$invoice_error</div>";
}

// Count reservations and unseen reservations
$today = new DateTime();
$reservations = [];
$unseenReservationsCount = 0;
foreach ($collections as $c) {
    $startDate = new DateTime($c['StartDate']);
    $startDateOnly = $startDate->format('Y-m-d');
    $todayDate = $today->format('Y-m-d');
    if ($c['Status'] === 'Reservation' && $startDate > $today && $startDateOnly !== $todayDate) {
        $reservations[] = $c;
        if ($c['IsViewed'] == 0) {
            $unseenReservationsCount++;
        }
    }
}
$totalReservationsCount = count($reservations);
?>

<?php include('../includes/head.php'); ?>
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        #mini-modal {
            position: absolute;
            display: none;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 8px 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            font-size: 14px;
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        #mini-modal.error { background-color: #fee2e2; color: #b91c1c; }
        #mini-modal.success { background-color: #dcfce7; color: #15803d; }
        .reservation-row { background-color: #e0f7fa; cursor: pointer; }
        .from-reservation { background: linear-gradient(to right, #bef264, #fefcbf); cursor: pointer; }
        .settled-row { background-color: #f5f5f5; color: #9e9e9e; }
        .settle-row { cursor: pointer; }
        #view-reservations-modal td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }
        #view-reservations-modal table { table-layout: fixed; }
        #view-reservations-modal .bg-[#FFF6E8] { max-width: 2000px; }
        #view-reservations-modal td, #view-reservations-modal th { font-size: 0.80rem; }
        #view-reservations-modal .overflow-x-auto { overflow-x: hidden; }
        #view-reservations-modal table { width: 100%; }
        .modal { transition: all 0.3s ease-in-out; }
        .modal.hidden { opacity: 0; transform: scale(0.95); }
        .modal:not(.hidden) { opacity: 1; transform: scale(1); }
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #ef4444;
            color: white;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: bold;
            padding: 2px 6px;
            min-width: 20px;
            text-align: center;
        }
        .notification-unseen {
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .unseen-reservation {
            animation: blink 1.5s infinite;
            background-color: #e0f7fa; 
        }
        @keyframes blink {
            0% { background-color: #e0f7fa; }
            50% { background-color: #ffcccc; }
            100% { background-color: #e0f7fa; }
        }
        #reservationRows tr {
            background-color: #ffffff; 
            color: #000000; 
            transition: background-color 0.3s ease; 
        }
        .relative { position: relative; }
    </style>
</head>
<body>
    <div class="flex min-h-screen w-full">
        <?php include('../includes/sidebar.php'); ?>
        <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
            <?php include('../includes/navbar.php'); ?>
            <main class="px-8 py-8">
                <div class="w-full">
                    <div class="flex gap-4 mb-4">
                        <button id="add-payment-btn" class="p-3 bg-[#4E3B2A] rounded-lg text-white">Add Payment</button>
                        <div class="relative">
                            <button id="view-reservations-btn" class="p-3 bg-[#4E3B2A] rounded-lg text-white">
                                View Reservations
                                <?php if ($unseenReservationsCount > 0): ?>
                                    <span id="reservation-count" class="notification-badge <?= $unseenReservationsCount > 0 ? 'notification-unseen' : '' ?>">
                                        <?= $unseenReservationsCount ?>
                                    </span>
                                <?php endif; ?>
                            </button>
                        </div>
                        <button id="generate-invoice-btn" class="p-3 bg-[#4E3B2A] rounded-lg text-white">Generate Invoice</button>
                    </div>

                    <div class="mt-2 overflow-x-auto">
                        <table class="min-w-full bg-white rounded-lg overflow-hidden shadow-md border">
                            <thead class="bg-[#4E3B2A] text-white">
                                <tr>
                                    <th class="py-3 px-2 md:px-4 text-left">Invoice ID</th>
                                    <th class="py-3 px-2 md:px-4 text-left">Guest Name</th>
                                    <th class="py-3 px-2 md:px-4 text-left">Total Amount</th>
                                    <th class="py-3 px-2 md:px-4 text-left">Amount Paid</th>
                                    <th class="py-3 px-2 md:px-4 text-left">Stay Duration</th>
                                    <th class="py-3 px-2 md:px-4 text-left">Payment Method</th>
                                    <th class="py-3 px-2 md:px-4 text-left">Status</th>
                                    <th class="py-3 px-2 md:px-4 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="dataRows" class="text-gray-700">
                            <?php
                            foreach ($collections as $c):
                                $startDate = new DateTime($c['StartDate']);
                                $endDate = new DateTime($c['EndDate']);
                                $stayDuration = '';
                                $durationColor = '';
                                $isZeroDays = false;
                                $isSettled = ($c['Status'] === 'Settled');
                                $isReservation = ($c['Status'] === 'Reservation');
                                $wasReservation = false;
                                $todayDate = $today->format('Y-m-d');
                                $startDateOnly = $startDate->format('Y-m-d');

                                if ($isReservation && $startDate > $today && $startDateOnly !== $todayDate) {
                                    continue; // Skip reservations (added to $reservations)
                                }

                                if ($isReservation && ($startDateOnly === $todayDate || $startDate <= $today)) {
                                    $isReservation = false;
                                    $wasReservation = true;
                                    $displayStatus = ($c['AmountPay'] < $c['TotalAmount']) ? 'Downpayment' : 'Fully Paid';
                                    $dataController->UpdatePayment($c['PaymentID'], null, null, null, null, null, null, $displayStatus);
                                } else {
                                    $displayStatus = $c['Status'];
                                    if (!$isSettled && !$isReservation) {
                                        $displayStatus = ($c['AmountPay'] < $c['TotalAmount']) ? 'Downpayment' : 'Fully Paid';
                                        if ($displayStatus !== $c['Status']) {
                                            $dataController->UpdatePayment($c['PaymentID'], null, $c['TotalAmount'], $c['AmountPay'], null, null, null, $displayStatus);
                                        }
                                    }
                                }

                                if ($today > $endDate) {
                                    $stayDuration = 'Expired';
                                    $durationColor = '';
                                } elseif ($today < $startDate) {
                                    $interval = $startDate->diff($endDate);
                                    $days = $interval->days;
                                    $stayDuration = $days . ' days';
                                    $durationColor = ($days >= 3) ? '#22c55e' : ($days === 2 ? '#ca8a04' : '#ef4444');
                                    $isZeroDays = ($days == 0 && !$isSettled);
                                } else {
                                    $interval = $today->diff($endDate);
                                    $days = $interval->days;
                                    $stayDuration = $days . ' days';
                                    $durationColor = ($days >= 3) ? '#22c55e' : ($days === 2 ? '#ca8a04' : '#ef4444');
                                    $isZeroDays = ($days == 0 && !$isSettled);
                                }

                                $displayStatus = str_replace('FullyPaid', 'Fully Paid', $displayStatus); 
                                $statusColor = ($displayStatus === 'Settled') ? 'gray' : ($displayStatus === 'Downpayment' ? 'red' : ($displayStatus === 'Reservation' ? 'blue' : 'green'));
                            ?>
                            <tr class="border-b hover:bg-gray-100 <?= $isSettled ? 'settled-row' : ($isReservation ? 'reservation-row' : ($wasReservation ? 'from-reservation' : ($isZeroDays ? 'settle-row' : ''))) ?>" 
                                data-id="<?= $c['PaymentID'] ?>" 
                                data-guest="<?= htmlspecialchars($c['GuestName']) ?>" 
                                data-total="<?= $c['TotalAmount'] ?>" 
                                data-paid="<?= $c['AmountPay'] ?>" 
                                data-method="<?= $c['PaymentType'] ?>" 
                                data-status="<?= $displayStatus ?>"
                                data-start="<?= $c['StartDate'] ?>"
                                data-end="<?= $c['EndDate'] ?>">
                                <td class="py-3 px-2 md:px-4"><?= $c['InvoiceID'] ?></td>
                                <td class="py-3 px-2 md:px-4"><?= $c['GuestName'] ?></td>
                                <td class="py-3 px-2 md:px-4">₱<?= number_format($c['TotalAmount'], 2) ?></td>
                                <td class="py-3 px-2 md:px-4">₱<?= number_format($c['AmountPay'], 2) ?></td>
                                <td class="py-3 px-2 md:px-4" style="color: <?= $durationColor; ?>"><?= $stayDuration ?></td>
                                <td class="py-3 px-2 md:px-4"><?= $c['PaymentType'] ?></td>
                                <td class="py-3 px-2 md:px-4" style="color: <?= $statusColor; ?>"><?= $displayStatus ?></td>
                                <td class="py-3 px-2 flex flex-wrap justify-center gap-1">
                                    <button class="px-2 md:px-4 py-2 bg-blue-300 rounded-md text-xs md:text-sm ViewInfo" data-id="<?= $c['PaymentID'] ?>" title="View">
                                        <i class="fa-solid fa-eye text-blue-700"></i>
                                    </button>
                                    <button class="px-2 md:px-4 py-2 bg-green-300 rounded-md text-xs md:text-sm EditInfo <?= $isSettled ? 'opacity-50 cursor-not-allowed' : '' ?>" 
                                            data-id="<?= $c['PaymentID'] ?>" title="Edit" <?= $isSettled ? 'disabled' : '' ?>>
                                        <i class="fa-solid fa-pencil text-green-700"></i>
                                    </button>
                                    <button class="px-2 md:px-4 py-2 bg-red-300 rounded-md text-xs md:text-sm delete-payment" data-id="<?= $c['PaymentID'] ?>" title="Delete">
                                        <i class="fa-solid fa-trash text-red-700"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Inline ViewReservationsModal -->
                    <div id="view-reservations-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden">
                        <div class="bg-[#FFF6E8] rounded-lg shadow-lg p-6 w-full max-w-4xl">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-xl font-bold">Advanced Reservations</h2>
                                <button id="close-reservations-btn" class="text-gray-600 hover:text-gray-800">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white rounded-lg shadow-md border">
                                    <thead class="bg-[#4E3B2A] text-white">
                                        <tr>
                                            <th class="py-3 px-4 text-left">Invoice ID</th>
                                            <th class="py-3 px-4 text-left">Guest Name</th>
                                            <th class="py-3 px-4 text-left">Total Amount</th>
                                            <th class="py-3 px-4 text-left">Amount Paid</th>
                                            <th class="py-3 px-4 text-left">Check-In</th>
                                            <th class="py-3 px-4 text-left">Check-Out</th>
                                            <th class="py-3 px-4 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="reservationRows" class="text-gray-700">
                                        <?php foreach ($reservations as $r): ?>
                                        <tr class="border-b hover:bg-gray-100 reservation-row <?= $r['IsViewed'] == 0 ? 'unseen-reservation' : '' ?>" 
                                            data-id="<?= $r['PaymentID'] ?>" 
                                            data-guest="<?= htmlspecialchars($r['GuestName']) ?>" 
                                            data-total="<?= $r['TotalAmount'] ?>" 
                                            data-paid="<?= $r['AmountPay'] ?>" 
                                            data-start="<?= $r['StartDate'] ?>" 
                                            data-end="<?= $r['EndDate'] ?>" 
                                            data-is-viewed="<?= $r['IsViewed'] ?>">
                                            <td class="py-3 px-4"><?= $r['InvoiceID'] ?></td>
                                            <td class="py-3 px-4"><?= $r['GuestName'] ?></td>
                                            <td class="py-3 px-4">₱<?= number_format($r['TotalAmount'], 2) ?></td>
                                            <td class="py-3 px-4">₱<?= number_format($r['AmountPay'], 2) ?></td>
                                            <td class="py-3 px-4"><?= date('m/d/Y H:i', strtotime($r['StartDate'])) ?></td>
                                            <td class="py-3 px-4"><?= date('m/d/Y H:i', strtotime($r['EndDate'])) ?></td>
                                            <td class="py-3 px-4 flex justify-center gap-2">
                                                <button class="px-4 py-2 bg-green-300 rounded-md text-sm edit-reservation" data-id="<?= $r['PaymentID'] ?>" title="Edit">
                                                    <i class="fa-solid fa-pencil text-green-700"></i>
                                                </button>
                                                <button class="px-4 py-2 bg-red-300 rounded-md text-sm cancel-reservation" data-id="<?= $r['PaymentID'] ?>" title="Cancel">
                                                    <i class="fa-solid fa-trash text-red-700"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <?php include('includes/modals/CreatePaymentModal.php'); ?>
                    <?php include('includes/modals/EditReservationModal.php'); ?>
                    <?php include('includes/modals/VerifyReservationModal.php'); ?>
                    <?php include('includes/modals/ViewPaymentModal.php'); ?>
                    <?php include('includes/modals/EditPaymentModal.php'); ?>
                    <?php include('includes/modals/SettlementModal.php'); ?>
                    <?php include('includes/modals/GenerateInvoiceModal.php'); ?>
                    <?php include('includes/modals/InvoicePreviewModal.php'); ?>


                    <div id="mini-modal"></div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js.js"></script>
    <script src="./assets/collection.js"></script>
</body>
</html>