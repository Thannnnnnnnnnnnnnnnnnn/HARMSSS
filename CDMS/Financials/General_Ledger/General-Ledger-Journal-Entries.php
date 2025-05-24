<?php 
session_start();
include('includes/header.php');
include('../Database/connection.php');
?>

<!-- creating data Modal start -->
<!-- jentries create Modal start -->
<div class="modal fade" id="createjentrydata" tabindex="-1" aria-labelledby="createjentrydataLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header border border-1 border-dark bg-[#f7e6ca]">
        <h1 class="modal-title fs-5 font-bold" id="createjentrydataLabel">Create New Entry</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

    <form action="code.php" method="POST">
        <div class="modal-body border border-1 border-dark">
            <div class="form-group mb-3">
                <label for="">Account ID</label>
                <input type="number" class="form-control" name="jentryaccid" placeholder="Enter Account ID">
            </div>
            <div class="form-group mb-3">
                <label for="">Transaction ID</label>
                <input type="number" class="form-control" name="jentrytransid" placeholder="Enter Transaction ID">
            </div>
            <div class="form-group">
                <label>Entry Type</label>
                    <select name="entrytype" class="form-control">
                        <option value="Debit">Debit</option>
                        <option value="Credit">Credit</option>
                    </select>
            </div>
            <div class="form-group mb-3">
                <label for="">Amount</label>
                <input type="number" class="form-control" name="amount" placeholder="Enter Amount">
            </div>
            <div class="form-group mb-3">
                <label for="">Entry Date</label>
                <input type="datetime-local" class="form-control" name="jentrydate" placeholder="Enter Entry Date">
            </div>
            <div class="form-group mb-3">
                <label for="">Description</label>
                <input type="text" class="form-control" name="description" placeholder="Enter Description">
            </div>
        </div>
            <div class="modal-footer border border-1 border-dark bg-[#f7e6ca]">
            <button type="button" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold" data-bs-dismiss="modal">Close</button>
            <button type="submit" name="save_jentrydata" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold">Save Entry</button>
        </div>
    </form>
    </div>
  </div>
</div>
<!-- #jentries create Modal end -->
<!-- creating data Modal end -->

<!-- viewing Modal start -->
<!-- start view journalentries Modal -->
<div class="modal fade" id="viewjournalentriesdata" tabindex="-1" aria-labelledby="viewjournalentriesLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border border-1 border-dark bg-[#f7e6ca]">
        <h1 class="modal-title fs-5 font-bold" id="viewjournalentriesLabel">View Journal Entry</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body border border-1 border-dark">
        <div class="journalentriesview">
        </div>
      </div>
      <div class="modal-footer border border-1 border-dark bg-[#f7e6ca]">
        <button type="button" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div><!-- end view journalentries Modal -->
<!-- viewing Modal end -->

<!-- editing Modal start -->
<!-- jentries edit Modal start -->
<div class="modal fade" id="editjentrydata" tabindex="-1" aria-labelledby="editjentrydataLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header border border-1 border-dark bg-[#f7e6ca]">
        <h1 class="modal-title fs-5 font-bold" id="editjentrydataLabel">Update Entry</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

    <form action="code.php" method="POST">
        <div class="modal-body border border-1 border-dark">
            <div class="form-group mb-3"> <!-- editdbid -->
                <input type="hidden" class="form-control" id="entryid" name="entryid">
            </div>
            <div class="form-group mb-3">
                <label for="">Account ID</label>
                <input type="number" class="form-control" id="jentryaccid" name="jentryaccid" placeholder="Enter Account ID">
            </div>
            <div class="form-group mb-3">
                <label for="">Transaction ID</label>
                <input type="number" class="form-control" id="jentrytransid" name="jentrytransid" placeholder="Enter Transaction ID">
            </div>
            <div class="form-group">
                <label>Entry Type</label>
                    <select id="entrytype" name="entrytype" class="form-control">
                        <option value="Debit">Debit</option>
                        <option value="Credit">Credit</option>
                    </select>
            </div>
            <div class="form-group mb-3">
                <label for="">Amount</label>
                <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter Amount">
            </div>
            <div class="form-group mb-3">
                <label for="">Entry Date</label>
                <input type="datetime-local" class="form-control" id="jentrydate" name="jentrydate" placeholder="Enter Entry Date">
            </div>
            <div class="form-group mb-3">
                <label for="">Description</label>
                <input type="text" class="form-control" id="description" name="description" placeholder="Enter Description">
            </div>
        </div>
            <div class="modal-footer border border-1 border-dark bg-[#f7e6ca]">
            <button type="button" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold" data-bs-dismiss="modal">Close</button>
            <button type="submit" name="update_jentrydata" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold">Update Entry</button>
        </div>
    </form>
    </div>
  </div>
