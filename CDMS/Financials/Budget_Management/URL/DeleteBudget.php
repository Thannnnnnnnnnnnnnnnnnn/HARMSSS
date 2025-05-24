<?php
include("../config/controller.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['BudgetID'])) {
    $budgetid = $_POST['BudgetID']; 
    $delete = new Data();
    $deleteSuccess = $delete->Delete($budgetid);

    if ($deleteSuccess) {
        echo "success"; 
    } else {
        echo "error";
    }
    exit();
}
?>
