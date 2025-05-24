
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
              <header class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">Disbursement</h1>
                    </header>

                    <!-- Button to open the modal -->
                    <div class="text-left mb-8">
                        <button onclick="openModal()" class="bg-[#4E3B2A] text-white px-6 py-3 rounded-lg hover:bg-[#4E3B2A] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors shadow-md">
                            Create New Request
                        </button>
                    </div>

                    <!-- Create Request Modal -->
                    <div id="createRequestModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                        <div class="bg-white p-8 rounded-xl shadow-xl w-full max-w-md">
                            <h2 class="text-2xl font-semibold mb-6 text-gray-800">Create Disbursement Request</h2>
                            <form action="backend/submit_request.php" method="POST" class="space-y-6">
                                <!-- Employee ID Field -->
                                <div>
                                    <label for="employeeId" class="block text-sm font-medium text-gray-700 mb-1">Employee ID</label>
                                    <input type="number" id="employeeId" name="employeeId" required
                                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                           oninput="fetchEmployeeName(this.value)">
                                </div>
                                <!-- Employee Name Field -->
                                <div>
                                    <label for="employeeName" class="block text-sm font-medium text-gray-700 mb-1">Employee Name</label>
                                    <input type="text" id="employeeName" name="employeeName" readonly
                                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm bg-gray-100 cursor-not-allowed">
                                </div>

                                <div>
                                    <label for="employeeName" class="block text-sm font-medium text-gray-700 mb-1">Types</label>
                                    <input type="text" id="type" name="type" readonly
                                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm bg-gray-100 cursor-not-allowed">
                                </div>
                                <!-- Budget ID Field -->
                                <div>
                                    <label for="budgetId" class="block text-sm font-medium text-gray-700 mb-1">Allocation ID</label>
                                    <input type="number" id="budgetId" name="budgetId" required
                                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>
                                <!-- Amount Field -->
                                <div>
                                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                                    <input type="number" id="amount" name="amount" step="0.01" required
                                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>
                                <!-- Buttons -->
                                <div class="flex justify-end space-x-4 pt-4">
                                    <button type="button" onclick="closeModal()" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                                        Cancel
                                    </button>
                                    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                        Submit
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Disbursement Requests Table -->
                    <div class="bg-white p-8 rounded-xl shadow-md mb-12">
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                               <thead class="bg-[#4E3B2A]">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-100 uppercase tracking-wider rounded-tl-lg" >Request ID</th>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-100 uppercase tracking-wider" >Employee ID</th>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-100 uppercase tracking-wider" >Employee Name</th>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-100 uppercase tracking-wider" >Allocation ID</th>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-100 uppercase tracking-wider" >Amount</th>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-100 uppercase tracking-wider" >Request Date</th>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-100 uppercase tracking-wider rounded-tr-lg" >Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php
                                    // Database connection
                                    $host = '127.0.0.1';
                                    $db = 'fin_disbursement';
                                    $user = '3206_CENTRALIZED_DATABASE';
                                    $pass = '4562526';
                                    $conn = new mysqli($host, $user, $pass, $db);
                                    if ($conn->connect_error) {
                                        die("Connection failed: " . $conn->connect_error);
                                    }

                                   
                                    $query = "SELECT d.RequestID, d.EmployeeID, e.EmployeeName, d.AllocationID, d.Amount, d.DateOfRequest, d.Status 
                                              FROM disbursementrequests d 
                                              JOIN employees e ON d.EmployeeID = e.EmployeeID 
                                              ORDER BY d.RequestID ASC";
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
                                                    <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-800'>{$row['RequestID']}</td>
                                                    <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-800'>{$row['EmployeeID']}</td>
                                                    <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-800'>{$row['EmployeeName']}</td>
                                                    <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-800'>{$row['AllocationID']}</td>
                                                    <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-800'>₱" . number_format($row['Amount'], 2) . "</td>
                                                    <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-800'>{$row['DateOfRequest']}</td>
                                                    <td class='px-6 py-4 whitespace-nowrap text-sm font-medium {$statusColor}'>{$row['Status']}</td>
                                                  </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='7' class='px-6 py-4 text-center text-red-500'>No records found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>           
            </main>
        </div>
        
    </div>
    <script src="../assets/js.js"></script>
  <script>
   function openModal() {
            document.getElementById('createRequestModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('createRequestModal').classList.add('hidden');
        }

        function fetchEmployeeName(employeeId) {
            if (employeeId) {
                fetch(`backend/getEmployeeName.php?employeeId=${employeeId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('employeeName').value = data.employeeName;
                            document.getElementById('type').value = data.type;
                        } else {
                            document.getElementById('employeeName').value = ''; 
                            alert('Employee not found!');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching employee name:', error);
                    });
            } else {
                document.getElementById('employeeName').value = ''; 
            }
        }
       
  </script>
</body>
</html>
