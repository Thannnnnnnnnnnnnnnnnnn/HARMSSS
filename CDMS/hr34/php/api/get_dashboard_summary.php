<?php
<<<<<<< HEAD
// hr34/php/api/get_hr_analytics_summary.php

// --- Error Reporting & Headers ---
error_reporting(E_ALL);
ini_set('display_errors', 0); 
ini_set('log_errors', 1);
// ini_set('error_log', '/path/to/your/php-error.log'); 
=======
/**
 * API Endpoint: Get Dashboard Summary
 * Retrieves summary data for the main dashboard.
 *
 * This script now uses the 'departments' table instead of 'OrganizationalStructure'.
 */
>>>>>>> 7167a6bd828b7e7919c21d42f9e4f9b2de845d60

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

<<<<<<< HEAD
// --- Database Connection ---
$pdo = null;
try {
    require_once '../db_connect.php'; 
    if (!isset($pdo) || !$pdo instanceof PDO) {
        throw new Exception('Database connection object ($pdo) not properly created by db_connect.php.');
    }
} catch (Throwable $e) {
    error_log("PHP Error in " . __FILE__ . " (db_connect include): " . $e->getMessage());
    if (!headers_sent()) { 
        header('Content-Type: application/json'); 
        http_response_code(500); 
    }
    echo json_encode(['error' => 'Server configuration error: Could not connect to the database.']);
    exit;
}

// --- Authorization Check (Simplified for Default Admin) ---
// Since we've removed login, we assume any call to this endpoint is for the default admin.
// No specific role check is needed here anymore if this endpoint is intended for the admin dashboard.
// If specific role-based data filtering were still needed, it would have to be re-thought.
// For now, we proceed as if authorized.
// $allowed_roles = [1, 2]; // System Admin, HR Admin
// if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id']) || !in_array((int)$_SESSION['role_id'], $allowed_roles)) {
//      http_response_code(403); 
//      echo json_encode(['error' => 'Permission denied. You do not have rights to view HR analytics.']);
//      exit;
// }
// --- End Simplified Authorization Check ---

$analytics_summary = [
    'totalActiveEmployees' => 0,
    'headcountByDepartment' => [],
    'totalLeaveDaysRequestedThisYear' => 0,
    'totalPayrollCostLastRun' => 0,
    'lastPayrollRunIdForCost' => null,
    'averageTenureYears' => 0, 
    'totalLeaveTypes' => 0,      
    'leaveDaysByTypeThisYear' => [], 
    'error' => null 
];

