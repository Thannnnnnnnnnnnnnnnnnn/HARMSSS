</nav>
<?php include "manager.php" ?>

        <!-- Main Content -->
        <main class="px-8 py-8">
        <?php


// Function to determine the performance category based on the average rating
function getPerformanceCategory($avgRating) {
    if ($avgRating >= 8) {
        return 'Excellent';
    } elseif ($avgRating >= 7) {
        return 'On Track';
    } else {
        return 'Needs Improvement';
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle Add Goal
    if (isset($_POST['add_goal'])) {
        $goal = $_POST['goal'];
        $deadline = $_POST['deadline'];
        $department = $_POST['department'];
        $required_rating = $_POST['required_rating'];

        $kpi_result = $conn->query("SELECT EmployeeID FROM kpis");

        if ($kpi_result->num_rows > 0) {
            while ($kpi_row = $kpi_result->fetch_assoc()) {
                $employee_id = $kpi_row['EmployeeID'];
                $sql = "INSERT INTO goals (Goal, Deadline, Department, EmployeeID, RequiredRating) 
                        VALUES ('$goal', '$deadline', '$department', '$employee_id', '$required_rating')";
                $conn->query($sql);
            }
        }
    }

    // Handle Delete Goal
    if (isset($_POST['delete_goal'])) {
        $delete_goal_id = $_POST['delete_goal_id'];
        $conn->query("DELETE FROM goals WHERE GoalID = '$delete_goal_id'");
    }
}

$result = $conn->query("SELECT * FROM goals");
$kpi_result = $conn->query("SELECT KPIID, EmployeeID, AvgRating, PerformanceCategory, DateCreated FROM kpis");
$kpi_data = [];
while ($kpi_row = $kpi_result->fetch_assoc()) {
    $kpi_data[$kpi_row['EmployeeID']] = [
        'AvgRating' => round($kpi_row['AvgRating'], 2),
        'PerformanceCategory' => $kpi_row['PerformanceCategory'],
        'DateCreated' => $kpi_row['DateCreated']
    ];
}

// Get the overall average rating from the performance reviews table
$overall_avg_rating = 0;
$review_result = $conn->query("SELECT AVG(Rating) AS OverallAvg FROM performancereviews");

if ($review_result && $review_row = $review_result->fetch_assoc()) {
    $overall_avg_rating = round($review_row['OverallAvg'], 2);
}

$overall_performance = getPerformanceCategory($overall_avg_rating);
$overall_progress = ($overall_avg_rating >= 7) ? 'On Track' : 'Needs Improvement';

// Function to calculate goal status based on required rating and overall average rating
function getGoalStatus($required_rating, $overall_avg_rating) {
    return ($required_rating <= $overall_avg_rating) ? 'On Track' : 'Needs Improvement';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Goals and Performance</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex">
    <main class="flex-1 p-5">

        <!-- Overall Employee Performance Computation Section -->
        <h2 class="text-2xl font-bold mb-6">📊 Overall Employee Performance</h2>
        <div class="bg-gradient-to-r from-amber-100 to-yellow-50 p-6 rounded-xl shadow-lg border border-yellow-300 mb-10">
            <p class="text-lg font-semibold text-gray-800 mb-2">⭐ <span class="text-gray-700">Overall Average Rating:</span> 
                <span class="text-yellow-700"><?php echo $overall_avg_rating; ?></span>
            </p>
            <p class="text-lg font-semibold text-gray-800 mb-2">🎯 <span class="text-gray-700">Performance Category:</span> 
                <span class="text-<?php echo ($overall_performance == 'Excellent') ? 'green' : (($overall_performance == 'On Track') ? 'blue' : 'red'); ?>-500">
                    <?php echo $overall_performance; ?>
                </span>
            </p>
            <p class="text-lg font-semibold text-gray-800">📈 <span class="text-gray-700">Overall Progress:</span> 
                <span class="text-<?php echo ($overall_progress == 'On Track') ? 'green' : 'red'; ?>-500">
                    <?php echo $overall_progress; ?>
                </span>
            </p>
        </div>

        <!-- Add Goal Form Section -->
        <h1 class="text-2xl font-bold mb-4">Add Goal</h1>
        <form method="POST" class="mb-4">
            <input type="text" name="goal" placeholder="Goal" required class="border p-2 mb-2 block w-full">
            <input type="date" name="deadline" required class="border p-2 mb-2 block w-full">
            <input type="text" name="department" placeholder="Department" required class="border p-2 mb-2 block w-full">
            <input type="number" name="required_rating" placeholder="Required Rating" required class="border p-2 mb-2 block w-full">
            <button type="submit" name="add_goal" class="p-2 w-full bg-yellow-800 text-white rounded hover:bg-yellow-700">Add Goal</button>
        </form>

        <!-- Goals Table Section -->
        <h2 class="text-xl font-semibold mb-4 mt-8">Goals Overview</h2>
        <div class="bg-white p-4 mt-4 shadow rounded">
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-300 px-4 py-2">Goal</th>
                        <th class="border border-gray-300 px-4 py-2">Department</th>
                        <th class="border border-gray-300 px-4 py-2">Deadline</th>
                        <th class="border border-gray-300 px-4 py-2">Required Rating</th>
                        <th class="border border-gray-300 px-4 py-2">Status</th>
                        <th class="border border-gray-300 px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="border border-gray-300 px-4 py-2"><?php echo $row['Goal']; ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?php echo $row['Department']; ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?php echo $row['Deadline']; ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?php echo $row['RequiredRating']; ?></td>
                        <td class="border border-gray-300 px-4 py-2">
                            <?php
                            $status = getGoalStatus($row['RequiredRating'], $overall_avg_rating);
                            echo "<span class='text-" . (($status == 'On Track') ? 'green' : 'red') . "-500'>" . $status . "</span>";
                            ?>
                        </td>
                        <td class="border border-gray-300 px-4 py-2">
                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this goal?');">
                                <input type="hidden" name="delete_goal_id" value="<?php echo $row['GoalID']; ?>">
                                <button type="submit" name="delete_goal" class="p-1 bg-red-600 text-white rounded hover:bg-red-500">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </main>
</body>
</html>

<?php $conn->close(); ?>





