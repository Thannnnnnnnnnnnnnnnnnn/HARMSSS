<?php
/**
 * API Endpoint: Process Payroll Run
 * Calculates payslips for all eligible employees for a given payroll run ID
 * and inserts them into the Payslips table.
 * v2.2 - Highlighting placeholder functions for statutory calculations.
 */

// --- Error Reporting & Headers ---
error_reporting(E_ALL);
ini_set('display_errors', 0); // Keep errors hidden from user
ini_set('log_errors', 1);
// ini_set('error_log', '/path/to/your/php-error.log'); // Optional custom log file

// --- Increase execution time limit for potentially long process ---
set_time_limit(300); // 5 minutes, adjust as needed

// --- Session Start (Required for Auth Checks) ---
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // IMPORTANT: Restrict this in production!
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// --- Database Connection ---
$pdo = null;
try {
    require_once '../db_connect.php'; // Adjust path if necessary
    if (!isset($pdo) || !$pdo instanceof PDO) {
        throw new Exception('DB connection object not created.');
    }
} catch (Throwable $e) {
    error_log("Process Payroll Error (DB Connection): " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server configuration error.']);
    exit;
}

// --- Authentication & Authorization Check ---
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Authentication required.']);
    exit;
}
// Example: Allow System Admin (1) and HR Admin (2) to run payroll
$allowed_roles = [1, 2];
if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], $allowed_roles)) {
     http_response_code(403); // Forbidden
     echo json_encode(['error' => 'Permission denied. You do not have rights to process payroll.']);
     exit;
}
// --- End Auth Check ---


// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'POST method required.']);
    exit;
}

// --- Get Data from POST Request (expecting JSON) ---
$input_data = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON payload received.']);
    exit;
}

// Extract and validate payroll ID
$payroll_id = isset($input_data['payroll_id']) ? filter_var($input_data['payroll_id'], FILTER_VALIDATE_INT) : null;

if (empty($payroll_id) || $payroll_id === false || $payroll_id <= 0) {
     http_response_code(400); // Bad Request
     echo json_encode(['error' => 'Valid Payroll Run ID is required.']);
     exit;
}

// =============================================================
// === PLACEHOLDER CALCULATION FUNCTIONS - REPLACE WITH REAL LOGIC ===
// =============================================================

/**
 * Calculates SSS contribution based on gross income or salary bracket.
 * @param float $gross_income The gross income for the period.
 * @return float The calculated SSS contribution.
 */
function calculate_sss(float $gross_income): float {
    // ***************************************************************
    // *** START SSS PLACEHOLDER - REPLACE WITH ACTUAL SSS LOGIC ***
    // Consult the latest SSS Contribution Table.
    // This typically involves checking the gross income against salary brackets.
    // Example structure:
    // if ($gross_income <= BRACKET_1_MAX) return BRACKET_1_CONTRIBUTION;
    // elseif ($gross_income <= BRACKET_2_MAX) return BRACKET_2_CONTRIBUTION;
    // ... and so on ...
    // else return MAX_CONTRIBUTION;

    // Simple Placeholder:
    if ($gross_income <= 3250) return 135.00;
    if ($gross_income <= 24750) return 1125.00;
    return 1125.00; // Placeholder max
    // *** END SSS PLACEHOLDER ***
    // ***************************************************************
}

/**
 * Calculates PhilHealth contribution based on gross income or salary bracket.
 * @param float $gross_income The gross income for the period.
 * @return float The calculated PhilHealth contribution (Employee Share).
 */
function calculate_philhealth(float $gross_income): float {
    // **********************************************************************
    // *** START PHILHEALTH PLACEHOLDER - REPLACE WITH ACTUAL PHILHEALTH LOGIC ***
    // Consult the latest PhilHealth Contribution Table/Rate (e.g., percentage of basic salary, with floor/ceiling).
    // Remember to calculate the EMPLOYEE'S SHARE (often 50% of the total contribution).
    // Example structure (using a hypothetical rate and caps):
    // $rate = 0.04; // Example rate
    // $min_total_contribution = 400; // Example floor
    // $max_total_contribution = 3200; // Example ceiling
    // $basic_salary_basis = $gross_income; // Or use actual basic salary if needed
    // $total_contribution = $basic_salary_basis * $rate;
    // if ($total_contribution < $min_total_contribution) $total_contribution = $min_total_contribution;
    // if ($total_contribution > $max_total_contribution) $total_contribution = $max_total_contribution;
    // return $total_contribution / 2; // Return employee share

    // Simple Placeholder:
    $rate = 0.04;
    $min_contribution = 400;
    $max_contribution = 3200;
    $contribution = $gross_income * $rate;
    if ($contribution < $min_contribution) return $min_contribution / 2;
    if ($contribution > $max_contribution) return $max_contribution / 2;
    return $contribution / 2;
    // *** END PHILHEALTH PLACEHOLDER ***
    // **********************************************************************
}

