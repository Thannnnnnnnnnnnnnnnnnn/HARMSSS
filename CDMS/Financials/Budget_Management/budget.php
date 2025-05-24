<?php
include('config/controller.php');

$data = new Data();
$budgets = $data->View();

?>
<?php
if(isset($_POST['submit'])){
   $budgetname = ($_POST['budgetname']);
   $totalAmt = ($_POST['totalAmount']);
   $start = ($_POST['startDate']);
   $end = ($_POST['endDate']);
    $add_data = new Data();
    $add_data->Create($budgetname, $totalAmt, $start, $end);
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
                <!-- All Content Put Here -->
           <div class="w-full">
                  <button id="add-budget-btn" class="p-3 bg-[#4E3B2A] rounded-lg text-white">Create Budget</button>  
                <div class="mt-2 overflow-x-auto">
                    <table class="min-w-full bg-white rounded-lg overflow-hidden shadow-md border">
                        <thead class="bg-[#4E3B2A] text-white">
                            <tr>
                                <th class="py-3 px-2 md:px-4 text-left">Budget ID</th>
                                <th class="py-3 px-2 md:px-4 text-left">Budget Name</th>
                                <th class="py-3 px-2 md:px-4 text-left">Total Amount</th>
                                <th class="py-3 px-2 md:px-4 text-left">Start/End Date</th>
                                <th class="py-3 px-2 md:px-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="dataRows" class="text-gray-700">
                            <?php foreach ($budgets as $b): ?>
                            <tr class="border-b hover:bg-gray-100">
                                <td    class="py-3 px-2 md:px-4"><?= $b['BudgetID']; ?></td>
                                <td class="py-3 px-2 md:px-4"><?= $b['BudgetName']; ?></td>
                                <td class="py-3 px-2 md:px-4">₱<?= number_format($b['TotalAmount'], 2  ); ?></td>
                                <td class="py-3 px-2 md:px-4"><?= $b['StartDate'] . "/" . $b['EndDate']; ?></td>
                                <td class="py-3 px-2 flex flex-wrap justify-center gap-1">
                                    <button  class="px-2 md:px-4 py-2 bg-blue-300 rounded-md text-xs md:text-sm ViewInfo" data-id=<?= $b['BudgetID'] ?>  title="View"><i class="fa-solid fa-eye"></i></button>
                                    <button class="px-2 md:px-4 py-2 bg-green-300 rounded-md text-xs md:text-sm EditInfo" title="Edit" data-id=<?= $b['BudgetID'] ?>><i class="fa-solid fa-pencil"></i></button>
                                    <button class="px-2 md:px-4 py-2 bg-red-300 rounded-md text-xs md:text-sm delete-patient" title="Delete" data-id=<?= $b['BudgetID'] ?>><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
<?php 
   include("includes/CreateModal.php");
   ?>

<?php 
   include("includes/viewModal.php");
 
   ?>
   <?php 
   include('includes/EditModal.php')
   ?>

                </div>
            </main>
        </div>
        
    </div>

  
    <script src="../assets/js.js"></script>
    <script>
    
    const addBudgetBtn = document.getElementById('add-budget-btn');
    const modal = document.getElementById('create-budget-modal');
    const closeBtn = document.getElementById('close-btn');

    addBudgetBtn.addEventListener('click', () => {
        modal.classList.remove('hidden'); 
    });
    closeBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
    });
 
</script>
<script type="text/javascript">
// View function
$(document).ready(function() {
    $('.ViewInfo').click(function() {
       let budgetID = $(this).data('id'); 
        $.ajax({
            url: 'URL/ViewBudget.php', 
            type: 'POST',
            data: { budgetID: budgetID },   
            success: function(response) {
                $('.modal-body-view').html(response);
                $('#viewPatientModal').removeClass('hidden');
            }
        });
    });
    $('#close-modal-btn').click(function() {
        $('#viewPatientModal').addClass('hidden'); 
    });
});
</script>

<script type="text/javascript">
//edit function
$(document).ready(function() {
    $('.EditInfo').click(function() {
        let editID = $(this).data('id');
       $.ajax({
        url: 'URL/EditBudget.php',
        type: 'POST',
        data: {EditID: editID},
        success: function(response){
            $('.modal-body-edit').html(response);
            $('#editBudgetModal').removeClass('hidden');
        }
    });
    $('#close-edit-btn').click(function() {
        $('#editBudgetModal').addClass('hidden'); 
    });
    });
    $(document).on('submit', '#editBudget', function(e) {
        e.preventDefault();
       let formData = $(this).serialize();
        $.ajax({
            url: 'URL/EditBudget.php',   
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
            }
        });
    });
});
</script>

<script>
//delete function
 $(document).ready(function() {
    $(document).on('click', '.delete-patient', function(e) {
        e.preventDefault();
        const ID = $(this).data('id');
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
                    url: 'URL/DeleteBudget.php',
                    type: 'POST',
                    data: {
                        BudgetID: ID
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
