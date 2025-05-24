<?php
include('../config/AllocationController.php');

// Handle AJAX form submission
if ($_SERVER["REQUEST_METHOD"] == "POST"  && isset($_POST['budgetname'])) {
    $Editid = $_POST['EditID'];
    $deptName = $_POST['deptName'];
    $budgetName = $_POST['budgetname'];
    
    $edit = new Data();
    $editSuccess = $edit->Update($budgetName, $deptName,$Editid);
    
    if ($editSuccess) {
        echo "success";
    } else {
        echo "Update failed. Please try again.";
    }
    
    exit();
}

// Handle form display request
if (isset($_POST['EditID'])) {
    // FETCH OPERATION
    $Editid = $_POST['EditID'];
    $view = new Data();
    $viewData = $view->getById($Editid);
    
    // Return the form with populated data
    ?>
    <form id="AllocateEditBudget" method="POST">
        <!-- Hidden Budget ID input -->
        <input type="hidden" name="EditID" value="<?= htmlspecialchars($Editid) ?>">
        
        <!-- Budget Name Section -->
        <div class="flex space-x-4 mb-4">
            <div class="flex-1">
                <label for="budgetname" class="block text-sm font-medium text-gray-700">Budget Name</label>
                <input type="text" id="budgetname" name="budgetname" value="<?= htmlspecialchars($viewData['BudgetName']) ?>" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter Budget Name">
            </div>
        </div>
        
        <!-- Department Name Section -->
        <div class="flex space-x-4 mb-4">
            <div class="flex-1">
                <label for="deptName" class="block text-sm font-medium text-gray-700">Department Name</label>
                <input type="text" id="deptName" name="deptName" value="<?= htmlspecialchars($viewData['DepartmentName']) ?>" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        
        <!-- Submit Button -->
        <div class="mt-6 flex justify-end">
            <button type="submit" name="AllocateSubmit"  class="p-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Update Budget
            </button>
        </div>
    </form>
    <?php
    exit(); 
}
?>