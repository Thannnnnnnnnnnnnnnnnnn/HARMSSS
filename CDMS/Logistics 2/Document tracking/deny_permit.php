<?php
session_start();
include("../../connection.php");

$db_name = "logs1_procurement";
$log2_dt = "logs2_document_tracking";
$usm_db = "logs1_usm";

$conn = $connections[$db_name];
$log2_conn = $connections[$log2_dt];
$usm_conn = $connections[$usm_db];

// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['permit_id'])) {
    $permit_id = urldecode($_POST['permit_id']);

    // Step 1: Get permit details including status
    $getPermit = $log2_conn->prepare("SELECT User_ID, item_name, requested_date, purpose, type_of_item, submitted_by, status FROM permits_approval WHERE permit_id = ?");
    $getPermit->bind_param("s", $permit_id);
    $getPermit->execute();
    $permitResult = $getPermit->get_result();

    if ($permitResult->num_rows === 0) {
        die("Permit not found.");
    }

    $permit = $permitResult->fetch_assoc();
    $user_id = $permit['User_ID'];
    $item_name = $permit['item_name'];
    $requested_date = $permit['requested_date'];
    $purpose = $permit['purpose'];
    $type_of_item = $permit['type_of_item'];
    $submitted_by = $permit['submitted_by'];
    $permit_status = $permit['status'];

    $getPermit->close();

    // ✅ Step 2: Block if status is already Denied or Approved
    if (in_array($permit_status, ['Permit Denied', 'Permit Approved'])) {
        echo '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Already Processed</title>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: "info",
                    title: "Action Not Allowed",
                    text: "This permit has already been ' . addslashes($permit_status) . ' and cannot be modified.",
                    confirmButtonText: "OK"
                }).then(() => {
                    window.location.href = "permits_approvals.php";
                });
            </script>
        </body>
        </html>';
        exit;
    }

    // Step 3: Update permits_approval to Denied
    $denyStmt = $log2_conn->prepare("UPDATE permits_approval SET status = 'Permit Denied' WHERE permit_id = ?");
    $denyStmt->bind_param("s", $permit_id);
    $denyStmt->execute();
    $denyStmt->close();

    // Step 4: Update purchase_request status using purchase_id
    $findPurchase = $conn->prepare("SELECT purchase_id FROM purchase_request WHERE User_ID = ? AND item_name = ? ORDER BY requested_date DESC LIMIT 1");
    $findPurchase->bind_param("ss", $user_id, $item_name);
    $findPurchase->execute();
    $result = $findPurchase->get_result();

    if ($result->num_rows > 0) {
        $purchase = $result->fetch_assoc();
        $purchase_id = $purchase['purchase_id'];
        $findPurchase->close();

        $updatePurchase = $conn->prepare("UPDATE purchase_request SET status = 'Permit denied' WHERE purchase_id = ?");
        $updatePurchase->bind_param("i", $purchase_id);
        $updatePurchase->execute();
        $updatePurchase->close();
    }

    // Step 5: Send notification
    $notification_title = "Purchase Request Denied";
    $notification_message = "<strong>$submitted_by</strong>'s permit for \"<strong>$item_name</strong>\" has been denied.";
    $notification_status = "Unread";
    $date_sent = date("Y-m-d H:i:s");
    $recipient_role = $submitted_by; // Adjust to correct role
    $module = "Purchase Management";

    $notifStmt = $conn->prepare("INSERT INTO notification_dt (title, message, status, date_sent, sent_by, User_ID, recipient_role, module) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $notifStmt->bind_param("ssssssss", $notification_title, $notification_message, $notification_status, $date_sent, $submitted_by, $user_id, $recipient_role, $module);
    $notifStmt->execute();
    $notifStmt->close();

    // Step 6: Show success message
    echo '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Denied</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: "success",
                title: "Permit Denied",
                text: "The request for \"' . addslashes($item_name) . '\" has been denied.",
                confirmButtonText: "OK"
            }).then(() => {
                window.location.href = "permits_approvals.php";
            });
        </script>
    </body>
    </html>';
    exit;
}
?>
