<?php
// test_hr12_db.php
// Place this file in your hr34/php/api/ directory.
// Access it via your browser, e.g., http://localhost/hr34/php/api/test_hr12_db.php

error_reporting(E_ALL);
ini_set('display_errors', 1); // Temporarily display errors for this test script

echo "<h1>Testing HR 1-2 Database Connection</h1>";

// --- HR 1-2 Database Connection Details ---
// !! IMPORTANT !!
// REPLACE these with your actual HR 1-2 Database credentials.
$db_host_hr12 = 'localhost'; // Your HR 1-2 DB host (often 'localhost' or '127.0.0.1')
$db_name_hr12 = 'hr_1_2_new_hire_onboarding_and_employee_self-service'; // The target database name
$db_user_hr12 = 'root'; // REPLACE with your HR 1-2 DB username
$db_pass_hr12 = ''; // REPLACE with your HR 1-2 DB password
$charset_hr12 = 'utf8mb4';

echo "<p>Attempting to connect to database: <strong>" . htmlspecialchars($db_name_hr12) . "</strong> on host <strong>" . htmlspecialchars($db_host_hr12) . "</strong> with user <strong>" . htmlspecialchars($db_user_hr12) . "</strong>.</p>";

$dsn_hr12 = "mysql:host={$db_host_hr12};dbname={$db_name_hr12};charset={$charset_hr12}";
$options_hr12 = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo_hr12 = null;
try {
    $pdo_hr12 = new PDO($dsn_hr12, $db_user_hr12, $db_pass_hr12, $options_hr12);
    echo "<p style='color:green; font-weight:bold;'>SUCCESS: Successfully connected to the HR 1-2 database ('" . htmlspecialchars($db_name_hr12) . "')!</p>";

    // Optional: Test a simple query to verify table access
    echo "<p>Attempting a simple query on the 'employees' table...</p>";
    $stmt = $pdo_hr12->query("SELECT EmployeeID, FirstName, LastName FROM employees LIMIT 1");
    if ($stmt) {
        $row = $stmt->fetch();
        if ($row) {
            echo "<p style='color:green;'>SUCCESS: Successfully fetched a record from the 'employees' table:</p>";
            echo "<pre>" . htmlspecialchars(print_r($row, true)) . "</pre>";
        } else {
            echo "<p style='color:orange;'>NOTICE: Connected to the database, but the 'employees' table appears to be empty or the query returned no results.</p>";
        }
    } else {
        echo "<p style='color:red;'>ERROR: Failed to execute the test query on the 'employees' table.</p>";
    }

    echo "<p>Attempting a simple query on the 'departments' table...</p>";
    $stmt_dept = $pdo_hr12->query("SELECT dept_id, department_name FROM departments LIMIT 1");
    if ($stmt_dept) {
        $row_dept = $stmt_dept->fetch();
        if ($row_dept) {
            echo "<p style='color:green;'>SUCCESS: Successfully fetched a record from the 'departments' table:</p>";
            echo "<pre>" . htmlspecialchars(print_r($row_dept, true)) . "</pre>";
        } else {
            echo "<p style='color:orange;'>NOTICE: Connected to the database, but the 'departments' table appears to be empty or the query returned no results.</p>";
        }
    } else {
        echo "<p style='color:red;'>ERROR: Failed to execute the test query on the 'departments' table.</p>";
    }


} catch (PDOException $e) {
    echo "<p style='color:red; font-weight:bold;'>ERROR: Database Connection Failed!</p>";
    echo "<p><strong>Error Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>DSN Used:</strong> " . htmlspecialchars($dsn_hr12) . "</p>";
    echo "<p><strong>Troubleshooting Tips:</strong></p>";
    echo "<ul>";
    echo "<li>Verify that the database server at '<strong>" . htmlspecialchars($db_host_hr12) . "</strong>' is running.</li>";
    echo "<li>Ensure the database named '<strong>" . htmlspecialchars($db_name_hr12) . "</strong>' exists.</li>";
    echo "<li>Double-check the username ('<strong>" . htmlspecialchars($db_user_hr12) . "</strong>') and password.</li>";
    echo "<li>Make sure the database user has permission to connect to the database from your web server's IP address (or 'localhost'/'127.0.0.1').</li>";
    echo "<li>Check if the MySQL/MariaDB port (default 3306) is open if connecting to a remote host.</li>";
    echo "</ul>";
}

echo "<h2>Test Complete</h2>";
?>