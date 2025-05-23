<?php
include("connection.php");
$query = "SELECT * FROM `Competencies`"; 
$result = mysqli_query($connections, $query);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Databases</title>
    <link rel="shortcut icon" href="emsaa.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.7/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">


   <link rel="stylesheet" href="pendings.css">
    <link rel="stylesheet" href="tt.css">
    <script src="t.js"></script>

</head>
<div class="wrapper">
        <!-- Sidebar  -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h4>Centralized Database</h4>
            </div>

            <ul class="list-unstyled components">
                
         <li>

         <a href="#homeSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
    <i class='bx bxs-data'></i> HR part 1 - 2</a>
    <ul class="collapse list-unstyled" id="homeSubmenu">
        <li><a href="competencies.php"><i class="bx bx-briefcase"></i> Competency management</a></li>
        <li><a href="#"><i class="bx bx-book"></i> Learning & Training</a></li>
        <li><a href="#"><i class="bx bx-user-check"></i> New Hire Onboarding</a></li>
        <li><a href="#"><i class="bx bx-line-chart"></i> Performance Management</a></li>
        <li><a href="#"><i class="bx bx-search-alt"></i> Recruitment</a></li>
        <li><a href="#"><i class="bx bx-star"></i> Social Recognition</a></li>
        <li><a href="#"><i class="bx bx-clipboard"></i> Succession Planning</a></li>
    </ul>
</li>

<li>
    <a href="#hrPart34" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">    
        <i class='bx bxs-data'></i> HR part 3 - 4</a>
    </a>
    <ul class="collapse list-unstyled" id="hrPart34">
    <li><a href="#"><i class='bx bx-bar-chart-alt-2'></i> Analytics</a></li>
    <li><a href="#"><i class='bx bx-money'></i> Claims and reimbursement</a></li>
    <li><a href="#"><i class='bx bx-calculator'></i> Compensation planning and administration</a></li>
    <li><a href="#"><i class='bx bx-group'></i> Core human capital management</a></li>
    <li><a href="#"><i class='bx bx-line-chart'></i> HR analytics</a></li>
    <li><a href="#"><i class='bx bx-calendar-check'></i> Leave management</a></li>
    <li><a href="#"><i class='bx bx-wallet'></i> Payroll</a></li>
    <li><a href="#"><i class='bx bx-time'></i> Time and attendance</a></li>
</ul>

</li>

<li>
    <a href="#logistics1" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
        <i class="bx bxs-data"></i> Logistics 1</a>
    <ul class="collapse list-unstyled" id="logistics1">
    <li><a href="#"><i class="bx bx-box"></i> Asset management</a></li>
    <li><a href="#"><i class="bx bx-cart"></i> Procurement</a></li>
    <li><a href="#"><i class="bx bx-task"></i> Project management</a></li>
    <li><a href="#"><i class="bx bx-package"></i> Warehousing</a></li>
    </ul>
</li>

<li>
    <a href="#logistics2" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
        <i class="bx bxs-data"></i> Logistics 2
    </a>
    <ul class="collapse list-unstyled" id="logistics2">
    <li><a href="#"><i class="bx bx-check-shield"></i> Audit management</a></li>
    <li><a href="#"><i class="bx bx-file-find"></i> Document tracking system</a></li>
    <li><a href="#"><i class="bx bx-car"></i> Fleet management</a></li>
    <li><a href="#"><i class="bx bx-calendar-check"></i> Vehicle reservation system</a></li>
    <li><a href="#"><i class="bx bx-network-chart"></i> Vendor portal</a></li>

    </ul>
</li>

<li>
    <a href="#coreTransaction1" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
        <i class="bx bxs-data"></i> Core transaction 1
    </a>
    <ul class="collapse list-unstyled" id="coreTransaction1">
    <li><a href="#"><i class="bx bx-food-menu"></i> Food and beverage costing</a></li>
    <li><a href="#"><i class="bx bx-package"></i> Inventory management</a></li>
    <li><a href="#"><i class="bx bx-restaurant"></i> Kitchen/bar module</a></li>
    <li><a href="#"><i class="bx bx-receipt"></i> Order management with POS</a></li>
    <li><a href="#"><i class="bx bx-line-chart"></i> Restaurant analytics</a></li>

    </ul>
</li>

<li>
    <a href="#coreTransaction2" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
        <i class="bx bxs-data"></i> Core transaction 2
    </a>
    <ul class="collapse list-unstyled" id="coreTransaction2">
    <li><a href="#"><i class="bx bx-credit-card"></i> Billing</a></li>
    <li><a href="#"><i class="bx bx-building"></i> Front office</a></li>
    <li><a href="#"><i class="bx bx-bath"></i> Housekeeping/laundry management</a></li>
    <li><a href="#"><i class="bx bx-bed"></i> Room facilities</a></li>
    <li><a href="#"><i class="bx bx-group"></i> Supplier management</a></li>

    </ul>
</li>

