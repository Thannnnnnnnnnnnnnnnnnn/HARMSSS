<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}


// Get total awards count
$total_awards_sql = "SELECT COUNT(*) as total FROM awards";
$total_awards_result = mysqli_query($conn, $total_awards_sql);
$total_awards = mysqli_fetch_assoc($total_awards_result)['total'];

// Get active categories count
$total_categories_sql = "SELECT COUNT(*) as total FROM recognitioncategories";
$total_categories_result = mysqli_query($conn, $total_categories_sql);
$total_categories = mysqli_fetch_assoc($total_categories_result)['total'];

// Get total employee recognitions
$total_recognitions_sql = "SELECT COUNT(*) as total FROM employeerecognition";
$total_recognitions_result = mysqli_query($conn, $total_recognitions_sql);
$total_recognitions = mysqli_fetch_assoc($total_recognitions_result)['total'];

// Calculate recognition rate (percentage of employees who have received recognition)
$recognition_rate_sql = "SELECT 
    (COUNT(DISTINCT er.EmployeeID) * 100.0 / COUNT(DISTINCT e.EmployeeID)) as rate 
    FROM employees e 
    LEFT JOIN employeerecognition er ON e.EmployeeID = er.EmployeeID";
$recognition_rate_result = mysqli_query($conn, $recognition_rate_sql);
$recognition_rate = round(mysqli_fetch_assoc($recognition_rate_result)['rate'], 1);

// Get recent activities (latest 5 recognitions)
$recent_activities_sql = "SELECT er.*, e.`Employee name`, a.AwardName, c.CategoryName 
    FROM employeerecognition er 
    JOIN employees e ON er.EmployeeID = e.EmployeeID 
    JOIN awards a ON er.AwardID = a.AwardID 
    JOIN recognitioncategories c ON a.CategoryID = c.CategoryID 
    ORDER BY er.RecognitionDate DESC LIMIT 5";
$recent_activities_result = mysqli_query($conn, $recent_activities_sql);

// Get top performers (employees with most awards)
$top_performers_sql = "SELECT e.`Employee name`, e.Department, COUNT(er.RecognitionID) as award_count 
    FROM employees e 
    JOIN employeerecognition er ON e.EmployeeID = er.EmployeeID 
    GROUP BY e.EmployeeID 
    ORDER BY award_count DESC LIMIT 5";
$top_performers_result = mysqli_query($conn, $top_performers_sql);

?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-secondary">Social Recognition Dashboard</h1>
    </div>

    <!-- Stats Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

        <!-- Total Awards Card -->
        <div class="bg-white rounded-lg p-6 shadow-sm border border-accent">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-award text-secondary text-xl"></i>
                </div>
                <span class="text-sm text-primary">Total Awards</span>
            </div>
            <h3 class="text-2xl font-bold text-secondary"><?php echo $total_awards; ?></h3>
            <p class="text-sm text-primary mt-2">Available awards in the system</p>
        </div>

        <!-- Active Categories Card -->
        <div class="bg-white rounded-lg p-6 shadow-sm border border-accent">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-tags text-secondary text-xl"></i>
                </div>
                <span class="text-sm text-primary">Active Categories</span>
            </div>
            <h3 class="text-2xl font-bold text-secondary"><?php echo $total_categories; ?></h3>
            <p class="text-sm text-primary mt-2">Recognition categories</p>
        </div>

        <!-- Employee Recognitions Card -->
        <div class="bg-white rounded-lg p-6 shadow-sm border border-accent">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-users text-secondary text-xl"></i>
                </div>
                <span class="text-sm text-primary">Employee Recognitions</span>
            </div>
            <h3 class="text-2xl font-bold text-secondary"><?php echo $total_recognitions; ?></h3>
            <p class="text-sm text-primary mt-2">Total recognitions given</p>
        </div>

        <!-- Recognition Rate Card -->
        <div class="bg-white rounded-lg p-6 shadow-sm border border-accent">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-chart-line text-secondary text-xl"></i>
                </div>
                <span class="text-sm text-primary">Recognition Rate</span>
            </div>
            <h3 class="text-2xl font-bold text-secondary"><?php echo $recognition_rate; ?>%</h3>
            <p class="text-sm text-primary mt-2">Employees recognized</p>
        </div>
    </div>

    <!-- Recent Activity and Top Performers Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg p-6 shadow-sm border border-accent">
            <h2 class="text-xl font-bold text-secondary mb-4">Recent Activity</h2>
            <div class="space-y-4">
                <?php if (mysqli_num_rows($recent_activities_result) > 0) {
                    while ($activity = mysqli_fetch_assoc($recent_activities_result)) {
                        echo '<div class="flex items-center space-x-4 p-3 hover:bg-accent/30 rounded-lg transition-colors">';
                        echo '<div class="w-10 h-10 bg-accent rounded-full flex items-center justify-center">';
                        echo '<i class="fa-solid fa-user text-secondary"></i>';
                        echo '</div>';
                        echo '<div class="flex-1">';
                        echo '<p class="text-sm font-medium text-secondary">' . htmlspecialchars($activity['Employee name'], ENT_QUOTES, 'UTF-8') . ' received the ' . 
                             htmlspecialchars($activity['AwardName'], ENT_QUOTES, 'UTF-8') . ' award</p>';
                        echo '<p class="text-xs text-primary mt-1">' . date('M d, Y', strtotime($activity['RecognitionDate'])) . '</p>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p class="text-sm text-primary">No recent activities</p>';
                }
                ?>
            </div>
        </div>

        <!-- Top Performers -->
        <div class="bg-white rounded-lg p-6 shadow-sm border border-accent">
            <h2 class="text-xl font-bold text-secondary mb-4">Top Performers</h2>
            <div class="space-y-4">
                <?php if (mysqli_num_rows($top_performers_result) > 0) {
                    while ($performer = mysqli_fetch_assoc($top_performers_result)) {
                        echo '<div class="flex items-center justify-between p-3 hover:bg-accent/30 rounded-lg transition-colors">';
                        echo '<div class="flex items-center space-x-4">';
                        echo '<div class="w-10 h-10 bg-accent rounded-full flex items-center justify-center">';
                        echo '<i class="fa-solid fa-user text-secondary"></i>';
                        echo '</div>';
                        echo '<div>';
                        echo '<p class="text-sm font-medium text-secondary">' . htmlspecialchars($performer['Employee name'], ENT_QUOTES, 'UTF-8') . '</p>';
                        echo '<p class="text-xs text-primary">' . htmlspecialchars($performer['Department'], ENT_QUOTES, 'UTF-8') . '</p>';
                        echo '</div>';
                        echo '</div>';
                        echo '<div class="text-right">';
                        echo '<p class="text-sm font-medium text-secondary">' . $performer['award_count'] . ' Awards</p>';
                        echo '<p class="text-xs text-primary">Total</p>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p class="text-sm text-primary">No performers found</p>';
                }
                ?>
            </div>
        </div>
    </div>
</div>
?> 