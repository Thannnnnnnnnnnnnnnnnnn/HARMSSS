<?php
/**
 * API Endpoint: Get Employees
 * Retrieves a list of employees with detailed information from the unified HR 1-2 Database.
 * (The HR 1-2 database now contains both HR 1-2 and HR 3-4 tables).
 */

// --- Error Reporting & Headers ---
error_reporting(E_ALL);
ini_set('display_errors', 0); // Keep 0 for production
ini_set('log_errors', 1);
// ini_set('error_log', __DIR__ . '/../../php-error.log'); // Ensure this path is writable

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Adjust for production

// --- Database Connection (Uses the main $pdo from db_connect.php) ---
$pdo = null;
try {
    require_once '../db_connect.php'; // This now connects to the unified HR 1-2 database
    if (!isset($pdo) || !$pdo instanceof PDO) {
        throw new Exception('Database connection object ($pdo) not properly created by db_connect.php.');
    }
} catch (Throwable $e) {
    error_log("PHP Error in get_employees.php (db_connect include): " . $e->getMessage());
    if (!headers_sent()) { http_response_code(500); }
    echo json_encode(['error' => 'Server configuration error: Could not connect to the database. DB Name expected: hr_1_2_new_hire_onboarding_and_employee_self-service']);
    exit;
}
// --- End Database Connection ---

try {
    // SQL query to fetch from the existing 'employees' and 'departments' tables in HR 1-2 DB
    $sql = "SELECT
                e.EmployeeID,
                e.FirstName,
                e.MiddleName,
                e.LastName,
                e.Suffix,
                e.Email,
                e.PersonalEmail,
                e.PhoneNumber,
                e.DateOfBirth,
                e.Gender,
                e.MaritalStatus,
                e.Nationality,
                e.AddressLine1,
                e.AddressLine2,
                e.City,
                e.StateProvince,
                e.PostalCode,
                e.Country,
                e.EmergencyContactName,
                e.EmergencyContactRelationship,
                e.EmergencyContactPhone,
                e.HireDate,
                e.JobTitle,
                e.DepartmentID AS HR12_DepartmentID, /* Original DepartmentID from HR1-2 employees table */
                d.department_name AS DepartmentName, /* Name from HR 1-2 departments table */
                e.ManagerID, /* This ManagerID refers to an EmployeeID within the HR 1-2 employees table */
                CONCAT(m.FirstName, ' ', m.LastName) AS ManagerName,
                e.IsActive,
                e.TerminationDate,
                e.TerminationReason,
                e.EmployeePhotoPath,
                u.UserID AS HR34_UserID, /* UserID from the Users table (HR 3-4 specific) */
                r.RoleName AS HR34_RoleName /* RoleName from the Roles table (HR 3-4 specific) */
            FROM
                employees e /* This is the HR 1-2 employees table */
            LEFT JOIN
                departments d ON e.DepartmentID = d.dept_id /* HR 1-2 departments table */
            LEFT JOIN
                employees m ON e.ManagerID = m.EmployeeID /* Manager from HR 1-2 employees table */
            LEFT JOIN
                Users u ON e.EmployeeID = u.EmployeeID /* HR 3-4 Users table (now in the same DB) */
            LEFT JOIN
                Roles r ON u.RoleID = r.RoleID /* HR 3-4 Roles table (now in the same DB) */
            ORDER BY
                e.LastName, e.FirstName";

    $stmt = $pdo->query($sql); // Use the $pdo from db_connect.php
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($employees as &$employee) {
        if (!empty($employee['HireDate'])) {
            $employee['HireDateFormatted'] = date('M d, Y', strtotime($employee['HireDate']));
        }
        if (!empty($employee['DateOfBirth'])) {
            $employee['DateOfBirthFormatted'] = date('M d, Y', strtotime($employee['DateOfBirth']));
        }
        if (!empty($employee['TerminationDate'])) {
            $employee['TerminationDateFormatted'] = date('M d, Y', strtotime($employee['TerminationDate']));
        }
        $employee['Status'] = ($employee['IsActive'] == 1) ? 'Active' : 'Inactive';
        // Ensure HR34_UserID and HR34_RoleName are null if no matching user/role exists
        $employee['HR34_UserID'] = $employee['HR34_UserID'] ?? null;
        $employee['HR34_RoleName'] = $employee['HR34_RoleName'] ?? null;
    }
    unset($employee);

    if (headers_sent()) { exit; }
    http_response_code(200);
    echo json_encode($employees);

} catch (PDOException $e) {
    error_log("API Error (get_employees from unified DB): " . $e->getMessage() . " | SQL: " . $sql);
    if (!headers_sent()) { http_response_code(500); }
    echo json_encode(['error' => 'Failed to retrieve employee data from the database. SQL error.']);
} catch (Throwable $e) {
    error_log("PHP Throwable in get_employees.php (unified DB): " . $e->getMessage());
    if (!headers_sent()) { http_response_code(500); }
    echo json_encode(['error' => 'Unexpected server error retrieving employee data.']);
}
exit;
?>
