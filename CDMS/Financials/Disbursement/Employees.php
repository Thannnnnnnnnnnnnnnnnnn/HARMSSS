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
                    <div class="bg-white p-8 rounded-xl shadow-md">
                        <h2 class="text-2xl font-semibold mb-6 text-gray-800">Employee List</h2>
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead class="bg-[#4E3B2A]">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-100 uppercase tracking-wider rounded-tl-lg" >Employee ID</th>
                                        <th class="px-6 py-4 text-left text-sm font-bold text-gray-100 uppercase tracking-wider" >Employee Name</th>
                                         <th class="px-6 py-4 text-left text-sm font-bold text-gray-100 uppercase tracking-wider" >Types</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php
                                        $host = '127.0.0.1';
                                    $db = 'fin_disbursement';
                                    $user = '3206_CENTRALIZED_DATABASE';
                                    $pass = '4562526';
                                    // Create connection
                                    $conn = new mysqli($host, $user, $pass, $db);
                                    $query = "SELECT * FROM employees";
                                    $result = $conn->query($query);

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr class='hover:bg-gray-50'>
                                                    <td class='px-6 py-4 whitespace-nowrap text-md text-gray-800'>{$row['EmployeeID']}</td>
                                                    <td class='px-6 py-4 whitespace-nowrap text-md text-gray-800'>{$row['EmployeeName']}</td>
                                                     <td class='px-6 py-4 whitespace-nowrap text-md text-gray-800'>{$row['Types']}</td>
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

</body>
</html>
