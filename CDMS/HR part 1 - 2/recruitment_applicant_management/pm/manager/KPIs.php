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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee KPI Status</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans flex">
    <main class="flex-1 p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Employee KPI Status</h1>

        <div class="overflow-x-auto bg-white rounded-lg shadow-md p-4">
            <table class="w-full text-left border-collapse">
                <thead class="bg-yellow-100 text-yellow-800">
                    <tr>
                        <th class="border px-4 py-3">📄 Employee ID</th>
                        <th class="border px-4 py-3">📊 Total Reviews</th>
                        <th class="border px-4 py-3">⭐ Average Rating</th>
                        <th class="border px-4 py-3">🏅 KPI Status</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php while ($row = $result->fetch_assoc()): 
                        $avg = round($row['AverageRating'], 2);
                        $status = "";
                        if ($avg >= 8) {
                            $status = "Excellent";
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
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>

<?php $conn->close(); ?>






         
       
