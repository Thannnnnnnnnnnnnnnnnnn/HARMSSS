<?php
 include 'staff.php'; 
include '../connection.php'; // database connection

// Count total performance reviews
$result = $conn->query("SELECT COUNT(*) as total_reviews FROM performancereviews");
$total_reviews = 0;
if ($row = $result->fetch_assoc()) {
    $total_reviews = $row['total_reviews'];
}

// Calculate Overall Average Rating
$overall_avg_rating = 0;
$review_result = $conn->query("SELECT AVG(Rating) AS OverallAvg FROM performancereviews");

if ($review_result && $review_row = $review_result->fetch_assoc()) {
    $overall_avg_rating = round($review_row['OverallAvg'], 2);
}

// Function to determine the performance category
function getPerformanceCategory($avgRating) {
    if ($avgRating >= 8) {
        return 'Excellent';
    } elseif ($avgRating >= 7) {
        return 'On Track';
    } else {
        return 'Needs Improvement';
    }
}

$overall_performance = getPerformanceCategory($overall_avg_rating);
$overall_progress = ($overall_avg_rating >= 7) ? 'On Track' : 'Needs Improvement';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    

    <main class="flex-1 p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">📊 Dashboard</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">

            <!-- Total Performance Reviews Card -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold text-gray-700 mb-2">📝 Total Performance Reviews</h2>
                <p class="text-4xl font-bold text-amber-700"><?php echo $total_reviews; ?></p>
            </div>

            <!-- Overall Average Rating Card -->
            <div class="bg-white p-6 rounded-lg shadow-md border border-yellow-300">
                <h2 class="text-xl font-semibold text-gray-700 mb-2">⭐ Overall Average Rating</h2>
                <p class="text-4xl font-bold text-yellow-700"><?php echo $overall_avg_rating; ?></p>
            </div>

            <!-- Performance Category Card -->
            <div class="bg-white p-6 rounded-lg shadow-md border border-yellow-300">
                <h2 class="text-xl font-semibold text-gray-700 mb-2">🎯 Performance Category</h2>
                <p class="text-2xl font-bold text-<?php echo ($overall_performance == 'Excellent') ? 'green' : (($overall_performance == 'On Track') ? 'blue' : 'red'); ?>-500">
                    <?php echo $overall_performance; ?>
                </p>
            </div>

            <!-- Overall Progress Card -->
            <div class="bg-white p-6 rounded-lg shadow-md border border-yellow-300">
                <h2 class="text-xl font-semibold text-gray-700 mb-2">📈 Overall Progress</h2>
                <p class="text-2xl font-bold text-<?php echo ($overall_progress == 'On Track') ? 'green' : 'red'; ?>-500">
                    <?php echo $overall_progress; ?>
                </p>
            </div>

        </div>
    </main>
</body>
</html>

<?php $conn->close(); ?>
