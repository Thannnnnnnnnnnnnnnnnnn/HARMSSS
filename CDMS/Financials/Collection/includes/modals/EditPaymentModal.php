<div id="editPaymentModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 hidden">
    <div class="bg-[#FFF6E8] p-4 sm:p-6 rounded-lg shadow-lg w-full max-w-lg sm:max-w-md mx-auto">
        <h2 class="text-lg sm:text-xl font-bold mb-4 text-gray-800 flex items-center"><i class="fa-solid fa-pencil mr-2 text-green-500"></i>Edit Payment</h2>
        <div class="modal-body-edit">
            <form id="editPaymentForm" method="POST" class="space-y-4">
                <input type="hidden" name="paymentID" id="editPaymentID">
                <div>
                    <label class="block text-sm font-semibold text-gray-800"><i class="fa-solid fa-user mr-2 text-green-500"></i>Guest Name</label>
                    <input type="text" name="guestName" id="editGuestName" class="w-full p-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 bg-gray-50" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-800"><i class="fa-solid fa-peso-sign mr-2 text-yellow-500"></i>Total Amount</label>
                    <input type="number" step="0.01" name="totalAmount" id="editTotalAmount" class="w-full p-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-gray-50" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-800"><i class="fa-solid fa-money-bill mr-2 text-green-500"></i>Amount Paid</label>
                    <input type="number" step="0.01" name="amountPay" id="editAmountPay" class="w-full p-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 bg-gray-50" required>
                </div>
                <div class="relative">
                    <label class="block text-sm font-semibold text-gray-800"><i class="fa-solid fa-calendar-day mr-2 text-blue-500"></i>Check-In Date & Time</label>
                    <input type="text" name="startDate" id="editStartDate" class="w-full p-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50" readonly required>
                    <i class="fa-solid fa-calendar-alt absolute right-3 top-9 text-blue-500 cursor-pointer" id="startDateTrigger"></i>
                </div>
                <div class="relative">
                    <label class="block text-sm font-semibold text-gray-800"><i class="fa-solid fa-calendar-check mr-2 text-blue-500"></i>Check-Out Date & Time</label>
                    <input type="text" name="endDate" id="editEndDate" class="w-full p-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50" readonly required>
                    <i class="fa-solid fa-calendar-alt absolute right-3 top-9 text-blue-500 cursor-pointer" id="endDateTrigger"></i>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-800"><i class="fa-solid fa-credit-card mr-2 text-purple-500"></i>Payment Method</label>
                    <select name="paymentType" id="editPaymentType" class="w-full p-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-50" required>
                        <option value="Credit/Debit">Credit/Debit</option>
                        <option value="Cash">Cash</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" id="close-edit-btn" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 transition-colors flex items-center">
                        <i class="fa-solid fa-times mr-2 text-gray-600"></i>Cancel
                    </button>
                    <button type="submit" name="update" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors flex items-center">
                        <i class="fa-solid fa-check mr-2"></i>Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>