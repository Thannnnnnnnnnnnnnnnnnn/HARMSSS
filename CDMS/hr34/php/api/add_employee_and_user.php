<?php
/**
 * API Endpoint: Admin Update Employee Details
 * Allows an admin to update an existing employee's details, department, and active status
 * in the HR 1-2 'employees' table.
 * This script NO LONGER adds new employees or creates user accounts.
 */

// --- Error Reporting & Headers ---
error_reporting(E_ALL);
ini_set('display_errors', 0); // Keep 0 for production
ini_set('log_errors', 1);
// ini_set('error_log', __DIR__ . '/../../php-error.log'); // Ensure this path is writable

session_start(); // Needed for authorization check if you re-enable it

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Adjust for production
header('Access-Control-Allow-Methods: POST, OPTIONS'); // Using POST for updates
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// --- Database Connection (Uses the main $pdo from db_connect.php) ---
$pdo = null;
try {
    require_once '../db_connect.php'; // This connects to the unified HR 1-2 database
    if (!isset($pdo) || !$pdo instanceof PDO) {
        throw new Exception('Database connection object ($pdo) not properly created by db_connect.php.');
    }
} catch (Throwable $e) {
    error_log("PHP Error in admin_update_employee_details.php (db_connect include): " . $e->getMessage());
    if (!headers_sent()) { http_response_code(500); }
    echo json_encode(['error' => 'Server configuration error: Could not connect to the database. DB Name expected: hr_1_2_new_hire_onboarding_and_employee_self-service']);
    exit;
}

// --- Authorization Check (Simplified - Assuming Admin Access) ---
// Example: RoleID 1 for System Admin
// $allowed_roles = [1];
// if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], $allowed_roles)) {
//      http_response_code(403);
//      echo json_encode(['error' => 'Permission denied to update employee details.']);
//      exit;
// }
// --- End Authorization Check ---

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'POST method required.']);
    exit;
}

$input_data = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON payload received. Error: ' . json_last_error_msg()]);
    exit;
}

// --- Extract and sanitize data for update ---
$employee_id_to_update = isset($input_data['employee_id']) ? filter_var($input_data['employee_id'], FILTER_VALIDATE_INT) : null;

// Fields that can be updated
$update_params = [];
$set_clauses = [];

// Helper function to add to update if key exists in input
function add_to_update_if_present($key, $input_data, &$set_clauses, &$update_params, $validation_filter = FILTER_DEFAULT, $options = []) {
    if (array_key_exists($key, $input_data)) {
        $value = trim($input_data[$key]);
        if ($validation_filter !== FILTER_DEFAULT) {
            $value = filter_var($value, $validation_filter, $options);
        }
        // Allow empty strings to be set to NULL for optional fields if appropriate for your DB schema
        // For now, we'll just bind what's given after trim.
        // If a field should be nullable and an empty string means NULL, handle that here.
        $set_clauses[] = "`" . $key . "` = :" . $key; // Use backticks for field names
        $update_params[':' . $key] = ($value === '' && in_array($key, ['MiddleName', 'Suffix', 'PersonalEmail', 'PhoneNumber', 'DateOfBirth', 'Gender', 'MaritalStatus', 'Nationality', 'AddressLine1', 'AddressLine2', 'City', 'StateProvince', 'PostalCode', 'Country', 'EmergencyContactName', 'EmergencyContactRelationship', 'EmergencyContactPhone', 'TerminationDate', 'TerminationReason', 'EmployeePhotoPath'])) ? null : $value;
        return true; // Indicates the key was present
    }
    return false;
}

// --- Validate Essential ID ---
$errors = [];
if (empty($employee_id_to_update) || $employee_id_to_update <= 0) {
    $errors['employee_id'] = 'A valid Employee ID to update is required.';
} else {
    // Check if employee_id exists
    try {
        $stmt_check_emp = $pdo->prepare("SELECT EmployeeID FROM employees WHERE EmployeeID = :employee_id");
        $stmt_check_emp->bindParam(':employee_id', $employee_id_to_update, PDO::PARAM_INT);
        $stmt_check_emp->execute();
        if (!$stmt_check_emp->fetch()) {
            $errors['employee_id_invalid'] = 'The Employee ID to update does not exist.';
        }
    } catch (\PDOException $e) {
        error_log("API Error (update employee - Emp Check): " . $e->getMessage());
        $errors['database_check_emp'] = 'Error verifying employee existence for update.';
    }
}

// --- Process and Validate Updatable Fields ---
add_to_update_if_present('FirstName', $input_data, $set_clauses, $update_params);
add_to_update_if_present('LastName', $input_data, $set_clauses, $update_params);
add_to_update_if_present('MiddleName', $input_data, $set_clauses, $update_params);
add_to_update_if_present('Suffix', $input_data, $set_clauses, $update_params);

