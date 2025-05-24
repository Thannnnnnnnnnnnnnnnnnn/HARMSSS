<?php
include('../config/controller.php');
   if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['budgetname'])) {
        $budgetid = $_POST['budgetID'];
        $budgetname =$_POST['budgetname'];
        $totalAmt = $_POST['totalAmount'];
        $start = $_POST['startDate'];
        $end = $_POST['endDate'];
        $edit = new Data();
        $editSuccess = $edit->Update($budgetid, $budgetname, $totalAmt, $start, $end);
           
    if ($editSuccess) {
        echo "success"; 
    }
  
        exit(); 
    }

if (isset($_POST['EditID'])) {
    $budgetid = $_POST['EditID']; 
    $view = new Data();
    $viewData = $view->getById($budgetid); 


?>
<form id="editBudget" method="POST">
    <!-- Hidden BudgetID -->
    <input type="hidden" name="budgetID" value="<?= htmlspecialchars($budgetid) ?>">

    <!-- Budget Name and Total Amount Section -->
    <div class="flex space-x-4 mb-4">
        <div class="flex-1">
            <label for="budget-name" class="block text-sm font-medium text-gray-700">Budget Name</label>
            <input type="text" id="budget-name" name="budgetname" value="<?= htmlspecialchars($viewData['BudgetName']) ?>"  class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter Budget Name">
        </div>
        <div class="flex-1">
            <label for="total-amount" class="block text-sm font-medium text-gray-700">Total Amount</label>
            <input type="number" id="total-amount" name="totalAmount" value="<?= htmlspecialchars($viewData['TotalAmount']) ?>"  class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter Total Amount">
        </div>
    </div>
    
    <!-- Start Date and End Date Section -->
    <div class="flex space-x-4 mb-4">
        <div class="flex-1">
            <label for="start-date" class="block text-sm font-medium text-gray-700">Start Date</label>
            <input type="date" id="start-date" name="startDate" value="<?= htmlspecialchars($viewData['StartDate']) ?>"  class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex-1">
            <label for="end-date" class="block text-sm font-medium text-gray-700">End Date</label>
            <input type="date" id="end-date" name="endDate" value="<?= htmlspecialchars($viewData['EndDate']) ?>"  class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
    </div>
    
    <!-- Submit Button -->
    <div class="mt-6 flex justify-end">
        <button type="submit"  class="p-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Update Budget
        </button>
    </div>
</form>
<?php
exit(); 
}
?>
