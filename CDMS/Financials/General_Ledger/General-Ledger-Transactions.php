<?php 
session_start();
include('includes/header.php');
include('../Database/connection.php');
?>

<!-- creating data Modal start -->
<!-- #transaction create Modal start -->
<div class="modal fade" id="createtransdata" tabindex="-1" aria-labelledby="createtransdataLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header border border-1 border-dark bg-[#f7e6ca]">
        <h1 class="modal-title fs-5 font-bold" id="createtransdataLabel">Create New Transaction</h1> 
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

    <form action="code.php" method="POST">
        <div class="modal-body border border-1 border-dark">
            <div class="form-group mb-3">
                <label for="">Entry ID</label>
                <input type="number" class="form-control" name="entryid" placeholder="Enter Entry ID">
            </div>
            <div class="form-group mb-3">
                <label for="">Payment ID</label>
                <input type="number" class="form-control" name="paymentid" placeholder="Enter Payment ID">
            </div>
            <div class="form-group">
                <label>Transaction From</label>
                    <select name="transactionfrom" class="form-control">
                        <option value="Guest">Guest</option>
                        <option value="Vendor">Vendor</option>
                        <option value="Budget">Budget</option>
                    </select>
            </div>
            <div class="form-group mb-3">
                <label for="">Transaction Date</label>
                <input type="datetime-local" class="form-control" name="transactiondate" placeholder="Enter transaction date">
            </div>
        </div>
            <div class="modal-footer border border-1 border-dark bg-[#f7e6ca]">
            <button type="button" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold" data-bs-dismiss="modal">Close</button>
            <button type="submit" name="save_transdata" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold">Save Transaction</button>
        </div>
    </form>
    </div>
  </div>
</div><!-- transaction create Modal end -->
<!-- creating data Modal end -->

<!-- viewing Modal start -->
<!-- start view transaction Modal -->
<div class="modal fade" id="viewtransdata" tabindex="-1" aria-labelledby="viewtransdataLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border border-1 border-dark bg-[#f7e6ca]">
        <h1 class="modal-title fs-5 font-bold" id="viewtransdataLabel">View Transaction</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body border border-1 border-dark">
        <div class="transview">
        </div>
      </div>
      <div class="modal-footer border border-1 border-dark bg-[#f7e6ca]">
        <button type="button" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div><!-- end view transaction Modal -->
<!-- viewing Modal end -->

<!-- editing Modal start -->
<!-- #transaction edit Modal start -->
<div class="modal fade" id="edittransdata" tabindex="-1" aria-labelledby="edittransdataLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header border border-1 border-dark bg-[#f7e6ca]">
        <h1 class="modal-title fs-5 font-bold" id="edittransdataLabel">Edit Transaction</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

    <form action="code.php" method="POST">
        <div class="modal-body border border-1 border-dark">
            <div class="form-group mb-3"> <!-- editdbid -->
                <input type="hidden" class="form-control" id="transactionid" name="transactionid">
            </div>
            <div class="form-group mb-3">
                <label for="">Entry ID</label>
                <input type="number" class="form-control" id="entryid" name="entryid" placeholder="Enter Entry ID">
            </div>
            <div class="form-group mb-3">
                <label for="">Payment ID</label>
                <input type="number" class="form-control" id="paymentid" name="paymentid" placeholder="Enter Payment ID">
            </div>
            <div class="form-group">
                <label>Transaction From</label>
                    <select id="transactionfrom" name="transactionfrom" class="form-control">
                        <option value="Guest">Guest</option>
                        <option value="Vendor">Vendor</option>
                        <option value="Budget">Budget</option>
                        <option value="Employee">Employee</option>
                    </select>
            </div>
            <div class="form-group mb-3">
                <label for="">Transaction Date</label>
                <input type="datetime-local" class="form-control" id="transactiondate" name="transactiondate" placeholder="Enter transaction date">
            </div>
        </div>
            <div class="modal-footer border border-1 border-dark bg-[#f7e6ca]">
            <button type="button" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold" data-bs-dismiss="modal">Close</button>
            <button type="submit" name="update_transdata" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold">Update Transaction</button>
        </div>
    </form>
    </div>
  </div>
