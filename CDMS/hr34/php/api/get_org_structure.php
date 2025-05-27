<?php
/**
 * API Endpoint: Get Departments (from HR 1-2)
 * Retrieves a flat list of departments from the HR 1-2 database.
 */

error_reporting(E_ALL);
ini_set('display_errors', 0); 
ini_set('log_errors', 1);
// ini_set('error_log', __DIR__ . '/../../php-error.log');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// --- Database Connection (Uses the main $pdo from db_connect.php) ---
$pdo = null;
try {
    require_once '../db_connect.php'; // This now connects to the unified HR 1-2 database
    if (!isset($pdo) || !$pdo instanceof PDO) {
        throw new Exception('Database connection object ($pdo) not properly created by db_connect.php.');
    }
} catch (Throwable $e) {
    error_log("PHP Error in get_org_structure.php (db_connect include): " . $e->getMessage());
    if (!headers_sent()) { http_response_code(500); }
    echo json_encode(['error' => 'Server configuration error: Could not connect to the database. DB Name expected: hr_1_2_new_hire_onboarding_and_employee_self-service']);
    exit;
}
// --- End Database Connection ---

try {
    // Fetch all departments from the HR 1-2 'departments' table
    $sql = "SELECT 
                d.dept_id AS DepartmentID,  -- Alias to match expected frontend if necessary
                d.department_name AS DepartmentName
            FROM 
                departments d -- This is the HR 1-2 departments table
            ORDER BY 
                d.department_name ASC";
    
    $stmt = $pdo->query($sql);
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (headers_sent()) { exit; } 
    http_response_code(200);
    echo json_encode($departments); // This will be a flat list

} catch (PDOException $e) {
    error_log("API Error (get_org_structure from HR 1-2 departments): " . $e->getMessage());
    if (!headers_sent()) { http_response_code(500); }
    echo json_encode(['error' => 'Failed to retrieve department list. Details: ' . $e->getMessage()]);
} catch (Throwable $e) {
    error_log("PHP Throwable in get_org_structure.php: " . $e->getMessage());
    if (!headers_sent()) { http_response_code(500); }
    echo json_encode(['error' => 'Unexpected server error.']);
}
exit;
?>
