<?php
/**
 * API Endpoint: Get Employees
 * Retrieves a list of employees with detailed information from the HR 1-2 Database.
 * v3.0 - Modified to connect to and fetch from the HR 1-2 New Hire Onboarding database.
 */

// --- Error Reporting & Headers ---
error_reporting(E_ALL);
ini_set('display_errors', 0); // Keep 0 for production
ini_set('log_errors', 1);
// ini_set('error_log', __DIR__ . '/../../logs/php-error.log'); // Ensure 'logs' directory exists and is writable

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Adjust for production

// --- Database Connection for HR 1-2 System ---
$pdo_hr12 = null;
// !! IMPORTANT !! REPLACE WITH YOUR ACTUAL HR 1-2 DATABASE CREDENTIALS
$db_config_hr12 = [
    'dsn' => 'mysql:host=127.0.0.1;dbname=hr_1&2_new_hire_onboarding_and_employee_self-service;charset=utf8mb4',
    'username' => '3206_CENTRALIZED_DATABASE', // REPLACE with HR 1-2 DB username
    'password' => '4562526'      // REPLACE with HR 1-2 DB password
];

try {
    $pdo_hr12 = new PDO(
        $db_config_hr12['dsn'],
        $db_config_hr12['username'],
        $db_config_hr12['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    error_log("PHP Error in get_employees.php (HR 1-2 DB Connection): " . $e->getMessage());
    if (!headers_sent()) {
        http_response_code(500);
    }
    echo json_encode(['error' => 'Server configuration error: Could not connect to the HR 1-2 database.']);
    exit;
}

try {
    // Prepare SQL statement to select detailed employee data from HR 1-2 DB
    // Uses lowercase table names as per hr_1_2_new_hire_onboarding_and_employee_self-service.sql
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
                dpt.department_name AS DepartmentName, -- Joined from HR 1-2 'departments' table
                e.ManagerID,
                CONCAT(m.FirstName, ' ', m.LastName) AS ManagerName, -- Self-join on HR 1-2 'employees' table
                e.IsActive,
                e.TerminationDate,
                e.TerminationReason,
                e.EmployeePhotoPath
                -- UserID from HR 3-4 Users table is not directly available here
            FROM
                employees e -- from hr_1_2 database
            LEFT JOIN
                departments dpt ON e.DepartmentID = dpt.dept_id -- from hr_1_2 database
            LEFT JOIN
                employees m ON e.ManagerID = m.EmployeeID -- self-join on hr_1_2 database
            ORDER BY
                e.LastName, e.FirstName";

    $stmt = $pdo_hr12->query($sql);
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format dates and other fields as needed
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
    unset($employee); // Unset the reference

    // Output the results as JSON
    if (headers_sent()) {
        exit;
    }
    http_response_code(200);
    echo json_encode($employees);

} catch (\PDOException $e) {
    error_log("API Error (get_employees from HR 1-2 DB): " . $e->getMessage());
    if (!headers_sent()) {
        http_response_code(500);
    }
    echo json_encode(['error' => 'Failed to retrieve employee data from HR 1-2 database.']);
} catch (Throwable $e) {
    error_log("PHP Throwable in get_employees.php (HR 1-2 DB): " . $e->getMessage());
    if (!headers_sent()) {
        http_response_code(500);
    }
    echo json_encode(['error' => 'Unexpected server error while retrieving employee data from HR 1-2.']);
}
exit;
?>