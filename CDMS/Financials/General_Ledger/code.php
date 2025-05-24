<?php
session_start();
$connection = mysqli_connect("127.0.0.1","3206_CENTRALIZED_DATABASE","4562526","fin_general_ledger"); 
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

// #create btn start.
if(isset($_POST['save_transdata'])){ // create transaction btn start.
    $entryid = $_POST['entryid'];
    $paymentid = $_POST['paymentid'];
    $transactionfrom = $_POST['transactionfrom'];
    $transactiondate = $_POST['transactiondate'];

    $_insert_query = "INSERT INTO transactions (EntryID, PaymentID, TransactionFrom, TransactionDate) VALUES ('$entryid','$paymentid','$transactionfrom','$transactiondate')";
    $_insert_query_run = mysqli_query($connection, $_insert_query);

    if($_insert_query_run) {
        $_SESSION['status'] = "Successfully Created New Transactions!";
    } else {
        $_SESSION['status'] = "Failed to Create New Transactions!";
    }
    header("Location: General-Ledger-Transactions.php");}// create journalentries btn end.

if(isset($_POST['save_jentrydata'])){ // create journalentries btn start.
    $jentryaccid = $_POST['jentryaccid'];
    $jentrytransid = $_POST['jentrytransid'];
    $entrytype = $_POST['entrytype'];
    $amount = $_POST['amount'];
    $jentrydate = $_POST['jentrydate'];
    $description = $_POST['description'];

    $_insert_query = "INSERT INTO journalentries (AccountID, TransactionID, EntryType, Amount, EntryDate, Description) VALUES ('$jentryaccid','$jentrytransid','$entrytype','$amount','$jentrydate','$description')";
    $_insert_query_run = mysqli_query($connection, $_insert_query);

    if($_insert_query_run)
    {
        $_SESSION['status'] = "Successfully Created New Journal Entry!";
        header("Location: General-Ledger-Journal-Entries.php");
    } else {
        $_SESSION['status'] = "Failed to Create New Journal Entry!";
        header("Location: General-Ledger-Journal-Entries.php");
    }}// create journalentries btn end.
if(isset($_POST['save_accdata'])){ // create account btn start.
    $accname = $_POST['accname'];
    $acctype = $_POST['acctype'];

    $_insert_query = "INSERT INTO accounts (AccountName, AccountType) VALUES ('$accname','$acctype')";
    $_insert_query_run = mysqli_query($connection, $_insert_query);

    if($_insert_query_run)
    {
        $_SESSION['status'] = "Successfully Created New Account!";
        header("Location: General-Ledger-Account.php");
    } else {
        $_SESSION['status'] = "Failed to Created New Account!";
        header("Location: General-Ledger-Account.php");
    }}// create account btn end.
// create btn end.

