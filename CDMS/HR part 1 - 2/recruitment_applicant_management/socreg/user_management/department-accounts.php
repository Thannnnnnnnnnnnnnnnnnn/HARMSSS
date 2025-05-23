<?php
require_once '../SOCREG\config.php';
require_once __DIR__ . '/../includes/audit_helpers.php';
require_once __DIR__ . '/../includes/rbac.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check if user has permission to view user management
if (!hasPermission('user_management', 'view', $_SESSION['role'])) {
    echo "<script>
        Swal.fire({
            title: 'Access Denied',
            text: 'You do not have permission to access this page.',
            icon: 'error',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../SOCREG/finaltemplate.php';
            }
        });
    </script>";
    exit();
}

// Initialize variables
$error = '';
$success = '';

// Handle AJAX requests
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // This is an AJAX request
    if (isset($_GET['delete']) && !empty($_GET['delete'])) {
        // Check if user has permission to delete users
        if (!hasPermission('user_management', 'delete', $_SESSION['role'])) {
            echo "error: You do not have permission to delete users";
            exit;
        }
        
        $delete_id = intval($_GET['delete']);
        error_log("Delete request received for user ID: " . $delete_id);
        
        try {
            // First check if the user exists
            $check_sql = "SELECT * FROM department_accounts WHERE dept_accounts_id = ?";
            $check_stmt = $conn->prepare($check_sql);
            
            if (!$check_stmt) {
                throw new Exception("Database prepare error: " . $conn->error);
            }
            
            $check_stmt->bind_param("i", $delete_id);
            if (!$check_stmt->execute()) {
                throw new Exception("Error executing check query: " . $check_stmt->error);
            }
            
            $result = $check_stmt->get_result();
            
            if ($result->num_rows === 0) {
                throw new Exception("User not found with ID: " . $delete_id);
            }
            
            $user_data = $result->fetch_assoc();
            
            // Prevent self-deletion
            if ($delete_id == $_SESSION['user_id']) {
                throw new Exception("You cannot delete your own account");
            }
            
            // Begin transaction
            $conn->begin_transaction();
            
            // Delete user
            $delete_sql = "DELETE FROM department_accounts WHERE dept_accounts_id = ?";
            $stmt = $conn->prepare($delete_sql);
            
            if (!$stmt) {
                throw new Exception("Database prepare error for delete: " . $conn->error);
            }
            
            $stmt->bind_param("i", $delete_id);
            if (!$stmt->execute()) {
                throw new Exception("Error executing delete query: " . $stmt->error);
            }
            
            // Verify the deletion
            $verify_sql = "SELECT COUNT(*) as count FROM department_accounts WHERE dept_accounts_id = ?";
            $verify_stmt = $conn->prepare($verify_sql);
            $verify_stmt->bind_param("i", $delete_id);
            $verify_stmt->execute();
            $verify_result = $verify_stmt->get_result();
            $verify_row = $verify_result->fetch_assoc();
            
            if ($verify_row['count'] > 0) {
                throw new Exception("Failed to delete user. Record still exists.");
            }
            
            // Log the deletion
            $logged_in_user_id = $_SESSION['user_id'];
            $logged_in_department_id = $_SESSION['department_id'] ?? 0;
            $logged_in_user_name = $_SESSION['name'];
            $logged_in_role = $_SESSION['role'];
            
            // Insert into audit trail
            $audit_sql = "INSERT INTO department_audit_trail (department_id, user_id, action, department_affected, module_affected, role, user_name) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $audit_stmt = $conn->prepare($audit_sql);
            
            if (!$audit_stmt) {
                throw new Exception("Database prepare error for audit: " . $conn->error);
            }
            
            $action = 'Delete User';
            $department_affected = getDepartmentAffectedName($user_data['department_id']);
            $module_affected = "user_management";
            
            $audit_stmt->bind_param("iisssss", $logged_in_department_id, $logged_in_user_id, $action, $department_affected, $module_affected, $logged_in_role, $logged_in_user_name);
            
            if (!$audit_stmt->execute()) {
                throw new Exception("Error executing audit query: " . $audit_stmt->error);
            }
            
            // Commit transaction
            $conn->commit();
            
            // Final verification
            $final_check_sql = "SELECT COUNT(*) as count FROM department_accounts WHERE dept_accounts_id = ?";
            $final_check_stmt = $conn->prepare($final_check_sql);
            $final_check_stmt->bind_param("i", $delete_id);
            $final_check_stmt->execute();
            $final_check_result = $final_check_stmt->get_result();
            $final_check_row = $final_check_result->fetch_assoc();
            
            if ($final_check_row['count'] > 0) {
                throw new Exception("User deletion failed. Record still exists after commit.");
            }
            
            error_log("User successfully deleted. ID: " . $delete_id);
            echo "success";
            exit;
            
        } catch (Exception $e) {
            // Rollback transaction on error
            // Check if connection is valid and transaction is active before rollback
            if ($conn && method_exists($conn, 'inTransaction') && $conn->inTransaction()) {
                $conn->rollback();
            }
            error_log("Error during delete operation: " . $e->getMessage());
            echo "error: " . $e->getMessage();
            exit;
        }
    }
    exit; // Stop here for AJAX requests
}

