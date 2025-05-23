<?php

$databases = ["cr3_re"];
$connections = [];

foreach ($databases as $db) {
    $connection = mysqli_connect("localhost:3307", "root", "", $db);
    if (!$connection) {
        error_log("Connection failed to $db: " . mysqli_connect_error());
        header("Location: ../testing/login.php");
        exit();
    }
    $connections[$db] = $connection;
}
?>