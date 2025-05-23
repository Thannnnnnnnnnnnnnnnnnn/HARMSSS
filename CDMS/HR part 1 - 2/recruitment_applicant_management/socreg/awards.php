<?php
require_once 'socreg/config.php';
require_once __DIR__ . '/includes/audit_helpers.php';
require_once __DIR__ . '/includes/rbac.php';

// Check if user is logged in
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }
// dd($_SESSION);
// Check if user has permission to view awards
// requirePermission('awards', 'view');

// Handle add award
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_award'])) {
    // Check if user has permission to add awards
    // requirePermission('awards', 'add');

    $awardName = $_POST['award_name'];
    $categoryID = $_POST['category_id'];
    $description = $_POST['description'];

    $sql = "INSERT INTO awards (AwardName, CategoryID, Description) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sis", $awardName, $categoryID, $description);

    if ($stmt->execute()) {
        // Audit trail insert for add award
        $user_id = $_SESSION['user_id'] ?? 0;
        $user_name = $_SESSION['name'] ?? '';
        $role = $_SESSION['role'] ?? '';
        $department_id = $_SESSION['department_id'] ?? 0;
        $action = 'Add Award';
        $department_affected = getDepartmentAffectedName($department_id);
        $module_affected = 'social recognition';

        $audit_sql = "INSERT INTO department_audit_trail (department_id, user_id, action, department_affected, module_affected, role, user_name) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $audit_stmt = $conn->prepare($audit_sql);
        $audit_stmt->bind_param("iisssss", $department_id, $user_id, $action, $department_affected, $module_affected, $role, $user_name);
        $audit_stmt->execute();

        // header("Location: ?page=awards&success=add");
        // exit();
    } else {
        $error = "Error adding award: " . $conn->error;
    }
}

// Handle update award
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_award'])) {
    // Check if user has permission to edit awards
    // requirePermission('awards', 'edit');

    $awardID = $_POST['award_id'];
    $awardName = $_POST['award_name'];
    $categoryID = $_POST['category_id'];
    $description = $_POST['description'];

    $sql = "UPDATE awards SET AwardName = ?, CategoryID = ?, Description = ? WHERE AwardID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisi", $awardName, $categoryID, $description, $awardID);

    if ($stmt->execute()) {
        // Audit trail insert for update award
        $user_id = $_SESSION['user_id'] ?? 0;
        $user_name = $_SESSION['name'] ?? '';
        $role = $_SESSION['role'] ?? '';
        $department_id = $_SESSION['department_id'] ?? 0;
        $action = 'Update Award';
        $department_affected = getDepartmentAffectedName($department_id);
        $module_affected = 'social recognition';

        $audit_sql = "INSERT INTO department_audit_trail (department_id, user_id, action, department_affected, module_affected, role, user_name) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $audit_stmt = $conn->prepare($audit_sql);
        $audit_stmt->bind_param("iisssss", $department_id, $user_id, $action, $department_affected, $module_affected, $role, $user_name);
        $audit_stmt->execute();

        header("Location: ?page=awards&success=update");
        exit();
    } else {
        $error = "Error updating award: " . $conn->error;
    }
}

// Handle delete award
if (isset($_GET['delete'])) {
    // Check if user has permission to delete awards
    // requirePermission('awards', 'delete');

    $awardID = $_GET['delete'];

    // Check if award is being used in any recognitions
    $check_sql = "SELECT COUNT(*) as count FROM employeerecognition WHERE AwardID = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $awardID);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $check_row = $check_result->fetch_assoc();

    if ($check_row['count'] > 0) {
        $error = "Cannot delete award as it is being used in recognitions";
    } else {
        $sql = "DELETE FROM awards WHERE AwardID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $awardID);

        if ($stmt->execute()) {
            // Audit trail insert for delete award
            $user_id = $_SESSION['user_id'] ?? 0;
            $user_name = $_SESSION['name'] ?? '';
            $role = $_SESSION['role'] ?? '';
            $department_id = $_SESSION['department_id'] ?? 0;
            $action = 'Delete Award';
            $department_affected = getDepartmentAffectedName($department_id);
            $module_affected = 'social recognition';

            $audit_sql = "INSERT INTO department_audit_trail (department_id, user_id, action, department_affected, module_affected, role, user_name) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $audit_stmt = $conn->prepare($audit_sql);
            $audit_stmt->bind_param("iisssss", $department_id, $user_id, $action, $department_affected, $module_affected, $role, $user_name);
            $audit_stmt->execute();

            header("Location: ?page=awards&success=delete");
            exit();
        } else {
            $error = "Error deleting award: " . $conn->error;
        }
    }
}

