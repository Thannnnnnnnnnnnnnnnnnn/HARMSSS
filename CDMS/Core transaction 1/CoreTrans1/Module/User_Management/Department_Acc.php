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

$query = "SELECT * FROM department_accounts";
$result = $conn->query($query);
?>
<body>
    <div class="flex min-h-screen w-full">
        <?php include('../../includes/sidebar.php'); ?>
        
        <div class="main w-full bg-[#FFF6E8] md:ml-[320px]" id="main-content">
            <?php include('../../includes/navbar.php'); ?>
            
            <main class="px-4 sm:px-6 lg:px-8 py-8 flex-1">
                <h3 class="text-2xl sm:text-3xl p-3 font-bold text-[#594423] font-[Cinzel]">Department Accounts</h3>
                <div class="w-full">
                    <div class="mt-2 overflow-x-auto">
                        <table class="w-full bg-white rounded-lg shadow-md border-2 border-[#594423] table-auto">
                            <thead class="bg-[#4E3B2A] text-white text-sm sm:text-base">
                                <tr>
                                    <th class="py-2 px-3 text-left border border-[#594423] min-w-[100px]">Account ID</th>
                                    <th class="py-2 px-3 text-left border border-[#594423] min-w-[100px]">User ID</th>
                                    <th class="py-2 px-3 text-left border border-[#594423] min-w-[120px]">Name</th>
                                    <th class="hidden sm:table-cell py-2 px-3 text-left border border-[#594423] min-w-[100px]">Role</th>
                                    <th class="py-2 px-3 text-center border border-[#594423] min-w-[100px]">Status</th>
                                    <th class="py-2 px-3 text-center border border-[#594423] min-w-[120px] sticky right-0 bg-[#4E3B2A]">Action</th>
                                </tr>
                            </thead>
                            <tbody id="dataRows" class="text-[#4E3B2A] text-sm sm:text-base">
                                <?php if ($result && mysqli_num_rows($result) > 0) : ?>
                                    <?php while($row = mysqli_fetch_assoc($result)) : ?>
                                        <tr class="border-b border-[#594423] hover:bg-[#F7E6CA] transition">
                                            <td class="py-2 px-3 border border-[#594423] truncate"><?php echo htmlspecialchars($row['Dept_Accounts_ID']); ?></td>
                                            <td class="py-2 px-3 border border-[#594423] truncate"><?php echo htmlspecialchars($row['User_ID']); ?></td>
                                            <td class="py-2 px-3 border border-[#594423] truncate"><?php echo htmlspecialchars($row['Name']); ?></td>
                                            <td class="hidden sm:table-cell py-2 px-3 border border-[#594423] truncate"><?php echo htmlspecialchars($row['Role']); ?></td>
                                            <td class="py-2 px-3 text-center border border-[#594423]"><?php echo htmlspecialchars($row['Status']); ?></td>
                                            <td class="py-2 px-3 text-center border border-[#594423] sticky right-0 bg-white flex space-x-2 justify-center">
                                                <button 
                                                    class="manage-button bg-[#F7E6CA] text-[#594423] p-2 rounded-md border border-[#594423] hover:bg-[#594423] hover:text-[#F7E6CA] transition"
                                                    data-modal-toggle="combinedModal" 
                                                    data-account-id="<?php echo htmlspecialchars($row['Dept_Accounts_ID']); ?>"
                                                    data-status="<?php echo htmlspecialchars($row['Status']); ?>">
                                                    <i class="fa-solid fa-sliders"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="6" class="py-2 px-3 text-center text-[#4E3B2A]">No records found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div id="combinedModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" aria-hidden="true">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center">
                <h5 class="text-xl font-bold text-[#594423] font-[Cinzel]" id="combinedModalLabel">Manage Account</h5>
                <button type="button" class="text-gray-500 hover:text-gray-700 modal-close">×</button>
            </div>
            <div class="mt-4 text-[#4E3B2A]">
                <p>Account ID: <span id="modal-account-id"></span></p>
                <p>Status: <span id="modal-status"></span></p>
            </div>
            <div class="mt-4 flex justify-end space-x-2">
                <button type="button" class="modal-close bg-[#F7E6CA] text-[#594423] px-4 py-2 rounded-md border border-[#594423] hover:bg-[#594423] hover:text-[#F7E6CA] transition">Close</button>
                <button type="button" class="bg-[#F7E6CA] text-[#594423] px-4 py-2 rounded-md border border-[#594423] hover:bg-[#594423] hover:text-[#F7E6CA] transition">Save changes</button>
            </div>
        </div>
    </div>

    <script src="../../assets/scripts.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebarToggle = document.querySelector('#sidebar-toggle');
            const mainContent = document.getElementById('main-content');
            const sidebar = document.querySelector('.sidebar');

            if (sidebarToggle && sidebar && mainContent) {
                sidebarToggle.addEventListener('click', function () {
                    sidebar.classList.toggle('hidden');
                    if (window.innerWidth >= 768) { 
                        if (sidebar.classList.contains('hidden')) {
                            mainContent.classList.remove('md:ml-[320px]');
                            mainContent.classList.add('md:ml-0');
                        } else {
                            mainContent.classList.add('md:ml-[320px]');
                            mainContent.classList.remove('md:ml-0');
                        }
                    }
                });
                 if (window.innerWidth >= 768 && sidebar.classList.contains('hidden')) {
                     mainContent.classList.remove('md:ml-[320px]');
                     mainContent.classList.add('md:ml-0');
                } else if (window.innerWidth >= 768 && !sidebar.classList.contains('hidden')) {
                     mainContent.classList.add('md:ml-[320px]');
                     mainContent.classList.remove('md:ml-0');
                }
            }

            const manageButtons = document.querySelectorAll('.manage-button');
            const manageModal = document.getElementById('combinedModal');
            const closeButtons = document.querySelectorAll('.modal-close');

            manageButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const accountId = this.getAttribute('data-account-id');
                    const status = this.getAttribute('data-status');
                    if(document.getElementById('modal-account-id')) document.getElementById('modal-account-id').textContent = accountId;
                    if(document.getElementById('modal-status')) document.getElementById('modal-status').textContent = status;
                    if(manageModal) manageModal.classList.remove('hidden');
                });
            });

            closeButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const parentModal = this.closest('.fixed.inset-0');
                    if (parentModal) {
                        parentModal.classList.add('hidden');
                    }
                });
            });

            if(manageModal) {
                manageModal.addEventListener('click', function (e) {
                    if (e.target === manageModal) {
                        manageModal.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</body>
</html>
