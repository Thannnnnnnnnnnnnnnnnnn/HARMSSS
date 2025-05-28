<?php
/**
 * API Endpoint: Get Departments
 * Retrieves a list of departments from the 'departments' table
 * and includes a count of active employees in each department.
 * v2.0 - Added EmployeeCount.
 */

error_reporting(E_ALL);
ini_set('display_errors', 0); 
ini_set('log_errors', 1);
// ini_set('error_log', __DIR__ . '/../../php-error.log'); // Ensure this path is writable

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Adjust for production

$pdo = null;
try {
    require_once '../db_connect.php'; // Path relative to this api script
    if (!isset($pdo) || !$pdo instanceof PDO) {
        throw new Exception('Database connection object ($pdo) not properly created by db_connect.php.');
    }
} catch (Throwable $e) {
    error_log("PHP Error in get_org_structure.php (db_connect include): " . $e->getMessage());
    if (!headers_sent()) { http_response_code(500); }
    echo json_encode(['error' => 'Server configuration error: Could not connect to the database.']);
    exit;
}

try {
    // Fetch departments and count of active employees in each
    // The Employees table uses DepartmentID which links to departments.dept_id
    $sql = "SELECT 
                d.dept_id AS DepartmentID,
                d.department_name AS DepartmentName,
                (SELECT COUNT(e.EmployeeID) FROM Employees e WHERE e.DepartmentID = d.dept_id AND e.IsActive = TRUE) AS EmployeeCount
            FROM 
                departments d
            ORDER BY 
                d.department_name ASC";
    
    $stmt = $pdo->query($sql);
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ensure EmployeeCount is an integer
    foreach ($departments as &$dept) {
        $dept['EmployeeCount'] = (int)$dept['EmployeeCount'];
    }
    unset($dept);

    if (headers_sent()) { 
        error_log("Headers already sent before JSON output in get_org_structure.php");
        exit; 
    } 
    http_response_code(200);
    echo json_encode($departments);

} catch (PDOException $e) {
    error_log("API Error (get_org_structure with employee count): " . $e->getMessage() . " SQL: " . $sql);
    if (!headers_sent()) { http_response_code(500); }
    echo json_encode(['error' => 'Failed to retrieve department list with employee counts. Details: ' . $e->getMessage()]);
} catch (Throwable $e) {
    error_log("PHP Throwable in get_org_structure.php: " . $e->getMessage());
    if (!headers_sent()) { http_response_code(500); }
    echo json_encode(['error' => 'Unexpected server error while fetching department data.']);
}
exit;
?>