</div>
<!-- #jentries edit Modal end -->
<!-- editing Modal end-->

<!-- deleting Modal start -->
<!-- start delete Modal -->
<div class="modal fade" id="deletejentrydata" tabindex="-1" aria-labelledby="deletejentrydataLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border border-1 border-dark bg-[#f7e6ca]">
        <h1 class="modal-title fs-5 font-bold" id="deletejentrydataLabel">Delete Journal Entry</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
        <form action="code.php" method="POST">
            <input type="hidden" name="jentry_id" id="delete_jentry_id">
            <div class="modal-body border border-1 border-dark">
                <h4>Are you sure you want to delete this Journal Entry?</h4>
            </div>
            <div class="modal-footer border border-1 border-dark bg-[#f7e6ca]">
                <button type="button" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold" data-bs-dismiss="modal">Close</button>
                <button type="submit" name="delete_jentrydata" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold">Delete Journal Entry</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div><!-- end delete Modal -->
<!-- deleting Modal end -->

<!-- jentryreport Modal start -->
<!-- Preview Journal Entry Report Modal start -->
<div class="modal fade" id="previewjentrydata" tabindex="-1" aria-labelledby="previewjentrydataLabel" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen-xxl-down">
    <div class="modal-content">
      <div class="modal-header border border-2 border-dark bg-[#f7e6ca]">
        <h1 class="modal-title fs-5 font-bold" id="previewjentrydataLabel">Journal Entry Report Preview</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body border border-2 border-dark bg-dark">
        <iframe id="reportFrame" src="./generate-report/journal-entry-report.php" width="100%" height="600px" style="border:none;"></iframe>
      </div>
      <div class="modal-footer border border-2 border-dark bg-[#f7e6ca]">
        <button type="button" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold" onclick="printReport()">Print</button>
        <button type="button" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold" onclick="downloadReport()">Generate</button>
        <button type="button" class="btn rounded-lg bg-[#f7e6ca] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a] font-bold" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div><!-- Preview Journal Entry Report Modal end -->
<!-- jentryreport Modal end -->

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
            <h2 class="text-2xl font-semibold mb-6 text-gray-800">Journal Entries Table</h2>
            <!-- btn trigger modal create start -->
            <div class="mt-2 flex justify-between text-[#4e3b2a] font-semibold">
                <button type="button" class="w-48 h-10 rounded-lg bg-[transparent] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a]" data-bs-toggle="modal" data-bs-target="#createjentrydata">
                    Create New Entry
                </button> 
                <button type="button" class="w-48 h-10 rounded-lg bg-[transparent] hover:bg-[#4e3b2a] hover:text-white border-2 border-[#4e3b2a]" data-bs-toggle="modal" data-bs-target="#previewjentrydata">
                    View Report
                </button>
            </div>
            <!-- btn trigger modal create end -->
            <!-- Journal Entry Table start -->
            <table class="mt-2 w-full md-table-fixed table-striped table-danger">
                <thead class="text-white bg-[#4e3b2a] border-[#4e3b2a]">
                    <tr class="text-center">
                        <th class="p-3 text-sm font-semibold tracking-wide rounded-tl-lg">EntryID</th>
                        <th class="p-3 text-sm font-semibold tracking-wide">AccountID</th>
                        <th class="p-3 text-sm font-semibold tracking-wide">TransactionID</th>
                        <th class="p-3 text-sm font-semibold tracking-wide">EntryType</th>
                        <th class="p-3 text-sm font-semibold tracking-wide">Amount</th>
                        <th class="p-3 text-sm font-semibold tracking-wide">EntryDate</th>
                        <th class="p-3 text-sm font-semibold tracking-wide">Description</th>
                        <th class="p-3 text-sm font-semibold tracking-wide rounded-tr-lg">Action</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <!-- fetching journalentries db into table start -->                       
                    <?php
                        // Instantiate the Database class and connect
                        $database = new Database();
                        $connection = $database->connect("fin_general_ledger");
                        
                        $fetch_query = "SELECT * FROM journalentries";
                        $fetch_query_run = mysqli_query($connection, $fetch_query);
                        
                        if (mysqli_num_rows($fetch_query_run) > 0) {
                            while ($row = mysqli_fetch_array($fetch_query_run)) {
                    ?>
                                <tr class="bg-[#ffffff]">
                                    <td class="p-2 text-sm text-gray-700 rounded-bl-lg jent_id"><?php echo $row['EntryID']; ?></td>
                                    <td class="p-2 text-sm text-gray-700"><?php echo $row['AccountID']; ?></td>
                                    <td class="p-2 text-sm text-gray-700"><?php echo $row['TransactionID']; ?></td>
                                    <td class="p-2 text-sm text-gray-700"><?php echo $row['EntryType']; ?></td>
                                    <td class="p-2 text-sm text-gray-700">₱ <?php echo $row['Amount']; ?></td>
                                    <td class="p-2 text-sm text-gray-700"><?php echo $row['EntryDate']; ?></td>
                                    <td class="p-2 text-sm text-gray-700"><?php echo $row['Description']; ?></td>
                                    <td class="p-2 text-xl text-white flex justify-center rounded-br-lg">
                                        <a href="#" class="btn bg-[#80bfff] btn-md hover:bg-[#4da3ff] grow-3 mr-1 jentview_data"><i class='bx bx-show'></i></a>
                                        <a href="#" class="btn bg-[#ffdb70] btn-md hover:bg-[#ffd446] grow-3 mr-1 jentedit_data"><i class='bx bx-edit'></i></a>
                                        <a href="#" class="btn bg-[#ff8a80] btn-md hover:bg-[#ff5252] grow-3 mr-1 jentdelete_data"><i class='bx bx-trash'></i></a>
                                    </td>
                                </tr>
                    <?php
                            }
                        } else {
                    ?>
                            <tr><td colspan="8">No record found!</td></tr>
                    <?php
                        }
                    ?>
                    <!-- fetching journalentries db into table end -->  
                </tbody>
            </table>
            <!-- Journal Entry Table end -->
        </main>
        <!-- Main Content End -->
    </div>
