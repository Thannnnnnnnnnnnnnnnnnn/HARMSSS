<?php
/**
 * API Endpoint: Get Users
 * Retrieves a list of system users with their associated employee and role details.
 * Optionally filters by RoleID.
 * Version: Simplified for default admin access
 */

// --- Error Reporting & Headers ---
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
// ini_set('error_log', '/path/to/your/php-error.log');

// session_start(); // No longer strictly needed for this script's direct purpose

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Adjust for production
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
// header('Access-Control-Allow-Credentials: true'); // Not needed if not relying on session cookies

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// --- Database Connection ---
$pdo = null;
try {
    require_once '../db_connect.php';
    if (!isset($pdo) || !$pdo instanceof PDO) {
        throw new Exception('DB connection failed');
    }
} catch (Throwable $e) {
    error_log("PHP Error in get_users.php (db_connect include): " . $e->getMessage());
    if (!headers_sent()) { http_response_code(500); }
    echo json_encode(['error' => 'Server configuration error.']);
    exit;
}

// --- Authorization Check (Simplified for Default Admin) ---
// Since login is bypassed, we assume any call to this endpoint is authorized for the default admin.
// The original check was:
// $allowed_roles = [1]; // RoleID 1 = System Admin
// if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], $allowed_roles)) {
//      http_response_code(403); // Forbidden
//      echo json_encode(['error' => 'Permission denied. You do not have rights to view users.']);
//      exit;
// }
// --- End Simplified Auth Check ---


// --- Optional Filters ---
$role_id_filter = isset($_GET['role_id']) ? filter_var($_GET['role_id'], FILTER_VALIDATE_INT) : null;
// --- End Filters ---

// --- Fetch Logic ---
$sql = '';
$params = [];
try {
    $sql = "SELECT
                u.UserID,
                u.EmployeeID,
                u.Username,
                u.RoleID,
                u.IsActive,
                u.CreatedAt,
                u.UpdatedAt,
                r.RoleName,
                CONCAT(e.FirstName, ' ', e.LastName) AS EmployeeName,
                e.JobTitle AS EmployeeJobTitle,
                e.Email AS EmployeeEmail
            FROM
                Users u
            JOIN
                Roles r ON u.RoleID = r.RoleID
            JOIN
                Employees e ON u.EmployeeID = e.EmployeeID";

    $conditions = [];

    // Since we simplified roles to only RoleID = 1 (System Admin),
    // this filter might always effectively be for RoleID 1 or not needed.
    // However, keeping the logic in case it's used for other purposes or if roles are re-introduced.
    if ($role_id_filter !== null && $role_id_filter > 0) {
        $conditions[] = "u.RoleID = :role_id";
        $params[':role_id'] = $role_id_filter;
    } else {
        // If no role filter, and we only have System Admins, we can implicitly filter for RoleID 1
        // or just fetch all users if the role simplification means all users are now RoleID 1.
        // The SQL already joins on RoleID, so it will fetch users with their assigned roles.
        // If all users were updated to RoleID 1, this will fetch all of them.
    }


    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " ORDER BY e.LastName, e.FirstName"; // Order by employee name

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format dates (optional)
    foreach ($users as &$user) {
        if (!empty($user['CreatedAt'])) {
            $user['CreatedAtFormatted'] = date('M d, Y H:i', strtotime($user['CreatedAt']));
        }
        if (!empty($user['UpdatedAt'])) {
            $user['UpdatedAtFormatted'] = date('M d, Y H:i', strtotime($user['UpdatedAt']));
        }
    }
    unset($user);

    // --- Final JSON Output ---
    if (headers_sent()) { exit; }
    http_response_code(200);
    echo json_encode($users);

} catch (\PDOException $e) {
    error_log("PHP PDOException in get_users.php: " . $e->getMessage() . " | SQL: " . $sql);
    if (!headers_sent()) { http_response_code(500); }
    echo json_encode(['error' => 'Database error retrieving users.']);
} catch (Throwable $e) {
    error_log("PHP Throwable in get_users.php: " . $e->getMessage());
    if (!headers_sent()) { http_response_code(500); }
    echo json_encode(['error' => 'Unexpected server error retrieving users.']);
}
exit;
?>
