</nav>
<?php include "manager.php" ?>

        <!-- Main Content -->
        <main class="px-8 py-8">
        <?php

// Get average rating per employee
$sql = "
    SELECT 
        EmployeeID, 
        COUNT(*) AS TotalReviews, 
        AVG(Rating) AS AverageRating 
    FROM performancereviews 
    GROUP BY EmployeeID
";
$result = $conn->query($sql);

// Handle form submission for appraisal action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['employeeID'])) {
    $employeeID = $_POST['employeeID'];
    $actionDescription = $_POST['actionDescription'];
    $appraisalStatus = 'Pending'; // Automatically set status to Pending

    // Insert the appraisal action into the database
    $conn->query("INSERT INTO appraisals (EmployeeID, ActionDescription, Status) VALUES ('$employeeID', '$actionDescription', '$appraisalStatus')");
}

// Handle form submission for deleting an appraisal action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteAppraisal']) && isset($_POST['appraisalID'])) {
    $appraisalID = $_POST['appraisalID'];

    // Delete the appraisal action from the database
    $conn->query("DELETE FROM appraisals WHERE AppraisalID = '$appraisalID'");
}

// Fetch all appraisal actions for display
$appraisalActions = $conn->query("SELECT * FROM appraisals ORDER BY CreatedAt DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appraisals</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <main class="max-w-7xl mx-auto p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Employee KPI Status</h1>

        <!-- Employee KPI Status Table -->
        <div class="overflow-x-auto bg-white rounded-lg shadow-md p-4 mb-8">
            <table class="w-full text-left border-collapse">
                <thead class="bg-yellow-100 text-yellow-800">
                    <tr>
                        <th class="border px-4 py-3">📄 Employee ID</th>
                        <th class="border px-4 py-3">📊 Total Reviews</th>
                        <th class="border px-4 py-3">⭐ Average Rating</th>
                        <th class="border px-4 py-3">🏅 KPI Status</th>
                        <th class="border px-4 py-3">💼 Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php while ($row = $result->fetch_assoc()): 
                        $avg = round($row['AverageRating'], 2);
                        $status = "";
                        $action_button = "";
                        
                        if ($avg >= 8) {
                            $status = "Excellent";
                            $action_button = "<button class='bg-green-500 text-white px-4 py-2 rounded-md mt-2' onclick='showActionSection(\"{$row['EmployeeID']}\")'>Request Appraisal</button>";
                        } elseif ($avg >= 5) {
                            $status = "Good";
                        } else {
                            $status = "Needs Improvement";
                        }
                    ?>
                        <tr class="hover:bg-yellow-50 transition">
                            <td class="border px-4 py-2"><?php echo $row['EmployeeID']; ?></td>
                            <td class="border px-4 py-2"><?php echo $row['TotalReviews']; ?></td>
                            <td class="border px-4 py-2"><?php echo $avg; ?></td>
                            <td class="border px-4 py-2 font-semibold 
                                <?php 
                                    echo ($status == "Excellent") ? 'text-green-600' : 
                                         (($status == "Good") ? 'text-yellow-600' : 'text-red-600'); 
                                ?>">
                                <?php echo $status; ?>
                            </td>
                            <td class="border px-4 py-2"><?php echo $action_button; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Action section (hidden by default) -->
        <div id="actionSection" class="mt-4 hidden bg-yellow-50 p-4 rounded-lg shadow-md">
            <h3 class="font-semibold text-xl text-yellow-800">Add Appraisal Action</h3>
            <form action="appraisals.php" method="POST">
                <div class="mb-4">
                    <label for="actionDescription" class="block text-gray-700">Action Description</label>
                    <textarea id="actionDescription" name="actionDescription" rows="4" class="w-full p-2 border border-gray-300 rounded-md"></textarea>
                </div>
                <div class="flex items-center">
                    <input type="hidden" name="employeeID" id="employeeID">
                    <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-md">Submit Action</button>
                </div>
            </form>
        </div>

        <!-- Display submitted appraisal actions -->
        <div class="mt-8 bg-white p-4 rounded-lg shadow-md">
            <h3 class="font-semibold text-xl text-gray-800">Submitted Appraisal Actions</h3>
            <table class="w-full mt-4 text-left border-collapse">
                <thead class="bg-gray-200 text-gray-600">
                    <tr>
                        <th class="border px-4 py-3">Employee ID</th>
                        <th class="border px-4 py-3">Action Description</th>
                        <th class="border px-4 py-3">Status</th>
                        <th class="border px-4 py-3">Date Submitted</th>
                        <th class="border px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php while ($action = $appraisalActions->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="border px-4 py-2"><?php echo $action['EmployeeID']; ?></td>
                            <td class="border px-4 py-2"><?php echo $action['ActionDescription']; ?></td>
                            <td class="border px-4 py-2"><?php echo $action['Status']; ?></td>
                            <td class="border px-4 py-2"><?php echo $action['CreatedAt']; ?></td>
                            <td class="border px-4 py-2">
                                <form action="appraisals.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="appraisalID" value="<?php echo $action['AppraisalID']; ?>">
                                    <button type="submit" name="deleteAppraisal" class="bg-red-500 text-white px-4 py-2 rounded-md">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        // Show action section when "Request Appraisal" button is clicked
        function showActionSection(employeeID) {
            document.getElementById('actionSection').classList.remove('hidden');
            document.getElementById('employeeID').value = employeeID; // Set employee ID in the hidden input
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>


