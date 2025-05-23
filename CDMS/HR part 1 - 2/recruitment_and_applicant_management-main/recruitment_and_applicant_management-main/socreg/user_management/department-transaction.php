<?php
require_once '../SOCREG\config.php';
require_once __DIR__ . '/../includes/rbac.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check if user has permission to view transactions
if (!hasPermission('transactions', 'view', $_SESSION['role'])) {
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

// Fetch all department transactions
$sql = "SELECT * FROM department_transaction ORDER BY dept_transc_id DESC";
$result = $conn->query($sql);
?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-secondary">Department Transaction</h1>
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

    <div class="bg-white rounded-lg shadow-sm border border-accent overflow-hidden max-w-4xl mx-auto">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-accent/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider w-20">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider w-32">Department ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider w-40">Transaction Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider w-32">Role</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-secondary uppercase tracking-wider w-16">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-accent">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-accent/30 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-primary"><?php echo $row['dept_transc_id']; ?></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-primary"><?php echo htmlspecialchars($row['department_id']); ?></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-primary"><?php echo htmlspecialchars($row['transaction_type']); ?></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-primary"><?php echo htmlspecialchars($row['role']); ?></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <button onclick="viewTransaction(<?php echo htmlspecialchars(json_encode($row)); ?>)" class="text-primary hover:text-secondary transition-colors" title="View Details">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <?php if (hasPermission('transactions', 'delete', $_SESSION['role'])): ?>
                                        <button onclick="confirmDeleteTransaction(<?php echo $row['transaction_id']; ?>)" class="text-primary hover:text-red-500 transition-colors" title="Delete Transaction">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="px-4 py-3 text-center text-sm text-primary">No transactions found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- View Transaction Modal -->
<div id="viewTransactionModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium leading-6 text-secondary" id="modal-title">Transaction Details</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('viewTransactionModal').classList.add('hidden')">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="mt-3 text-sm text-primary">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="font-medium">Transaction ID:</p>
                            <p id="modal-transaction-id" class="mt-1"></p>
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
                            <p class="font-medium">User Transaction ID:</p>
                            <p id="modal-user-transaction-id" class="mt-1"></p>
                        </div>
                        <div>
                            <p class="font-medium">Transaction Type:</p>
                            <p id="modal-transaction-type" class="mt-1"></p>
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
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm" onclick="document.getElementById('viewTransactionModal').classList.add('hidden')">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function viewTransaction(data) {
    // Populate modal with data
    document.getElementById('modal-transaction-id').textContent = data.dept_transc_id;
    document.getElementById('modal-department-id').textContent = data.department_id;
    document.getElementById('modal-user-id').textContent = data.user_id;
    document.getElementById('modal-user-transaction-id').textContent = data.user_transc_id;
    document.getElementById('modal-transaction-type').textContent = data.transaction_type;
    document.getElementById('modal-role').textContent = data.role;
    document.getElementById('modal-user-name').textContent = data.user_name;

    // Show modal
    document.getElementById('viewTransactionModal').classList.remove('hidden');
}

// Close modal when clicking outside
document.getElementById('viewTransactionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});

function confirmDeleteTransaction(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Add delete functionality here
            Swal.fire(
                'Deleted!',
                'Transaction has been deleted.',
                'success'
            );
        }
    });
}
</script>
