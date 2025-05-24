
<?php
include('../config/AdjustController.php');

if (isset($_POST['viewID'])) {
    $viewID = $_POST['viewID']; 
    $view = new Data();
    $viewData = $view->getById($viewID); 
}
        ?>
        <form method="POST">
            <!-- Budget Name Section -->
            <div class="flex space-x-4 mb-4">
                <div class="flex-1">
                    <label for="budget-name" class="block text-sm font-medium text-gray-700">Allocated ID</label>
                    <input type="text" id="budget-name" name="budgetname" value="<?= htmlspecialchars($viewData['AllocationID']) ?>" readonly class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter Budget Name">
                </div>
                <div class="flex-1">
                    <label for="total-amount" class="block text-sm font-medium text-gray-700">Budget Name</label>
                    <input type="text" id="total-amount" name="totalAmount" value="<?= htmlspecialchars($viewData['BudgetName']) ?>" readonly class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter Total Amount">
                </div>
            </div>
            <!-- Start Date and End Date Section -->
            <div class="flex space-x-4 mb-4">
                <div class="flex-1">
                    <label for="start-date" class="block text-sm font-medium text-gray-700">Budget Allocated</label>
                    <input type="int" id="start-date" name="startDate" value="<?= htmlspecialchars($viewData['BudgetAllocated']) ?>" readonly class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex-1">
                    <label for="end-date" class="block text-sm font-medium text-gray-700">Department Name</label>
                    <input type="text" id="end-date" name="endDate" value="<?= htmlspecialchars($viewData['DepartmentName']) ?>" readonly class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
             <div class="flex space-x-4 mb-4">
                <div class="flex-1">
                    <label for="start-date" class="block text-sm font-medium text-gray-700">Adjustment Reason</label>
                    <input type="int" id="start-date" name="startDate" value="<?= htmlspecialchars($viewData['AdjustmentReason']) ?>" readonly class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex-1">
                    <label for="end-date" class="block text-sm font-medium text-gray-700">Adjusted Amount</label>
                    <input type="text" id="end-date" name="endDate" value="<?= htmlspecialchars($viewData['AdjustmentAmount']) ?>" readonly class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        
        </form>
     
