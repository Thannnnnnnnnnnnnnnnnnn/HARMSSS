<?php
include('../config/AdjustController.php');


if (isset($_POST['adjReason'])) {
    $Editid = $_POST['EditID'];
    $adjustID = $_POST['AllocID'];
    $adjReason = $_POST['adjReason'];
    $adjustAmt = $_POST['adjAmount'];
    
    $edit = new Data();
    $editSuccess = $edit->Update($adjReason,$adjustAmt,$Editid);
    $editSuccess = $edit->UpdatedAmount($adjustAmt, $adjustID);
    
    if ($editSuccess) {
        echo "success";
    } else {
        echo "Update failed. Please try again.";
    }
    
    exit();
}


if (isset($_POST['EditID'])) {
   
    $Editid = $_POST['EditID'];
    $view = new Data();
    $viewData = $view->getById($Editid);
    
    
    ?>
          <form method="POST" id="AdjustEditBudget">
            <!-- Budget Name Section -->
               <input type="hidden" name="EditID" value="<?= htmlspecialchars($Editid) ?>">
                 <input type="hidden" name="AllocID" value="<?= htmlspecialchars($viewData['AllocationID']) ?>">
             <div class="flex space-x-4 mb-4">
                <div class="flex-1">
                    <label for="adjReason" class="block text-sm font-medium text-gray-700">Adjustment Reason</label>
                    <input type="int" id="adjReason" name="adjReason" value="<?= htmlspecialchars($viewData['AdjustmentReason']) ?>" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
                <div class="flex space-x-4 mb-4">
                <div class="flex-1">
                    <label for="adjAmount" class="block text-sm font-medium text-gray-700">Adjusted Amount</label>
                    <input type="text" id="adjAmount" name="adjAmount" value="<?= htmlspecialchars($viewData['AdjustmentAmount']) ?>" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
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



