</nav>
<?php include "staff.php" ?>

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
    <?php if ($_SESSION['Role'] !== 'manager' && $_SESSION['Role'] !== 'admin'): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded relative">
            <strong class="font-bold">Unauthorized Access:</strong>
            <span class="block sm:inline">You are not authorized to view or request appraisals.</span>
        </div>
    <?php else: ?>
        <!-- Original appraisal content starts here -->
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Employee KPI Status</h1>

        <!-- (Insert your full KPI table, action form, and appraisal display here — all the HTML you already have) -->

    <?php endif; ?>
</main>

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


