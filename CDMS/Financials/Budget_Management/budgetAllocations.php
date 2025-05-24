<?php
include('config/AllocationController.php');
$allocate = new Data();
$allocData = $allocate->View();
$budgetData = $allocate->Budget();

if(isset($_POST['submit'])){
    $select_budget = $_POST['budget_ID'];
    $budgetname = $_POST['budget_name'];
    $totalamt = $_POST['total_amount'];
    $departName = $_POST['department_name'];
    $allocAmt = $_POST['allocated_amount'];
    $allocate->Create($select_budget, $budgetname, $totalamt, $departName, $allocAmt);
    try {
        $allocationID = $allocate->getLastInsertedID();
        $secondDb = new mysqli('localhost:3307', 'root', '', 'fin_general_ledger');
        
        if ($secondDb->connect_error) {
            die("Connection failed: " . $secondDb->connect_error);
        }
        $query = "INSERT INTO transactions (AllocationID, BudgetAllocated, BudgetName, Allocated_Department, TransactionFrom) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $secondDb->prepare($query);
        if ($stmt === false) {
            throw new Exception("Statement preparation failed: " . $secondDb->error);
        }
        $transactionFrom = 'Budget'; 
        $stmt->bind_param("issss", $allocationID, $allocAmt, $budgetname, $departName, $transactionFrom);
        $stmt->execute();

        $stmt->close();
        $secondDb->close();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<?php include('../includes/head.php'); ?>
<body>
    <div class="flex min-h-screen w-full">
        <!-- Sidebar -->
        <?php include('../includes/sidebar.php'); ?>

        <!-- Main + Navbar -->
        <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
            <!-- Navbar -->
            <?php include('../includes/navbar.php'); ?>
            
            <!-- Main Content -->
            <main class="px-8 py-8">
                <div class="w-full">
                    <button id="add-budget-btn" class="p-3 bg-[#4E3B2A] rounded-lg text-white">Allocate Budget</button>
                    <div class="mt-2 overflow-x-auto">
                        <table class="min-w-full bg-white rounded-lg overflow-hidden shadow-md border">
                            <thead class="bg-[#4E3B2A] text-white">
                                <tr>
                                    <th class="py-3 px-2 md:px-4 text-left">Allocation ID</th>
                                    <th class="py-3 px-2 md:px-4 text-left">Budget ID</th>    
                                    <th class="py-3 px-2 md:px-4 text-left">Budget Name</th>
                                    <th class="py-3 px-2 md:px-4 text-left">Total Amount</th>
                                    <th class="py-3 px-2 md:px-4 text-center">Budget Allocated </th>
                                    <th class="py-3 px-2 md:px-4 text-center">Department Name</th>
                                    <th class="py-3 px-2 md:px-4 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="dataRows" class="text-gray-700">
                                <?php if (!empty($allocData)): ?>
                                    <?php foreach ($allocData as $a): ?>
                                        <tr class="border-b hover:bg-gray-100">
                                            <td class="py-3 px-2 md:px-4"><?= htmlspecialchars($a['AllocationID']); ?></td>
                                            <td class="py-3 px-2 md:px-4"><?= htmlspecialchars($a['BudgetID']); ?></td>
                                            <td class="py-3 px-2 md:px-4"><?= htmlspecialchars($a['BudgetName']); ?></td>
                                            <td class="py-3 px-2 md:px-4">₱<?=  number_format(htmlspecialchars($a['TotalAmount']),2); ?></td>
                                            <td class="py-3 px-2 md:px-4">₱<?= number_format(htmlspecialchars($a['AllocatedAmount']),2); ?></td>
                                            <td class="py-3 px-2 md:px-4"><?= htmlspecialchars($a['DepartmentName']); ?></td>
                                            <td class="py-3 px-2 flex flex-wrap justify-center gap-1">
                                                <button class="px-2 md:px-4 py-2 bg-blue-300 rounded-md text-xs md:text-sm ViewInfo" data-id="<?= htmlspecialchars($a['AllocationID']); ?>" title="View">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                                <button class="px-2 md:px-4 py-2 bg-green-300 rounded-md text-xs md:text-sm EditInfo" title="Edit" data-id="<?= htmlspecialchars($a['AllocationID']); ?>">
                                                    <i class="fa-solid fa-pencil"></i>
                                                </button>
                                                <button class="px-2 md:px-4 py-2 bg-red-300 rounded-md text-xs md:text-sm DeleteInfo" title="Delete" data-id="<?= htmlspecialchars($a['AllocationID']); ?>">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">No data available.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                           <?php 
                            include('includes/FormAllocate.php');
                            
                            ?>
                            <?php 
                          
                            include('Modal/ViewAllocatedModal.php');
                            ?>
                             <?php 
                          
                            include('Modal/EditAllocatedModal.php');
                            ?>
                </div>
            </main>
        </div>
    </div>
        <script src="../assets/js.js"></script>
 <script>
    
    const addBudgetBtn = document.getElementById('add-budget-btn');
    const modal = document.getElementById('allocate-budget-modal');
    const closeBtn = document.getElementById('close-btn');

    addBudgetBtn.addEventListener('click', () => {
        modal.classList.remove('hidden'); 
    });
    closeBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
    });
 