/**
 * Calculates Pag-IBIG contribution.
 * @param float $gross_income The gross income for the period.
 * @return float The calculated Pag-IBIG contribution (Employee Share).
 */
function calculate_pagibig(float $gross_income): float {
    // ******************************************************************
    // *** START PAGIBIG PLACEHOLDER - REPLACE WITH ACTUAL PAGIBIG LOGIC ***
    // Consult the latest Pag-IBIG Contribution Rules.
    // Often a fixed amount or percentage based on salary range.
    // Example structure:
    // if ($gross_income > 1500) return 100.00; // Common rate
    // elseif ($gross_income >= 1000) return $gross_income * 0.01; // Example lower rate
    // else return 0.00;

    // Simple Placeholder:
    if ($gross_income > 1500) {
        return 100.00;
    }
    return 0.00;
    // *** END PAGIBIG PLACEHOLDER ***
    // ******************************************************************
}

/**
 * Calculates Withholding Tax based on taxable income.
 * @param float $taxable_income Income after statutory deductions.
 * @param string $pay_frequency Pay frequency ('Monthly', 'Bi-Weekly', etc.) to use correct BIR table.
 * @return float The calculated withholding tax.
 */
function calculate_withholding_tax(float $taxable_income, string $pay_frequency): float {
    // ********************************************************************
    // *** START TAX PLACEHOLDER - REPLACE WITH ACTUAL BIR TAX TABLE LOGIC ***
    // This is the most complex part. You need to implement the graduated tax rates
    // based on the official BIR tables for the given pay frequency.
    // Example structure (VERY simplified pseudo-code for Monthly):
    // if ($pay_frequency === 'Monthly') {
    //     if ($taxable_income <= 20833) return 0.00;
    //     elseif ($taxable_income <= 33333) return ($taxable_income - 20833) * 0.15; // 2023 TRAIN Law example rate
    //     elseif ($taxable_income <= 66667) return 1875 + (($taxable_income - 33333) * 0.20); // Example
    //     // ... add all other brackets ...
    //     else return 102500 + (($taxable_income - 666667) * 0.35); // Example top bracket
    // } elseif ($pay_frequency === 'Bi-Weekly') {
    //     // Implement similar logic using the Bi-Weekly BIR table
    //     if ($taxable_income <= 10417) return 0.00; // Example threshold
    //     // ... etc ...
    // }
    // // Add logic for other frequencies ('Weekly', 'Daily')

    // Simple Placeholder:
    $tax_rate = 0.10; // Highly simplified rate
    $tax = 0.00;
    if ($pay_frequency === 'Monthly' && $taxable_income > 20833) {
        $tax = ($taxable_income - 20833) * $tax_rate;
    } elseif ($pay_frequency === 'Bi-Weekly' && $taxable_income > 10417) {
         $tax = ($taxable_income - 10417) * $tax_rate;
    }
    return max(0, $tax); // Ensure tax is not negative
    // *** END TAX PLACEHOLDER ***
    // ********************************************************************
}

// =============================================================
// === END PLACEHOLDER CALCULATION FUNCTIONS ===
// =============================================================


/**
 * Calculates Overtime Pay.
 * @param float $overtime_hours Hours worked as overtime.
 * @param float $hourly_rate Regular hourly rate.
 * @param float $ot_multiplier Overtime rate multiplier (e.g., 1.25 for 125%).
 * @return float Calculated overtime pay.
 */