if (add_to_update_if_present('Email', $input_data, $set_clauses, $update_params, FILTER_VALIDATE_EMAIL)) {
    if ($input_data['Email'] !== false && empty($input_data['Email'])) { // Check if it was validated to false but was not empty
         $errors['Email'] = 'Invalid Work Email format.';
    } else if ($input_data['Email'] !== false && !empty($input_data['Email'])) {
        // Check for duplicate work email if it's being changed
        try {
            $stmt_check_email = $pdo->prepare("SELECT EmployeeID FROM employees WHERE Email = :Email AND EmployeeID != :employee_id_to_update");
            $stmt_check_email->bindParam(':Email', $input_data['Email'], PDO::PARAM_STR);
            $stmt_check_email->bindParam(':employee_id_to_update', $employee_id_to_update, PDO::PARAM_INT);
            $stmt_check_email->execute();
            if ($stmt_check_email->fetch()) {
                $errors['email_duplicate'] = 'This work email address is already in use by another employee.';
            }
        } catch (\PDOException $e) {
            $errors['database_check_email'] = 'Error checking work email uniqueness.';
        }
    }
}
if (add_to_update_if_present('PersonalEmail', $input_data, $set_clauses, $update_params, FILTER_VALIDATE_EMAIL, FILTER_NULL_ON_FAILURE)) {
    if ($input_data['PersonalEmail'] !== false && !empty($input_data['PersonalEmail']) && $update_params[':PersonalEmail'] === null && $input_data['PersonalEmail'] !== '') {
         $errors['PersonalEmail'] = 'Invalid Personal Email format.';
    }
}


add_to_update_if_present('PhoneNumber', $input_data, $set_clauses, $update_params);
if (add_to_update_if_present('DateOfBirth', $input_data, $set_clauses, $update_params)) {
    if (!empty($input_data['DateOfBirth']) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $input_data['DateOfBirth'])) {
        $errors['DateOfBirth'] = 'Date of Birth must be YYYY-MM-DD if provided.';
    }
}
add_to_update_if_present('Gender', $input_data, $set_clauses, $update_params);
add_to_update_if_present('MaritalStatus', $input_data, $set_clauses, $update_params);
add_to_update_if_present('Nationality', $input_data, $set_clauses, $update_params);
add_to_update_if_present('JobTitle', $input_data, $set_clauses, $update_params);

if (add_to_update_if_present('DepartmentID', $input_data, $set_clauses, $update_params, FILTER_VALIDATE_INT)) {
    if ($update_params[':DepartmentID'] !== null && $update_params[':DepartmentID'] <= 0) {
        $errors['DepartmentID_value'] = 'Department ID must be a positive integer if provided.';
    } elseif ($update_params[':DepartmentID'] !== null) {
        // Check if department_id exists
        try {
            $stmt_check_dept = $pdo->prepare("SELECT dept_id FROM departments WHERE dept_id = :DepartmentID");
            $stmt_check_dept->bindParam(':DepartmentID', $update_params[':DepartmentID'], PDO::PARAM_INT);
            $stmt_check_dept->execute();
            if (!$stmt_check_dept->fetch()) {
                $errors['DepartmentID_invalid'] = 'The provided Department ID does not exist.';
            }
        } catch (\PDOException $e) {
            $errors['database_check_dept'] = 'Error verifying department.';
        }
    }
}

if (add_to_update_if_present('IsActive', $input_data, $set_clauses, $update_params, FILTER_VALIDATE_INT)) {
    if (!in_array((int)$update_params[':IsActive'], [0, 1], true)) {
        $errors['IsActive'] = 'Employee active status must be 0 or 1.';
    }
}


// Add other updatable fields as needed following the pattern:
// add_to_update_if_present('AddressLine1', $input_data, $set_clauses, $update_params);
// ... etc.

if (empty($set_clauses) && empty($errors)) {
    http_response_code(200);
    echo json_encode(['message' => 'No fields provided to update for the employee.']);
    exit;
}

if (!empty($errors)) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Validation failed.', 'details' => $errors]);
    exit;
}
// --- End Validation ---


// --- Database Update ---
try {
    $pdo->beginTransaction();

    $sql_update_employee = "UPDATE employees SET " . implode(", ", $set_clauses) . " WHERE EmployeeID = :employee_id_to_update";
    $stmt_update_employee = $pdo->prepare($sql_update_employee);

    // Bind the employee_id for the WHERE clause
    $update_params[':employee_id_to_update'] = $employee_id_to_update;

    $stmt_update_employee->execute($update_params);

    if ($stmt_update_employee->rowCount() > 0) {
        $pdo->commit();
        http_response_code(200);
        echo json_encode([
            'message' => 'Employee details updated successfully.',
            'employee_id' => $employee_id_to_update
        ]);
    } else {
        // No rows affected - could be because data was the same or employee_id not found (already checked)
        $pdo->rollBack();
        http_response_code(200); // Still OK, just no actual change in DB
        echo json_encode(['message' => 'Employee details submitted, but no changes were made to the record.', 'employee_id' => $employee_id_to_update]);
    }

} catch (\PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("PHP PDOException in admin_update_employee_details.php (transaction): " . $e->getMessage());
    if ($e->getCode() == '23000') { // Integrity constraint violation
         http_response_code(400);
         echo json_encode(['error' => 'Failed to update employee. Data conflict or invalid foreign key (e.g., DepartmentID).', 'details' => $e->getMessage()]);
    } else {
         http_response_code(500);
         echo json_encode(['error' => 'Database error during employee update.']);
    }
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("PHP Throwable in admin_update_employee_details.php (transaction): " . $e->getMessage());
    if (!headers_sent()) { http_response_code(500); }
    echo json_encode(['error' => 'Unexpected server error during employee update.']);
}
exit;
?>
