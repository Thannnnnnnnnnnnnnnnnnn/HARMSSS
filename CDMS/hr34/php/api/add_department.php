<?php
/**
 * API Endpoint: Add or Update Employee and User
 * - If 'employee_id' is provided, updates an existing employee's details
 * and syncs their 'IsActive' status with the Users table.
 * - Otherwise, attempts to add a new employee and user (original functionality,
 * may need further refinement if used for adding).
 */

// --- Error Reporting & Headers ---
error_reporting(E_ALL);
ini_set('display_errors', 0); // Keep 0 for production
ini_set('log_errors', 1);
// ini_set('error_log', __DIR__ . '/../../php-error.log'); // Ensure this path is writable

session_start(); 

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// --- Database Connection ---
$pdo = null;
try {
    require_once '../db_connect.php'; 
    if (!isset($pdo) || !$pdo instanceof PDO) {
        throw new Exception('Database connection object ($pdo) not properly created by db_connect.php.');
    }
} catch (Throwable $e) {
    error_log("PHP Error in add_employee_and_user.php (db_connect include): " . $e->getMessage());
    if (!headers_sent()) { http_response_code(500); }
    echo json_encode(['error' => 'Server configuration error.']);
    exit;
}

// --- Authorization Check (Simplified for Default Admin - Adapt as needed) ---
// Example: RoleID 1 for System Admin. You might want HR Admin (RoleID 2) too.
$allowed_roles = [1, 2]; 
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], $allowed_roles)) {
     http_response_code(403);
     echo json_encode(['error' => 'Permission denied. Administrator or HR access required.']);
     exit;
}
// --- End Authorization Check ---

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'POST method required.']);
    exit;
}

$input_data = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON payload. Error: ' . json_last_error_msg()]);
    exit;
}

// --- Determine mode: ADD or UPDATE ---
$employee_id_for_action = isset($input_data['employee_id']) ? filter_var($input_data['employee_id'], FILTER_VALIDATE_INT) : null;
$is_update_mode = ($employee_id_for_action !== null && $employee_id_for_action > 0);

// --- Extract and sanitize data ---
// Common Employee Fields
$first_name = isset($input_data['FirstName']) ? trim(htmlspecialchars($input_data['FirstName'])) : null;
$last_name = isset($input_data['LastName']) ? trim(htmlspecialchars($input_data['LastName'])) : null;
$middle_name = isset($input_data['MiddleName']) ? trim(htmlspecialchars($input_data['MiddleName'])) : null;
$suffix = isset($input_data['Suffix']) ? trim(htmlspecialchars($input_data['Suffix'])) : null;
$email = isset($input_data['Email']) ? filter_var(trim($input_data['Email']), FILTER_VALIDATE_EMAIL) : null;
$personal_email = isset($input_data['PersonalEmail']) ? filter_var(trim($input_data['PersonalEmail']), FILTER_VALIDATE_EMAIL, FILTER_NULL_ON_FAILURE) : null;
$phone_number = isset($input_data['PhoneNumber']) ? trim(htmlspecialchars($input_data['PhoneNumber'])) : null;
$date_of_birth = isset($input_data['DateOfBirth']) ? $input_data['DateOfBirth'] : null; 
$gender = isset($input_data['Gender']) ? trim(htmlspecialchars($input_data['Gender'])) : null;
$marital_status = isset($input_data['MaritalStatus']) ? trim(htmlspecialchars($input_data['MaritalStatus'])) : null;
$nationality = isset($input_data['Nationality']) ? trim(htmlspecialchars($input_data['Nationality'])) : null;
$job_title = isset($input_data['JobTitle']) ? trim(htmlspecialchars($input_data['JobTitle'])) : null;
$department_id = isset($input_data['DepartmentID']) ? filter_var($input_data['DepartmentID'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE) : null;
$is_active_employee = isset($input_data['IsActive']) ? filter_var($input_data['IsActive'], FILTER_VALIDATE_INT) : null; // 0 or 1

// Fields specific to ADD mode (User account creation)
$username = isset($input_data['Username']) ? trim(htmlspecialchars($input_data['Username'])) : null;
$password = isset($input_data['Password']) ? $input_data['Password'] : null;
$role_id = isset($input_data['RoleID']) ? filter_var($input_data['RoleID'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE) : null;

// --- Validate Input ---
$errors = [];

// Common validations for both add and update (for fields being submitted)
if (array_key_exists('FirstName', $input_data) && empty($first_name)) $errors['FirstName'] = 'First Name is required.';
if (array_key_exists('LastName', $input_data) && empty($last_name)) $errors['LastName'] = 'Last Name is required.';
if (array_key_exists('Email', $input_data) && empty($email)) $errors['Email'] = 'A valid Work Email is required.';
if (array_key_exists('JobTitle', $input_data) && empty($job_title)) $errors['JobTitle'] = 'Job Title is required.';
if (array_key_exists('DepartmentID', $input_data) && ($department_id === null || $department_id <= 0)) $errors['DepartmentID'] = 'Valid Department ID is required.';

if ($is_active_employee !== null && !in_array($is_active_employee, [0, 1], true)) {
    $errors['IsActive'] = 'Employee active status must be 0 or 1.';
}
if (!empty($date_of_birth) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_of_birth)) {
    $errors['DateOfBirth'] = 'Date of Birth must be YYYY-MM-DD if provided.';
} elseif (empty($date_of_birth)) {
    $date_of_birth = null;
}


// Duplicate Email Check (for both add and update, ensuring it's not taken by *another* employee)
if (empty($errors) && $email) {
    $sql_check_email = "SELECT EmployeeID FROM employees WHERE Email = :Email";
    $params_check_email = [':Email' => $email];
    if ($is_update_mode) {
        $sql_check_email .= " AND EmployeeID != :employee_id_for_action";
        $params_check_email[':employee_id_for_action'] = $employee_id_for_action;
    }
    try {
        $stmt_check_email = $pdo->prepare($sql_check_email);
        $stmt_check_email->execute($params_check_email);
        if ($stmt_check_email->fetch()) {
            $errors['Email_duplicate'] = 'This work email address is already in use.';
        }
    } catch (\PDOException $e) {
        $errors['database_check_email'] = 'Error checking work email uniqueness.';
    }
}


if (!$is_update_mode) { // Validations specific to ADDING a new employee and user
    if (empty($username)) $errors['Username'] = 'Username is required for new user.';
    if (empty($password)) $errors['Password'] = 'Password is required for new user.';
    if ($role_id === null || $role_id <= 0) $errors['RoleID'] = 'Valid Role ID is required for new user.';
    
    if (empty($errors) && !empty($username)) {
        try {
            $stmt_check_username = $pdo->prepare("SELECT UserID FROM Users WHERE Username = :username");
            $stmt_check_username->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt_check_username->execute();
            if ($stmt_check_username->fetch()) {
                $errors['Username_duplicate'] = 'This username is already taken.';
            }
        } catch (\PDOException $e) {
            $errors['database_check_username'] = 'Error checking username uniqueness.';
        }
    }
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['error' => 'Validation failed.', 'details' => $errors]);
    exit;
}

