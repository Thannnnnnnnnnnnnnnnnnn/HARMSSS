<?php
/**
 * API Endpoint: Get Employees
 * Retrieves a list of employees with detailed information from the HR 1-2 Database.
 * v3.1 - Corrected database name for HR 1-2 connection.
 */

// --- Error Reporting & Headers ---
error_reporting(E_ALL);
ini_set('display_errors', 0); // Keep 0 for production
ini_set('log_errors', 1);
// ini_set('error_log', __DIR__ . '/../../php-error.log'); // Ensure this path is writable

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Adjust for production

// --- Database Connection for HR 1-2 System ---
// !! IMPORTANT !!
// Ensure these credentials are correct for your HR 1-2 Database.
// The database name below MUST match the one from your hr_1_2_*.sql file.

$db_host_hr12 = getenv('DB_HOST_HR12') ?: '127.0.0.1'; // Your HR 1-2 DB host
// Corrected Database Name:
$db_host_hr12 = '127.0.0.1'; // Your HR 1-2 DB host (often 'localhost' or '127.0.0.1')
$db_name_hr12 = 'hr_1&2_new_hire_onboarding_and_employee_self-service'; // The target database name
$db_user_hr12 = '3206_CENTRALIZED_DATABASE'; // REPLACE with your HR 1-2 DB username
$db_pass_hr12 = '456252'; // REPLACE with your HR 1-2 DB password
$charset_hr12 = 'utf8mb4';

$dsn_hr12 = "mysql:host={$db_host_hr12};dbname={$db_name_hr12};charset={$charset_hr12}";
$options_hr12 = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo_hr12 = null;
try {
    $pdo_hr12 = new PDO($dsn_hr12, $db_user_hr12, $db_pass_hr12, $options_hr12);
} catch (PDOException $e) {
    error_log("PHP Error in get_employees.php (HR 1-2 DB Connection): " . $e->getMessage() . " | Attempted DB: " . $db_name_hr12);
    if (!headers_sent()) { http_response_code(500); }
    echo json_encode(['error' => 'Server configuration error: Could not connect to the HR 1-2 database. Check connection details and database name. Expected: ' . $db_name_hr12]);
    exit;
}
// --- End Database Connection ---

try {
    // SQL query remains the same as previous correct version
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
                e.DepartmentID,
                d.department_name AS DepartmentName,
                e.ManagerID,
                CONCAT(m.FirstName, ' ', m.LastName) AS ManagerName,
                e.IsActive,
                e.TerminationDate,
                e.TerminationReason,
                e.EmployeePhotoPath,
                NULL AS UserID
            FROM
                employees e
            LEFT JOIN
                departments d ON e.DepartmentID = d.dept_id
            LEFT JOIN
                employees m ON e.ManagerID = m.EmployeeID
            ORDER BY
                e.LastName, e.FirstName";

    $stmt = $pdo_hr12->query($sql);
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
    }
    unset($employee);

    if (headers_sent()) { exit; }
    http_response_code(200);
    echo json_encode($employees);

} catch (PDOException $e) {
    error_log("API Error (get_employees from HR 1-2): " . $e->getMessage() . " | SQL: " . $sql);
    if (!headers_sent()) { http_response_code(500); }
    echo json_encode(['error' => 'Failed to retrieve employee data from HR 1-2 database. SQL error.']);
} catch (Throwable $e) {
    error_log("PHP Throwable in get_employees.php (HR 1-2): " . $e->getMessage());
    if (!headers_sent()) { http_response_code(500); }
    echo json_encode(['error' => 'Unexpected server error retrieving employee data from HR 1-2.']);
}
exit;
?>