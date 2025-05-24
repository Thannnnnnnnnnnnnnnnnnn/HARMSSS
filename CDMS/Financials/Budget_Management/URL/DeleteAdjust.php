<?php
include("../config/AdjustController.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['DeleteID'])) {
    $DeleteID = $_POST['DeleteID']; 
    $delete = new Data();
    $deleteSuccess = $delete->Delete($DeleteID);

    if ($deleteSuccess) {
        echo "success"; 
    } else {
        echo "error";
    }
    exit();
}
?>
