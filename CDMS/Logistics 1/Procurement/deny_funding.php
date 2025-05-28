<?php
session_start();
include("../../connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['funding_id'])) {
    $funding_id = $_POST['funding_id'];
    $conn = $connections['logs1_procurement'];

    $stmt = $conn->prepare("UPDATE for_funding SET status = 'Funds requisition was cancelled' WHERE funding_id = ?");
    $stmt->bind_param("s", $funding_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>
        alert('Funding denied.');
        window.location.href = 'For_funding.php';
    </script>";
}
?>
