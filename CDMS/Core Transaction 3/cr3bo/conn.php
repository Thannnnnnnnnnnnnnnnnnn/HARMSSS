<?php

$databases = ["cr3_re", "cr3_re_usm"];
$connections = [];

foreach ($databases as $db) {
    $connection = mysqli_connect("localhost:3206", "root", "", $db);
    if (!$connection) {
        error_log("Connection failed to $db: " . mysqli_connect_error());
        header("Location: ../testing/login.php");
        exit();
    }
    $connections[$db] = $connection;
}
