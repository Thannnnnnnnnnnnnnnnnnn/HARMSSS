<?php
/**
 * API Endpoint: Get Dashboard Summary
 * Retrieves summary data for the main dashboard.
 *
 * This script now uses the 'departments' table instead of 'OrganizationalStructure'.
 */

error_reporting(E_ALL);
ini_set('display_errors', 0); // Keep 0 for production
ini_set('log_errors', 1);
// ini_set('error_log', __DIR__ . '/../../php-error.log'); // Ensure this path is writable

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Adjust for production

$pdo = null;
try {
    require_once '../db_connect.php'; // Connects to the unified HR 1-2 database
    if (!isset($pdo) || !$pdo instanceof PDO) {
        throw new Exception('Database connection object ($pdo) not properly created by db_connect.php.');
    }
} catch (Throwable $e) {
    error_log("PHP Error in get_dashboard_summary.php (db_connect include): " . $e->getMessage());
    if (!headers_sent()) { http_response_code(500); }
    echo json_encode(['error' => 'Server configuration error: Could not connect to the database.']);
    exit;
}

$summary_data = [
    'total_employees' => 0,
    'active_employees' => 0,
    'pending_leave_requests' => 0,
    'upcoming_payroll_runs' => 0,
    'employees_by_department' => [],
    'recent_hires' => [],
    'upcoming_birthdays' => [],
    'pending_claims' => 0,
];

try {
    // Total Employees
    $stmt = $pdo->query("SELECT COUNT(*) FROM employees");
    $summary_data['total_employees'] = (int)$stmt->fetchColumn();

    // Active Employees
    $stmt = $pdo->query("SELECT COUNT(*) FROM employees WHERE IsActive = 1");
    $summary_data['active_employees'] = (int)$stmt->fetchColumn();

    // Pending Leave Requests
    $stmt = $pdo->query("SELECT COUNT(*) FROM LeaveRequests WHERE Status = 'Pending'");
    $summary_data['pending_leave_requests'] = (int)$stmt->fetchColumn();
    
    // Upcoming Payroll Runs (e.g., status 'Pending' or 'Scheduled' and payment date in future)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM PayrollRuns WHERE Status IN ('Pending', 'Scheduled') AND PaymentDate >= CURDATE()");
    $stmt->execute();
    $summary_data['upcoming_payroll_runs'] = (int)$stmt->fetchColumn();

    // Employees by Department
    // Uses the 'departments' table from HR 1-2 schema
    $sql_dept = "SELECT d.department_name AS DepartmentName, COUNT(e.EmployeeID) AS EmployeeCount
                 FROM employees e
                 JOIN departments d ON e.DepartmentID = d.dept_id
                 WHERE e.IsActive = 1
                 GROUP BY d.dept_id, d.department_name
                 ORDER BY EmployeeCount DESC
                 LIMIT 5"; // Show top 5 departments or adjust as needed
    $stmt = $pdo->query($sql_dept);
    $summary_data['employees_by_department'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Recent Hires (e.g., hired in the last 30 days)
    $sql_recent_hires = "SELECT EmployeeID, FirstName, LastName, HireDate, JobTitle
                         FROM employees
                         WHERE HireDate >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND IsActive = 1
                         ORDER BY HireDate DESC
                         LIMIT 5";
    $stmt = $pdo->query($sql_recent_hires);
    $summary_data['recent_hires'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($summary_data['recent_hires'] as &$hire) {
        if (!empty($hire['HireDate'])) {
            $hire['HireDateFormatted'] = date('M d, Y', strtotime($hire['HireDate']));
        }
    }
    unset($hire);

    // Upcoming Birthdays (e.g., in the next 7 days, ignoring year)
    $sql_birthdays = "SELECT EmployeeID, FirstName, LastName, DateOfBirth
                      FROM employees
                      WHERE IsActive = 1 AND DateOfBirth IS NOT NULL
                      AND MONTH(DateOfBirth) = MONTH(CURDATE()) AND DAY(DateOfBirth) >= DAY(CURDATE())
                      OR MONTH(DateOfBirth) = MONTH(DATE_ADD(CURDATE(), INTERVAL 7 DAY)) AND DAY(DateOfBirth) <= DAY(DATE_ADD(CURDATE(), INTERVAL 7 DAY))
                      AND CONCAT(MONTH(DateOfBirth), '-', DAY(DateOfBirth)) != CONCAT(MONTH(CURDATE()), '-', DAY(CURDATE())) -- Exclude today if already passed
                      ORDER BY MONTH(DateOfBirth), DAY(DateOfBirth)
                      LIMIT 5";
    // This birthday query is a bit complex to handle year-wrapping correctly.
    // A more robust way for upcoming birthdays (next X days):
    $sql_birthdays_robust = "SELECT EmployeeID, FirstName, LastName, DateOfBirth
                        FROM employees
                        WHERE IsActive = 1 AND DateOfBirth IS NOT NULL
                        AND (
                            (MONTH(DateOfBirth) = MONTH(CURDATE()) AND DAY(DateOfBirth) >= DAY(CURDATE())) OR
                            (MONTH(DateOfBirth) > MONTH(CURDATE()))
                        )
                        AND STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', MONTH(DateOfBirth), '-', DAY(DateOfBirth)), '%Y-%m-%d') <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                        ORDER BY MONTH(DateOfBirth), DAY(DateOfBirth)
                        LIMIT 5";
    // Simpler version for birthdays in the next 7 days (might miss some at year end/start)
    $sql_birthdays_simple = "SELECT EmployeeID, FirstName, LastName, DateOfBirth
                        FROM employees
                        WHERE IsActive = 1 AND DateOfBirth IS NOT NULL
                        AND (
                            (DATE_FORMAT(DateOfBirth, '%m-%d') >= DATE_FORMAT(CURDATE(), '%m-%d')) AND
                            (DATE_FORMAT(DateOfBirth, '%m-%d') <= DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 7 DAY), '%m-%d'))
                        )
                        ORDER BY DATE_FORMAT(DateOfBirth, '%m-%d') ASC
                        LIMIT 5";

    $stmt = $pdo->query($sql_birthdays_simple); // Using the simpler version for now
    $summary_data['upcoming_birthdays'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
     foreach ($summary_data['upcoming_birthdays'] as &$bday) {
        if (!empty($bday['DateOfBirth'])) {
            $bday['DateOfBirthFormatted'] = date('M d', strtotime($bday['DateOfBirth']));
        }
    }
    unset($bday);

    // Pending Claims
    $stmt = $pdo->query("SELECT COUNT(*) FROM Claims WHERE Status = 'Submitted' OR Status = 'Pending Approval'");
    $summary_data['pending_claims'] = (int)$stmt->fetchColumn();


    if (headers_sent()) { exit; }
    http_response_code(200);
    echo json_encode($summary_data);

} catch (PDOException $e) {
    error_log("API Error (get_dashboard_summary): " . $e->getMessage());
    if (!headers_sent()) { http_response_code(500); }
    echo json_encode(['error' => 'Database error. ' . $e->getMessage()]);
} catch (Throwable $e) {
    error_log("PHP Throwable in get_dashboard_summary.php: " . $e->getMessage());
    if (!headers_sent()) { http_response_code(500); }
    echo json_encode(['error' => 'Unexpected server error.']);
}
exit;
?>