function calculate_overtime_pay(float $overtime_hours, float $hourly_rate, float $ot_multiplier = 1.25): float {
    if ($overtime_hours <= 0 || $hourly_rate <= 0) {
        return 0.00;
    }
    // Basic OT calculation - refine based on PH labor laws (e.g., different rates for regular day, rest day, holiday OT)
    return $overtime_hours * $hourly_rate * $ot_multiplier;
}

/**
 * Calculates Regular Pay based on frequency, salary, and hours.
 * @param string $pay_frequency ('Monthly', 'Bi-Weekly', 'Hourly')
 * @param float $base_salary Base salary amount (relevant for non-hourly).
 * @param float $hourly_rate Hourly rate (relevant for hourly).
 * @param float $hours_worked Hours worked in the period.
 * @param string $period_start Start date of the period.
 * @param string $period_end End date of the period.
 * @return float Calculated regular pay for the period.
 */
function calculate_regular_pay(string $pay_frequency, float $base_salary, float $hourly_rate, float $hours_worked, string $period_start, string $period_end): float {
    if ($pay_frequency === 'Hourly') {
        // For hourly, regular pay is based on hours worked from timesheet
        return $hourly_rate * $hours_worked;
    } elseif ($pay_frequency === 'Monthly') {
        // Simplification: Return full base salary for monthly.
        // TODO: Implement proration logic for partial months if needed.
        return $base_salary;
    } elseif ($pay_frequency === 'Bi-Weekly') {
        // Simplification: Assume monthly base salary / 2.
        // TODO: Implement more accurate bi-weekly calculation (e.g., (Monthly * 12) / 26)
        return $base_salary / 2;
    }
    // Add logic for 'Weekly' or other frequencies
    error_log("Warning: Unhandled pay frequency '{$pay_frequency}' in calculate_regular_pay.");
    return 0.00; // Default if frequency not handled
}


// --- Payroll Processing Logic ---
$processed_count = 0;
$error_count = 0;
$employee_errors = [];

