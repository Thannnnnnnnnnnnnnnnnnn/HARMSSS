<?php
require_once 'socreg/config.php';
require_once __DIR__ . '/includes/audit_helpers.php';
require_once __DIR__ . '/includes/rbac.php';

// Check if user is logged in
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

// Check if user has permission to view categories
// requirePermission('categories', 'view');

// Handle add category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    // Check if user has permission to add categories
    // requirePermission('categories', 'add');

    $categoryName = $_POST['category_name'];
    $description = $_POST['description'];

    $sql = "INSERT INTO recognitioncategories (CategoryName, Description) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $categoryName, $description);

    if ($stmt->execute()) {
        // Audit trail insert for add category
        $user_id = $_SESSION['user_id'] ?? 0;
        $user_name = $_SESSION['name'] ?? '';
        $role = $_SESSION['role'] ?? '';
        $department_id = $_SESSION['department_id'] ?? 0;
        $user_audit_trail_id = 0;
        $action = 'Add Category';
        $department_affected = getDepartmentAffectedName($department_id);
        $module_affected = 'social recognition';

        $audit_sql = "INSERT INTO department_audit_trail (department_id, user_id, user_audit_trail_id, action, department_affected, module_affected, role, user_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $audit_stmt = $conn->prepare($audit_sql);
        $audit_stmt->bind_param("iiisssss", $department_id, $user_id, $user_audit_trail_id, $action, $department_affected, $module_affected, $role, $user_name);
        $audit_stmt->execute();

        // header("Location: ?page=categories&success=add");
        // exit();
    } else {
        $error = "Error adding category: " . $conn->error;
    }
}

// Handle update category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_category'])) {
    // Check if user has permission to edit categories
    // requirePermission('categories', 'edit');

    $categoryID = $_POST['category_id'];
    $categoryName = $_POST['category_name'];
    $description = $_POST['description'];

    $sql = "UPDATE recognitioncategories SET CategoryName = ?, Description = ? WHERE CategoryID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $categoryName, $description, $categoryID);

    if ($stmt->execute()) {
        // Audit trail insert for update category
        $user_id = $_SESSION['user_id'] ?? 0;
        $user_name = $_SESSION['name'] ?? '';
        $role = $_SESSION['role'] ?? '';
        $department_id = $_SESSION['department_id'] ?? 0;
        $user_audit_trail_id = 0;
        $action = 'Update Category';
        $department_affected = getDepartmentAffectedName($department_id);
        $module_affected = 'social recognition';

        $audit_sql = "INSERT INTO department_audit_trail (department_id, user_id, user_audit_trail_id, action, department_affected, module_affected, role, user_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $audit_stmt = $conn->prepare($audit_sql);
        $audit_stmt->bind_param("iiisssss", $department_id, $user_id, $user_audit_trail_id, $action, $department_affected, $module_affected, $role, $user_name);
        $audit_stmt->execute();

        // header("Location: ?page=categories&success=update");
        // exit();
    } else {
        $error = "Error updating category: " . $conn->error;
    }
}

// Handle delete category
if (isset($_GET['delete'])) {
    // Check if user has permission to delete categories
    // requirePermission('categories', 'delete');

    $categoryID = $_GET['delete'];

    // Check if category is being used in any awards
    $check_sql = "SELECT COUNT(*) as count FROM awards WHERE CategoryID = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $categoryID);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $check_row = $check_result->fetch_assoc();

    if ($check_row['count'] > 0) {
        $error = "Cannot delete category as it is being used in awards";
    } else {
        $sql = "DELETE FROM recognitioncategories WHERE CategoryID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $categoryID);

        if ($stmt->execute()) {
            // Audit trail insert for delete category
            $user_id = $_SESSION['user_id'] ?? 0;
            $user_name = $_SESSION['name'] ?? '';
            $role = $_SESSION['role'] ?? '';
            $department_id = $_SESSION['department_id'] ?? 0;
            $user_audit_trail_id = 0;
            $action = 'Delete Category';
            $department_affected = getDepartmentAffectedName($department_id);
            $module_affected = 'social recognition';

            $audit_sql = "INSERT INTO department_audit_trail (department_id, user_id, user_audit_trail_id, action, department_affected, module_affected, role, user_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $audit_stmt = $conn->prepare($audit_sql);
            $audit_stmt->bind_param("iiisssss", $department_id, $user_id, $user_audit_trail_id, $action, $department_affected, $module_affected, $role, $user_name);
            $audit_stmt->execute();

            // header("Location: ?page=categories&success=delete");
            // exit();
        } else {
            $error = "Error deleting category: " . $conn->error;
        }
    }
}

// Get category details for edit
$edit_category = null;
if (isset($_GET['edit'])) {
    $categoryID = $_GET['edit'];
    $sql = "SELECT * FROM recognitioncategories WHERE CategoryID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $categoryID);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_category = $result->fetch_assoc();
}

// Get category details for view
$view_category = null;
if (isset($_GET['view'])) {
    $categoryID = $_GET['view'];
    $sql = "SELECT * FROM recognitioncategories WHERE CategoryID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $categoryID);
    $stmt->execute();
    $result = $stmt->get_result();
    $view_category = $result->fetch_assoc();
}

