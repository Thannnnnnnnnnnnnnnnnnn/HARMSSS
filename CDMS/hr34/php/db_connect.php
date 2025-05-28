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

// --- START: Simplified Default Admin Session (FOR DEVELOPMENT/SIMPLIFIED NO-LOGIN MODE ONLY) ---
// Ensure session is started before trying to access/modify $_SESSION.
// Most API scripts call session_start() themselves, but this makes it robust.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// If no user_id in session, and we are in this simplified mode, set default admin.
// THIS IS A SECURITY RISK IN A PRODUCTION SYSTEM and is only for the bypassed login setup.
if (!isset($_SESSION['user_id'])) {
    // These values should correspond to your default admin user in the database
    // Assuming EmployeeID 1 ('System Administrator' from Employees table) maps to UserID 5 ('sysadmin' from Users table) 
    // and RoleID 1 ('System Admin' from Roles table)
    $_SESSION['user_id'] = 5;     // UserID of your default System Admin from the 'Users' table
    $_SESSION['employee_id'] = 1; // EmployeeID of your default System Admin from the 'Employees' table
    $_SESSION['username'] = 'sysadmin';  // Username of your default System Admin
    $_SESSION['full_name'] = 'System Administrator (Default)'; // Full name
    $_SESSION['role_id'] = 1;     // RoleID for 'System Admin'
    $_SESSION['role_name'] = 'System Admin'; // RoleName for 'System Admin'
    
    // Log that the default session was initialized (optional, for debugging)
    // error_log("Simplified Mode: Default admin session initialized in db_connect.php for UserID: " . $_SESSION['user_id']);
}
// --- END: Simplified Default Admin Session ---

?>