try {
    // 1. Total Active Employees
    $stmt_total_employees = $pdo->query("SELECT COUNT(*) FROM Employees WHERE IsActive = TRUE");
    if ($stmt_total_employees) {
        $analytics_summary['totalActiveEmployees'] = (int)$stmt_total_employees->fetchColumn();
    } else {
        throw new PDOException("Failed to execute query for total active employees.");
    }

    // 2. Headcount by Department (for active employees)
    // MODIFIED: Changed from OrganizationalStructure to departments
    $sql_headcount_dept = "SELECT d.department_name AS DepartmentName, COUNT(e.EmployeeID) as Headcount
                           FROM Employees e
                           JOIN departments d ON e.DepartmentID = d.dept_id
                           WHERE e.IsActive = TRUE
                           GROUP BY d.dept_id, d.department_name
                           ORDER BY Headcount DESC";
    $stmt_headcount_dept = $pdo->query($sql_headcount_dept);
    if ($stmt_headcount_dept) {
        $analytics_summary['headcountByDepartment'] = $stmt_headcount_dept->fetchAll(PDO::FETCH_ASSOC);
    } else {
        throw new PDOException("Failed to execute query for headcount by department.");
    }
    
    // 3. Total Approved Leave Days Requested This Year
    $current_year = date('Y');
    $sql_leave_days = "SELECT SUM(NumberOfDays) as TotalDays
                       FROM LeaveRequests
                       WHERE Status = 'Approved' AND YEAR(StartDate) = :year";
    $stmt_leave_days = $pdo->prepare($sql_leave_days);
    $stmt_leave_days->bindParam(':year', $current_year, PDO::PARAM_INT);
    $stmt_leave_days->execute();
    $total_leave_days = $stmt_leave_days->fetchColumn();
    $analytics_summary['totalLeaveDaysRequestedThisYear'] = $total_leave_days ? (float)$total_leave_days : 0;

    // 4. Total Payroll Cost for the Last Completed Payroll Run
    $sql_last_run = "SELECT PayrollID FROM PayrollRuns WHERE Status = 'Completed' ORDER BY ProcessedDate DESC LIMIT 1";
    $stmt_last_run = $pdo->query($sql_last_run);
    if ($stmt_last_run) {
        $last_payroll_id = $stmt_last_run->fetchColumn();
        if ($last_payroll_id) {
            $analytics_summary['lastPayrollRunIdForCost'] = (int)$last_payroll_id;
            $sql_payroll_cost = "SELECT SUM(NetIncome) as TotalCost FROM Payslips WHERE PayrollID = :payroll_id";
            $stmt_payroll_cost = $pdo->prepare($sql_payroll_cost);
            $stmt_payroll_cost->bindParam(':payroll_id', $last_payroll_id, PDO::PARAM_INT);
            $stmt_payroll_cost->execute();
            $total_cost = $stmt_payroll_cost->fetchColumn();
            $analytics_summary['totalPayrollCostLastRun'] = $total_cost ? (float)$total_cost : 0;
        } else {
            $analytics_summary['totalPayrollCostLastRun'] = 0;
            $analytics_summary['lastPayrollRunIdForCost'] = null;
        }
    } else {
         throw new PDOException("Failed to execute query for last payroll run.");
    }

    // 5. Average Active Employee Tenure (in years)
    $sql_avg_tenure = "SELECT AVG(DATEDIFF(CURDATE(), HireDate) / 365.25) as AvgTenure
                       FROM Employees
                       WHERE IsActive = TRUE AND HireDate IS NOT NULL";
    $stmt_avg_tenure = $pdo->query($sql_avg_tenure);
    if ($stmt_avg_tenure) {
        $avg_tenure = $stmt_avg_tenure->fetchColumn();
        $analytics_summary['averageTenureYears'] = $avg_tenure ? round((float)$avg_tenure, 1) : 0;
    } else {
        throw new PDOException("Failed to execute query for average employee tenure.");
    }

    // 6. Total Number of Configured Leave Types
    $stmt_total_leave_types = $pdo->query("SELECT COUNT(*) FROM LeaveTypes WHERE IsActive = TRUE");
    if ($stmt_total_leave_types) {
        $analytics_summary['totalLeaveTypes'] = (int)$stmt_total_leave_types->fetchColumn();
    } else {
        throw new PDOException("Failed to execute query for total leave types.");
    }

    // 7. Approved Leave Days by Type This Year (for Pie Chart)
    $sql_leave_by_type = "SELECT lt.TypeName, SUM(lr.NumberOfDays) as TotalDays
                          FROM LeaveRequests lr
                          JOIN LeaveTypes lt ON lr.LeaveTypeID = lt.LeaveTypeID
                          WHERE lr.Status = 'Approved' AND YEAR(lr.StartDate) = :year
                          GROUP BY lt.LeaveTypeID, lt.TypeName
                          HAVING SUM(lr.NumberOfDays) > 0
                          ORDER BY TotalDays DESC";
    $stmt_leave_by_type = $pdo->prepare($sql_leave_by_type);
    $stmt_leave_by_type->bindParam(':year', $current_year, PDO::PARAM_INT);
    $stmt_leave_by_type->execute();
    if ($stmt_leave_by_type) {
        $analytics_summary['leaveDaysByTypeThisYear'] = $stmt_leave_by_type->fetchAll(PDO::FETCH_ASSOC);
    } else {
        throw new PDOException("Failed to execute query for leave days by type.");
    }

    http_response_code(200);
    echo json_encode($analytics_summary);

} catch (\PDOException $e) {
    error_log("PHP PDOException in get_hr_analytics_summary.php: " . $e->getMessage());
    $analytics_summary['error'] = 'Database error retrieving HR analytics summary.';
    if (!headers_sent()) { 
        header('Content-Type: application/json'); 
        http_response_code(500); 
    }
    echo json_encode($analytics_summary); 
} catch (Throwable $e) { 
    error_log("PHP Throwable in get_hr_analytics_summary.php: " . $e->getMessage());
    $analytics_summary['error'] = 'Unexpected server error retrieving HR analytics summary.';
    if (!headers_sent()) { 
        header('Content-Type: application/json'); 
        http_response_code(500); 
    }
    echo json_encode($analytics_summary); 
}
exit; 
=======
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
>>>>>>> 7167a6bd828b7e7919c21d42f9e4f9b2de845d60
?>
