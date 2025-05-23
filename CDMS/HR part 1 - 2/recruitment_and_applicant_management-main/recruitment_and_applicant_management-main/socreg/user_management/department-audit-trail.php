<?php
require_once '../SOCREG\config.php';
require_once __DIR__ . '/../includes/audit_helpers.php';
require_once __DIR__ . '/../includes/rbac.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check if user has permission to view audit trail
if (!hasPermission('audit_trail', 'view', $_SESSION['role'])) {
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

$error = '';
$success = '';

// Handle delete single audit trail
if (isset($_GET['delete'])) {
    // Check if user has permission (only super admin or admin can delete)
    if (!in_array($_SESSION['role'], ['super admin', 'admin'])) {
        $error = "You don't have permission to delete audit trails.";
    } else {
        $audit_id = $_GET['delete'];
        
        try {
            // Delete the audit trail record
            $delete_sql = "DELETE FROM department_audit_trail WHERE dept_audit_trail_id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $audit_id);
            
            if ($delete_stmt->execute()) {
                $success = "Audit trail record has been deleted successfully.";
            } else {
                throw new Exception("Error deleting audit trail record: " . $delete_stmt->error);
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        // Get the current page URL without query parameters
        $current_page = strtok($_SERVER['REQUEST_URI'], '?');
        // Redirect to the same page with success/error message
        header("Location: " . $current_page . ($error ? "?error=" . urlencode($error) : "?success=" . urlencode($success)));
        exit();
    }
}

// Handle clear all audit trails
if (isset($_GET['clear']) && $_GET['clear'] === 'all') {
    // Check if user has permission (only super admin or admin can clear)
    if (!in_array($_SESSION['role'], ['super admin', 'admin'])) {
        $error = "You don't have permission to clear audit trails.";
    } else {
        // Get logged in user info for audit trail
        $logged_in_user_id = $_SESSION['user_id'];
        $logged_in_department_id = $_SESSION['department_id'];
        $logged_in_user_name = $_SESSION['name'];
        $logged_in_role = $_SESSION['role'];

        try {
            // First, log this action in the audit trail
            $audit_sql = "INSERT INTO department_audit_trail (department_id, user_id, action, department_affected, module_affected, role, user_name) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $audit_stmt = $conn->prepare($audit_sql);
            $action = 'Clear All Audit Trails';
            $department_affected = getDepartmentAffectedName($logged_in_department_id);
            $module_affected = 'user_management';
            $audit_stmt->bind_param("iisssss", $logged_in_department_id, $logged_in_user_id, $action, $department_affected, $module_affected, $logged_in_role, $logged_in_user_name);
            
            if ($audit_stmt->execute()) {
                // Now delete all records except the one we just inserted
                $last_id = $conn->insert_id;
                $clear_sql = "DELETE FROM department_audit_trail WHERE dept_audit_trail_id < ?";
                $clear_stmt = $conn->prepare($clear_sql);
                $clear_stmt->bind_param("i", $last_id);
                
                if ($clear_stmt->execute()) {
                    $success = "All audit trail records have been cleared successfully.";
                } else {
                    throw new Exception("Error clearing audit trail records: " . $clear_stmt->error);
                }
            } else {
                throw new Exception("Error logging clear action: " . $audit_stmt->error);
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        // Get the current page URL without query parameters
        $current_page = strtok($_SERVER['REQUEST_URI'], '?');
        // Redirect to the same page with success/error message
        header("Location: " . $current_page . ($error ? "?error=" . urlencode($error) : "?success=" . urlencode($success)));
        exit();
    }
}

// Fetch all department audit trail
$sql = "SELECT * FROM department_audit_trail ORDER BY dept_audit_trail_id DESC";
$result = $conn->query($sql);
?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-secondary">Audit Trail</h1>
        <?php if (hasPermission('audit_trail', 'clear', $_SESSION['role'])): ?>
        <button type="button" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors" onclick="confirmClearAuditTrail()">
            <i class="fa-solid fa-trash"></i>
            <span>Clear All</span>
        </button>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($_GET['error']); ?></span>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($_GET['success']); ?></span>
        </div>
    <?php endif; ?>

    <div class="max-w-screen-lg mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-accent overflow-hidden max-w-full">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-accent/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Department ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">User ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Action</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">User Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-accent">
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-accent/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-primary"><?php echo $row['dept_audit_trail_id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-primary"><?php echo htmlspecialchars($row['department_id']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-primary"><?php echo htmlspecialchars($row['user_id']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-primary"><?php echo htmlspecialchars($row['action']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-primary"><?php echo htmlspecialchars($row['role']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-primary"><?php echo htmlspecialchars($row['user_name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex items-center gap-2">
                                            <button onclick="viewAuditTrail(<?php echo htmlspecialchars(json_encode($row)); ?>)" class="text-primary hover:text-secondary transition-colors" title="View Details">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                            <?php if (hasPermission('audit_trail', 'delete', $_SESSION['role'])): ?>
                                            <button onclick="confirmDeleteAuditTrail(<?php echo $row['audit_id']; ?>)" class="text-primary hover:text-red-500 transition-colors" title="Delete Record">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="px-6 py-4 text-center text-sm text-primary">No audit trail found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View Audit Trail Modal -->
<div id="viewAuditTrailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium leading-6 text-secondary" id="modal-title">Audit Trail Details</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('viewAuditTrailModal').classList.add('hidden')">
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
                            <p class="font-medium">Department ID:</p>
                            <p id="modal-department-id" class="mt-1"></p>
                        </div>
                        <div>
                            <p class="font-medium">User ID:</p>
                            <p id="modal-user-id" class="mt-1"></p>
                        </div>
                        <div>
                            <p class="font-medium">User Audit Trail ID:</p>
                            <p id="modal-user-audit-trail-id" class="mt-1"></p>
                        </div>
                        <div>
                            <p class="font-medium">Action:</p>
                            <p id="modal-action" class="mt-1"></p>
                        </div>
                        <div>
                            <p class="font-medium">Department Affected:</p>
                            <p id="modal-department-affected" class="mt-1"></p>
                        </div>
                        <div>
                            <p class="font-medium">Module Affected:</p>
                            <p id="modal-module-affected" class="mt-1"></p>
                        </div>
                        <div>
                            <p class="font-medium">Role:</p>
                            <p id="modal-role" class="mt-1"></p>
                        </div>
                        <div>
                            <p class="font-medium">User Name:</p>
                            <p id="modal-user-name" class="mt-1"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm" onclick="document.getElementById('viewAuditTrailModal').classList.add('hidden')">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function viewAuditTrail(data) {
    // Populate modal with data
    document.getElementById('modal-id').textContent = data.dept_audit_trail_id;
    document.getElementById('modal-department-id').textContent = data.department_id;
    document.getElementById('modal-user-id').textContent = data.user_id;
    document.getElementById('modal-user-audit-trail-id').textContent = data.user_audit_trail_id;
    document.getElementById('modal-action').textContent = data.action;
    document.getElementById('modal-department-affected').textContent = data.department_affected;
    document.getElementById('modal-module-affected').textContent = data.module_affected;
    document.getElementById('modal-role').textContent = data.role;
    document.getElementById('modal-user-name').textContent = data.user_name;

    // Show modal
    document.getElementById('viewAuditTrailModal').classList.remove('hidden');
}

// Close modal when clicking outside
document.getElementById('viewAuditTrailModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});

function clearAuditTrail() {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will delete all audit trail records. This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, clear all records!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Get the current page URL
            const currentUrl = window.location.pathname;
            window.location.href = currentUrl + '?clear=all';
        }
    });
}
</script>
