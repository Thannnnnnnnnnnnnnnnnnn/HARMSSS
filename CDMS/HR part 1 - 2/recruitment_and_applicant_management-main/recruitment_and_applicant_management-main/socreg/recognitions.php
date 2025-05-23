<?php
require_once 'socreg/config.php';
require_once __DIR__ . '/includes/audit_helpers.php';
require_once __DIR__ . '/includes/rbac.php';

// Check if user is logged in
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

// Check if user has permission to view recognitions
// requirePermission('recognitions', 'view');

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_recognition'])) {
        // Check if user has permission to add recognitions
        // requirePermission('recognitions', 'add');

        // Create operation
        $employeeID = $_POST['employee_id'];
        $awardID = $_POST['award_id'];
        $recognitionDate = $_POST['recognition_date'];
        $notes = $_POST['notes'];

        $sql = "INSERT INTO employeerecognition (EmployeeID, AwardID, RecognitionDate, Description) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $error = "Error preparing statement: " . $conn->error;
        } else {
            $stmt->bind_param("iiss", $employeeID, $awardID, $recognitionDate, $notes);

            if ($stmt->execute()) {
                // Audit trail insert for add recognition
                $user_id = $_SESSION['user_id'] ?? 0;
                $user_name = $_SESSION['name'] ?? '';
                $role = $_SESSION['role'] ?? '';
                $department_id = $_SESSION['department_id'] ?? 0;
                $user_audit_trail_id = 0;
                $action = 'Add Recognition';
                $department_affected = getDepartmentAffectedName($department_id);
                $module_affected = 'social recognition';

                $audit_sql = "INSERT INTO department_audit_trail (department_id, user_id, user_audit_trail_id, action, department_affected, module_affected, role, user_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $audit_stmt = $conn->prepare($audit_sql);
                $audit_stmt->bind_param("iiisssss", $department_id, $user_id, $user_audit_trail_id, $action, $department_affected, $module_affected, $role, $user_name);
                $audit_stmt->execute();

                // header("Location: ?page=recognitions&success=add");
                // exit();
            } else {
                $error = "Error adding recognition: " . $stmt->error;
            }
        }
    } elseif (isset($_POST['update_recognition'])) {
        // Check if user has permission to edit recognitions
        // requirePermission('recognitions', 'edit');

        // Update operation
        $recognitionID = $_POST['recognition_id'];
        $employeeID = $_POST['employee_id'];
        $awardID = $_POST['award_id'];
        $recognitionDate = $_POST['recognition_date'];
        $notes = $_POST['notes'];

        $sql = "UPDATE employeerecognition SET EmployeeID = ?, AwardID = ?, RecognitionDate = ?, Description = ? WHERE RecognitionID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissi", $employeeID, $awardID, $recognitionDate, $notes, $recognitionID);

        if ($stmt->execute()) {
            // Audit trail insert for update recognition
            $user_id = $_SESSION['user_id'] ?? 0;
            $user_name = $_SESSION['name'] ?? '';
            $role = $_SESSION['role'] ?? '';
            $department_id = $_SESSION['department_id'] ?? 0;
            $user_audit_trail_id = 0;
            $action = 'Update Recognition';
            $department_affected = getDepartmentAffectedName($department_id);
            $module_affected = 'social recognition';

            $audit_sql = "INSERT INTO department_audit_trail (department_id, user_id, user_audit_trail_id, action, department_affected, module_affected, role, user_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $audit_stmt = $conn->prepare($audit_sql);
            $audit_stmt->bind_param("iiisssss", $department_id, $user_id, $user_audit_trail_id, $action, $department_affected, $module_affected, $role, $user_name);
            $audit_stmt->execute();

            // header("Location: ?page=recognitions&success=update");
            // exit();
        } else {
            $error = "Error updating recognition: " . $conn->error;
        }
    }
}

