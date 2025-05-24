<div id="view-reservations-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-[#FFF6E8] p-6 rounded-lg shadow-lg w-full max-w-[1100px] overflow-x-hidden" style="transform: translateX(10%);">
        <h2 class="text-2xl font-bold mb-4 text-gray-800 flex items-center justify-center"><i class="fa-solid fa-list-ul mr-3 text-gray-600"></i>Advance Reservations</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg overflow-hidden shadow-md border">
                <thead class="bg-[#4E3B2A] text-white">
                    <tr>
                        <th class="py-4 px-6 text-left"><i class="fa-solid fa-ticket mr-2"></i>Invoice ID</th>
                        <th class="py-4 px-6 text-left"><i class="fa-solid fa-user mr-2"></i>Guest Name</th>
                        <th class="py-4 px-6 text-left"><i class="fa-solid fa-peso-sign mr-2"></i>Total Amount</th>
                        <th class="py-4 px-6 text-left"><i class="fa-solid fa-money-bill mr-2"></i>Amount Paid</th>
                        <th class="py-4 px-6 text-left"><i class="fa-solid fa-calendar-day mr-2"></i>Check-In</th>
                        <th class="py-4 px-6 text-left"><i class="fa-solid fa-calendar-check mr-2"></i>Check-Out</th>
                        <th class="py-4 px-6 text-center"><i class="fa-solid fa-gears mr-2"></i>Action</th>
                    </tr>
                </thead>
                <tbody id="reservationRows" class="text-gray-700">
                    <?php foreach ($reservations as $r): ?>
                    <tr class="border-b" 
                        data-id="<?= $r['PaymentID'] ?>" 
                        data-guest="<?= htmlspecialchars($r['GuestName']) ?>" 
                        data-total="<?= $r['TotalAmount'] ?>" 
                        data-paid="<?= $r['AmountPay'] ?>" 
                        data-start="<?= $r['StartDate'] ?>" 
                        data-end="<?= $r['EndDate'] ?>">
                        <td class="py-4 px-6 truncate"><?= $r['InvoiceID']; ?></td>
                        <td class="py-4 px-6 truncate"><?= $r['GuestName']; ?></td>
                        <td class="py-4 px-6 truncate">₱<?= number_format($r['TotalAmount'], 2); ?></td>
                        <td class="py-4 px-6 truncate">₱<?= number_format($r['AmountPay'], 2); ?></td>
                        <td class="py-4 px-6 truncate"><?= date('Y-m-d H:i', strtotime($r['StartDate'])); ?></td>
                        <td class="py-4 px-6 truncate"><?= date('Y-m-d H:i', strtotime($r['EndDate'])); ?></td>
                        <td class="py-4 px-6 flex justify-center gap-2">
                            <button class="px-3 md:px-5 py-2 bg-green-300 rounded-md text-sm md:text-base edit-reservation" data-id="<?= $r['PaymentID'] ?>" title="Edit">
                                <i class="fa-solid fa-pencil text-green-700"></i>
                            </button>
                            <button class="px-3 md:px-5 py-2 bg-red-300 rounded-md text-sm md:text-base cancel-reservation" data-id="<?= $r['PaymentID'] ?>" title="Cancel Reservation">
                                <i class="fa-solid fa-ban text-red-700"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="flex justify-end mt-4">
            <button id="close-reservations-btn" class="px-5 py-3 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 transition-colors flex items-center text-base">
                <i class="fa-solid fa-times mr-2 text-gray-600"></i>Close
            </button>
        </div>
    </div>
</div>