</div><!-- transaction edit Modal end -->
<!-- editing Modal end-->

<!-- deleting Modal start -->
<!-- start delete Modal -->
<div class="modal fade" id="deletetransdata" tabindex="-1" aria-labelledby="deletetransdataLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border border-1 border-dark bg-[#f7e6ca]">
        <h1 class="modal-title fs-5 font-bold" id="deletetransdataLabel">Delete Transaction</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
        <form action="code.php" method="POST">
            <input type="hidden" name="trans_id" id="delete_trans_id">
            <div class="modal-body border border-1 border-dark">
                <h4>Are you sure you want to delete this transaction?</h4>
            </div>
            <div class="modal-footer border border-1 border-dark bg-[#f7e6ca]">
                <button type="button" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold" data-bs-dismiss="modal">Close</button>
                <button type="submit" name="delete_transdata" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold">Delete Transaction</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div><!-- end delete Modal -->
<!-- deleting Modal end -->

<!-- transreport Modal start -->
<!-- Preview Report Modal start -->
<div class="modal fade" id="previewtransdata" tabindex="-1" aria-labelledby="previewtransdataLabel" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen-xxl-down">
    <div class="modal-content">
      <div class="modal-header border border-2 border-dark bg-[#f7e6ca]">
        <h1 class="modal-title fs-5 font-bold" id="previewtransdataLabel">Transaction Report Preview</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body border border-2 border-dark bg-dark">
        <iframe id="reportFrame" src="./generate-report/transaction-report.php" width="100%" height="600px" style="border:none;"></iframe>
      </div>
      <div class="modal-footer border border-2 border-dark bg-[#f7e6ca]">
        <button type="button" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold" onclick="printReport()">Print</button>
        <button type="button" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold" onclick="downloadReport()">Generate</button>
        <button type="button" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div><!-- Preview Report Modal end -->
<!-- transreport Modal end -->

