<?php
session_start();
include("../../connection.php"); // Ensure this includes $connections[]

$db_name = "logs1_procurement";
$log2_dt = "logs2_document_tracking";
$usm_db = "logs1_usm"; // User database

if (!isset($connections[$db_name]) || !isset($connections[$usm_db])) {
    die("Database connection not found.");
}

$conn = $connections[$db_name];
$usm_conn = $connections[$usm_db];
$log2_conn = $connections[$log2_dt];


// Debugging only (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize form inputs
    $type_of_item = trim($_POST['type_of_item'] ?? '');
    $item_name = trim($_POST['item_name'] ?? '');
    $quantity = intval($_POST['quantity'] ?? 0);
    $estimated_budget = floatval($_POST['estimated_budget'] ?? 0);
    $purpose = trim($_POST['purpose'] ?? '');
    $requested_date = trim($_POST['requested_date'] ?? '');
    $status = "For permit approval"; // Default purchase_request status

    // Check user session
    if (!isset($_SESSION['User_ID'])) {
        die("User session expired. Please log in again.");
    }
    $user_id = $_SESSION['User_ID'];

    // Fetch user info if not in session
    if (!isset($_SESSION['Name']) || !isset($_SESSION['Role'])) {
        $getUser = $usm_conn->prepare("SELECT Name, Role FROM department_accounts WHERE User_ID = ?");
        $getUser->bind_param("s", $user_id);
        $getUser->execute();
        $result = $getUser->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $name = $user['Name'];
            $Role = $user['Role'];
            $_SESSION['Name'] = $name;
            $_SESSION['Role'] = $Role;
        } else {
            die("User not found in logs1_usm.");
        }
        $getUser->close();
    } else {
        $name = $_SESSION['Name'];
        $Role = $_SESSION['Role'];
    }

    // Insert into purchase_request
    $stmt = $conn->prepare("INSERT INTO purchase_request (type_of_item, item_name, quantity, estimated_budget, purpose, requested_date, submitted_by, User_ID, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssidsisss", $type_of_item, $item_name, $quantity, $estimated_budget, $purpose, $requested_date, $user_id, $user_id, $status);

    if ($stmt->execute()) {
        $stmt->close();

        // ✅ Insert into permits_approval
        $permit_status = "For permit approval";
        $permitStmt = $log2_conn->prepare("INSERT INTO permits_approval (User_ID, requested_date, status, purpose, type_of_item, submitted_by, item_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$permitStmt) {
            die("Permit prepare failed: " . $log2_conn->error);
        }
        $permitStmt->bind_param("sssssss", $user_id, $requested_date, $permit_status, $purpose, $type_of_item, $name, $item_name);
        if (!$permitStmt->execute()) {
            die("Permit insert failed: " . $permitStmt->error);
        }
        $permitStmt->close();

        // ✅ Insert Notification
        $notification_title = "New Purchase Request Submitted";
        $notification_message = "<strong>$name</strong> has submitted a purchase request for: <strong>\"$item_name\"</strong>.";
        $notification_status = "Unread";
        $date_sent = date("Y-m-d H:i:s");
        $recipient_role = $Role;
        $module = "Purchase Management";

        $notifStmt = $conn->prepare("INSERT INTO notification_pr (title, message, status, date_sent, sent_by, User_ID, recipient_role, module) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$notifStmt) {
            die("Notification prepare failed: " . $conn->error);
        }

        $notifStmt->bind_param("ssssssss", $notification_title, $notification_message, $notification_status, $date_sent, $name, $user_id, $recipient_role, $module);

        if (!$notifStmt->execute()) {
            die("Notification execute failed: " . $notifStmt->error);
        }
        $notifStmt->close();

        // ✅ SweetAlert2 success and redirect
        echo '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Request Submitted</title>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
Swal.fire({
    icon: "success",
    title: "Request Submitted!",
    text: "Your purchase request for \"' . addslashes($item_name) . '\" was submitted successfully.",
    confirmButtonText: "OK"
}).then(() => {
    window.location.href = "purchase_request.php";
});
</script>
</body>
</html>';
        exit;
    } else {
        die("Execute failed: " . $stmt->error);
    }
} else {
    header("Location: purchase_request.php");
    exit;
}
?>
