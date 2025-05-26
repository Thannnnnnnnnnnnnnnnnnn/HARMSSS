
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

                                   
                                    $query = "SELECT d.RequestID, d.EmployeeID, e.FirstName, d.Amount, d.DateOfRequest, d.Status 
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
                                                    <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-800'>{$row['FirstName']}</td>
                                           
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