// Handle Add User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    // Check if user has permission to add users
    requirePermission('user_management', 'add');
    
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $password = sanitize_input($_POST['password']);
    $role = sanitize_input($_POST['role']);
    $status = sanitize_input($_POST['status']);
    $department_id = sanitize_input($_POST['department_id']);
    $user_id_sql = "SELECT MAX(user_id) as max_user_id FROM department_accounts";
    $user_id_result = $conn->query($user_id_sql);
    $user_id_row = $user_id_result->fetch_assoc();
    $new_user_id = $user_id_row['max_user_id'] + 1;


    // Check if email already exists
    $check_sql = "SELECT COUNT(*) as count FROM department_accounts WHERE email = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        $error = "Email already exists.";
    } else {
        // Insert new user
        $insert_sql = "INSERT INTO department_accounts (department_id, user_id, name, password, role, status, email) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("iisssss", $department_id, $new_user_id, $name, $password, $role, $status, $email);
        if ($stmt->execute()) {
            $success = "User added successfully.";

            // Define logged-in user variables here
            $logged_in_user_id = $_SESSION['user_id'];

$logged_in_department_id = $_SESSION['department_id'] ?? 0;

            $logged_in_user_name = $_SESSION['name'];
            $logged_in_role = $_SESSION['role'];

            // Insert into department_log_history
            $log_sql = "INSERT INTO department_log_history (department_id, user_id, failure_reason, role, user_name) VALUES (?, ?, ?, ?, ?)";
            $log_stmt = $conn->prepare($log_sql);
            $failure_reason = 'none';
            $log_stmt->bind_param("iisss", $logged_in_department_id, $logged_in_user_id, $failure_reason, $role, $logged_in_user_name);
            $log_stmt->execute();

            // Insert into department_audit_trail
            $audit_sql = "INSERT INTO department_audit_trail (department_id, user_id, action, department_affected, module_affected, role, user_name) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $audit_stmt = $conn->prepare($audit_sql);
            $action = 'Add User';
error_log("Added user department ID: " . $department_id);
$department_affected = getDepartmentAffectedName($department_id);
error_log("Department affected from mapping: " . $department_affected);
$module_affected = "user_management";
$audit_stmt->bind_param("iisssss", $department_id, $logged_in_user_id, $action, $department_affected, $module_affected, $role, $logged_in_user_name);
if (!$audit_stmt->execute()) {
    error_log("Audit trail insert error (Add User): " . $audit_stmt->error);
}

            // Insert into department_transaction
            $transc_sql = "INSERT INTO department_transaction (department_id, user_id, transaction_type, role, user_name) VALUES (?, ?, ?, ?, ?)";
            $transc_stmt = $conn->prepare($transc_sql);
            $transaction_type = 'Add User';
            $transc_stmt->bind_param("iisss", $logged_in_department_id, $logged_in_user_id, $transaction_type, $role, $logged_in_user_name);
            $transc_stmt->execute();

        } else {
            $error = "Error adding user: " . $conn->error;
        }
    }
}