// #viewing btn start.
if(isset($_POST['click_transview_btn'])) {  
    $id = $_POST['trans_id'];
    // Use prepared statement to prevent SQL injection
    $fetch_query = "SELECT * FROM transactions WHERE TransactionID = ?";
    $stmt = mysqli_prepare($connection, $fetch_query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $fetch_query_run = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($fetch_query_run) > 0) {
        $row = mysqli_fetch_array($fetch_query_run);
        if ($row['TransactionFrom'] == 'Guest') {
            $guestName = $row['GuestName'] ?? 'N/A';
            $totalAmount = $row['TotalAmount'] ?? 'N/A';

            // If GuestName is '0' or invalid, fetch from fin_collection
            if ($guestName === '0' || $guestName === 'N/A' || empty($guestName)) {
                $collectionConnection = mysqli_connect("localhost:3307", "root", "", "fin_collection");
                if ($collectionConnection) {
                    $paymentID = $row['PaymentID'];
                    $query = "SELECT i.GuestName 
                              FROM collection_payments cp 
                              JOIN invoices i ON cp.InvoiceID = i.InvoiceID 
                              WHERE cp.PaymentID = ?";
                    $collStmt = mysqli_prepare($collectionConnection, $query);
                    mysqli_stmt_bind_param($collStmt, "i", $paymentID);
                    mysqli_stmt_execute($collStmt);
                    $collResult = mysqli_stmt_get_result($collStmt);
                    if ($collRow = mysqli_fetch_assoc($collResult)) {
                        $guestName = $collRow['GuestName'] ?: 'N/A';
                    }
                    mysqli_stmt_close($collStmt);
                    mysqli_close($collectionConnection);
                } else {
                    error_log("Failed to connect to fin_collection: " . mysqli_connect_error());
                }
            }

            echo '
            <table class="mt-3 table table-bordered table-striped portrait-table shadow-lg">
                <tbody class="border-dark">
                    <tr>
                        <th>Transaction ID:</th>
                        <td>'.htmlspecialchars($row['TransactionID']).'</td>
                    </tr>
                    <tr>
                        <th>Guest Name:</th>
                        <td>'.htmlspecialchars($guestName).'</td>
                    </tr>
                    <tr>
                        <th>Total Amount:</th>
                        <td>'.htmlspecialchars($totalAmount).'</td>
                    </tr>
                    <tr>
                        <th>Transaction From:</th>
                        <td>'.htmlspecialchars($row['TransactionFrom']).'</td>
                    </tr>
                    <tr>
                        <th>Transaction Date:</th>
                        <td>'.htmlspecialchars($row['TransactionDate']).'</td>
                    </tr>
                </tbody>
            </table>';
        } else if($row['TransactionFrom'] == 'Budget' && $row['AllocationID'] != 0) {
            echo '
            <table class="mt-3 table table-bordered table-striped portrait-table shadow-lg">
                <tbody class="border-dark">
                    <tr>
                        <th>Transaction ID:</th>
                        <td>'.htmlspecialchars($row['TransactionID']).'</td>
                    </tr>
                    <tr>
                        <th>Allocation ID:</th>
                        <td>'.htmlspecialchars($row['AllocationID']).'</td>
                    </tr>
                    <tr>
                        <th>Budget Name:</th>
                        <td>'.htmlspecialchars($row['BudgetName']).'</td>
                    </tr>
                    <tr>
                        <th>Budget Allocated:</th>
                        <td>'.htmlspecialchars($row['BudgetAllocated']).'</td>
                    </tr>
                    <tr>
                        <th>Department Allocated:</th>
                        <td>'.htmlspecialchars($row['Allocated_Department']).'</td>
                    </tr>
                </tbody>
            </table>';
        } else if($row['TransactionFrom'] == 'Budget' && $row['AdjustmentID'] != 0) {
            echo '
            <table class="mt-3 table table-bordered table-striped portrait-table shadow-lg">
                <tbody class="border-dark">
                    <tr>
                        <th>Transaction ID:</th>
                        <td>'.htmlspecialchars($row['TransactionID']).'</td>
                    </tr>
                    <tr>
                        <th>Adjustment ID:</th>
                        <td>'.htmlspecialchars($row['AdjustmentID']).'</td>
                    </tr>
                    <tr>
                        <th>Budget Name:</th>
                        <td>'.htmlspecialchars($row['BudgetName']).'</td>
                    </tr>
                    <tr>
                        <th>Budget Allocated:</th>
                        <td>'.htmlspecialchars($row['BudgetAllocated']).'</td>
                    </tr>
                    <tr>
                        <th>Department Allocated:</th>
                        <td>'.htmlspecialchars($row['Allocated_Department']).'</td>
                    </tr>
                    <tr>
                        <th>Adjustment Amount:</th>
                        <td>'.htmlspecialchars($row['AdjustmentAmount']).'</td>
                    </tr>
                </tbody>
            </table>';
        } else if($row['TransactionFrom'] == 'Employee') {
            echo '
            <table class="mt-3 table table-bordered table-striped portrait-table shadow-lg">
                <tbody class="border-dark">
                    <tr>
                        <th>Transaction ID:</th>
                        <td>'.htmlspecialchars($row['TransactionID']).'</td>
                    </tr>
                    <tr>
                        <th>Payable Payment ID:</th>
                        <td>'.htmlspecialchars($row['PayablePaymentID']).'</td>
                    </tr>
                    <tr>
                        <th>Budget Name:</th>
                        <td>'.htmlspecialchars($row['BudgetName']).'</td>
                    </tr>
                    <tr>
                        <th>Budget Allocated:</th>
                        <td>'.htmlspecialchars($row['BudgetAllocated']).'</td>
                    </tr>
                    <tr>
                        <th>Department Allocated:</th>
                        <td>'.htmlspecialchars($row['Allocated_Department']).'</td>
                    </tr>
                    <tr>
                        <th>Payment Method:</th>
                        <td>'.htmlspecialchars($row['PaymentMethod']).'</td>
                    </tr>
                </tbody>
            </table>';
        } else if($row['TransactionFrom'] == 'Vendor') {
            echo '
            <table class="mt-3 table table-bordered table-striped portrait-table shadow-lg">
                <tbody class="border-dark">
                    <tr>
                        <th>Transaction ID:</th>
                        <td>'.htmlspecialchars($row['TransactionID']).'</td>
                    </tr>
                    <tr>
                        <th>Payable Payment ID:</th>
                        <td>'.htmlspecialchars($row['PayablePaymentID']).'</td>
                    </tr>
                    <tr>
                        <th>Budget Name:</th>
                        <td>'.htmlspecialchars($row['BudgetName']).'</td>
                    </tr>
                    <tr>
                        <th>Budget Allocated:</th>
                        <td>'.htmlspecialchars($row['BudgetAllocated']).'</td>
                    </tr>
                    <tr>
                        <th>Department Allocated:</th>
                        <td>'.htmlspecialchars($row['Allocated_Department']).'</td>
                    </tr>
                    <tr>
                        <th>Payment Method:</th>
                        <td>'.htmlspecialchars($row['PaymentMethod']).'</td>
                    </tr>
                </tbody>
            </table>';
        }
    } else {
        echo '<h4>No data record found!</h4>';
    }
    mysqli_stmt_close($stmt);
}
// #viewing btn end.
      // transview_data functions end.

