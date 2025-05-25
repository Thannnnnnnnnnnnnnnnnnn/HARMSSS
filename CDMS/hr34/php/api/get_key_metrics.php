<?php
// hr34/php/api/get_key_metrics.php

// --- Error Reporting & Headers ---
error_reporting(E_ALL);
ini_set('display_errors', 0); 
ini_set('log_errors', 1);
// Ensure your PHP error log is configured and writable. You can specify a path:
// ini_set('error_log', __DIR__ . '/../../php_error.log'); 

// session_start(); // No longer strictly needed

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
// header('Access-Control-Allow-Credentials: true'); // Not needed if not relying on session cookies

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// --- Database Connection ---
$pdo = null;
$last_sql_attempt = "N/A"; // Variable to store the last attempted SQL for logging
try {
    require_once '../db_connect.php'; 
    if (!isset($pdo) || !$pdo instanceof PDO) {
        throw new Exception('Database connection object ($pdo) not properly created by db_connect.php.');
    }
} catch (Throwable $e) {
    error_log("PHP Error in " . basename(__FILE__) . " (db_connect include): " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
    if (!headers_sent()) { 
        header('Content-Type: application/json'); 
        http_response_code(500); 
    }
    echo json_encode(['error' => 'Server configuration error: Could not connect to the database.']);
    exit;
}

// --- Authorization Check (Simplified for Default Admin) ---
// --- End Simplified Authorization Check ---

// --- Get Parameters ---
$metric_name = isset($_GET['metric_name']) ? trim(htmlspecialchars($_GET['metric_name'])) : null;
$metric_period = isset($_GET['metric_period']) ? trim(htmlspecialchars($_GET['metric_period'])) : 'current'; 

$response_data = [
    'metricNameDisplay' => '', 
    'metricKey' => $metric_name,
    'metricPeriod' => $metric_period,
    'value' => null,        
    'dataPoints' => [],     
    'labels' => [],         
    'unit' => '',
    'notes' => '',
    'error' => null
];

if (empty($metric_name)) {
    $response_data['error'] = "Metric name is required.";
    http_response_code(400);
    echo json_encode($response_data);
    exit;
}

try {
    $current_date = date('Y-m-d');
    $year_start = date('Y-01-01');
    $year_end = date('Y-12-31');

    $trend_period_end = $current_date;
    if ($metric_period === 'monthly') {
        $trend_period_start = date('Y-m-01', strtotime('-11 months')); 
    } elseif ($metric_period === 'quarterly') {
        $current_quarter_month = ((ceil(date('n') / 3) -1) * 3) + 1;
        $trend_period_start = date('Y-m-01', strtotime(date('Y') . '-' . $current_quarter_month . '-01 -1 year +1 day'));
    } elseif ($metric_period === 'annual') {
        $trend_period_start = date('Y-01-01', strtotime('-2 years')); 
    } else { 
        $trend_period_start = $year_start; 
    }


    switch ($metric_name) {
        case 'headcount_by_department':
            $response_data['metricNameDisplay'] = 'Headcount by Department';
            $response_data['unit'] = 'Employees';
            $response_data['notes'] = 'Number of active employees in each department as of today.';
            
            $last_sql_attempt = "SELECT os.DepartmentName, COUNT(e.EmployeeID) as Headcount
                              FROM Employees e
                              JOIN OrganizationalStructure os ON e.DepartmentID = os.DepartmentID
                              WHERE e.IsActive = TRUE
                              GROUP BY os.DepartmentID, os.DepartmentName
                              ORDER BY Headcount DESC";
            $stmt_headcount = $pdo->query($last_sql_attempt);
            if (!$stmt_headcount) throw new PDOException("Failed to prepare statement for headcount_by_department.");
            $headcount_data = $stmt_headcount->fetchAll(PDO::FETCH_ASSOC);
            
            if ($headcount_data) {
                foreach($headcount_data as $dept) {
                    $response_data['labels'][] = $dept['DepartmentName'];
                    $response_data['dataPoints'][] = (int)$dept['Headcount'];
                }
            } else {
                 $response_data['notes'] = 'No department headcount data available.';
            }
            break;

        case 'turnover_rate':
            $response_data['metricNameDisplay'] = 'Employee Turnover Rate';
            $response_data['unit'] = "%";
            $response_data['notes'] = "Annualized turnover rate. Assumes 'TerminationDate' column exists in 'Employees' table.";
            
            $last_sql_attempt = "SELECT COUNT(*) FROM Employees WHERE TerminationDate BETWEEN :year_start AND :current_date";
            $stmt_terminations = $pdo->prepare($last_sql_attempt);
            if (!$stmt_terminations) throw new PDOException("Failed to prepare statement for terminations_count.");
            $stmt_terminations->bindParam(':year_start', $year_start, PDO::PARAM_STR);
            $stmt_terminations->bindParam(':current_date', $current_date, PDO::PARAM_STR);
            $stmt_terminations->execute();
            $terminations_count = (int)$stmt_terminations->fetchColumn();

            $last_sql_attempt = "SELECT COUNT(*) FROM Employees WHERE HireDate <= :year_start AND (TerminationDate IS NULL OR TerminationDate > :year_start)";
            $stmt_emp_start_year = $pdo->prepare($last_sql_attempt);
            if (!$stmt_emp_start_year) throw new PDOException("Failed to prepare statement for employees_at_start_of_year.");
            $stmt_emp_start_year->bindParam(':year_start', $year_start, PDO::PARAM_STR); // Explicitly PDO::PARAM_STR
            $stmt_emp_start_year->execute(); // No array passed here
            $employees_at_start_of_year = (int)$stmt_emp_start_year->fetchColumn();

            $last_sql_attempt = "SELECT COUNT(*) FROM Employees WHERE IsActive = TRUE";
            $stmt_emp_current = $pdo->query($last_sql_attempt);
            if (!$stmt_emp_current) throw new PDOException("Failed to prepare statement for employees_current.");
            $employees_current = (int)$stmt_emp_current->fetchColumn();
            
            $average_employees = ($employees_at_start_of_year + $employees_current) / 2;

            if ($average_employees > 0) {
                $days_in_year = (new DateTime($year_end))->diff(new DateTime($year_start))->days +1;
                $days_passed_this_year = (new DateTime($current_date))->diff(new DateTime($year_start))->days +1;
                
                if ($days_passed_this_year > 0 && $days_passed_this_year < $days_in_year) {
                    $annualization_factor = $days_in_year / $days_passed_this_year;
                    $turnover_rate = ($terminations_count / $average_employees) * $annualization_factor * 100;
                } else if ($days_passed_this_year >= $days_in_year) {
                     $turnover_rate = ($terminations_count / $average_employees) * 100;
                } else {
                    $turnover_rate = 0; 
                }
                $response_data['value'] = round($turnover_rate, 2);
            } else {
                $response_data['value'] = "0.00";
                $response_data['notes'] .= " (Average employee count is zero).";
            }
            break;

        case 'avg_time_to_hire':
            $response_data['metricNameDisplay'] = 'Average Time to Hire';
            $response_data['value'] = "N/A"; 
            $response_data['unit'] = "days";
            $response_data['notes'] = "Requires Recruitment module data (JobPostings, Applicants tables with relevant dates). Not yet implemented.";
            break;

        case 'training_completion_rate':
            $response_data['metricNameDisplay'] = 'Training Completion Rate';
            $response_data['value'] = "N/A"; 
            $response_data['unit'] = "%";
            $response_data['notes'] = "Requires Learning Management module data (TrainingCourses, EmployeeTraining tables). Not yet implemented.";
            break;
        
        default:
            $response_data['error'] = "Metric '{$metric_name}' is not recognized or implemented yet.";
            http_response_code(404);
            echo json_encode($response_data);
            exit;
    }

    http_response_code(200);
    echo json_encode($response_data);

} catch (\PDOException $e) {
    error_log("PHP PDOException in " . basename(__FILE__) . " for metric '{$metric_name}': " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString() . "\nLast SQL Attempt: " . $last_sql_attempt);
    $response_data['error'] = "Database error retrieving metric: {$metric_name}. Check server logs for details.";
    if (!headers_sent()) { 
        header('Content-Type: application/json'); 
        http_response_code(500); 
    }
    echo json_encode($response_data);
} catch (Throwable $e) { 
    error_log("PHP Throwable in " . basename(__FILE__) . " for metric '{$metric_name}': " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
    $response_data['error'] = "Unexpected server error retrieving metric: {$metric_name}. Check server logs for details.";
    if (!headers_sent()) { 
        header('Content-Type: application/json'); 
        http_response_code(500); 
    }
    echo json_encode($response_data);
}
exit; 
?>
