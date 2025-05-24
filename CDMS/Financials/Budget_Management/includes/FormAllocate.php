   <!-- Budget Allocation Form -->
    <div id="allocate-budget-modal" class="create-budget fixed top-0 left-0 w-full h-full bg-gray-700 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white p-6 shadow-lg rounded-lg max-w-[90%] sm:max-w-[800px] w-full ">
    <div class="flex align-center">
     <h1 class="text-2xl font-semibold mb-4 border-b pb-2 flex-1">Allocate Budget</h1>
        <i id="close-btn" class="fa-solid fa-xmark text-2xl cursor-pointer"></i>
    </div>
                    <form  method="POST" class="p-4 bg-white ">
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Select Budget:</label>
                            <select id="select_budget" name="select_budget" class="w-full px-3 py-2 border rounded-lg">
                                <option value="">Select Budget</option>
                                <?php foreach ($budgetData as $row): ?>
                                    <option value="<?= htmlspecialchars($row['BudgetID']); ?>" 
                                            data-amount="<?= htmlspecialchars($row['TotalAmount']); ?>">
                                        <?= htmlspecialchars($row['BudgetName']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                            <input type="text" hidden id="budget_ID" name="budget_ID" class="w-full px-3 py-2 border rounded-lg " >                     
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Total Amount:</label>
                            <input type="text" id="total_amount" name="total_amount" class="w-full px-3 py-2 border rounded-lg " readonly>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Budget Name:</label>
                            <input type="text" id="budget_name" name="budget_name" class="w-full px-3 py-2 border rounded-lg " >
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Department:</label>
                            <input type="text" name="department_name" class="w-full px-3 py-2 border rounded-lg" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2">Budget Allocated :</label>
                            <input type="number" name="allocated_amount" class="w-full px-3 py-2 border rounded-lg" required>
                        </div>
                         <div class="flex justify-end">
                         <button type="submit" name="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 ">Allocate Budget</button>
                         </div>
                    </form>
                    </div>
                  </div>