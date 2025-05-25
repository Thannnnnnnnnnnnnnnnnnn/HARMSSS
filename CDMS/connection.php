<?php

$dbHost = "127.0.0.1";
$dbUser = "3206_CENTRALIZED_DATABASE";
$dbPass = "4562526";

$connections = [];
$errors = [];

// Step 1: Connect without specifying a database
$mainConn = mysqli_connect($dbHost, $dbUser, $dbPass);

if (!$mainConn) {
    die("❌ Unable to connect to MySQL: " . mysqli_connect_error());
}

// Step 2: Fetch all databases
$dbList = mysqli_query($mainConn, "SHOW DATABASES");

if (!$dbList) {
    die("❌ Unable to fetch database list: " . mysqli_error($mainConn));
}

while ($row = mysqli_fetch_assoc($dbList)) {
    $dbName = $row['Database'];

    // Step 3: Attempt connection to each DB
    $conn = @mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

    if ($conn) {
        $connections[$dbName] = $conn;
    } else {
        $errors[] = "❌ Failed to connect to <strong>$dbName</strong>: " . mysqli_connect_error();
    }
}

if (!empty($errors)) {
    echo "<h3 style='color:red;'>Errors:</h3><ul>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>";
}
