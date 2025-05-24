<div id="verify-reservation-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-[#FFF6E8] p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4 text-gray-800"><i class="fa-solid fa-check-circle mr-2 text-blue-500"></i>Verify Guest Arrival</h2>
        <p class="mb-4">Is <span id="verify-guest-name"></span> physically present at the hotel?</p>
        <input type="hidden" id="verify-payment-id">
        <div class="flex justify-end gap-2">
            <button id="verify-no-btn" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 transition-colors flex items-center">
                <i class="fa-solid fa-times mr-2 text-gray-600"></i>No
            </button>
            <button id="verify-yes-btn" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors flex items-center">
                <i class="fa-solid fa-check mr-2"></i>Yes
            </button>
        </div>
    </div>
</div>