if(isset($_POST['click_jentview_btn'])){    // jentview_data functions start. 
    $id = $_POST['jentry_id'];
    $fetch_query = "SELECT * FROM journalentries WHERE EntryID='$id' ";
    $fetch_query_run = mysqli_query($connection, $fetch_query);
    if(mysqli_num_rows($fetch_query_run) > 0) {
        while($row = mysqli_fetch_array($fetch_query_run)) {
            echo '
                <table class="mt-3 table table-bordered table-striped portrait-table shadow-lg ">
                    <tbody class="border-dark">
                        <tr>
                            <th>Entry ID:</th>
                            <td>'.$row['EntryID'].'</td>
                        </tr>
                        <tr>
                            <th>Account ID:</th>
                            <td>'.$row['AccountID'].'</td>
                        </tr>
                        <tr>
                            <th>Transaction ID:</th>
                            <td>'.$row['TransactionID'].'</td>
                        </tr>
                        <tr>
                            <th>Entry Type:</th>
                            <td>'.$row['EntryType'].'</td>
                        </tr>
                        <tr>
                            <th>Entry Date:</th>
                            <td>'.$row['EntryDate'].'</td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>'.$row['Description'].'</td>
                        </tr>
                </tbody>
            </table>
            ';}}else { echo '<h4>no data record found!</h4>';} }       // jentview_data functions end.

if(isset($_POST['click_accview_btn'])){     // accview_data functions start.
    $id = $_POST['acc_id'];
    $fetch_query = "SELECT * FROM accounts WHERE AccountID='$id' ";
    $fetch_query_run = mysqli_query($connection, $fetch_query);
        if(mysqli_num_rows($fetch_query_run) > 0) {
            while($row = mysqli_fetch_array($fetch_query_run)) {
                echo '
                <table class="mt-3 table table-bordered table-striped portrait-table shadow-lg ">
                    <tbody class="border-dark">
                        <tr>
                            <th>Account ID:</th>
                            <td>'.$row['AccountID'].'</td>
                        </tr>
                        <tr>
                            <th>Account Name:</th>
                            <td>'.$row['AccountName'].'</td>
                        </tr>
                        <tr>
                            <th>Account Type::</th>
                            <td>'.$row['AccountType'].'</td>
                        </tr>
                </tbody>
            </table>
                ';}}else { echo '<h4>no data record found!</h4>';} }       // accview_data functions end.
// viewing btn end.

// #editing btn start.
if(isset($_POST['click_transedit_btn'])) { // transedit_data functions start.
    $id = $_POST['trans_id'];
    $arrayresult = [];
    $fetch_query = "SELECT * FROM transactions WHERE TransactionID='$id'";
    $fetch_query_run = mysqli_query($connection, $fetch_query);
    if(mysqli_num_rows($fetch_query_run) > 0) {
        while($row = mysqli_fetch_array($fetch_query_run)) {
            $row['TransactionDate'] = date('Y-m-d\TH:i', strtotime($row['TransactionDate']));
            array_push($arrayresult, $row);
        }
        header('content-type: application/json');
        echo json_encode($arrayresult);
    } else {
        echo '<h4>No data record found!</h4>';
    }}// transedit_data functions end.

if(isset($_POST['click_jentryedit_btn'])) { // jentryedit_data functions start.
        $id = $_POST['jent_id'];
        $arrayresult = [];
        $fetch_query = "SELECT * FROM journalentries WHERE EntryID='$id' ";
        $fetch_query_run = mysqli_query($connection, $fetch_query);
    
        if(mysqli_num_rows($fetch_query_run) > 0) {
            while($row = mysqli_fetch_array($fetch_query_run)) {
                // Format EntryDate for datetime-local input
                $row['EntryDate'] = date('Y-m-d\TH:i', strtotime($row['EntryDate']));
                array_push($arrayresult, $row);
            }
            header('Content-Type: application/json');
            echo json_encode($arrayresult);
        } else {
            echo '<h4>No data record found!</h4>';
        }
    }       // jentryedit_data functions end.

if(isset($_POST['click_accedit_btn'])){   // accedit_data functions start.
    $id = $_POST['acc_id'];
    $arrayresult = [];
    $fetch_query = "SELECT * FROM accounts WHERE AccountID='$id' ";
    $fetch_query_run = mysqli_query($connection, $fetch_query);
        if(mysqli_num_rows($fetch_query_run) > 0) {
            while($row = mysqli_fetch_array($fetch_query_run)) {
                array_push($arrayresult, $row);
                header('content-type: application/json');
                echo json_encode($arrayresult);

                }}else { echo '<h4>no data record found!</h4>';} }       // accedit_data functions end.

// editing btn end.

// #update btn start.
if(isset($_POST['update_transdata'])){ // #update trans-data-start.
    $transactionid = $_POST['transactionid'];
    $entryid = $_POST['entryid'];
    $paymentid = $_POST['paymentid'];
    $transactionfrom = $_POST['transactionfrom'];
    $transactiondate = $_POST['transactiondate'];

    $update_query = "UPDATE transactions SET entryid='$entryid',paymentid='$paymentid',transactionfrom='$transactionfrom', transactiondate='$transactiondate' WHERE transactionid='$transactionid' ";
    $update_query_run = mysqli_query($connection, $update_query);
    
    if($update_query_run) {
        $_SESSION['status'] = "Data updated successfully!";
    } else {
        $_SESSION['status'] = "Data not-updated!";
    }
    header("Location: General-Ledger-Transactions.php");
    }// #update trans-data-end.

if(isset($_POST['update_jentrydata'])){ // #update jentry-data-start.
    $entryid = $_POST['entryid'];
    $jentryaccid = $_POST['jentryaccid'];
    $jentrytransid = $_POST['jentrytransid'];
    $entrytype = $_POST['entrytype'];
    $amount = $_POST['amount'];
    $jentrydate = $_POST['jentrydate'];
    $description = $_POST['description'];
    
    $update_query = "UPDATE journalentries SET 
                    AccountID='$jentryaccid', 
                    TransactionID='$jentrytransid',
                    EntryType='$entrytype',
                    Amount='$amount',
                    EntryDate='$jentrydate',
                    Description='$description' 
                    WHERE EntryID='$entryid'";
    $update_query_run = mysqli_query($connection, $update_query);
        
    if($update_query_run){
        $_SESSION['status'] = "data updated successfully!";
        header("Location: General-Ledger-Journal-Entries.php");
    } else {
        $_SESSION['status'] = "data not-updated!";
        header("Location: General-Ledger-Journal-Entries.php");
    }}// #update jentry-data-end.

if(isset($_POST['update_accdata'])){
    $accountid = $_POST['accountid'];
    $accname = $_POST['accname'];
    $acctype = $_POST['acctype'];
    
    $update_query = "UPDATE accounts SET AccountName='$accname', AccountType='$acctype' WHERE AccountID='$accountid'";
    $update_query_run = mysqli_query($connection, $update_query);
        
    if($update_query_run){
        $_SESSION['status'] = "Data updated successfully!";
    } else {
        $_SESSION['status'] = "Data not updated!";
    }
    header("Location: General-Ledger-Account.php");
}// #update acc-data-end.
// update btn end.

// #deleting btn start.
if(isset($_POST['delete_transdata'])) {// #deleting transaction btn start.
    $trans_id = $_POST['trans_id'];
    
    if (!empty($trans_id) && is_numeric($trans_id)) {
        $delete_query = "DELETE FROM transactions WHERE TransactionID = '$trans_id'";
        $delete_query_run = mysqli_query($connection, $delete_query);

        if($delete_query_run) {
            $_SESSION['status'] = "Data deleted successfully!";
        } else {
            $_SESSION['status'] = "Data deletion failed!";
        }
    } else {
        $_SESSION['status'] = "Invalid Transaction ID!";
    }
    header("Location: General-Ledger-Transactions.php");
    exit();}// deleting transaction btn end.

if(isset($_POST['delete_jentrydata'])) {// #deleting jentry btn start.
    $jentry_id = $_POST['jentry_id'];
    
    if (!empty($jentry_id) && is_numeric($jentry_id)) {
        $delete_query = "DELETE FROM journalentries WHERE EntryID = '$jentry_id'";
        $delete_query_run = mysqli_query($connection, $delete_query);

        if($delete_query_run) {
            $_SESSION['status'] = "Data deleted successfully!";
        } else {
            $_SESSION['status'] = "Data deletion failed!";
        }
    } else {
        $_SESSION['status'] = "Invalid Transaction ID!";
    }
    header("Location: General-Ledger-Journal-Entries.php");
    exit();}// deleting jentry btn end.

if(isset($_POST['delete_accdata'])) {// #deleting account btn start.
    $acc_id = $_POST['acc_id'];
    
    if (!empty($acc_id) && is_numeric($acc_id)) {
        $delete_query = "DELETE FROM accounts WHERE AccountID = '$acc_id'";
        $delete_query_run = mysqli_query($connection, $delete_query);

        if($delete_query_run) {
            $_SESSION['status'] = "Data deleted successfully!";
        } else {
            $_SESSION['status'] = "Data deletion failed!";
        }
    } else {
        $_SESSION['status'] = "Invalid Transaction ID!";
    }
    header("Location: General-Ledger-Account.php");
    exit();}// deleting account btn end.
// deleting btn end.
?>