<?php
/**
 * Database Connection Script
 *
 * Establishes a PDO connection to the database.
 * Reads credentials from environment variables for improved security.
 */

// --- Error Reporting (Keep logging enabled, but don't display errors) ---
error_reporting(E_ALL);
ini_set('display_errors', 0); // Should be 0 in production
ini_set('log_errors', 1);
// ini_set('error_log', '/path/to/your/php-error.log'); // Optional: Set a specific log file path

// --- Database Configuration ---
// Update these to your HR 1-2 Database Credentials
$db_host = getenv('DB_HOST_HR12') ?: '127.0.0.1';
$db_name = getenv('DB_NAME_HR12') ?: 'hr_1_2_new_hire_onboarding_and_employee_self-service'; // HR 1-2 DB Name
$db_user = getenv('DB_USER_HR12') ?: 'root';      // HR 1-2 DB User
$db_pass = getenv('DB_PASS_HR12') ?: '';          // HR 1-2 DB Password
$charset = 'utf8mb4';

// --- Validate that essential variables were loaded ---
if ($db_host === '127.0.0.1' && getenv('DB_HOST_HR12') === false) {
    error_log("Warning: DB_HOST_HR12 environment variable not set. Using default '127.0.0.1'.");
}
if ($db_name === 'hr_1_2_new_hire_onboarding_and_employee_self-service' && getenv('DB_NAME_HR12') === false) {
    error_log("Warning: DB_NAME_HR12 environment variable not set. Using default 'hr_1_2_new_hire_onboarding_and_employee_self-service'.");
}
if ($db_user === 'root' && getenv('DB_USER_HR12') === false) {
     error_log("Warning: DB_USER_HR12 environment variable not set. Using default 'root'.");
}

// --- Data Source Name (DSN) ---
$dsn = "mysql:host={$db_host};dbname={$db_name};charset={$charset}";

// --- PDO Options ---
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements for security
];

// --- Establish Connection ---
$pdo = null; 
try {
     $pdo = new PDO($dsn, $db_user, $db_pass, $options);
     // error_log("Database connection successful to {$db_name}@{$db_host}");

} catch (\PDOException $e) {
     error_log("Database Connection Error: " . $e->getMessage() . " (DSN: " . $dsn . ", User: " . $db_user . ")");
     if (!headers_sent()) {
        header('Content-Type: application/json');
        http_response_code(500); 
     }
     die(json_encode(['error' => 'Database connection failed. Please contact the administrator. DB Name: ' . $db_name]));
}

// The $pdo variable is now available for use in the script that includes this file.
?>
