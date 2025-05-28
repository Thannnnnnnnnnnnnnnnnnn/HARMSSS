<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 0 for production, 1 for development
ini_set('log_errors', 1);
// Ensure this path is writable by the web server:
// ini_set('error_log', __DIR__ . '/../../php-error.log'); 

// session_start(); // No longer strictly needed for this script's direct purpose

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
// header('Access-Control-Allow-Credentials: true'); // Not needed if not relying on session cookies

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../db_connect.php'; // Adjust path as needed

// --- Simplified Authentication: Assume Default Admin ---
$defaultAdminUserId = 5; 
$defaultAdminRoleId = 1; // Assuming RoleID 1 is System Admin
$defaultAdminRoleName = 'System Admin';
$defaultAdminEmployeeId = 1; // EmployeeID for Maria Santos (Sys Admin)

$role = isset($_GET['role']) ? $_GET['role'] : $defaultAdminRoleName; // Use default admin role
$loggedInUserId = $defaultAdminUserId; 
$loggedInEmployeeId = $defaultAdminEmployeeId; 
// --- End Simplified Authentication ---


$summaryData = [
    'charts' => [] 
];

try {
    // For the simplified version, we always show the System Admin / HR Admin dashboard view
    
    // Total Employees
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Employees");
    $summaryData['total_employees'] = $stmt->fetchColumn();

    // Active Employees
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Employees WHERE IsActive = 1");
    $summaryData['active_employees'] = $stmt->fetchColumn();
    $inactive_employees = $summaryData['total_employees'] - $summaryData['active_employees'];
    $summaryData['charts']['employee_status_distribution'] = [
        'labels' => ['Active', 'Inactive'],
        'data' => [(int)$summaryData['active_employees'], (int)$inactive_employees]
    ];

    // Pending Leave Requests (System-wide)
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM LeaveRequests WHERE Status = 'Pending'");
    $summaryData['pending_leave_requests'] = $stmt->fetchColumn();

    // Total Departments
    // Corrected table name from 'organizationalstructure' to 'departments'
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM departments"); 
    $summaryData['total_departments'] = $stmt->fetchColumn();
    
    // Recent Hires (Last 30 days)
    $stmt_recent_hires = $pdo->query("SELECT COUNT(*) as count FROM Employees WHERE HireDate >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
    $summaryData['recent_hires_last_30_days'] = $stmt_recent_hires->fetchColumn();

    // Leave Requests by Type (Last 30 Days, System-wide)
    // Corrected ORDER BY clause
    $stmt_leave_types = $pdo->query("
        SELECT lt.TypeName, COUNT(lr.RequestID) as request_count 
        FROM LeaveRequests lr
        JOIN LeaveTypes lt ON lr.LeaveTypeID = lt.LeaveTypeID
        WHERE lr.RequestDate >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY lt.TypeName
        ORDER BY COUNT(lr.RequestID) DESC 
        LIMIT 5
    ");
    $leave_type_labels = [];
    $leave_type_data = [];
    while ($row = $stmt_leave_types->fetch(PDO::FETCH_ASSOC)) {
        $leave_type_labels[] = $row['TypeName'];
        $leave_type_data[] = (int)$row['request_count']; // Use the alias request_count
    }
    $summaryData['charts']['leave_requests_by_type'] = [
        'labels' => $leave_type_labels,
        'data' => $leave_type_data
    ];

    // Employee Distribution by Department
    // Corrected table name, column names, and ORDER BY clause
    $stmt_dept_dist = $pdo->query("
        SELECT d.department_name AS DepartmentName, COUNT(e.EmployeeID) as employee_count
        FROM Employees e
        JOIN departments d ON e.DepartmentID = d.dept_id
        WHERE e.IsActive = 1
        GROUP BY d.department_name
        ORDER BY COUNT(e.EmployeeID) DESC 
    ");
    $dept_dist_labels = [];
    $dept_dist_data = [];
    while ($row = $stmt_dept_dist->fetch(PDO::FETCH_ASSOC)) {
        $dept_dist_labels[] = $row['DepartmentName'];
        $dept_dist_data[] = (int)$row['employee_count']; // Use the alias employee_count
    }
    $summaryData['charts']['employee_distribution_by_department'] = [
        'labels' => $dept_dist_labels,
        'data' => $dept_dist_data
    ];

    echo json_encode($summaryData);

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Database Error in get_dashboard_summary.php: " . $e->getMessage());
    echo json_encode(['error' => 'Database error. ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    error_log("General Error in get_dashboard_summary.php: " . $e->getMessage());
    echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
}
?>