// Get all categories
$sql = "SELECT * FROM recognitioncategories ORDER BY CategoryID ASC";
$result = mysqli_query($conn, $sql);
?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold ">Categories</h1>
        <button type="button" class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors" onclick="document.getElementById('addCategoryModal').classList.remove('hidden')">
            <i class="fa-solid fa-plus"></i>
            <span>Add Category</span>
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
                        $message = 'Category added successfully!';
                        break;
                    case 'update':
                        $message = 'Category updated successfully!';
                        break;
                    case 'delete':
                        $message = 'Category deleted successfully!';
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
                        <th class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Category Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-accent">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="hover:bg-accent/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm "><?php echo $row['CategoryID']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm "><?php echo htmlspecialchars($row['CategoryName'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center gap-2">
                                        <button class=" hover: transition-colors" onclick="viewCategory(<?php echo $row['CategoryID']; ?>)" title="View Category">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <button class=" hover: transition-colors" onclick="editCategory(<?php echo $row['CategoryID']; ?>)" title="Edit Category">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button class=" hover:text-red-500 transition-colors" onclick="confirmDeleteCategory(<?php echo $row['CategoryID']; ?>)" title="Delete Category">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-sm ">No categories found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div id="addCategoryModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium leading-6 " id="modal-title">Add New Category</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('addCategoryModal').classList.add('hidden')">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="mb-4">
                        <label for="category_name" class="block text-sm font-medium ">Category Name</label>
                        <input type="text" class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="category_name" name="category_name" required>
                    </div>
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium ">Description</label>
                        <textarea class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="px-4 py-2 text-sm font-medium  bg-white border border-accent rounded-md hover:bg-accent hover: focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" onclick="document.getElementById('addCategoryModal').classList.add('hidden')">Cancel</button>
                        <button type="submit" name="add_category" class="px-4 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-md hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">Add Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="editCategoryModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium leading-6 " id="modal-title">Edit Category</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('editCategoryModal').classList.add('hidden')">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <?php if ($edit_category): ?>
                    <form method="POST" action="">
                        <input type="hidden" name="category_id" value="<?php echo $edit_category['CategoryID']; ?>">
                        <div class="mb-4">
                            <label for="edit_category_name" class="block text-sm font-medium ">Category Name</label>
                            <input type="text" class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="edit_category_name" name="category_name"
                                value="<?php echo htmlspecialchars($edit_category['CategoryName'], ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="mb-4">
                            <label for="edit_description" class="block text-sm font-medium ">Description</label>
                            <textarea class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="edit_description" name="description" rows="3"><?php
                                                                                                                                                                                                                                echo htmlspecialchars($edit_category['Description'], ENT_QUOTES, 'UTF-8');
                                                                                                                                                                                                                                ?></textarea>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" class="px-4 py-2 text-sm font-medium  bg-white border border-accent rounded-md hover:bg-accent hover: focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" onclick="document.getElementById('editCategoryModal').classList.add('hidden')">Cancel</button>
                            <button type="submit" name="update_category" class="px-4 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-md hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">Update Category</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- View Category Modal -->
<div id="viewCategoryModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium leading-6 " id="modal-title">Category Details</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('viewCategoryModal').classList.add('hidden')">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <?php if ($view_category): ?>
                    <div class="space-y-4">
                        <div>
                            <h6 class="text-sm font-medium  mb-2">Category Information</h6>
                            <div class="space-y-2">
                                <p class="text-sm "><span class="font-medium ">Name:</span> <?php echo htmlspecialchars($view_category['CategoryName'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <p class="text-sm "><span class="font-medium ">Description:</span> <?php echo htmlspecialchars($view_category['Description'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="p-4 text-sm text-red-500 bg-red-50 rounded-md">Category details not found.</div>
                <?php endif; ?>
                <div class="mt-6 flex justify-end">
                    <button type="button" class="px-4 py-2 text-sm font-medium  bg-white border border-accent rounded-md hover:bg-accent hover: focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" onclick="document.getElementById('viewCategoryModal').classList.add('hidden')">Close</button>
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
            document.getElementById('viewCategoryModal').classList.remove('hidden');
        }
        if (urlParams.has('edit')) {
            document.getElementById('editCategoryModal').classList.remove('hidden');
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

    function viewCategory(id) {
        window.location.href = '?page=categories&view=' + id;
    }

    function editCategory(id) {
        window.location.href = '?page=categories&edit=' + id;
    }

    function deleteCategory(id) {
        // Deprecated: replaced by confirmDeleteCategory with SweetAlert2
        if (confirm('Are you sure you want to delete this category?')) {
            window.location.href = '?page=categories&delete=' + id;
        }
    }

    function confirmDeleteCategory(id) {
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
                window.location.href = '?page=categories&delete=' + id;
            }
        });
    }

    // Initialize Bootstrap tooltips - No longer needed with Tailwind
    // document.addEventListener('DOMContentLoaded', function () {
    //     var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    //     var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    //         return new bootstrap.Tooltip(tooltipTriggerEl)
    //     })
    // });
</script>