// Handle Edit User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    // Check if user has permission to edit users
    requirePermission('user_management', 'edit');
    
    $dept_accounts_id = sanitize_input($_POST['dept_accounts_id']);
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $password = sanitize_input($_POST['password']);
    $role = sanitize_input($_POST['role']);
    $status = sanitize_input($_POST['status']);
    $department_id = sanitize_input($_POST['department_id']);

    // Check if email already exists for other users
    $check_sql = "SELECT COUNT(*) as count FROM department_accounts WHERE email = ? AND dept_accounts_id != ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("si", $email, $dept_accounts_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        $error = "Email already exists for another user.";
    } else {
        // Update user
        $update_sql = "UPDATE department_accounts SET name = ?, email = ?, password = ?, role = ?, status = ?, department_id = ? WHERE dept_accounts_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssssssi", $name, $email, $password, $role, $status, $department_id, $dept_accounts_id);
        
    if ($stmt->execute()) {
            $success = "User updated successfully.";
        
            // Define logged-in user variables
        $logged_in_user_id = $_SESSION['user_id'];
            $logged_in_department_id = $_SESSION['department_id'] ?? 0;
        $logged_in_user_name = $_SESSION['name'];
        $logged_in_role = $_SESSION['role'];
        
        // Insert into department_log_history
        $log_sql = "INSERT INTO department_log_history (department_id, user_id, failure_reason, role, user_name) VALUES (?, ?, ?, ?, ?)";
        $log_stmt = $conn->prepare($log_sql);
        $failure_reason = 'none';
        $log_stmt->bind_param("iisss", $logged_in_department_id, $logged_in_user_id, $failure_reason, $role, $logged_in_user_name);
        $log_stmt->execute();

            // Insert into department_audit_trail
            $audit_sql = "INSERT INTO department_audit_trail (department_id, user_id, action, department_affected, module_affected, role, user_name) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $audit_stmt = $conn->prepare($audit_sql);
            $action = 'Edit User';
            $department_affected = getDepartmentAffectedName($department_id);
            $module_affected = "user_management";
            $audit_stmt->bind_param("iisssss", $logged_in_department_id, $logged_in_user_id, $action, $department_affected, $module_affected, $role, $logged_in_user_name);
            $audit_stmt->execute();

        // Insert into department_transaction
        $transc_sql = "INSERT INTO department_transaction (department_id, user_id, transaction_type, role, user_name) VALUES (?, ?, ?, ?, ?)";
        $transc_stmt = $conn->prepare($transc_sql);
            $transaction_type = 'Edit User';
        $transc_stmt->bind_param("iisss", $logged_in_department_id, $logged_in_user_id, $transaction_type, $role, $logged_in_user_name);
        $transc_stmt->execute();

        } else {
            $error = "Error updating user: " . $conn->error;
        }
    }
}

// Fetch all users
$users_sql = "SELECT * FROM department_accounts ORDER BY dept_accounts_id ASC";
$users_result = $conn->query($users_sql);