try {
    // 1. Fetch Payroll Run Details
    $sql_run = "SELECT PayPeriodStartDate, PayPeriodEndDate, PaymentDate, Status FROM PayrollRuns WHERE PayrollID = :payroll_id";
    $stmt_run = $pdo->prepare($sql_run);
    $stmt_run->bindParam(':payroll_id', $payroll_id, PDO::PARAM_INT);
    $stmt_run->execute();
    $payroll_run = $stmt_run->fetch();

    if (!$payroll_run) {
        http_response_code(404);
        echo json_encode(['error' => "Payroll Run ID {$payroll_id} not found."]);
        exit;
    }

    // Check if already processed or currently processing
    if ($payroll_run['Status'] === 'Completed' || $payroll_run['Status'] === 'Processing') {
         http_response_code(409); // Conflict
         echo json_encode(['error' => "Payroll Run ID {$payroll_id} is already {$payroll_run['Status']}."]);
         exit;
    }
     if ($payroll_run['Status'] !== 'Pending') { // Only process Pending runs
         http_response_code(409); // Conflict
         echo json_encode(['error' => "Payroll Run ID {$payroll_id} has status '{$payroll_run['Status']}' and cannot be processed."]);
         exit;
    }

    $pay_period_start = $payroll_run['PayPeriodStartDate'];
    $pay_period_end = $payroll_run['PayPeriodEndDate'];
    $payment_date = $payroll_run['PaymentDate'];

    // 2. Update Payroll Run status to 'Processing'
    $sql_update_status = "UPDATE PayrollRuns SET Status = 'Processing' WHERE PayrollID = :payroll_id";
    $stmt_update_status = $pdo->prepare($sql_update_status);
    $stmt_update_status->bindParam(':payroll_id', $payroll_id, PDO::PARAM_INT);
    $stmt_update_status->execute();

    // 3. Fetch Eligible Employees (Active Employees)
    $sql_employees = "SELECT EmployeeID FROM Employees WHERE IsActive = TRUE";
    $stmt_employees = $pdo->query($sql_employees);

    // 4. Loop Through Employees and Calculate Payslips
    while ($employee = $stmt_employees->fetch()) {
        $employee_id = $employee['EmployeeID'];
        error_log("Processing EmployeeID: {$employee_id} for PayrollRunID: {$payroll_id}");
        $pdo->beginTransaction(); // Start transaction for each employee's payslip

        try {
            // --- Fetch Data for Employee ---
            // a) Current Salary
            $sql_salary = "SELECT BaseSalary, PayFrequency, PayRate FROM EmployeeSalaries
                           WHERE EmployeeID = :employee_id AND IsCurrent = TRUE";
            $stmt_salary = $pdo->prepare($sql_salary);
            $stmt_salary->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
            $stmt_salary->execute();
            $salary_info = $stmt_salary->fetch();

            if (!$salary_info) {
                throw new Exception("No current salary record found.");
            }
            $base_salary = (float)($salary_info['BaseSalary'] ?? 0);
            $pay_frequency = $salary_info['PayFrequency'] ?? 'Monthly'; // Default if somehow null
            $hourly_rate = (float)($salary_info['PayRate'] ?? 0);

            // b) Approved Timesheet for the period
            $sql_timesheet = "SELECT TotalHoursWorked, OvertimeHours FROM Timesheets
                              WHERE EmployeeID = :employee_id
                                AND PeriodStartDate = :start_date
                                AND PeriodEndDate = :end_date
                                AND Status = 'Approved'";
            $stmt_timesheet = $pdo->prepare($sql_timesheet);
            $stmt_timesheet->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
            $stmt_timesheet->bindParam(':start_date', $pay_period_start, PDO::PARAM_STR);
            $stmt_timesheet->bindParam(':end_date', $pay_period_end, PDO::PARAM_STR);
            $stmt_timesheet->execute();
            $timesheet_info = $stmt_timesheet->fetch();

            // Use timesheet hours if available and PayFrequency is Hourly
            // For non-hourly, hours_worked might be calculated differently or based on standard hours
            $hours_worked = 0.00;
            $overtime_hours = 0.00;
            if ($pay_frequency === 'Hourly' && $timesheet_info) {
                 $hours_worked = (float)($timesheet_info['TotalHoursWorked'] ?? 0);
                 $overtime_hours = (float)($timesheet_info['OvertimeHours'] ?? 0);
            } else if ($pay_frequency !== 'Hourly') {
                // For non-hourly, maybe base hours on standard work days in period? Needs logic.
                // Placeholder: Assume standard hours if no timesheet logic implemented
                // $hours_worked = 8 * 10; // Example: 10 working days in bi-weekly
            }
            error_log("EmployeeID {$employee_id}: Using HoursWorked={$hours_worked}, OvertimeHours={$overtime_hours}");


            // c) Bonuses linked to this Payroll Run OR Awarded within the period
            $sql_bonuses = "SELECT SUM(BonusAmount) as TotalBonus FROM Bonuses
                            WHERE EmployeeID = :employee_id
                              AND (PayrollID = :payroll_id OR (PayrollID IS NULL AND AwardDate BETWEEN :start_date AND :end_date))";
            $stmt_bonuses = $pdo->prepare($sql_bonuses);
            $stmt_bonuses->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
            $stmt_bonuses->bindParam(':payroll_id', $payroll_id, PDO::PARAM_INT); // Link explicitly
            $stmt_bonuses->bindParam(':start_date', $pay_period_start, PDO::PARAM_STR); // Fallback if not linked
            $stmt_bonuses->bindParam(':end_date', $pay_period_end, PDO::PARAM_STR); // Fallback if not linked
            $stmt_bonuses->execute();
            $bonuses_total = (float)($stmt_bonuses->fetchColumn() ?: 0);
            error_log("EmployeeID {$employee_id}: Found BonusesTotal={$bonuses_total}");

            // d) Other Deductions linked to this Payroll Run
            $sql_deductions = "SELECT SUM(DeductionAmount) as TotalOtherDeductions FROM Deductions
                               WHERE EmployeeID = :employee_id AND PayrollID = :payroll_id";
            $stmt_deductions = $pdo->prepare($sql_deductions);
            $stmt_deductions->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
            $stmt_deductions->bindParam(':payroll_id', $payroll_id, PDO::PARAM_INT);
            $stmt_deductions->execute();
            $other_deductions_total = (float)($stmt_deductions->fetchColumn() ?: 0);
            error_log("EmployeeID {$employee_id}: Found OtherDeductionsTotal={$other_deductions_total}");

            // --- Calculations ---
            $regular_pay = calculate_regular_pay($pay_frequency, $base_salary, $hourly_rate, $hours_worked, $pay_period_start, $pay_period_end);
            $overtime_pay = calculate_overtime_pay($overtime_hours, $hourly_rate);

            // TODO: Implement Holiday Pay & Night Differential logic if needed
            $holiday_pay = 0.00;
            $night_differential_pay = 0.00;

            $gross_income = $regular_pay + $overtime_pay + $holiday_pay + $night_differential_pay + $bonuses_total;

            // Calculate Statutory Deductions using placeholder functions
            $sss_contribution = calculate_sss($gross_income);
            $philhealth_contribution = calculate_philhealth($gross_income); // Pass gross or basic? Check rules.
            $pagibig_contribution = calculate_pagibig($gross_income);

            // Calculate Taxable Income
            $taxable_income = $gross_income - ($sss_contribution + $philhealth_contribution + $pagibig_contribution);
            $taxable_income = max(0, $taxable_income); // Ensure not negative

            // Calculate Withholding Tax using placeholder function
            $withholding_tax = calculate_withholding_tax($taxable_income, $pay_frequency);

            // Calculate Total Deductions
            $total_deductions = $sss_contribution + $philhealth_contribution + $pagibig_contribution + $withholding_tax + $other_deductions_total;
            $net_income = $gross_income - $total_deductions;

            error_log("EmployeeID {$employee_id}: Calculated Gross={$gross_income}, SSS={$sss_contribution}, PhilH={$philhealth_contribution}, PagIBIG={$pagibig_contribution}, Tax={$withholding_tax}, OtherDed={$other_deductions_total}, TotalDed={$total_deductions}, Net={$net_income}");


            // --- Insert into Payslips Table ---
            $sql_insert_payslip = "INSERT INTO Payslips (
                                    PayrollID, EmployeeID, PayPeriodStartDate, PayPeriodEndDate, PaymentDate,
                                    BasicSalary, HourlyRate, HoursWorked, OvertimeHours, RegularPay,
                                    OvertimePay, HolidayPay, NightDifferentialPay, BonusesTotal, OtherEarnings,
                                    GrossIncome, SSS_Contribution, PhilHealth_Contribution, PagIBIG_Contribution,
                                    WithholdingTax, OtherDeductionsTotal, TotalDeductions, NetIncome
                                ) VALUES (
                                    :payroll_id, :employee_id, :start_date, :end_date, :payment_date,
                                    :basic_salary, :hourly_rate, :hours_worked, :overtime_hours, :regular_pay,
                                    :overtime_pay, :holiday_pay, :night_diff_pay, :bonuses_total, 0.00, -- Placeholder for OtherEarnings
                                    :gross_income, :sss, :philhealth, :pagibig,
                                    :tax, :other_deductions, :total_deductions, :net_income
                                )";
            $stmt_insert = $pdo->prepare($sql_insert_payslip);
            $stmt_insert->execute([
                ':payroll_id' => $payroll_id, ':employee_id' => $employee_id, ':start_date' => $pay_period_start, ':end_date' => $pay_period_end, ':payment_date' => $payment_date,
                ':basic_salary' => $base_salary, ':hourly_rate' => ($hourly_rate > 0 ? $hourly_rate : null), ':hours_worked' => $hours_worked, ':overtime_hours' => $overtime_hours, ':regular_pay' => $regular_pay,
                ':overtime_pay' => $overtime_pay, ':holiday_pay' => $holiday_pay, ':night_diff_pay' => $night_differential_pay, ':bonuses_total' => $bonuses_total,
                ':gross_income' => $gross_income, ':sss' => $sss_contribution, ':philhealth' => $philhealth_contribution, ':pagibig' => $pagibig_contribution,
                ':tax' => $withholding_tax, ':other_deductions' => $other_deductions_total, ':total_deductions' => $total_deductions, ':net_income' => $net_income
            ]);

            // Optional: Update Bonus/Deduction records to link them to this PayrollID
            $sql_update_bonuses = "UPDATE Bonuses SET PayrollID = :payroll_id
                                   WHERE EmployeeID = :employee_id
                                     AND PayrollID IS NULL
                                     AND AwardDate BETWEEN :start_date AND :end_date";
            $stmt_update_bonuses = $pdo->prepare($sql_update_bonuses);
            $stmt_update_bonuses->execute([
                ':payroll_id' => $payroll_id,
                ':employee_id' => $employee_id,
                ':start_date' => $pay_period_start,
                ':end_date' => $pay_period_end
            ]);


            $pdo->commit(); // Commit successful payslip generation for this employee
            $processed_count++;
            error_log("Successfully processed EmployeeID: {$employee_id}");


        } catch (Exception $e) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); } // Rollback for this specific employee
            $error_count++;
            $employee_errors[$employee_id] = $e->getMessage();
            error_log("Error processing payroll for EmployeeID {$employee_id} in PayrollRun {$payroll_id}: " . $e->getMessage());
        }
    } // End employee loop

    // 5. Update Payroll Run status to 'Completed' or 'Failed'/'Partial'
    $final_status = ($error_count === 0 && $processed_count > 0) ? 'Completed' : (($error_count > 0 && $processed_count > 0) ? 'Partial Failure' : 'Failed');
    // Handle case where no employees were eligible/processed
    if ($processed_count === 0 && $error_count === 0) {
        $final_status = 'Failed'; // Or maybe 'Empty'?
        $employee_errors['general'] = 'No eligible employees found or processed for this run.';
    } elseif ($processed_count === 0 && $error_count > 0) {
         $final_status = 'Failed';
    }

    $sql_final_update = "UPDATE PayrollRuns SET Status = :final_status, ProcessedDate = NOW() WHERE PayrollID = :payroll_id";
    $stmt_final_update = $pdo->prepare($sql_final_update);
    $stmt_final_update->bindParam(':final_status', $final_status, PDO::PARAM_STR);
    $stmt_final_update->bindParam(':payroll_id', $payroll_id, PDO::PARAM_INT);
    $stmt_final_update->execute();

    // --- Final Response ---
    http_response_code(200);
    $response_message = "Payroll Run {$payroll_id} processing finished. Status: {$final_status}. {$processed_count} employees successful.";
    if ($error_count > 0) {
        $response_message .= " {$error_count} employees failed.";
    }
    echo json_encode([
        'message' => $response_message,
        'payroll_id' => $payroll_id,
        'status' => $final_status,
        'processed_count' => $processed_count,
        'error_count' => $error_count,
        'errors' => $employee_errors // Include specific employee errors if any
    ]);


} catch (\PDOException $e) {
    // Rollback if transaction was started for an employee but not committed/rolled back yet
    if (isset($pdo) && $pdo->inTransaction()) { $pdo->rollBack(); }
    // Attempt to set status back to 'Failed'
    try {
         $sql_fail_update = "UPDATE PayrollRuns SET Status = 'Failed' WHERE PayrollID = :payroll_id AND Status = 'Processing'";
         $stmt_fail = $pdo->prepare($sql_fail_update);
         $stmt_fail->bindParam(':payroll_id', $payroll_id, PDO::PARAM_INT);
         $stmt_fail->execute();
    } catch (Exception $inner_e) {
         error_log("Failed to reset payroll run status to Failed after error: " . $inner_e->getMessage());
    }

    error_log("Process Payroll Error (PDOException): " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'A database error occurred during payroll processing.']);
} catch (Throwable $e) {
     if (isset($pdo) && $pdo->inTransaction()) { $pdo->rollBack(); }
     // Attempt to set status back to 'Failed'
     try {
         $sql_fail_update = "UPDATE PayrollRuns SET Status = 'Failed' WHERE PayrollID = :payroll_id AND Status = 'Processing'";
         $stmt_fail = $pdo->prepare($sql_fail_update);
         $stmt_fail->bindParam(':payroll_id', $payroll_id, PDO::PARAM_INT);
         $stmt_fail->execute();
     } catch (Exception $inner_e) {
          error_log("Failed to reset payroll run status to Failed after error: " . $inner_e->getMessage());
     }

     error_log("Process Payroll Error (Throwable): " . $e->getMessage());
     http_response_code(500);
     echo json_encode(['error' => 'An unexpected error occurred during payroll processing.']);
}

exit;
?>