// Get award details for edit
$edit_award = null;
if (isset($_GET['edit'])) {
    $awardID = $_GET['edit'];
    $sql = "SELECT a.*, c.CategoryName 
            FROM awards a 
            JOIN recognitioncategories c ON a.CategoryID = c.CategoryID 
            WHERE a.AwardID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $awardID);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_award = $result->fetch_assoc();
}

// Get award details for view
$view_award = null;
if (isset($_GET['view'])) {
    $awardID = $_GET['view'];
    $sql = "SELECT a.*, c.CategoryName 
            FROM awards a 
            JOIN recognitioncategories c ON a.CategoryID = c.CategoryID 
            WHERE a.AwardID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $awardID);
    $stmt->execute();
    $result = $stmt->get_result();
    $view_award = $result->fetch_assoc();
}

// Get all awards with categories
$sql = "SELECT a.*, c.CategoryName 
        FROM awards a 
        JOIN recognitioncategories c ON a.CategoryID = c.CategoryID 
        ORDER BY a.AwardID ASC";
$result = mysqli_query($conn, $sql);

// Get categories for dropdown
$categories_sql = "SELECT * FROM recognitioncategories ORDER BY CategoryName";
$categories_result = mysqli_query($conn, $categories_sql);
?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-secondary">Awards</h1>
        <!-- add award if conditional -->
        <button type="button" class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors" onclick="document.getElementById('addAwardModal').classList.remove('hidden')">
            <i class="fa-solid fa-plus"></i>
            <span>Add Award</span>
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($error)): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: <?php echo json_encode($error); ?>,
                });
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
                <?php
                $message = '';
                switch ($_GET['success']) {
                    case 'add':
                        $message = 'Award added successfully!';
                        break;
                    case 'update':
                        $message = 'Award updated successfully!';
                        break;
                    case 'delete':
                        $message = 'Award deleted successfully!';
                        break;
                }
                ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: <?php echo json_encode($message); ?>,
                    timer: 2000,
                    showConfirmButton: false
                });
            <?php endif; ?>
        });
    </script>

    <div class="bg-white rounded-lg shadow-sm border border-[#594423] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-[#594423] text-white">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Award Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-accent">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="hover:bg-accent/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm"><?php echo $row['AwardID']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm"><?php echo htmlspecialchars($row['AwardName'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm"><?php echo htmlspecialchars($row['CategoryName'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center gap-2">
                                        <button class="text-secondary hover:text-primary transition-colors" onclick="viewAward(<?php echo $row['AwardID']; ?>)" title="View Award">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <button class="text-secondary hover:text-primary transition-colors" onclick="editAward(<?php echo $row['AwardID']; ?>)" title="Edit Award">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button class="text-secondary hover:text-red-500 transition-colors" onclick="confirmDeleteAward(<?php echo $row['AwardID']; ?>)" title="Delete Award">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm">No awards found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Award Modal -->
<div id="addAwardModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium leading-6 text-secondary" id="modal-title">Add New Award</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('addAwardModal').classList.add('hidden')">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="mb-4">
                        <label for="award_name" class="block text-sm font-medium text-secondary">Award Name</label>
                        <input type="text" class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="award_name" name="award_name" required>
                    </div>
                    <div class="mb-4">
                        <label for="category_id" class="block text-sm font-medium text-secondary">Category</label>
                        <select class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="category_id" name="category_id" required>
                            <?php
                            mysqli_data_seek($categories_result, 0);
                            while ($cat = mysqli_fetch_assoc($categories_result)) {
                                echo "<option value='" . $cat['CategoryID'] . "'>" . $cat['CategoryName'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-secondary">Description</label>
                        <textarea class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="px-4 py-2 text-sm font-medium text-secondary bg-white border border-accent rounded-md hover:bg-accent hover:text-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" onclick="document.getElementById('addAwardModal').classList.add('hidden')">Cancel</button>
                        <button type="submit" name="add_award" class="px-4 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-md hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">Add Award</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Award Modal -->
<div id="editAwardModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium leading-6 text-secondary" id="modal-title">Edit Award</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('editAwardModal').classList.add('hidden')">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <?php if ($edit_award): ?>
                    <form method="POST" action="">
                        <input type="hidden" name="award_id" value="<?php echo $edit_award['AwardID']; ?>">
                        <div class="mb-4">
                            <label for="edit_award_name" class="block text-sm font-medium text-secondary">Award Name</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="edit_award_name" name="award_name"
                                value="<?php echo htmlspecialchars($edit_award['AwardName'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="mb-4">
                            <label for="edit_category_id" class="block text-sm font-medium text-secondary">Category</label>
                            <select class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="edit_category_id" name="category_id" required>
                                <?php
                                mysqli_data_seek($categories_result, 0);
                                while ($cat = mysqli_fetch_assoc($categories_result)) {
                                    $selected = ($cat['CategoryID'] == $edit_award['CategoryID']) ? 'selected' : '';
                                    echo "<option value='" . $cat['CategoryID'] . "' " . $selected . ">" . $cat['CategoryName'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="edit_description" class="block text-sm font-medium text-secondary">Description</label>
                            <textarea class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="edit_description" name="description" rows="3"><?php
                                                                                                                                                                                                                                echo htmlspecialchars($edit_award['Description'], ENT_QUOTES, 'UTF-8');
                                                                                                                                                                                                                                ?></textarea>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" class="px-4 py-2 text-sm font-medium text-secondary bg-white border border-accent rounded-md hover:bg-accent hover:text-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" onclick="document.getElementById('editAwardModal').classList.add('hidden')">Cancel</button>
                            <button type="submit" name="update_award" class="px-4 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-md hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">Update Award</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- View Award Modal -->
<div id=" " class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium leading-6 text-secondary" id="modal-title">Award Details</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('viewAwardModal').classList.add('hidden')">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <?php if ($view_award): ?>
                    <div class="space-y-4">
                        <div>
                            <h6 class="text-sm font-medium text-secondary mb-2">Award Information</h6>
                            <div class="space-y-2">
                                <p class="text-sm"><span class="font-medium text-secondary">Name:</span> <?php echo htmlspecialchars($view_award['AwardName'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <p class="text-sm"><span class="font-medium text-secondary">Category:</span> <?php echo htmlspecialchars($view_award['CategoryName'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <p class="text-sm"><span class="font-medium text-secondary">Description:</span> <?php echo htmlspecialchars($view_award['Description'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="p-4 text-sm text-red-500 bg-red-50 rounded-md">Award details not found.</div>
                <?php endif; ?>
                <div class="mt-6 flex justify-end">
                    <button type="button" class="px-4 py-2 text-sm font-medium text-secondary bg-white border border-accent rounded-md hover:bg-accent hover:text-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" onclick="document.getElementById('viewAwardModal').classList.add('hidden')">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize modals
    let viewModal, editModal, addModal;
    document.addEventListener('DOMContentLoaded', function() {
        // Show modals based on URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('view')) {
            document.getElementById('viewAwardModal').classList.remove('hidden');
        }
        if (urlParams.has('edit')) {
            document.getElementById('editAwardModal').classList.remove('hidden');
        }

        // Handle modal close events
        const modals = document.querySelectorAll('[id$="Modal"]');
        modals.forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                    // Remove URL parameters when modal is closed
                    const url = new URL(window.location);
                    url.searchParams.delete('view');
                    url.searchParams.delete('edit');
                    window.history.replaceState({}, '', url);
                }
            });
        });

        // Add click event listeners to close buttons
        document.querySelectorAll('[onclick*="hide()"]').forEach(button => {
            button.addEventListener('click', function() {
                const modal = this.closest('[id$="Modal"]');
                if (modal) {
                    modal.classList.add('hidden');
                    // Remove URL parameters when modal is closed
                    const url = new URL(window.location);
                    url.searchParams.delete('view');
                    url.searchParams.delete('edit');
                    window.history.replaceState({}, '', url);
                }
            });
        });
    });

    function viewAward(id) {
        window.location.href = '?page=awards&view=' + id;
    }

    function editAward(id) {
        window.location.href = '?page=awards&edit=' + id;
    }

    function confirmDeleteAward(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '?page=awards&delete=' + id;
            }
        });
    }
</script>