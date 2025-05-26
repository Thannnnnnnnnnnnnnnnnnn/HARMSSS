
<?php include('../includes/head.php'); ?>
<body>
    <div class="flex min-h-screen w-full">
        <!-- Sidebar -->
        <?php include('../includes/sidebar.php'); ?>

        <!-- Main + Navbar -->
        <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
            <!-- Navbar -->
            <?php include('../includes/navbar.php'); ?>
            <!-- Main Content -->
            <main class="px-8 py-8">
                <!-- All Content Put Here -->


                     <div id="approveModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                        <div class="bg-white p-8 rounded-xl shadow-xl w-full max-w-md">
                            <h2 class="text-2xl font-semibold mb-6 text-gray-800">Approve Request</h2>
                            <form id="approveForm" action="backend/update_approval.php" method="POST" class="space-y-6">
                                <input type="hidden" id="approvalIdInput" name="approvalId">
                                <input type="hidden" name="status" value="Approved">

                                <div>
                                    <label for="approverId" class="block text-sm font-medium text-gray-700 mb-1">Approver ID</label>
                                    <input type="number" id="approverId" name="approverId" required
                                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>
                                <div>
                                    <label for="allocationId" class="block text-sm font-medium text-gray-700 mb-1">Allocation ID</label>
                                    <input type="number" id="allocationId" name="allocationId" required
                                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>
                                <div class="flex justify-end space-x-4 pt-4">
                                    <button type="button" onclick="closeApproveModal()" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                                        Cancel
                                    </button>
                                    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                        Submit Approval
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Reject Modal -->
                    <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                        <div class="bg-white p-8 rounded-xl shadow-xl w-full max-w-md">
                            <h2 class="text-2xl font-semibold mb-6 text-gray-800">Reject Request</h2>
                            <form id="rejectForm" action="backend/update_approval.php" method="POST" class="space-y-6">
                                <input type="hidden" id="rejectApprovalIdInput" name="approvalId">
                                <input type="hidden" name="status" value="Rejected">
                                <div>
                                    <label for="rejectApproverId" class="block text-sm font-medium text-gray-700 mb-1">Approver ID</label>
                                    <input type="number" id="rejectApproverId" name="approverId" required
                                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>
                                  <div>
                                    <label for="allocationId" class="block text-sm font-medium text-gray-700 mb-1">Allocation ID</label>
                                    <input type="number" id="allocationId" name="allocationId" required
                                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>
                                <div>
                                    <label for="rejectReason" class="block text-sm font-medium text-gray-700 mb-1">Reason for Rejection (Optional)</label>
                                    <textarea id="rejectReason" name="rejectReason"
                                              class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors"></textarea>
                                </div>
                                <div class="flex justify-end space-x-4 pt-4">
                                    <button type="button" onclick="closeRejectModal()" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                                        Cancel
                                    </button>
                                    <button type="submit" class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                                        Submit Rejection
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Approvals Table -->
                    <div class="bg-white p-8 rounded-xl shadow-md">
                        <h2 class="text-2xl font-semibold mb-6 text-gray-800">Approvals</h2>
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead class="bg-[#4E3B2A]">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-100 uppercase tracking-wider rounded-tl-lg" ">Approval ID</th>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-100 uppercase tracking-wider" ">Request ID</th>

                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-100 uppercase tracking-wider" ">Amount</th>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-100 uppercase tracking-wider" ">Status</th>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-100 uppercase tracking-wider" ">Date of Approval</th>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-100 uppercase tracking-wider rounded-tr-lg" ">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php
                                      include_once __DIR__ . '/.././Database/connection.php';
                                        $db = new Database();
                                        $conn = $db->connect('fin_disbursement'); 
                                        $query = "SELECT * FROM approvals";
                                        $result = $conn->query($query);

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $statusColor = 'text-gray-800';
                                            if ($row['Status'] == 'Approved') {
                                                $statusColor = 'text-[#86EFAC]';
                                            } elseif ($row['Status'] == 'Rejected') {
                                                $statusColor = 'text-[#FCA5A5]';
                                            } elseif ($row['Status'] == 'Pending') {
                                                $statusColor = 'text-yellow-600';
                                            }

                                            echo "<tr class='hover:bg-gray-50'>
                                                    <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-800'>{$row['ApprovalID']}</td>
                                                    <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-800'>{$row['RequestID']}</td>
                                                   
                                                    <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-800'>₱" . number_format($row['Amount'], 2) . "</td>
                                                    <td class='px-6 py-4 whitespace-nowrap text-sm font-medium {$statusColor}'>{$row['Status']}</td>
                                                    <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-800'>{$row['DateOfApproval']}</td>
                                                    <td class='px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2'>
                                                      <button onclick='openApproveModal({$row['ApprovalID']})' class='bg-[#86EFAC] text-[#1F2937] px-4 py-2 rounded-lg hover:bg-green-600 transition-colors'>Approve</button>
                                                      <button onclick='openRejectModal({$row['ApprovalID']})' class='bg-[#FCA5A5] text-[#1F2937] px-4 py-2 rounded-lg hover:bg-red-600 transition-colors'>Reject</button>
                                                    </td>
                                                  </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='px-6 py-4 text-center text-red-500'>No approvals found.</td></tr>";
                                    }

                                    $conn->close();
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    </div>           
            </main>
        </div>
        
    </div>
    <script src="../assets/js.js"></script>
  <script>
     function openApproveModal(approvalId) {
            document.getElementById('approvalIdInput').value = approvalId;
            document.getElementById('approveModal').classList.remove('hidden');
        }

        function closeApproveModal() {
            document.getElementById('approveModal').classList.add('hidden');
        }

        function openRejectModal(approvalId) {
            document.getElementById('rejectApprovalIdInput').value = approvalId;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        window.onclick = function(event) {
            const modal = document.getElementById('createRequestModal');
            const approveModal = document.getElementById('approveModal');
            const rejectModal = document.getElementById('rejectModal');
            if (event.target == modal) {
                closeModal();
            }
            if (event.target == approveModal) {
                closeApproveModal();
            }
            if (event.target == rejectModal) {
                closeRejectModal();
            }
        }
       
  </script>
</body>
</html>
