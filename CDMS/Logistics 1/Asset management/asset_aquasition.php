<?php
session_start();
include("../../connection.php");

$conn = $connections["logs1_asset"];
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asset_id = $_POST['asset_id'] ?? '';
    $action = $_POST['action'] ?? '';

    if (!$asset_id || !$action) {
        echo json_encode(['success' => false, 'message' => 'Missing parameters.']);
        exit;
    }

    $approval = ($action === 'approve') ? "Asset added permitted" : "Asset denied";

    // Ensure asset exists
    $check = $conn->prepare("SELECT asset_id FROM assets WHERE asset_id = ?");
    $check->bind_param("i", $asset_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Asset not found.']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE assets SET approval = ? WHERE asset_id = ?");
    $stmt->bind_param("si", $approval, $asset_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => "Asset $approval successfully."]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
    }

    $stmt->close();
}
?>
