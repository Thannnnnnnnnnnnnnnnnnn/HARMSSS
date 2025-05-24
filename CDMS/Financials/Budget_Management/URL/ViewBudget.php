
<?php
include('../config/controller.php');

if (isset($_POST['budgetID'])) {
    $budgetid = $_POST['budgetID']; 
    $view = new Data();
    $viewData = $view->getById($budgetid); 
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
                    <input type="number" id="total-amount" name="totalAmount" value="<?= htmlspecialchars($viewData['TotalAmount']) ?>" readonly class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter Total Amount">
                </div>
            </div>
            <!-- Start Date and End Date Section -->
            <div class="flex space-x-4 mb-4">
                <div class="flex-1">
                    <label for="start-date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" id="start-date" name="startDate" value="<?= htmlspecialchars($viewData['StartDate']) ?>" readonly class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex-1">
                    <label for="end-date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" id="end-date" name="endDate" value="<?= htmlspecialchars($viewData['EndDate']) ?>" readonly class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        
        </form>
     
