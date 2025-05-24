<div id="editReservationModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-[#FFF6E8] p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4 text-gray-800"><i class="fa-solid fa-pencil mr-2 text-purple-500"></i>Edit Reservation</h2>
        <form id="editReservationForm" method="POST" action="./includes/ReservationHandler.php" class="space-y-4">
            <input type="hidden" name="paymentID" id="editReservationPaymentID">
            <div>
                <label class="block text-sm font-semibold text-gray-800"><i class="fa-solid fa-user mr-2 text-purple-500"></i>Guest Name</label>
                <input type="text" name="guestName" id="editReservationGuestName" class="w-full p-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-50" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-800"><i class="fa-solid fa-peso-sign mr-2 text-yellow-500"></i>Total Amount</label>
                <input type="number" step="0.01" name="totalAmount" id="editReservationTotalAmount" class="w-full p-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-gray-50" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-800"><i class="fa-solid fa-money-bill mr-2 text-green-500"></i>Amount Paid</label>
                <input type="number" step="0.01" name="amountPay" id="editReservationAmountPay" class="w-full p-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 bg-gray-50" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-800"><i class="fa-solid fa-calendar-day mr-2 text-purple-500"></i>Check-In Date & Time</label>
                <input type="datetime-local" name="startDate" id="editReservationStartDate" class="w-full p-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-50" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-800"><i class="fa-solid fa-calendar-check mr-2 text-purple-500"></i>Check-Out Date & Time</label>
                <input type="datetime-local" name="endDate" id="editReservationEndDate" class="w-full p-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-50" required>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" id="close-edit-reservation-btn" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 transition-colors flex items-center">
                    <i class="fa-solid fa-times mr-2 text-gray-600"></i>Cancel
                </button>
                <button type="submit" name="update_reservation" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-gray-600 transition-colors flex items-center">
                    <i class="fa-solid fa-check mr-2"></i>Save
                </button>
            </div>
        </form>
    </div>
</div>