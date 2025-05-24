<div id="settlementModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-[#FFF6E8] p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4 text-gray-800"><i class="fa-solid fa-handshake mr-2 text-orange-500"></i>Settlement</h2>
        <form id="settlementForm" method="POST" class="space-y-4">
            <input type="hidden" name="paymentID" id="settlementPaymentID">
            <div>
                <label class="block text-sm font-semibold text-gray-800"><i class="fa-solid fa-user mr-2 text-green-500"></i>Guest Name</label>
                <input type="text" id="settlementGuestName" class="w-full p-2 mt-1 border border-gray-300 rounded-md bg-gray-100 text-gray-600" readonly>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-800"><i class="fa-solid fa-peso-sign mr-2 text-yellow-500"></i>Total Amount</label>
                <input type="number" step="0.01" name="totalAmount" id="settlementTotalAmount" class="w-full p-2 mt-1 border border-gray-300 rounded-md bg-gray-100 text-gray-600" readonly>
            </div>
            <div class="flex items-center gap-2">
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-800"><i class="fa-solid fa-money-bill mr-2 text-green-500"></i>Amount Paid</label>
                    <input type="number" step="0.01" name="amountPay" id="settlementAmountPay" class="w-full p-2 mt-1 border border-gray-300 rounded-md text-gray-600" required>
                </div>
                <button type="button" id="settleFullPayment" class="px-3 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition-colors flex items-center mt-6">
                    <i class="fa-solid fa-coins mr-2"></i>Pay Full
                </button>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-800"><i class="fa-solid fa-circle-check mr-2 text-orange-500"></i>Status</label>
                <input type="text" id="settlementStatus" class="w-full p-2 mt-1 border border-gray-300 rounded-md bg-gray-100 text-gray-600" readonly>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-800"><i class="fa-solid fa-credit-card mr-2 text-purple-500"></i>Payment Method</label>
                <select name="paymentType" id="settlementPaymentType" class="w-full p-2 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 bg-gray-50" required>
                    <option value="Credit/Debit">Credit/Debit</option>
                    <option value="Cash">Cash</option>
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" id="close-settlement-btn" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 transition-colors flex items-center">
                    <i class="fa-solid fa-times mr-2 text-gray-600"></i>Cancel
                </button>
                <button type="submit" name="settle" id="settleButton" class="px-4 py-2 bg-orange-500 text-white rounded-md transition-colors flex items-center" disabled>
                    <i class="fa-solid fa-check mr-2"></i>Settle
                </button>
            </div>
        </form>
    </div>
</div>