// --- Database Operations ---
try {
    $pdo->beginTransaction();

    if ($is_update_mode) {
        // --- UPDATE EMPLOYEE ---
        $update_fields_employee = [];
        $params_employee_update = [':employee_id_for_action' => $employee_id_for_action];

        // Helper to build SET clauses
        function add_to_params($field, $value, &$clauses, &$params) {
            if (array_key_exists(str_replace(':', '', $field), $GLOBALS['input_data'])) { // Check if field was in original input
                 // Use backticks for field names in case they are reserved words
                $clauses[] = "`" . str_replace(':', '', $field) . "` = " . $field;
                $params[$field] = ($value === '' && in_array(str_replace(':', '', $field), ['MiddleName', 'Suffix', 'PersonalEmail', 'PhoneNumber', 'DateOfBirth', 'Gender', 'MaritalStatus', 'Nationality'])) ? null : $value;
            }
        }
        
        add_to_params(':FirstName', $first_name, $update_fields_employee, $params_employee_update);
        add_to_params(':LastName', $last_name, $update_fields_employee, $params_employee_update);
        add_to_params(':MiddleName', $middle_name, $update_fields_employee, $params_employee_update);
        add_to_params(':Suffix', $suffix, $update_fields_employee, $params_employee_update);
        add_to_params(':Email', $email, $update_fields_employee, $params_employee_update);
        add_to_params(':PersonalEmail', $personal_email, $update_fields_employee, $params_employee_update);
        add_to_params(':PhoneNumber', $phone_number, $update_fields_employee, $params_employee_update);
        add_to_params(':DateOfBirth', $date_of_birth, $update_fields_employee, $params_employee_update);
        add_to_params(':Gender', $gender, $update_fields_employee, $params_employee_update);
        add_to_params(':MaritalStatus', $marital_status, $update_fields_employee, $params_employee_update);
        add_to_params(':Nationality', $nationality, $update_fields_employee, $params_employee_update);
        add_to_params(':JobTitle', $job_title, $update_fields_employee, $params_employee_update);
        add_to_params(':DepartmentID', $department_id, $update_fields_employee, $params_employee_update);
        
        if ($is_active_employee !== null) { // Specifically check if IsActive was sent
            $update_fields_employee[] = "`IsActive` = :IsActive";
            $params_employee_update[':IsActive'] = $is_active_employee;
        }

        if (empty($update_fields_employee)) {
            $pdo->rollBack();
            http_response_code(200);
            echo json_encode(['message' => 'No employee details provided to update.']);
            exit;
        }
        
        $sql_update_employee = "UPDATE employees SET " . implode(", ", $update_fields_employee) . " WHERE EmployeeID = :employee_id_for_action";
        $stmt_update_employee = $pdo->prepare($sql_update_employee);
        $stmt_update_employee->execute($params_employee_update);

        // Sync User's IsActive status if employee's IsActive status was part of the update
        if ($is_active_employee !== null && $stmt_update_employee->rowCount() > 0) {
            $sql_user_status = "UPDATE Users SET IsActive = :is_active_user WHERE EmployeeID = :employee_id_for_action";
            $stmt_user_status = $pdo->prepare($sql_user_status);
            $stmt_user_status->bindParam(':is_active_user', $is_active_employee, PDO::PARAM_INT);
            $stmt_user_status->bindParam(':employee_id_for_action', $employee_id_for_action, PDO::PARAM_INT);
            $stmt_user_status->execute();
            // We don't strictly require user rowCount to be > 0, as an employee might not have a user yet
        }
        
        $pdo->commit();
        http_response_code(200);
        echo json_encode([
            'message' => 'Employee details updated successfully.',
            'employee_id' => $employee_id_for_action
        ]);

    } else {
        // --- ADD NEW EMPLOYEE AND USER ---
        // This part remains for adding new employees, but user_management.js currently only calls for updates.
        // Ensure all required fields ($first_name, $last_name, $email, $job_title, $department_id, $username, $password, $role_id) are validated above if this path is taken.
        
        $sql_employee = "INSERT INTO employees (FirstName, LastName, MiddleName, Suffix, Email, PersonalEmail, PhoneNumber, DateOfBirth, Gender, MaritalStatus, Nationality, JobTitle, DepartmentID, IsActive)
                         VALUES (:FirstName, :LastName, :MiddleName, :Suffix, :Email, :PersonalEmail, :PhoneNumber, :DateOfBirth, :Gender, :MaritalStatus, :Nationality, :JobTitle, :DepartmentID, :IsActive)";
        $stmt_employee = $pdo->prepare($sql_employee);
        $stmt_employee->execute([
            ':FirstName' => $first_name, ':LastName' => $last_name, ':MiddleName' => $middle_name ?: null, ':Suffix' => $suffix ?: null,
            ':Email' => $email, ':PersonalEmail' => $personal_email ?: null, ':PhoneNumber' => $phone_number ?: null,
            ':DateOfBirth' => $date_of_birth, ':Gender' => $gender ?: null, ':MaritalStatus' => $marital_status ?: null,
            ':Nationality' => $nationality ?: null, ':JobTitle' => $job_title, ':DepartmentID' => $department_id,
            ':IsActive' => ($is_active_employee !== null ? $is_active_employee : 1) // Default to active if not specified
        ]);
        $new_employee_id = $pdo->lastInsertId();

        if (!$new_employee_id) {
            throw new Exception("Failed to create employee record.");
        }

        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        if ($password_hash === false) throw new Exception('Failed to hash password.');

        $sql_user = "INSERT INTO Users (EmployeeID, Username, PasswordHash, RoleID, IsActive)
                     VALUES (:EmployeeID, :Username, :PasswordHash, :RoleID, :IsActive)";
        $stmt_user = $pdo->prepare($sql_user);
        $stmt_user->execute([
            ':EmployeeID' => $new_employee_id, ':Username' => $username, ':PasswordHash' => $password_hash,
            ':RoleID' => $role_id, ':IsActive' => ($is_active_employee !== null ? $is_active_employee : 1)
        ]);
        $new_user_id = $pdo->lastInsertId();
        
        if (!$new_user_id) {
            throw new Exception("Failed to create user account after creating employee record.");
        }
        
        $pdo->commit();
        http_response_code(201);
        echo json_encode([
            'message' => 'Employee and User account created successfully.',
            'employee_id' => $new_employee_id,
            'user_id' => $new_user_id
        ]);
    }

} catch (\PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log("PHP PDOException in add_employee_and_user.php: " . $e->getMessage());
    $error_code = $e->getCode();
    $error_message = 'Database error during operation.';
    if ($error_code == '23000') { // Integrity constraint violation
        if (strpos($e->getMessage(), 'Username') !== false) $error_message = 'Username already exists.';
        elseif (strpos($e->getMessage(), 'Email') !== false) $error_message = 'Email already exists for an employee.';
        elseif (strpos($e->getMessage(), 'FK_Users_Employee') !== false) $error_message = 'Invalid Employee ID for user creation.';
        elseif (strpos($e->getMessage(), 'FK_Users_Role') !== false) $error_message = 'Invalid Role ID for user creation.';
        elseif (strpos($e->getMessage(), 'FK_Employee_Department') !== false) $error_message = 'Invalid Department ID for employee.';
        http_response_code(409); // Conflict
    } else {
        http_response_code(500);
    }
    echo json_encode(['error' => $error_message, 'details' => $e->getMessage()]);

} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log("PHP Throwable in add_employee_and_user.php: " . $e->getMessage());
    if (!headers_sent()) { http_response_code(500); }
    echo json_encode(['error' => 'Unexpected server error.']);
}
exit;
?>
    