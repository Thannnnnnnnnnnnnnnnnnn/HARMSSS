<?php
// Prevent caching to avoid back button showing cached login page after login
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies

require_once 'config.php';

// Redirect logged-in users away from login page
if (isset($_SESSION['user_id'])) {
    header("Location: finaltemplate.php");
    exit();
}

$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email']);
    $password = sanitize_input($_POST['password']);

    // Query user by email and active status
    $sql = "SELECT dept_accounts_id, name, password, role, department_id FROM department_accounts WHERE email = ? AND status = 'active' LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        error_log("Initial user data: " . print_r($user, true));

        // For now, assuming password stored in plain text (not recommended)
        // If password is hashed, use password_verify($password, $user['password'])
        if ($password === $user['password']) {
            // Set session variables
            $_SESSION['user_id'] = $user['dept_accounts_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['department_id'] = $user['department_id'];

            // Debug log
            error_log("User role from database: " . $user['role']);
            error_log("User ID: " . $user['dept_accounts_id']);
            error_log("User Name: " . $user['name']);
            error_log("Department ID: " . $user['department_id']);

            // Log login event to department_log_history
            $log_sql = "INSERT INTO department_log_history (department_id, user_id, failure_reason, role, user_name) VALUES (?, ?, ?, ?, ?)";
            $log_stmt = $conn->prepare($log_sql);
            if (!$log_stmt) {
                error_log("Error preparing log statement: " . $conn->error);
            }
            $failure_reason = 'none';
            if (!$log_stmt->bind_param("iisss", $user['department_id'], $user['dept_accounts_id'], $failure_reason, $user['role'], $user['name'])) {
                error_log("Error binding log parameters: " . $log_stmt->error);
            }
            if (!$log_stmt->execute()) {
                error_log("Error executing log insert: " . $log_stmt->error);
            } else {
                error_log("Successfully inserted log entry");
            }

            // Log login event to department_audit_trail
            $audit_sql = "INSERT INTO department_audit_trail (department_id, user_id, action, department_affected, module_affected, role, user_name) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $audit_stmt = $conn->prepare($audit_sql);
            if (!$audit_stmt) {
                error_log("Error preparing audit statement: " . $conn->error);
            }
            $action = 'User Login';
            $department_affected = 'User Management';
            $module_affected = 'login';
            if (!$audit_stmt->bind_param("iisssss", $user['department_id'], $user['dept_accounts_id'], $action, $department_affected, $module_affected, $user['role'], $user['name'])) {
                error_log("Error binding audit parameters: " . $audit_stmt->error);
            }
            if (!$audit_stmt->execute()) {
                error_log("Error executing audit insert: " . $audit_stmt->error);
            } else {
                error_log("Successfully inserted audit entry");
            }

            // Redirect to dashboard
            redirect('finaltemplate.php');
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-md">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Login</h2>
            </div>
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>
            <form class="mt-8 space-y-6" method="POST" action="login.php">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                        <input type="email" id="email" name="email" required autofocus
                            class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" />
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" id="password" name="password" required
                            class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" />
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Login
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>