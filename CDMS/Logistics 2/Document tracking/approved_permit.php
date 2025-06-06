<?php
session_start();
include("../../connection.php");

$db_name = "logs1_procurement";
$log2_dt = "logs2_document_tracking";
$log1_pm = "logs1_project_management";
$usm_db = "logs1_usm";

$conn = $connections[$db_name];
$log2_conn = $connections[$log2_dt];
$log1_pm_conn = $connections[$log1_pm];

$usm_conn = $connections[$usm_db];

// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['permit_id'])) {
    $permit_id = urldecode($_POST['permit_id']);

    // Step 1: Get permit details including status
    $getPermit = $log2_conn->prepare("
        SELECT User_ID, item_name, requested_date, purpose, type_of_item, submitted_by, status 
        FROM permits_approval 
        WHERE permit_id = ?
    ");
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

    // ✅ Step 2: Block if already processed
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

    // ✅ Step 3: Approve the permit
    $approveStmt = $log2_conn->prepare("
        UPDATE permits_approval SET status = 'Permit Approved' WHERE permit_id = ?
    ");
    $approveStmt->bind_param("s", $permit_id);
    $approveStmt->execute();
    $approveStmt->close();

    // ✅ Step 3.5: Update asset status if asset_id is present
    $checkAssetStmt = $log2_conn->prepare("
        SELECT asset_id FROM permits_approval WHERE permit_id = ? AND asset_id IS NOT NULL
    ");
    $checkAssetStmt->bind_param("s", $permit_id);
    $checkAssetStmt->execute();
    $checkAssetResult = $checkAssetStmt->get_result();

    if ($checkAssetResult->num_rows > 0) {
        $assetRow = $checkAssetResult->fetch_assoc();
        $asset_id = $assetRow['asset_id'];

        // Update asset status in logs1_asset
        $assetConn = $connections['logs1_asset'];
        $updateAsset = $assetConn->prepare("
            UPDATE assets SET asset_status = 'Asset added permitted' WHERE asset_id = ?
        ");
        $updateAsset->bind_param("i", $asset_id);
        $updateAsset->execute();
        $updateAsset->close();
    }
    $checkAssetStmt->close();

    // ✅ Step 3.6: Update project status if project_id is present
    $checkProjectStmt = $log2_conn->prepare("
        SELECT project_id FROM permits_approval WHERE permit_id = ? AND project_id IS NOT NULL
    ");
    $checkProjectStmt->bind_param("s", $permit_id);
    $checkProjectStmt->execute();
    $checkProjectResult = $checkProjectStmt->get_result();

    if ($checkProjectResult->num_rows > 0) {
        $projectRow = $checkProjectResult->fetch_assoc();
        $project_id = $projectRow['project_id'];

        // Update project status in logs1_project_management
        $projectConn = $connections['logs1_project_management'];
        $updateProject = $projectConn->prepare("
            UPDATE project SET project_status = 'Project creation permitted' WHERE project_id = ?
        ");
        $updateProject->bind_param("i", $project_id);
        $updateProject->execute();
        $updateProject->close();
    }
    $checkProjectStmt->close();

    // ✅ Step 4: Update purchase_request and insert into for_funding
    $findPurchase = $conn->prepare("
        SELECT purchase_id, estimated_budget 
        FROM purchase_request 
        WHERE User_ID = ? AND item_name = ? 
        ORDER BY requested_date DESC LIMIT 1
    ");
    $findPurchase->bind_param("ss", $user_id, $item_name);
    $findPurchase->execute();
    $result = $findPurchase->get_result();

    if ($result->num_rows > 0) {
        $purchase = $result->fetch_assoc();
        $purchase_id = $purchase['purchase_id'];
        $estimated_budget = $purchase['estimated_budget'];
        $findPurchase->close();

        // Update purchase_request status
        $updatePurchase = $conn->prepare("
            UPDATE purchase_request SET status = 'Permit Approved' WHERE purchase_id = ?
        ");
        $updatePurchase->bind_param("i", $purchase_id);
        $updatePurchase->execute();
        $updatePurchase->close();

        // Insert into for_funding
        $funding_status = "Pending for funds request";
        $insertFunding = $conn->prepare("
            INSERT INTO for_funding 
            (User_ID, requested_date, status, purpose, type_of_item, estimated_budget, submitted_by, item_name)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $insertFunding->bind_param("sssssdss", $user_id, $requested_date, $funding_status, $purpose, $type_of_item, $estimated_budget, $submitted_by, $item_name);
        $insertFunding->execute();
        $insertFunding->close();
    }

    // ✅ Step 5: Notification
    $notification_title = "Purchase Request Approved";
    $notification_message = "<strong>$submitted_by</strong>'s permit for \"<strong>$item_name</strong>\" has been approved.";
    $notification_status = "Unread";
    $date_sent = date("Y-m-d H:i:s");
    $recipient_role = $submitted_by;
    $module = "Document Tracking";

    $notifStmt = $log2_conn->prepare("
        INSERT INTO notification_dt 
        (title, message, status, date_sent, sent_by, User_ID, recipient_role, module) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $notifStmt->bind_param("ssssssss", $notification_title, $notification_message, $notification_status, $date_sent, $submitted_by, $user_id, $recipient_role, $module);
    $notifStmt->execute();
    $notifStmt->close();

    // ✅ Step 6: Feedback to user
    echo '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Approved</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: "success",
                title: "Permit Approved",
                text: "The request for \"' . addslashes($item_name) . '\" has been Approved.",
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