// Handle Delete operation
if (isset($_GET['delete'])) {
    // Check if user has permission to delete recognitions
    // requirePermission('recognitions', 'delete');

    $recognitionID = $_GET['delete'];
    $sql = "DELETE FROM employeerecognition WHERE RecognitionID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $recognitionID);

    if ($stmt->execute()) {
        // Audit trail insert for delete recognition
        $user_id = $_SESSION['user_id'] ?? 0;
        $user_name = $_SESSION['name'] ?? '';
        $role = $_SESSION['role'] ?? '';
        $department_id = $_SESSION['department_id'] ?? 0;
        $user_audit_trail_id = 0;
        $action = 'Delete Recognition';
        $department_affected = getDepartmentAffectedName($department_id);
        $module_affected = 'social recognition';

        $audit_sql = "INSERT INTO department_audit_trail (department_id, user_id, user_audit_trail_id, action, department_affected, module_affected, role, user_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $audit_stmt = $conn->prepare($audit_sql);
        $audit_stmt->bind_param("iiisssss", $department_id, $user_id, $user_audit_trail_id, $action, $department_affected, $module_affected, $role, $user_name);
        $audit_stmt->execute();

        // header("Location: ?page=recognitions&success=delete");
        // exit();
    } else {
        $error = "Error deleting recognition: " . $conn->error;
    }
}

// Get recognition details for edit
$edit_recognition = null;
if (isset($_GET['edit'])) {
    $recognitionID = $_GET['edit'];
    $sql = "SELECT * FROM employeerecognition WHERE RecognitionID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $recognitionID);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_recognition = $result->fetch_assoc();
}

// Get recognition details for view
$view_recognition = null;
if (isset($_GET['view'])) {
    $recognitionID = $_GET['view'];
    $sql = "SELECT er.*, e.`Employee name` as employee_name, e.Department, e.Position, 
                   a.`AwardName` as award_name, a.Description as award_description,
                   c.CategoryName
            FROM employeerecognition er 
            JOIN employees e ON er.EmployeeID = e.EmployeeID 
            JOIN awards a ON er.AwardID = a.AwardID
            JOIN recognitioncategories c ON a.CategoryID = c.CategoryID
            WHERE er.RecognitionID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $recognitionID);
    $stmt->execute();
    $result = $stmt->get_result();
    $view_recognition = $result->fetch_assoc();
}

// Get all recognitions with employee and award details
$sql = "SELECT er.*, e.`Employee name` as employee_name, a.`AwardName` as award_name 
        FROM employeerecognition er 
        JOIN employees e ON er.EmployeeID = e.EmployeeID 
        JOIN awards a ON er.AwardID = a.AwardID 
        ORDER BY er.RecognitionID ASC";
$result = mysqli_query($conn, $sql);

// Get employees for dropdown
$employees_sql = "SELECT * FROM employees ORDER BY `Employee name`";
$employees_result = mysqli_query($conn, $employees_sql);

// Get awards for dropdown with descriptions
$awards_sql = "SELECT * FROM awards ORDER BY AwardName";
$awards_result = mysqli_query($conn, $awards_sql);

