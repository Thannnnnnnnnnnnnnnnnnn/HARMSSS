<?php 
session_start();
include('includes/header.php');
include('../Database/connection.php');
?>

<!-- creating data Modal start -->
<!-- account create Modal start -->
<div class="modal fade" id="createaccdata" tabindex="-1" aria-labelledby="createaccdataLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header border border-1 border-dark bg-[#f7e6ca]">
        <h1 class="modal-title fs-5 font-bold" id="createaccdataLabel">Create New Account</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

    <form action="code.php" method="POST">
        <div class="modal-body border border-1 border-dark">
            <div class="form-group mb-3">
                <label for="">Account Name</label>
                <input type="text" class="form-control" name="accname" placeholder="enter account name">
            </div>
            <div class="form-group">
                <label>Account Type</label>
                    <select name="acctype" class="form-control">
                        <option value="Asset">Asset</option>
                        <option value="Liability">Liability</option>
                    </select>
            </div>
        </div>
            <div class="modal-footer border border-1 border-dark bg-[#f7e6ca]">
            <button type="button" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold" data-bs-dismiss="modal">Close</button>
            <button type="submit" name="save_accdata" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold">Save Account</button>
        </div>
    </form>

    </div>
  </div>
</div>
<!-- #account create Modal end -->
<!-- creating data Modal end -->

<!-- viewing Modal start -->
<!-- start view account Modal -->
<div class="modal fade" id="viewaccdata" tabindex="-1" aria-labelledby="viewaccdataLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border border-1 border-dark bg-[#f7e6ca]">
        <h1 class="modal-title fs-5 font-bold" id="viewaccdataLabel">View Account</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body border border-1 border-dark">
        <div class="accview">
        </div>
      </div>
      <div class="modal-footer border border-1 border-dark bg-[#f7e6ca]">
        <button type="button" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div><!-- end view acccount Modal -->
<!-- viewing Modal end -->

<!-- editing Modal start -->
<!-- account edit Modal start -->
<div class="modal fade" id="editaccdata" tabindex="-1" aria-labelledby="editaccdataLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header border border-1 border-dark bg-[#f7e6ca]">
        <h1 class="modal-title fs-5 font-bold" id="editaccdataLabel">Update Account</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

    <form action="code.php" method="POST">
        <div class="modal-body border border-1 border-dark">
            <div class="form-group mb-3"> <!-- editdbid -->
                <input type="hidden" class="form-control" id="accountid" name="accountid">
            </div>
            <div class="form-group mb-3">
                <label for="">Account Name</label>
                <input type="text" class="form-control" id="accname" name="accname" placeholder="enter account name">
            </div>
            <div class="form-group">
                <label>Account Type</label>
                    <select id="acctype" name="acctype" class="form-control">
                        <option value="Asset">Asset</option>
                        <option value="Liability">Liability</option>
                    </select>
            </div>
            
        </div>
            <div class="modal-footer border border-1 border-dark bg-[#f7e6ca]">
            <button type="button" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold" data-bs-dismiss="modal">Close</button>
            <button type="submit" name="update_accdata" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold">Update Account</button>
        </div>
    </form>

    </div>
  </div>
</div>
<!-- #account create Modal end -->
<!-- editing Modal end-->

<!-- deleting Modal start -->
 <!-- start delete Modal -->
 <div class="modal fade" id="deleteaccdata" tabindex="-1" aria-labelledby="deleteaccdataLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border border-1 border-dark bg-[#f7e6ca]">
        <h1 class="modal-title fs-5 font-bold" id="deleteaccdataLabel">Delete Account</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
        <form action="code.php" method="POST">
            <input type="hidden" name="acc_id" id="delete_acc_id">
            <div class="modal-body border border-1 border-dark">
                <h4>Are you sure you want to delete this Account?</h4>
            </div>
            <div class="modal-footer border border-1 border-dark bg-[#f7e6ca]">
                <button type="button" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold" data-bs-dismiss="modal">Close</button>
                <button type="submit" name="delete_accdata" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold">Delete Transaction</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div><!-- end delete Modal -->
<!-- deleting Modal end -->

<!-- previewing report Modal start -->
 <!-- start preview Modal -->