<div class="flex min-h-screen w-full">
    <!-- Sidebar -->
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
                    <!-- Commented search bar -->
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
            <h2 class="text-2xl font-semibold mb-6 text-gray-800">Transactions History</h2>
            <!-- btn trigger modal create start -->
            <div class="flex justify-between text-[#4e3b2a] font-semibold">
                <button type="button" class="w-48 h-10 rounded-lg bg-[transparent] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a]" data-bs-toggle="modal" data-bs-target="#createtransdata">
                    Create New Transaction
                </button> 
                <button type="button" class="w-48 h-10 rounded-lg bg-[transparent] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a]" data-bs-toggle="modal" data-bs-target="#previewtransdata">
                    View Report
                </button>
            </div>
            <!-- btn trigger modal create end -->
            <table class="mt-2 w-full table-striped table-danger">
                <thead class="text-white bg-[#4e3b2a] border-[#4e3b2a]">
                    <tr class="text-center">
                        <th class="p-3 text-sm font-semibold tracking-wide rounded-tl-lg">TransactionID</th>
                        <th class="p-3 text-sm font-semibold tracking-wide">TransactionFrom</th>
                        <th class="p-3 text-sm font-semibold tracking-wide">TransactionDate</th>
                        <th class="p-3 text-sm font-semibold tracking-wide rounded-tr-lg">Action</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <!-- fetching transactions db into table start -->                       
                    <?php
                        // Instantiate the Database class and connect
                        $database = new Database();
                        $connection = $database->connect("fin_general_ledger");
                        
                        $fetch_query = "SELECT * FROM transactions";
                        $fetch_query_run = mysqli_query($connection, $fetch_query);
                        
                        if (mysqli_num_rows($fetch_query_run) > 0) {
                            while ($row = mysqli_fetch_array($fetch_query_run)) {
                    ?>
                                <tr class="bg-[#ffffff]">
                                    <td class="p-2 text-sm text-gray-700 rounded-bl-lg trans_id"><?php echo $row['TransactionID']; ?></td>
                                    <td class="p-2 text-sm text-gray-700"><?php echo $row['TransactionFrom']; ?></td>
                                    <td class="p-2 text-sm text-gray-700"><?php echo $row['TransactionDate']; ?></td>
                                    <td class="p-2 text-xl text-white flex justify-center rounded-br-lg">
                                        <a href="#" class="btn bg-[#80bfff] btn-md hover:bg-[#4da3ff] grow-3 mr-1 transview_data"><i class='bx bx-show'></i></a>
                                        <a href="#" class="btn bg-[#ffdb70] btn-md hover:bg-[#ffd446] grow-3 mr-1 transedit_data"><i class='bx bx-edit'></i></a>
                                        <a href="#" class="btn bg-[#ff8a80] btn-md hover:bg-[#ff5252] grow-3 mr-1 transdelete_data"><i class='bx bx-trash'></i></a>
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
                    <!-- fetching transactions db into table end -->
                </tbody>
            </table>
            <!-- Transactions Table end -->                            
        </main>
        <!-- Main Content End -->
    </div>
</div>

<?php include('includes/footer.php'); ?>

<script>
//<!-- viewing jquery script start -->
$(document).ready(function() { //<!-- #transview jquery script start -->
    $('.transview_data').click(function (e) {
        e.preventDefault();
        var trans_id = $(this).closest('tr').find('.trans_id').text();
        $.ajax({
            method: "POST",
            url: "code.php",
            data: {
                'click_transview_btn': true,
                'trans_id': trans_id,
            },
            success: function (response) {
                console.log(response);
                $('.transview').html(response);
                $('#viewtransdata').modal('show');
            }
        });
    });
}); //<!-- transview jquery script end -->
//<!-- viewing jquery script end -->

//<!-- editing jquery script start -->
$(document).ready(function() { //<!-- #transedit jquery script start -->
    $('.transedit_data').click(function (e) {
        e.preventDefault();
        var trans_id = $(this).closest('tr').find('.trans_id').text();
        $.ajax({
            method: "POST",
            url: "code.php",
            data: {
                'click_transedit_btn': true,
                'trans_id': trans_id,
            },
            success: function (response) {
                $.each(response, function (key, value) {
                    $('#transactionid').val(value['TransactionID']);
                    $('#entryid').val(value['EntryID']);
                    $('#paymentid').val(value['PaymentID']);
                    $('#transactionfrom').val(value['TransactionFrom']);
                    $('#transactiondate').val(value['TransactionDate'].replace(' ', 'T').slice(0, 16));
                });
                $('#edittransdata').modal('show');
            }
        });
    });
}); //<!-- transedit jquery script end -->
//<!-- editing jquery script end -->

//<!-- deleting jquery script start -->
$(document).ready(function() { //<!-- transdelete jquery script start -->
    $('.transdelete_data').click(function (e) {
        e.preventDefault();
        var trans_id = $(this).closest('tr').find('.trans_id').text();
        $('#delete_trans_id').val(trans_id);
        $('#deletetransdata').modal('show');
    });
}); //<!-- transdelete jquery script end -->
//<!-- deleting jquery script end -->

//<!-- report jquery script start -->
function printReport() { //print pdf start
    const iframe = document.getElementById('reportFrame');
    if (iframe && iframe.contentWindow) {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
    } else {
        alert('Report preview is not available for printing.');
    }
} //print pdf end
function downloadReport() { //download pdf start
    window.location.href = './generate-report/transaction-report.php?download=true';
} //download pdf end
//<!-- report jquery script end -->
</script>