</script>
 <script>
    $(document).ready(function () {
        $("#select_budget").change(function () {
            let selectedOption = $(this).find("option:selected");
            let budgetID = selectedOption.val();
            let totalAmount = selectedOption.data("amount");
            $("#budget_ID").val(budgetID);
            $("#total_amount").val(totalAmount);
        });
    });
</script>
<script type="text/javascript">
// View function
$(document).ready(function() {
    $('.ViewInfo').click(function() {
       let allocateID = $(this).data('id'); 
        $.ajax({
            url: 'URL/ViewAllocated.php', 
            type: 'POST',
            data: { allocateID: allocateID },   
            success: function(response) {
                $('.modal-body-viewAllocated').html(response);
                $('#ViewAllocatedModal').removeClass('hidden');
            }
        });
    });
    $('#close-modal-btn').click(function() {
        $('#ViewAllocatedModal').addClass('hidden'); 
    });
});
</script>
 <script type="text/javascript">
 //edit data
$(document).ready(function() {
    $('.EditInfo').click(function() {
        const EditID = $(this).data('id');
        
     
        $.ajax({
            url: 'URL/EditAllocate.php',
            type: 'POST',
            data: {EditID: EditID},
            success: function(response) {
                $('.modal-body-editAllocated').html(response);
                $('#EditAllocatedModal').removeClass('hidden');
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load record: ' + error
                });
            }
        });
    });
    
    
    $(document).on('click', '#close-modal-btn', function() {
        $('#EditAllocatedModal').addClass('hidden');
    });
    
    // Form handler
    $(document).on('submit', '#AllocateEditBudget', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const submitBtn = $(this).find('button[type="submit"]');
        
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: 'URL/EditAllocate.php', 
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.trim() === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Successfully Updated!',
                        showConfirmButton: false,
                        timer: 1300,
                        heightAuto: false,
                        customClass: {
                            popup: 'my-swal-popup'
                        }
                    }).then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: response
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update record: ' + error
                });
            },
            complete: function() {
                submitBtn.prop('disabled', false);
            }
        });
    });
});
</script>
<script>
//delete function
 $(document).ready(function() {
    $(document).on('click', '.DeleteInfo', function(e) {
        e.preventDefault();
        
        const DeleteID = $(this).data('id');
    
        Swal.fire({
            title: 'Are you sure?',
            text: `Do you want to delete this record?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'URL/DeleteAllocate.php',
                    type: 'POST',
                    data: {
                        DeleteID: DeleteID
                    },
                    success: function(response) {
                        if (response.trim() === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'The record has been deleted.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to delete the record.',
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'There was an error connecting to the server.',
                        });
                    }
                });
            }
        });
    });
});
</script>
</body>
</html>