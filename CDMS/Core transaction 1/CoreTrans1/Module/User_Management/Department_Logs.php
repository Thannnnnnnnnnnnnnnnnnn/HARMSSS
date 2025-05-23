<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

include('../../includes/head.php');
require_once '../../includes/Database.php';

$db = new Database();
$conn = $db->connect('usm');

$query = "SELECT Dept_Log_ID as Log_ID, Department_ID, User_ID, Name, Role, Log_Status, Log_Date_Time as Date_Time, Failure_reason, Attempt_type FROM department_log_history ORDER BY Log_Date_Time DESC";
$result = $conn->query($query);
?>
<body>
    <div class="flex min-h-screen w-full">
        <?php include('../../includes/sidebar.php'); ?>
        <div class="main w-full bg-[#FFF6E8] md:ml-[320px]" id="main-content">
            <?php include('../../includes/navbar.php'); ?>
            <main class="px-4 sm:px-6 lg:px-8 py-8">
                <h3 class="text-2xl sm:text-3xl p-3 font-bold text-[#594423] font-[Cinzel]">Department Log History</h3>
                <div class="w-full">
                    <div class="mt-2 overflow-x-auto">
                        <table class="min-w-full bg-white rounded-lg shadow-md border-2 border-[#594423] table-auto">
                            <thead class="bg-[#4E3B2A] text-white text-sm sm:text-base">
                                <tr>
                                    <th class="py-2 px-3 text-left border border-[#594423]">Log ID</th>
                                    <th class="py-2 px-3 text-left border border-[#594423]">Dept ID</th>
                                    <th class="py-2 px-3 text-left border border-[#594423]">User ID</th>
                                    <th class="py-2 px-3 text-left border border-[#594423]">Name</th>
                                    <th class="py-2 px-3 text-left border border-[#594423]">Role</th>
                                    <th class="py-2 px-3 text-center border border-[#594423]">Log Status</th>
                                    <th class="py-2 px-3 text-center border border-[#594423]">Date/Time</th>
                                    <th class="py-2 px-3 text-left border border-[#594423]">Reason/Type</th>
                                </tr>
                            </thead>
                            <tbody id="dataRows" class="text-[#4E3B2A] text-sm sm:text-base">
                                <?php if ($result && mysqli_num_rows($result) > 0) : ?>
                                    <?php while($row = mysqli_fetch_assoc($result)) : ?>
                                        <tr class="border-b border-[#594423] hover:bg-[#F7E6CA] transition">
                                            <td class="py-2 px-3 border border-[#594423] truncate"><?php echo htmlspecialchars($row['Log_ID']); ?></td>
                                            <td class="py-2 px-3 border border-[#594423] truncate"><?php echo htmlspecialchars($row['Department_ID'] ?? 'N/A'); ?></td>
                                            <td class="py-2 px-3 border border-[#594423] truncate"><?php echo htmlspecialchars($row['User_ID']); ?></td>
                                            <td class="py-2 px-3 border border-[#594423] truncate"><?php echo htmlspecialchars($row['Name']); ?></td>
                                            <td class="py-2 px-3 border border-[#594423] truncate"><?php echo htmlspecialchars($row['Role']); ?></td>
                                            <td class="py-2 px-3 text-center border border-[#594423] truncate"><?php echo htmlspecialchars($row['Log_Status']); ?></td>
                                            <td class="py-2 px-3 text-center border border-[#594423] truncate"><?php echo htmlspecialchars($row['Date_Time']); ?></td>
                                            <td class="py-2 px-3 border border-[#594423] truncate">
                                                <?php 
                                                echo htmlspecialchars($row['Attempt_type']);
                                                if (!empty($row['Failure_reason'])) {
                                                    echo ": " . htmlspecialchars($row['Failure_reason']);
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="8" class="py-3 px-4 text-center text-[#4E3B2A]">No log records found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="../../assets/scripts.js"></script>
</body>
</html>