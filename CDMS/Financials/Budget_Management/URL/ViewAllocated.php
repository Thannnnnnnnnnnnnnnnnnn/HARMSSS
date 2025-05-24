
<?php
include('../config/AllocationController.php');

if (isset($_POST['allocateID'])) {
    $allocateID = $_POST['allocateID']; 
    $view = new Data();
    $viewData = $view->getById($allocateID); 
}
        ?>
        <form method="POST">
            <!-- Budget Name Section -->
            <div class="flex space-x-4 mb-4">
            
                <div class="flex-1">
                    <label for="budget-name" class="block text-sm font-medium text-gray-700">Budget Name</label>
                    <input type="text" id="budget-name" name="budgetname" value="<?= htmlspecialchars($viewData['BudgetName']) ?>" readonly class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter Budget Name">
                </div>
                <div class="flex-1">
                    <label for="total-amount" class="block text-sm font-medium text-gray-700">Total Amount</label>
                    <input type="text" id="total-amount" name="totalAmount" value="<?= htmlspecialchars($viewData['TotalAmount']) ?>" readonly class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter Total Amount">
                </div>
            </div>
            <!-- Start Date and End Date Section -->
            <div class="flex space-x-4 mb-4">
                <div class="flex-1">
                    <label for="start-date" class="block text-sm font-medium text-gray-700">Allocated Amount</label>
                    <input type="text" id="start-date" name="startDate" value="<?= htmlspecialchars($viewData['AllocatedAmount']) ?>" readonly class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex-1">
                    <label for="end-date" class="block text-sm font-medium text-gray-700">Department Name</label>
                    <input type="text" id="end-date" name="endDate" value="<?= htmlspecialchars($viewData['DepartmentName']) ?>" readonly class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        
        </form>