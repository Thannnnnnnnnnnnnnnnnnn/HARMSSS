<?php
session_start();
include '../../connection.php'; // adjust path as needed

// Define the database names
$db_name = "logs1_asset";
$db_logs1_usm = "logs1_usm";
$db_logs2_doc = "logs2_document_tracking";

if (!isset($connections[$db_name]) || !isset($connections[$db_logs1_usm]) || !isset($connections[$db_logs2_doc])) {
    die("One or more database connections are missing.");
}

$conn = $connections[$db_name];
$logs1_usm_conn = $connections[$db_logs1_usm];
$logs2_doc_conn = $connections[$db_logs2_doc];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asset_id = $_POST['asset_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if (!$asset_id || !in_array($action, ['approve', 'deny'])) {
        die("Invalid request.");
    }

    $asset_id = intval($asset_id);

    // Set custom status
    $status = ($action === 'approve') ? 'Asset successfully added' : 'Denied';

    $stmt = $conn->prepare("UPDATE assets SET asset_status = ? WHERE asset_id = ?");
    $stmt->bind_param("si", $status, $asset_id);

    if ($stmt->execute()) {
        // Optional redirect
        header("Location: add_asset.php?success=1");
        exit();
    } else {
        echo "Error updating asset: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid access method.";
}
?>
