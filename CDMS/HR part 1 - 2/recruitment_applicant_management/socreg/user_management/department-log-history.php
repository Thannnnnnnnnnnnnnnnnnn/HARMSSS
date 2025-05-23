<?php
require_once '../SOCREG\config.php';
require_once __DIR__ . '/../includes/rbac.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check if user has permission to view log history
if (!hasPermission('log_history', 'view', $_SESSION['role'])) {
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

// Fetch all department log history with role
$sql = "SELECT l.*, a.role as user_role 
        FROM department_log_history l 
        LEFT JOIN department_accounts a ON l.user_id = a.dept_accounts_id 
        ORDER BY l.dept_log_id DESC 
        LIMIT 100";
$result = $conn->query($sql);

// Debug log
error_log("SQL Query: " . $sql);
if (!$result) {
    error_log("Query Error: " . $conn->error);
} else {
    error_log("Number of rows: " . $result->num_rows);
}
?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-secondary">Department Log History</h1>
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

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-accent overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-accent/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider w-20">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider w-32">Department ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider w-32">Role</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-secondary uppercase tracking-wider w-16">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-accent">
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <?php 
                                error_log("Row data: " . print_r($row, true));
                                error_log("Role value: " . (isset($row['user_role']) ? $row['user_role'] : 'not set'));
                                ?>
                                <tr class="hover:bg-accent/30 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-primary"><?php echo $row['dept_log_id']; ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-primary"><?php echo htmlspecialchars($row['department_id']); ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-primary"><?php echo isset($row['user_role']) ? htmlspecialchars($row['user_role']) : 'N/A'; ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <button onclick="viewLogHistory(<?php echo htmlspecialchars(json_encode($row)); ?>)" class="text-primary hover:text-secondary transition-colors" title="View Details">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                            <?php if (hasPermission('log_history', 'delete', $_SESSION['role'])): ?>
                                            <button onclick="confirmDeleteLogHistory(<?php echo $row['log_id']; ?>)" class="text-primary hover:text-red-500 transition-colors" title="Delete Log">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="px-4 py-3 text-center text-sm text-primary">No log history found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View Log History Modal -->
<div id="viewLogModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium leading-6 text-secondary" id="modal-title">Log History Details</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('viewLogModal').classList.add('hidden')">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="mt-3 text-sm text-primary">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="font-medium">Log ID:</p>
                            <p id="modal-log-id" class="mt-1"></p>
                        </div>
                        <div>
                            <p class="font-medium">Department ID:</p>
                            <p id="modal-department-id" class="mt-1"></p>
                        </div>
                        <div>
                            <p class="font-medium">User Log ID:</p>
                            <p id="modal-user-log-id" class="mt-1"></p>
                        </div>
                        <div>
                            <p class="font-medium">User ID:</p>
                            <p id="modal-user-id" class="mt-1"></p>
                        </div>
                        <div>
                            <p class="font-medium">Failure Reason:</p>
                            <p id="modal-failure-reason" class="mt-1"></p>
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
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm" onclick="document.getElementById('viewLogModal').classList.add('hidden')">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function viewLogHistory(data) {
    // Populate modal with data
    document.getElementById('modal-log-id').textContent = data.dept_log_id;
    document.getElementById('modal-department-id').textContent = data.department_id;
    document.getElementById('modal-user-log-id').textContent = data.user_log_id;
    document.getElementById('modal-user-id').textContent = data.user_id;
    document.getElementById('modal-failure-reason').textContent = data.failure_reason;
    document.getElementById('modal-role').textContent = data.user_role;
    document.getElementById('modal-user-name').textContent = data.user_name;

    // Show modal
    document.getElementById('viewLogModal').classList.remove('hidden');
}

// Close modal when clicking outside
document.getElementById('viewLogModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});
</script>
