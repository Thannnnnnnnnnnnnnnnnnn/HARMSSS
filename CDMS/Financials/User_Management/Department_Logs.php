<?php
include("../Database/connection.php");


// Define the database name
$db_name = "fin_usm";

// Instantiate the Database class and connect
$db = new Database();
$connection = $db->connect($db_name);

if (!$connection) {
    die("Database connection not found for $db_name");
}
~
$query = "SELECT Dept_Log_ID, Department_ID, User_ID, Name, Role, Log_Status, Log_Date_Time FROM `department_log_history` ORDER BY Log_Date_Time DESC"; 
$result = mysqli_query($connection, $query);

if (!$result) {
    die("Fetch query failed: " . mysqli_error($connection));
}

$pageTitle = "Department Logs";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('../includes/head.php'); ?>

</head>
<body class="flex min-h-screen w-full bg-[#FFF6E8]">
    <!-- Sidebar -->
    <?php include('../includes/sidebar.php'); ?>

    <!-- Main + Navbar -->
    <div id="main-content" class="main w-full md:ml-[320px] transition-all duration-300">
        <!-- Navbar -->
        <?php include('../includes/navbar.php'); ?>

        <!-- Main Content -->
        <main class="px-8 py-8 flex-1">
            <h3 class="text-3xl p-3 font-bold">Department Logs</h3>
            <div class="w-full">
                <div class="mt-2 overflow-x-auto">
                    <table class="min-w-full bg-white rounded-lg overflow-hidden shadow-md border border-[#4E3B2A] table-auto">
                        <thead class="bg-[#4E3B2A] text-white">
                            <tr>
                                <th class="py-3 px-4 text-left border border-[#4E3B2A]">Log ID</th>
                                <th class="py-3 px-4 text-left border border-[#4E3B2A]">Dept ID</th>
                                <th class="py-3 px-4 text-left border border-[#4E3B2A]">User ID</th>
                                <th class="py-3 px-4 text-left border border-[#4E3B2A]">Name</th>
                                <th class="py-3 px-4 text-left border border-[#4E3B2A]">Role</th>
                                <th class="py-3 px-4 text-center border border-[#4E3B2A]">Log Status</th>
                                <th class="py-3 px-4 text-center border border-[#4E3B2A]">Date/Time</th>
                                <th class="py-3 px-4 text-center border border-[#4E3B2A]">Action</th>
                            </tr>
                        </thead>
                        <tbody id="dataRows" class="text-gray-700">
                            <?php if ($result && mysqli_num_rows($result) > 0) : ?>
                                <?php while($row = mysqli_fetch_assoc($result)) : ?>
                                    <tr class="border-b border-[#F7E6CA] hover:bg-gray-100">
                                        <td class="py-3 px-4 border border-[#F7E6CA]"><?php echo htmlspecialchars($row['Dept_Log_ID']); ?></td>
                                        <td class="py-3 px-4 border border-[#F7E6CA]"><?php echo htmlspecialchars($row['Department_ID']); ?></td>
                                        <td class="py-3 px-4 border border-[#F7E6CA]"><?php echo htmlspecialchars($row['User_ID']); ?></td>
                                        <td class="py-3 px-4 border border-[#F7E6CA]"><?php echo htmlspecialchars($row['Name']); ?></td>
                                        <td class="py-3 px-4 border border-[#F7E6CA]"><?php echo htmlspecialchars($row['Role']); ?></td>
                                        <td class="py-3 px-4 text-center border border-[#F7E6CA]"><?php echo htmlspecialchars($row['Log_Status']); ?></td>
                                        <td class="py-3 px-4 text-center border border-[#F7E6CA]"><?php echo htmlspecialchars($row['Log_Date_Time']); ?></td>
                                        <td class="py-3 px-4 text-center border border-[#F7E6CA]">
                                            <button 
                                                class="manage-button bg-blue-600 text-white p-3 rounded-md"
                                                data-modal-toggle="combinedModal" 
                                                data-log-id="<?php echo htmlspecialchars($row['Dept_Log_ID']); ?>"
                                                data-log-status="<?php echo htmlspecialchars($row['Log_Status']); ?>">
                                                <i class="fa-solid fa-sliders"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="8" class="py-3 px-4 text-center text-gray-700">No records found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal for Unified CRUD -->
    <div id="combinedModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" aria-hidden="true">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center">
                <h5 class="text-xl font-bold" id="combinedModalLabel">Manage Log</h5>
                <button type="button" class="text-gray-500 hover:text-gray-700 modal-close">×</button>
            </div>
            <div class="mt-4">
                <p>Log ID: <span id="modal-log-id"></span></p>
                <p>Log Status: <span id="modal-log-status"></span></p>
                <!-- Add more fields or form elements as needed -->
            </div>
            <div class="mt-4 flex justify-end space-x-2">
                <button type="button" class="modal-close bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Close</button>
                <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Save changes</button>
            </div>
        </div>
    </div>

    <script src="../assets/js.js"></script>
    <script>
        // JavaScript to handle sidebar toggle and modal interactions
        document.addEventListener('DOMContentLoaded', function () {
            // Sidebar toggle
            const sidebarToggle = document.querySelector('#sidebar-toggle'); // Assumes toggle button in navbar.php
            const mainContent = document.getElementById('main-content');
            const sidebar = document.querySelector('.sidebar'); // Assumes sidebar has class 'sidebar'

            if (sidebarToggle && sidebar && mainContent) {
                sidebarToggle.addEventListener('click', function () {
                    sidebar.classList.toggle('hidden');
                    if (sidebar.classList.contains('hidden')) {
                        mainContent.classList.remove('md:ml-[320px]');
                        mainContent.classList.add('md:ml-0');
                    } else {
                        mainContent.classList.add('md:ml-[320px]');
                        mainContent.classList.remove('md:ml-0');
                    }
                });
            }

            // Modal handling
            const buttons = document.querySelectorAll('.manage-button');
            const modal = document.getElementById('combinedModal');
            const closeButtons = document.querySelectorAll('.modal-close');

            buttons.forEach(button => {
                button.addEventListener('click', function () {
                    const logId = this.getAttribute('data-log-id');
                    const logStatus = this.getAttribute('data-log-status');
                    document.getElementById('modal-log-id').textContent = logId;
                    document.getElementById('modal-log-status').textContent = logStatus;
                    modal.classList.remove('hidden');
                });
            });

            closeButtons.forEach(button => {
                button.addEventListener('click', function () {
                    modal.classList.add('hidden');
                });
            });

            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>