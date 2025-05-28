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
    'recent_hires' => [], // This key was missing in one version, added for consistency
    'upcoming_birthdays' => [],
    'pending_claims' => 0,
    // Added new keys from the "HEAD" version that seem relevant for a dashboard
    'charts' => [ // Assuming charts data might be structured like this
        'employee_status_distribution' => null,
        'leave_requests_by_type' => null,
        'employee_distribution_by_department' => null, // from the other version of `renderCharts`
        'my_leave_summary' => null // for employee role
    ],
    'recent_hires_last_30_days' => 0, // From the "HEAD" version
];

try {
    // Total Employees
    $stmt = $pdo->query("SELECT COUNT(*) FROM employees");
    $summary_data['total_employees'] = (int)$stmt->fetchColumn();

    // Active Employees
    $stmt = $pdo->query("SELECT COUNT(*) FROM employees WHERE IsActive = 1");
    $summary_data['active_employees'] = (int)$stmt->fetchColumn();
     $summary_data['charts']['employee_status_distribution'] = [
        'labels' => ['Active', 'Inactive'],
        'data' => [$summary_data['active_employees'], $summary_data['total_employees'] - $summary_data['active_employees']]
    ];


    // Pending Leave Requests
    $stmt = $pdo->query("SELECT COUNT(*) FROM LeaveRequests WHERE Status = 'Pending'");
    $summary_data['pending_leave_requests'] = (int)$stmt->fetchColumn();
    
    // Upcoming Payroll Runs (e.g., status 'Pending' or 'Scheduled' and payment date in future)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM PayrollRuns WHERE Status IN ('Pending', 'Scheduled') AND PaymentDate >= CURDATE()");
    $stmt->execute();
    $summary_data['upcoming_payroll_runs'] = (int)$stmt->fetchColumn();

    // Employees by Department
    $sql_dept = "SELECT d.department_name AS DepartmentName, COUNT(e.EmployeeID) AS EmployeeCount
                 FROM employees e
                 JOIN departments d ON e.DepartmentID = d.dept_id
                 WHERE e.IsActive = 1
                 GROUP BY d.dept_id, d.department_name
                 ORDER BY EmployeeCount DESC
                 LIMIT 5";
    $stmt = $pdo->query($sql_dept);
    $employees_by_dept_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $summary_data['employees_by_department'] = $employees_by_dept_data; // For direct use if needed
    $summary_data['charts']['employee_distribution_by_department'] = [
        'labels' => array_column($employees_by_dept_data, 'DepartmentName'),
        'data' => array_column($employees_by_dept_data, 'EmployeeCount')
    ];
    
    // Recent Hires (e.g., hired in the last 30 days)
    $sql_recent_hires = "SELECT EmployeeID, FirstName, LastName, HireDate, JobTitle
                         FROM employees
                         WHERE HireDate >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND IsActive = 1
                         ORDER BY HireDate DESC
                         LIMIT 5";
    $stmt = $pdo->query($sql_recent_hires);
    $summary_data['recent_hires'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $summary_data['recent_hires_last_30_days'] = count($summary_data['recent_hires']); // Set KPI value
    foreach ($summary_data['recent_hires'] as &$hire) {
        if (!empty($hire['HireDate'])) {
            $hire['HireDateFormatted'] = date('M d, Y', strtotime($hire['HireDate']));
        }
    }
    unset($hire);

    // Upcoming Birthdays (next 7 days)
    $sql_birthdays_simple = "SELECT EmployeeID, FirstName, LastName, DateOfBirth
                        FROM employees
                        WHERE IsActive = 1 AND DateOfBirth IS NOT NULL
                        AND (
                            (DATE_FORMAT(DateOfBirth, '%m-%d') >= DATE_FORMAT(CURDATE(), '%m-%d')) AND
                            (DATE_FORMAT(DateOfBirth, '%m-%d') <= DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 7 DAY), '%m-%d'))
                        )
                        ORDER BY DATE_FORMAT(DateOfBirth, '%m-%d') ASC
                        LIMIT 5";
    $stmt = $pdo->query($sql_birthdays_simple);
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
    
    // Data for Employee "My Leave Summary" chart (example)
    // This needs the current employee's ID if the role is 'Employee'
    // Assuming this script is generic, this data might be more complex to gather here
    // or the JS should handle it based on role.
    // For now, providing dummy structure:
    $summary_data['charts']['my_leave_summary'] = [
        'labels' => ['Available', 'Used', 'Pending'], // Example labels
        'data' => [10, 5, 2] // Example data
    ];
    
    // Data for "Leave Requests by Type (Last 30 Days)"
    $thirty_days_ago = date('Y-m-d', strtotime('-30 days'));
    $sql_leave_by_type = "SELECT lt.TypeName, COUNT(lr.RequestID) as RequestCount
                          FROM LeaveRequests lr
                          JOIN LeaveTypes lt ON lr.LeaveTypeID = lt.LeaveTypeID
                          WHERE lr.RequestDate >= :thirty_days_ago
                          GROUP BY lt.LeaveTypeID, lt.TypeName
                          ORDER BY RequestCount DESC";
    $stmt_leave_by_type = $pdo->prepare($sql_leave_by_type);
    $stmt_leave_by_type->bindParam(':thirty_days_ago', $thirty_days_ago, PDO::PARAM_STR);
    $stmt_leave_by_type->execute();
    $leave_by_type_data = $stmt_leave_by_type->fetchAll(PDO::FETCH_ASSOC);
    $summary_data['charts']['leave_requests_by_type'] = [
        'labels' => array_column($leave_by_type_data, 'TypeName'),
        'data' => array_map('intval', array_column($leave_by_type_data, 'RequestCount'))
    ];


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