// Store awards data for JavaScript
$awards_data = array();
while ($award = mysqli_fetch_assoc($awards_result)) {
    $awards_data[] = $award;
}
// Reset the pointer for later use
mysqli_data_seek($awards_result, 0);
?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold ">Employee Recognitions</h1>
        <button type="button" class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors" onclick="document.getElementById('addRecognitionModal').classList.remove('hidden')">
            <i class="fa-solid fa-plus"></i>
            <span>Add Recognition</span>
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
                        $message = 'Recognition added successfully!';
                        break;
                    case 'update':
                        $message = 'Recognition updated successfully!';
                        break;
                    case 'delete':
                        $message = 'Recognition deleted successfully!';
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
                        <th class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Award</th>
                        <th class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-accent">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="hover:bg-accent/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm "><?php echo $row['RecognitionID']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm "><?php echo htmlspecialchars($row['employee_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm "><?php echo htmlspecialchars($row['award_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm "><?php echo date('M d, Y', strtotime($row['RecognitionDate'])); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center gap-2">
                                        <button class=" hover: transition-colors" onclick="viewRecognition(<?php echo $row['RecognitionID']; ?>)" title="View Recognition">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <button class=" hover: transition-colors" onclick="editRecognition(<?php echo $row['RecognitionID']; ?>)" title="Edit Recognition">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button class=" hover:text-red-500 transition-colors" onclick="confirmDeleteRecognition(<?php echo $row['RecognitionID']; ?>)" title="Delete Recognition">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm ">No recognitions found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Recognition Modal -->
<div id="addRecognitionModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium leading-6 " id="modal-title">Add New Recognition</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('addRecognitionModal').classList.add('hidden')">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <form action="" method="POST" id="addRecognitionForm">
                    <div class="mb-4">
                        <label for="employee_id" class="block text-sm font-medium ">Employee</label>
                        <select class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="employee_id" name="employee_id" required>
                            <option value="">Select Employee</option>
                            <?php
                            mysqli_data_seek($employees_result, 0);
                            while ($employee = mysqli_fetch_assoc($employees_result)):
                            ?>
                                <option value="<?php echo $employee['EmployeeID']; ?>">
                                    <?php echo htmlspecialchars($employee['Employee name'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="award_id" class="block text-sm font-medium ">Award</label>
                        <select class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="award_id" name="award_id" required>
                            <option value="">Select Award</option>
                            <?php
                            mysqli_data_seek($awards_result, 0);
                            while ($award = mysqli_fetch_assoc($awards_result)):
                            ?>
                                <option value="<?php echo $award['AwardID']; ?>"
                                    data-description="<?php echo htmlspecialchars($award['Description'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($award['AwardName'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="recognition_date" class="block text-sm font-medium ">Recognition Date</label>
                        <input type="date" class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity50" id="recognition_date" name="recognition_date" required>
                    </div>
                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium ">Description</label>
                        <textarea class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="notes" name="notes" rows="3"></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="px-4 py-2 text-sm font-medium  bg-white border border-accent rounded-md hover:bg-accent hover: focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" onclick="document.getElementById('addRecognitionModal').classList.add('hidden')">Cancel</button>
                        <button type="submit" name="add_recognition" class="px-4 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-md hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">Add Recognition</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Recognition Modal -->
<div id="editRecognitionModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium leading-6 " id="modal-title">Edit Recognition</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('editRecognitionModal').classList.add('hidden')">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <?php if ($edit_recognition): ?>
                    <form method="POST" action="" id="editRecognitionForm">
                        <input type="hidden" name="recognition_id" value="<?php echo $edit_recognition['RecognitionID']; ?>">
                        <div class="mb-4">
                            <label for="edit_employee_id" class="block text-sm font-medium ">Employee</label>
                            <select class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="edit_employee_id" name="employee_id" required>
                                <option value="">Select Employee</option>
                                <?php
                                mysqli_data_seek($employees_result, 0);
                                while ($employee = mysqli_fetch_assoc($employees_result)):
                                ?>
                                    <option value="<?php echo $employee['EmployeeID']; ?>"
                                        <?php echo ($edit_recognition['EmployeeID'] == $employee['EmployeeID']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($employee['Employee name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="edit_award_id" class="block text-sm font-medium ">Award</label>
                            <select class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="edit_award_id" name="award_id" required>
                                <option value="">Select Award</option>
                                <?php
                                mysqli_data_seek($awards_result, 0);
                                while ($award = mysqli_fetch_assoc($awards_result)):
                                ?>
                                    <option value="<?php echo $award['AwardID']; ?>"
                                        data-description="<?php echo htmlspecialchars($award['Description'], ENT_QUOTES, 'UTF-8'); ?>"
                                        <?php echo ($edit_recognition['AwardID'] == $award['AwardID']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($award['AwardName'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="edit_recognition_date" class="block text-sm font-medium ">Recognition Date</label>
                            <input type="date" class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="edit_recognition_date" name="recognition_date"
                                value="<?php echo $edit_recognition['RecognitionDate']; ?>" required>
                        </div>
                        <div class="mb-4">
                            <label for="edit_notes" class="block text-sm font-medium ">Description</label>
                            <textarea class="mt-1 block w-full rounded-md border-accent shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" id="edit_notes" name="notes" rows="3"><?php
                                                                                                                                                                                                                    echo htmlspecialchars($edit_recognition['Description'], ENT_QUOTES, 'UTF-8');
                                                                                                                                                                                                                    ?></textarea>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" class="px-4 py-2 text-sm font-medium  bg-white border border-accent rounded-md hover:bg-accent hover: focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" onclick="document.getElementById('editRecognitionModal').classList.add('hidden')">Cancel</button>
                            <button type="submit" name="update_recognition" class="px-4 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-md hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">Update Recognition</button>
                        </div>
                    <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- View Recognition Modal -->
<div id="viewRecognitionModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium leading-6 " id="modal-title">Recognition Details</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('viewRecognitionModal').classList.add('hidden')">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <?php if ($view_recognition): ?>
                    <div class="space-y-4">
                        <div>
                            <h6 class="text-sm font-medium  mb-2">Employee Information</h6>
                            <div class="space-y-2">
                                <p class="text-sm "><span class="font-medium ">Name:</span> <?php echo htmlspecialchars($view_recognition['employee_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <p class="text-sm "><span class="font-medium ">Department:</span> <?php echo htmlspecialchars($view_recognition['Department'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <p class="text-sm "><span class="font-medium ">Position:</span> <?php echo htmlspecialchars($view_recognition['Position'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </div>

                        <div>
                            <h6 class="text-sm font-medium  mb-2">Recognition Details</h6>
                            <div class="space-y-2">
                                <p class="text-sm "><span class="font-medium ">Award:</span> <?php echo htmlspecialchars($view_recognition['award_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <p class="text-sm "><span class="font-medium ">Date:</span> <?php echo date('F j, Y', strtotime($view_recognition['RecognitionDate'])); ?></p>
                                <p class="text-sm "><span class="font-medium ">Category:</span> <?php echo htmlspecialchars($view_recognition['CategoryName'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <p class="text-sm "><span class="font-medium ">Description:</span> <?php echo htmlspecialchars($view_recognition['award_description'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="p-4 text-sm text-red-500 bg-red-50 rounded-md">Recognition details not found.</div>
                <?php endif; ?>
                <div class="mt-6 flex justify-end">
                    <button type="button" class="px-4 py-2 text-sm font-medium  bg-white border border-accent rounded-md hover:bg-accent hover: focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" onclick="document.getElementById('viewRecognitionModal').classList.add('hidden')">Close</button>
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
            document.getElementById('viewRecognitionModal').classList.remove('hidden');
        }
        if (urlParams.has('edit')) {
            document.getElementById('editRecognitionModal').classList.remove('hidden');
        }

        // Set today's date as default for recognition date in add modal
        const recognitionDateInput = document.getElementById('recognition_date');
        if (recognitionDateInput) recognitionDateInput.valueAsDate = new Date();

        // Handle award selection change for add modal
        const addAwardSelect = document.getElementById('award_id');
        if (addAwardSelect) {
            addAwardSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const description = selectedOption.getAttribute('data-description');
                const notesTextarea = document.getElementById('notes');

                if (description && notesTextarea && !notesTextarea.value) {
                    notesTextarea.value = description;
                }
            });
        }

        // Handle award selection change for edit modal
        const editAwardSelect = document.getElementById('edit_award_id');
        if (editAwardSelect) {
            editAwardSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const description = selectedOption.getAttribute('data-description');
                const notesTextarea = document.getElementById('edit_notes');

                if (description && notesTextarea && !notesTextarea.value) {
                    notesTextarea.value = description;
                }
            });
        }

        // Handle form submissions - Basic validation (can be enhanced)
        const addForm = document.getElementById('addRecognitionForm');
        if (addForm) {
            addForm.addEventListener('submit', function(e) {
                // Add validation logic here if needed
                // if (!validateAddForm()) {
                //     e.preventDefault();
                // }
            });
        }

        const editForm = document.getElementById('editRecognitionForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                // Add validation logic here if needed
                // if (!validateEditForm()) {
                //     e.preventDefault();
                // }
            });
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

    function viewRecognition(id) {
        window.location.href = '?page=recognitions&view=' + id;
    }

    function editRecognition(id) {
        window.location.href = '?page=recognitions&edit=' + id;
    }

    function deleteRecognition(id) {
        // Deprecated: replaced by confirmDeleteRecognition with SweetAlert2
        if (confirm('Are you sure you want to delete this recognition?')) {
            window.location.href = '?page=recognitions&delete=' + id;
        }
    }

    function confirmDeleteRecognition(id) {
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
                window.location.href = '?page=recognitions&delete=' + id;
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