<li>
    <a href="#coreTransaction3" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
        <i class="bx bxs-data"></i> Core transaction 3
    </a>
    <ul class="collapse list-unstyled" id="coreTransaction3">
    <li><a href="#"><i class="bx bx-calendar"></i> Booking</a></li>
    <li><a href="#"><i class="bx bx-user"></i> Customer/guest management</a></li>
    <li><a href="#"><i class="bx bx-chat"></i> Customer relationship management</a></li>
    <li><a href="#"><i class="bx bx-building-house"></i> Facilities management</a></li>
    <li><a href="#"><i class="bx bx-bookmark"></i> Reservation</a></li>

    </ul>
</li>

<li>
    <a href="#financials" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
        <i class="bx bxs-data"></i> Financials
    </a>
    <ul class="collapse list-unstyled" id="financials">
    <li><a href="#"><i class="bx bx-money"></i> Accounts payable/accounts receivables</a></li>
    <li><a href="#"><i class="bx bx-spreadsheet"></i> Budget management</a></li>
    <li><a href="#"><i class="bx bx-coin"></i> Collection</a></li>
    <li><a href="#"><i class="bx bx-wallet"></i> Disbursement</a></li>
    <li><a href="#"><i class="bx bx-book"></i> General ledger</a></li>

    </ul>
</li>


                <li>
                    <a href="#">
                        <i class="bx bx-line-chart"></i> Analytics & Statistics
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bx bxs-user"></i> Accounts
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bx bxs-cog"></i> Settings
                    </a>
                </li>
            </ul>

            
        </nav>

        <!-- Page Content  -->
        <div id="content">

            <nav class="navbar navbar-expand-lg navbar-white bg-#2C2C2C">
                <div class="container-fluid">

                    <button type="button" id="sidebarCollapse" class="btn btn-info">
                        <i class="fas fa-align-left"></i>
                        <span>Option</span>
                    </button>
                    

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="nav navbar-nav ml-auto">
                            <li class="nav-item active">
                                <a class="nav-link" href="#">Page</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Page</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Page</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Page</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <?php if ($result && mysqli_num_rows($result) > 0) : ?>
                    <table class="table">
                        <tr class="tr1">
                            <th>ID</th>
                            
                        </tr>
                        
                        <?php while($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td><?php echo $row['CompetencyID']; ?></td>
                            
                            <td>
                              
                                   
                                <a href="#" class="view-button" data-bs-toggle="modal" data-bs-target="#viewModal" data-case-id="<?php echo $row['CompetencyID']; ?>">
                                    <i class='fas fa-eye'></i>
                                </a>    <b> |</b>

                                <form action="approved.php?id=<?php echo $row['CompetencyID']; ?>" method="POST" style="display: inline;">
                                    <button type="submit" class="approve-button" style="border: none; padding: 0;">
                                        <i class="fas fa-check"></i>
                                    </button>
                                   <b> |</b>
                                </form>
                                <form action="rejected.php?id=<?php echo $row['CompetencyID']; ?>" method="POST" style="display: inline;">
                                    <button type="submit" class="reject-button" style="border: none;  padding: 0;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <b> |</b>
                                </form>
                                <form action="delete.php?id=<?php echo $row['CompetencyID']; ?>" method="POST" style="display: inline;">
                                    <button type="submit" class="delete-button" style="border: none; padding: 0;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                    <?php else : ?>
                    <p>No records found</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModal" tabindex="1" aria-labelledby="viewModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Full details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="caseDetails">
              
            </div>
        </div>
    </div>
</div>



    
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> 
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="edit-case-form">
               
            </div>
            <div class="modal-footer">
                
            </div>
        </div>
    </div>
</div>


  
   

<script>
 function addCurrentTimeToForm() {
    const now = new Date();

    const year = now.getFullYear();
    const month = (now.getMonth() + 1).toString().padStart(2, '0'); 
    const day = now.getDate().toString().padStart(2, '0');
    const currentDate = `${year}-${month}-${day}`; // Format: YYYY-MM-DD

    const hours = now.getHours();
    const minutes = now.getMinutes();
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const formattedHours = (hours % 12 || 12).toString().padStart(2, '0'); 
    const formattedMinutes = minutes.toString().padStart(2, '0'); 
    const time = `${formattedHours}:${formattedMinutes} ${ampm}`; 

    const dateInput = document.createElement('input');
    dateInput.type = 'hidden';
    dateInput.name = 'Date';
    dateInput.value = currentDate;

    const timeInput = document.createElement('input');
    timeInput.type = 'hidden';
    timeInput.name = 'Time';
    timeInput.value = time;

    const statusInput = document.createElement('input');
    statusInput.type = 'hidden';
    statusInput.name = 'Status';
    statusInput.value = 'Pending';

    const form = document.querySelector('form');
    form.appendChild(dateInput);
    form.appendChild(timeInput);
    form.appendChild(statusInput);
}





</script>

            