// Fetch departments for dropdown (assuming a departments table exists)
$departments_sql = "SELECT Department_ID, Dept_Name FROM Department ORDER BY Dept_Name";
$departments_result = $conn->query($departments_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-secondary">User Management</h1>
        <?php if (hasPermission('user_management', 'add', $_SESSION['role'])): ?>
        <button type="button" class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors" onclick="document.getElementById('addUserModal').classList.remove('hidden')">Add User</button>
        <?php endif; ?>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($success); ?></span>
        </div>
    <?php endif; ?>

    <div class="max-w-screen-lg mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-accent overflow-hidden max-w-full">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-accent/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Department ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">User ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-accent">
                        <?php if ($users_result && $users_result->num_rows > 0): ?>
                            <?php while ($user = $users_result->fetch_assoc()): ?>
                                <tr class="hover:bg-accent/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-primary"><?php echo $user['dept_accounts_id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-primary"><?php echo htmlspecialchars($user['role']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-primary"><?php echo htmlspecialchars($user['status']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-primary"><?php echo htmlspecialchars($user['department_id']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-primary"><?php echo htmlspecialchars($user['user_id']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex items-center gap-2">
                                            <button onclick="viewUser(<?php echo htmlspecialchars(json_encode($user)); ?>)" class="text-primary hover:text-secondary transition-colors" title="View Details">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                            <?php if (hasPermission('user_management', 'edit', $_SESSION['role'])): ?>
                                            <button onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)" class="text-primary hover:text-secondary transition-colors" title="Edit User">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <?php endif; ?>
                                            <?php if (hasPermission('user_management', 'delete', $_SESSION['role'])): ?>
                                            <button onclick="confirmDeleteUser(<?php echo $user['dept_accounts_id']; ?>)" class="text-primary hover:text-red-500 transition-colors" title="Delete User">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="px-6 py-4 text-center text-sm text-primary">No users found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium leading-6 text-secondary" id="modal-title">Add New User</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('addUserModal').classList.add('hidden')">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-secondary">Name</label>
                        <input type="text" class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="name" name="name" required />
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-secondary">Email</label>
                        <input type="email" class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="email" name="email" required />
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-secondary">Password</label>
                        <input type="text" class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="password" name="password" required />
                    </div>
                    <div class="mb-4">
                        <label for="role" class="block text-sm font-medium text-secondary">Role</label>
                        <select class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="role" name="role" required>
                            <option value="super admin">Super Admin</option>
                            <option value="admin">Admin</option>
                            <option value="manager">Manager</option>
                            <option value="staff">Staff</option>
                            <option value="applicant">Applicant</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-secondary">Status</label>
                        <select class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="department_id" class="block text-sm font-medium text-secondary">Department</label>
                        <select class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="department_id" name="department_id" required>
                            <option value="">Select Department</option>
                            <?php if ($departments_result && $departments_result->num_rows > 0): ?>
                                <?php while ($dept = $departments_result->fetch_assoc()): ?>
                                    <option value="<?php echo $dept['Department_ID']; ?>"><?php echo htmlspecialchars($dept['Dept_Name']); ?></option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="px-4 py-2 text-sm font-medium text-secondary bg-white border border-accent rounded-md hover:bg-accent hover:text-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" onclick="document.getElementById('addUserModal').classList.add('hidden')">Cancel</button>
                        <button type="submit" name="add_user" class="px-4 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-md hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View User Modal -->
<div id="viewUserModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium leading-6 text-secondary" id="modal-title">User Details</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('viewUserModal').classList.add('hidden')">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="mt-3 text-sm text-primary">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="font-medium">ID:</p>
                            <p id="modal-id" class="mt-1"></p>
                        </div>
                        <div>
                            <p class="font-medium">Name:</p>
                            <p id="modal-name" class="mt-1"></p>
                        </div>
                        <div>
                            <p class="font-medium">Email:</p>
                            <p id="modal-email" class="mt-1"></p>
                        </div>
                        <div>
                            <p class="font-medium">Role:</p>
                            <p id="modal-role" class="mt-1"></p>
                        </div>
                        <div>
                            <p class="font-medium">Status:</p>
                            <p id="modal-status" class="mt-1"></p>
                        </div>
                        <div>
                            <p class="font-medium">Department ID:</p>
                            <p id="modal-department-id" class="mt-1"></p>
                        </div>
                        <div>
                            <p class="font-medium">User ID:</p>
                            <p id="modal-user-id" class="mt-1"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm" onclick="document.getElementById('viewUserModal').classList.add('hidden')">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium leading-6 text-secondary" id="modal-title">Edit User</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('editUserModal').classList.add('hidden')">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <form method="POST" action="">
                    <input type="hidden" id="edit_dept_accounts_id" name="dept_accounts_id">
                    <div class="mb-4">
                        <label for="edit_name" class="block text-sm font-medium text-secondary">Name</label>
                        <input type="text" class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="edit_name" name="name" required />
                    </div>
                    <div class="mb-4">
                        <label for="edit_email" class="block text-sm font-medium text-secondary">Email</label>
                        <input type="email" class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="edit_email" name="email" required />
                    </div>
                    <div class="mb-4">
                        <label for="edit_password" class="block text-sm font-medium text-secondary">Password</label>
                        <input type="text" class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="edit_password" name="password" required />
                    </div>
                    <div class="mb-4">
                        <label for="edit_role" class="block text-sm font-medium text-secondary">Role</label>
                        <select class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="edit_role" name="role" required>
                            <option value="super admin">Super Admin</option>
                            <option value="admin">Admin</option>
                            <option value="manager">Manager</option>
                            <option value="staff">Staff</option>
                            <option value="applicant">Applicant</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="edit_status" class="block text-sm font-medium text-secondary">Status</label>
                        <select class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="edit_status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="edit_department_id" class="block text-sm font-medium text-secondary">Department</label>
                        <select class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="edit_department_id" name="department_id" required>
                            <option value="">Select Department</option>
                            <?php 
                            // Reset the departments result pointer
                            if ($departments_result) {
                                $departments_result->data_seek(0);
                                while ($dept = $departments_result->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $dept['Department_ID']; ?>"><?php echo htmlspecialchars($dept['Dept_Name']); ?></option>
                            <?php 
                                endwhile;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="px-4 py-2 text-sm font-medium text-secondary bg-white border border-accent rounded-md hover:bg-accent hover:text-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" onclick="document.getElementById('editUserModal').classList.add('hidden')">Cancel</button>
                        <button type="submit" name="edit_user" class="px-4 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-md hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle modal close when clicking outside
        const addUserModal = document.getElementById('addUserModal');
        if (addUserModal) {
            addUserModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        }
    });

function viewUser(data) {
    // Populate modal with data
    document.getElementById('modal-id').textContent = data.dept_accounts_id;
    document.getElementById('modal-name').textContent = data.name;
    document.getElementById('modal-email').textContent = data.email;
    document.getElementById('modal-role').textContent = data.role;
    document.getElementById('modal-status').textContent = data.status;
    document.getElementById('modal-department-id').textContent = data.department_id;
    document.getElementById('modal-user-id').textContent = data.user_id;

    // Show modal
    document.getElementById('viewUserModal').classList.remove('hidden');
}

// Close modal when clicking outside
document.getElementById('viewUserModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});

function editUser(data) {
    // Populate modal with data
    document.getElementById('edit_dept_accounts_id').value = data.dept_accounts_id;
    document.getElementById('edit_name').value = data.name;
    document.getElementById('edit_email').value = data.email;
    document.getElementById('edit_password').value = data.password;
    document.getElementById('edit_role').value = data.role;
    document.getElementById('edit_status').value = data.status;
    document.getElementById('edit_department_id').value = data.department_id;

    // Show modal
    document.getElementById('editUserModal').classList.remove('hidden');
}

function confirmDeleteUser(userId) {
    console.log('Delete function called for user ID:', userId);
    
    Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('Delete confirmed for user ID:', userId);
            
            // Show loading state
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait while we delete the user.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Make an AJAX call to the correct file
            fetch('user_management/department-accounts.php?delete=' + userId, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(data => {
                console.log('Delete response:', data);
                
                if (data.includes('error:')) {
                    const errorMessage = data.split('error:')[1].trim();
                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage || 'Failed to delete user. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                } else if (data.includes('success')) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'User has been deleted successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Force reload the page
                        window.location.href = window.location.href;
                    });
                } else {
                    throw new Error('Unexpected response from server');
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while deleting the user: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}

// Close edit modal when clicking outside
document.getElementById('editUserModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});
</script>
</body>
</html>
