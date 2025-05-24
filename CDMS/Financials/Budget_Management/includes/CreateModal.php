<!-- Create Budget Modal -->
 <div id="create-budget-modal" class="create-budget fixed top-0 left-0 w-full h-full bg-gray-700 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white p-6 shadow-lg rounded-lg max-w-[90%] sm:max-w-[800px] w-full ">
    <div class="flex align-center">
     <h1 class="text-2xl font-semibold mb-4 border-b pb-2 flex-1">Create Budget</h1>
        <i id="close-btn" class="fa-solid fa-xmark text-2xl cursor-pointer"></i>
    </div>
        <form method="POST">
            <!-- Budget Name Section -->
            <div class="flex space-x-4 mb-4">
                <div class="flex-1">
                    <label for="budget-name" class="block text-sm font-medium text-gray-700">Budget Name</label>
                    <input type="text" id="budget-name" name="budgetname" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter Budget Name">
                </div>
                <div class="flex-1">
                    <label for="total-amount" class="block text-sm font-medium text-gray-700">Total Amount</label>
                    <input type="number" id="total-amount" name="totalAmount" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter Total Amount">
                </div>
            </div>
            <!-- Start Date and End Date Section -->
            <div class="flex space-x-4 mb-4">
                <div class="flex-1">
                    <label for="start-date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" id="start-date" name="startDate" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex-1">
                    <label for="end-date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" id="end-date" name="endDate" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <!-- Submit Button Section -->
            <div class="mt-6 flex justify-end">
                <button type="submit" name="submit" class="w-32 p-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Create Budget
                </button>
            </div>
        </form>
    </div>
</div>