<div class="modal fade" id="previewaccdata" tabindex="-1" aria-labelledby="previewaccdataLabel" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen-xxl-down">
    <div class="modal-content">
      <div class="modal-header border border-2 border-dark bg-[#f7e6ca]">
        <h1 class="modal-title fs-5 font-bold" id="previewaccdataLabel">Account Report Preview</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body border border-2 border-dark bg-dark">
        <iframe id="accountReportFrame" src="./generate-report/account-report.php" width="100%" height="600px" style="border:none;"></iframe>
      </div>
      <div class="modal-footer border border-2 border-dark bg-[#f7e6ca]">
        <button type="button" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold" onclick="printAccountReport()">Print</button>
        <button type="button" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold" onclick="downloadAccountReport()">Generate</button>
        <button type="button" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div> <!-- end preview Modal -->
<!-- previewing report Modal end -->

    <div class="flex min-h-screen w-full">
        <!-- Overlay -->
        <?php include('../includes/sidebar.php'); ?>

        <!-- Main + Navbar -->
        <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
            <!-- Navbar -->
            <nav class="h-16 w-full bg-white border-b border-[#F7E6CA] flex justify-between items-center px-6 py-4">
                <!-- Left Navigation Section -->
                <div class="left-nav flex items-center space-x-4 max-w-96 w-full">
                <!-- Toggle Menu Button-->
                    <button aria-label="Toggle menu" class="menu-btn text-[#4E3B2A] focus:outline-none hover:bg-[#F7E6CA] hover:rounded-full">
                        <i class="fa-solid fa-bars text-[#594423] text-xl w-11 py-2"></i>
                    </button>
                    
                    <div class="relative w-full flex pr-2">
                        <!-- <input type="text" 
                               class="bg-[#FFF6E8] h-10 rounded-lg grow w-full pl-10 pr-4 focus:ring-2 focus:ring-[#F7E6CA] focus:outline-none" 
                               placeholder="Search something..." 
                               aria-label="Search input"/>
                        <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-[#4E3B2A]"></i> -->
                    </div>
                </div>

                <div>
                   <i class="fa-regular fa-user bg-[#594423] text-white px-4 py-2 rounded-lg cursor-pointer text-lg lg:hidden" aria-label="User profile"></i>
                </div>

                <!-- Right Navigation Section -->
                <div class="right-nav items-center space-x-6 hidden lg:flex">
                    <button aria-label="Notifications" class="text-[#4E3B2A] focus:outline-none border-r border-[#F7E6CA] pr-6 relative">
                        <i class="fa-regular fa-bell text-xl"></i>
                        <span class="absolute top-0.5 right-5 block w-2.5 h-2.5 bg-[#594423] rounded-full"></span>
                    </button>

                    <div class="flex items-center space-x-2">
                        <i class="fa-regular fa-user bg-[#594423] text-white px-4 py-2 rounded-lg text-lg" aria-label="User profile"></i>
                        <div class="info flex flex-col py-2">
                        </div>
                    </div>
                </div>
            </nav>

<!-- Main Content Start -->
            <main class="px-8 py-8">
<!-- All Content Start -->
<h2 class="text-2xl font-semibold mb-6 text-gray-800">Account Table</h2>
<!-- btn trigger modal report start  -->
<div class="mt-2 flex justify-between text-[#4e3b2a] font-semibold">
  <button type="button" class="w-40 h-10 rounded-lg bg-[transparent] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a]" data-bs-toggle="modal" data-bs-target="#createaccdata">
    Create New Account
  </button>

    <button type="button" class="w-40 h-10 rounded-lg bg-[transparent] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a]" data-bs-toggle="modal" data-bs-target="#previewaccdata">
      <a href="#">View Report</a>
    </button>
</div>
<!-- btn trigger modal report end  -->
<!-- Account Table start -->
<table class="mt-2 w-full table-striped table-danger">
    <thead class="text-white bg-[#4e3b2a] border-[#4e3b2a]">
        <tr class="text-center">
            <th class="p-3 text-sm font-semibold tracking-wide rounded-tl-lg">AccountID</th>
            <th class="p-3 text-sm font-semibold tracking-wide">AccountName</th>
            <th class="p-3 text-sm font-semibold tracking-wide">AccountType</th>
            <th class="p-3 text-sm font-semibold tracking-wide rounded-tr-lg">Action</th>
        </tr>
    </thead>
    <tbody class="text-center">
<!-- fetching account db into table start -->                       
<?php
    // Instantiate the Database class and connect
    $database = new Database();
    $connection = $database->connect("fin_general_ledger");
    
    $fetch_query = "SELECT * FROM accounts";
    $fetch_query_run = mysqli_query($connection, $fetch_query);
    
    if (mysqli_num_rows($fetch_query_run) > 0) {
        while ($row = mysqli_fetch_array($fetch_query_run)) {
        ?>
            <tr class="bg-[#ffffff]">
                <td class="p-2 text-sm text-gray-700 rounded-bl-lg acc_id"><?php echo $row['AccountID']; ?></td>
                <td class="p-2 text-sm text-gray-700"><?php echo $row['AccountName']; ?></td>
                <td class="p-2 text-sm text-gray-700"><?php echo $row['AccountType']; ?></td>
                <td class="p-2 text-xl text-white flex justify-center rounded-r-lg">
                    <a href="#" class="btn bg-[#80bfff] btn-md hover:bg-[#4da3ff] grow-3 mr-1 accview_data"><i class='bx bx-show'></i></a>
                    <a href="#" class="btn bg-[#ffdb70] btn-md hover:bg-[#ffd446] grow-3 mr-1 accedit_data"><i class='bx bx-edit'></i></a>
                    <a href="#" class="btn bg-[#ff8a80] btn-md hover:bg-[#ff5252] grow-3 mr-1 accdelete_data"><i class='bx bx-trash'></i></a>
                </td>
            </tr>
        <?php
        }
    } else {
        ?>
        <tr><td colspan="4">No record found!</td></tr>
        <?php
    }
?>
<!-- fetching account db into table end -->                                  
    </tbody>
</table>
<!-- Account Table end -->
<!-- All Content End -->
            </main>
<!-- Main Content End -->
        </div>
    </div>

<?php include('includes/footer.php'); ?>

<script>
//<!-- viewing jquery script start -->
$(document).ready(function(){ //<!-- #accview jquery script start -->
    $('.accview_data').click(function (e) {
        e.preventDefault();
        var acc_id = $(this).closest('tr').find('.acc_id').text();
        $.ajax({
            method: "POST",
            url: "code.php",
            data: {
                'click_accview_btn': true,
                'acc_id': acc_id,
            },
            success: function (response) {
                console.log(response);
                $('.accview').html(response);
                $('#viewaccdata').modal('show');
            }
        });
    });
}); //<!-- accview jquery script end -->
//<!-- viewing jquery script end -->

//<!-- editing jquery script start -->
$(document).ready(function(){ //<!-- #accedit jquery script start -->
    $('.accedit_data').click(function (e) {
        e.preventDefault();
        var acc_id = $(this).closest('tr').find('.acc_id').text();
        $.ajax({
            method: "POST",
            url: "code.php",
            data: {
                'click_accedit_btn': true,
                'acc_id': acc_id,
            },
            success: function (response) {
                $.each(response, function (key, value) {
                    $('#accountid').val(value['AccountID']);
                    $('#accname').val(value['AccountName']);
                    $('#acctype').val(value['AccountType']);
                });
                $('#editaccdata').modal('show');
            }
        });
    });
}); //<!-- accedit jquery script end -->        
//<!-- editing jquery script end -->

//<!-- accdelete jquery script start -->
$(document).ready(function() { //<!-- #accdelete script start -->
    $('.accdelete_data').click(function (e) {
        e.preventDefault();
        var acc_id = $(this).closest('tr').find('.acc_id').text();
        $('#delete_acc_id').val(acc_id);
        $('#deleteaccdata').modal('show');
    });
}); //<!-- #accdelete script end -->
//<!-- accdelete jquery script end -->

//<!-- previewing report Modal start -->
function printAccountReport() { //print pdf start
    const iframe = document.getElementById('accountReportFrame');
    iframe.contentWindow.print();
} //print pdf end
function downloadAccountReport() { //download pdf start
    window.location.href = './generate-report/account-report.php?download=true';
} //download pdf end
//<!-- previewing report Modal end -->
</script>