</div>

<?php include('includes/footer.php'); ?>

<script>
//<!-- viewing jquery script start -->
$(document).ready(function() { //<!-- #jentryview script start -->
    $('.jentview_data').click(function(e) {
        e.preventDefault();
        var jentry_id = $(this).closest('tr').find('.jent_id').text();
        $.ajax({
            method: "POST",
            url: "code.php",
            data: {
                'click_jentview_btn': true,
                'jentry_id': jentry_id,
            },
            success: function(response) {
                console.log(response);
                $('.journalentriesview').html(response);
                $('#viewjournalentriesdata').modal('show');
            }
        });
    });
}); //<!-- #jentryview script end -->
//<!-- viewing jquery script end -->

//<!-- editing jquery script start -->
$(document).ready(function() { //<!-- #jentryedit script start -->
    $('.jentedit_data').click(function(e) {
        e.preventDefault();
        var jent_id = $(this).closest('tr').find('.jent_id').text();
        $.ajax({
            method: "POST",
            url: "code.php",
            data: {
                'click_jentryedit_btn': true,
                'jent_id': jent_id,
            },
            dataType: 'json',
            success: function(response) {
                if (response.length > 0) {
                    var data = response[0];
                    $('#entryid').val(data.EntryID);
                    $('#jentryaccid').val(data.AccountID);
                    $('#jentrytransid').val(data.TransactionID);
                    $('#entrytype').val(data.EntryType);
                    $('#amount').val(data.Amount);
                    $('#jentrydate').val(data.EntryDate.replace(' ', 'T').slice(0, 16));
                    $('#description').val(data.Description);
                    $('#editjentrydata').modal('show');
                } else {
                    alert('No data found!');
                }
            },
            error: function() {
                alert('Error fetching data!');
            }
        });
    });
}); //<!-- #jentryedit script end -->
//<!-- editing jquery script end -->

//<!-- deleting jquery script start -->
$(document).ready(function() { //<!-- #jentrydelete script start -->
    $('.jentdelete_data').click(function(e) {
        e.preventDefault();
        var jentry_id = $(this).closest('tr').find('.jent_id').text();
        $('#delete_jentry_id').val(jentry_id);
        $('#deletejentrydata').modal('show');
    });
}); //<!-- #jentrydelete script end -->
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
    window.location.href = './generate-report/journal-entry-report.php?download=true';
} //download pdf end
//<!-- report jquery script end -->
</script>