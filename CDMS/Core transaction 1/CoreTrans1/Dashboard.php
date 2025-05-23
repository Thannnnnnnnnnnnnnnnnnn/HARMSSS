<?php
// THIS MUST BE THE VERY FIRST THING IN THE FILE.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$basePath = '/cr1/'; // Define basePath for includes

// Authentication check
if (!isset($_SESSION['user_id'])) { // Or use $_SESSION['user_name']
    header('Location: ' . rtrim($basePath, '/') . '/login.php'); // Redirect to login
    exit();
}
require_once 'includes/Database.php';
include('includes/head.php');

// Initialize database connections
$db = new Database();

// Connect to cr1_restaurant_analytics
$connAnalytics = $db->connect('analytics');
if (!$connAnalytics) {
    error_log("Analytics database connection failed: " . $db->getError());
    die("Analytics database connection failed. Please try again later.");
}

// Connect to cr1_order_management_with_pos
$connOrders = $db->connect('orders');
if (!$connOrders) {
    error_log("Orders database connection failed: " . $db->getError());
    die("Orders database connection failed. Please try again later.");
}

// Connect to cr1_kitchen_bar_module
$connKitchen = $db->connect('kitchen');
if (!$connKitchen) {
    error_log("Kitchen database connection failed: " . $db->getError());
    die("Kitchen database connection failed. Please try again later.");
}

// Fetch data for Info Cards
// Sales Report: Percentage change in TotalSales
$sales_query = "SELECT TotalSales FROM salesreports ORDER BY ReportDate DESC LIMIT 2";
$sales_result = $connAnalytics->query($sales_query);
$sales_percent = 'N/A';
if ($sales_result && $sales_result->num_rows >= 2) {
    $sales_data = $sales_result->fetch_all(MYSQLI_ASSOC);
    $current_sales = $sales_data[0]['TotalSales'];
    $previous_sales = $sales_data[1]['TotalSales'];
    if ($previous_sales > 0) {
        $sales_percent = number_format((($current_sales - $previous_sales) / $previous_sales) * 100, 2) . '%';
    }
} else {
    $sales_percent = '1.32%'; // Fallback
}

// Sales Report: Today, Monthly, Last Year Sales
$today_sales_query = "SELECT SUM(SubTotal) as sales FROM orderitems WHERE OrderDate = CURDATE()";
$today_sales_result = $connOrders->query($today_sales_query);
$today_sales = $today_sales_result && $today_sales_result->num_rows > 0 ? number_format($today_sales_result->fetch_assoc()['sales'], 2) : '15.00';

$monthly_sales_query = "SELECT SUM(SubTotal) as sales FROM orderitems WHERE OrderDate BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND CURDATE()";
$monthly_sales_result = $connOrders->query($monthly_sales_query);
$monthly_sales = $monthly_sales_result && $monthly_sales_result->num_rows > 0 ? number_format($monthly_sales_result->fetch_assoc()['sales'], 2) : '45.00';

$last_year_sales_query = "SELECT SUM(SubTotal) as sales FROM orderitems WHERE OrderDate BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 YEAR) AND CURDATE()";
$last_year_sales_result = $connOrders->query($last_year_sales_query);
$last_year_sales = $last_year_sales_result && $last_year_sales_result->num_rows > 0 ? number_format($last_year_sales_result->fetch_assoc()['sales'], 2) : '90.00';

// Customer Preference: Count unique preferences
$pref_query = "SELECT COUNT(DISTINCT PreferenceDetails) as count FROM customerpreferences";
$pref_result = $connAnalytics->query($pref_query);
$pref_count = $pref_result ? $pref_result->fetch_assoc()['count'] : 0;

// Fetch data for Charts
// Line Chart: Monthly TotalSales
$line_query = "SELECT DATE_FORMAT(ReportDate, '%M') as month, SUM(TotalSales) as sales 
               FROM salesreports 
               GROUP BY YEAR(ReportDate), MONTH(ReportDate) 
               ORDER BY ReportDate ASC LIMIT 6";
$line_result = $connAnalytics->query($line_query);
$line_labels = [];
$line_sales = [];
if ($line_result && $line_result->num_rows > 0) {
    while ($row = $line_result->fetch_assoc()) {
        $line_labels[] = $row['month'];
        $line_sales[] = (int)$row['sales'];
    }
} else {
    $line_labels = ['January', 'February', 'March', 'April', 'May', 'June'];
    $line_sales = [65, 59, 80, 81, 56, 55];
}

// Bar Chart: Sales for Today, Last Month, Last Year
$bar_data = [0, 0, 0];
$bar_data[0] = floatval($today_sales);
$bar_data[1] = floatval($monthly_sales);
$bar_data[2] = floatval($last_year_sales);
if ($bar_data == [0, 0, 0]) {
    $bar_data = [15, 45, 90]; // Fallback
}

// Fetch Top-Selling Dishes (5 items)
$best_seller_query = "SELECT oi.MenuName, SUM(oi.Quantity) as total_quantity, m.ImagePath 
                     FROM orderitems oi
                     JOIN cr1_kitchen_bar_module.menuitems m ON oi.MenuItemID = m.MenuItemID
                     GROUP BY oi.MenuName, m.ImagePath 
                     ORDER BY total_quantity DESC LIMIT 5";
$best_seller_result = $connOrders->query($best_seller_query);
$best_sellers = [];
$default_ratings = [5, 4, 4, 3, 3];
if ($best_seller_result && $best_seller_result->num_rows > 0) {
    $best_sellers = $best_seller_result->fetch_all(MYSQLI_ASSOC);
} else {
    $best_seller_fallback_query = "SELECT p.MenuName, p.PopularityScore as total_quantity, m.ImagePath 
                                  FROM populardishes p
                                  LEFT JOIN cr1_kitchen_bar_module.menuitems m ON p.MenuName = m.ItemName
                                  ORDER BY p.PopularityScore DESC LIMIT 5";
    $best_seller_fallback_result = $connAnalytics->query($best_seller_fallback_query);
    if ($best_seller_fallback_result && $best_seller_fallback_result->num_rows > 0) {
        $best_sellers = $best_seller_fallback_result->fetch_all(MYSQLI_ASSOC);
    } else {
        $best_sellers = [
            ['MenuName' => 'Adobong Manok', 'total_quantity' => 35, 'ImagePath' => 'image/adobo.jpg'],
            ['MenuName' => 'Sinigang', 'total_quantity' => 25, 'ImagePath' => 'image/sinigang.jpg'],
            ['MenuName' => 'Chicken Curry', 'total_quantity' => 40, 'ImagePath' => 'image/chicken-curry.jpg'],
            ['MenuName' => 'Pork Sisig', 'total_quantity' => 30, 'ImagePath' => 'image/pork-sisig.jpg'],
            ['MenuName' => 'Kare-Kare', 'total_quantity' => 20, 'ImagePath' => 'image/kare-kare.jpg']
        ];
    }
}

// Ensure top seller is first
usort($best_sellers, function($a, $b) {
    return $b['total_quantity'] - $a['total_quantity'];
});

// Fetch Sales Data for Cards and Chart
$modal_sales_data = [];
foreach ($best_sellers as $index => $dish) {
    $menu_name = $dish['MenuName'];
    $modal_data = ['today' => 0, 'last_month' => 0, 'last_year' => 0];

    // Today
    $stmt = $connOrders->prepare("SELECT SUM(Quantity) as sales FROM orderitems WHERE MenuName = ? AND OrderDate = CURDATE()");
    $stmt->bind_param("s", $menu_name);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $modal_data['today'] = (int)$result->fetch_assoc()['sales'];
    } else {
        $modal_data['today'] = [10, 5, 8, 7, 6][$index] ?? 0;
    }
    $stmt->close();

    // Last Month
    $stmt = $connOrders->prepare("SELECT SUM(Quantity) as sales FROM orderitems 
                                  WHERE MenuName = ? AND OrderDate BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND CURDATE()");
    $stmt->bind_param("s", $menu_name);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $modal_data['last_month'] = (int)$result->fetch_assoc()['sales'];
    } else {
        $modal_data['last_month'] = [150, 120, 130, 110, 100][$index] ?? 0;
    }
    $stmt->close();

    // Last Year
    $stmt = $connOrders->prepare("SELECT SUM(Quantity) as sales FROM orderitems 
                                  WHERE MenuName = ? AND OrderDate BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 YEAR) AND CURDATE()");
    $stmt->bind_param("s", $menu_name);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $modal_data['last_year'] = (int)$result->fetch_assoc()['sales'];
    } else {
        $modal_data['last_year'] = [2000, 1800, 1900, 1700, 1600][$index] ?? 0;
    }
    $stmt->close();

    $modal_sales_data[$menu_name] = $modal_data;
}

$connAnalytics->close();
$connOrders->close();
$connKitchen->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: Georgia, serif;
            overflow-x: hidden;
            box-sizing: border-box;
        }
        h1, h2, h3, h4, h5, h6, .font-header {
            font-family: 'Cinzel', Georgia, serif;
            font-weight: 700;
        }
        .stars {
            display: flex;
            justify-content: center;
            gap: 5px;
        }
        .star {
            font-size: 16px;
            color: #FFD700;
            cursor: pointer;
        }
        .star:hover {
            color: #FF8C00;
        }
        .image-container {
            position: relative;
        }
        .image-container img {
            transition: transform 0.3s ease;
        }
        .image-container:hover img {
            transform: scale(1.1);
        }
        .image-overlay::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50%;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.5), transparent);
        }
        .best-seller-tag {
            position: absolute;
            top: 0;
            left: 0;
            background-color: #D97706;
            color: white;
            padding: 4px 8px;
            border-radius: 0 0 5px 0;
            font-size: 10px;
            font-weight: bold;
            z-index: 1;
        }
        .best-seller-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .best-seller-card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.1);
        }
        .card-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }
        .chart-container {
            max-height: 180px;
            width: 100%;
        }
        .main {
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        @media (max-width: 640px) {
            .chart-container {
                max-height: 150px;
            }
            .star {
                font-size: 14px;
            }
            .best-seller-tag {
                font-size: 8px;
                padding: 3px 6px;
            }
            .card-content p, .card-content h4 {
                font-size: 0.875rem;
            }
            .main {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }
        @media (min-width: 768px) {
            .main {
                margin-left: 320px;
                width: calc(100% - 320px);
            }
            .sidebar-collapsed ~ .main {
                margin-left: 80px;
                width: calc(100% - 80px);
            }
        }
    </style>
</head>

<body class="flex min-h-screen w-full bg-[#FFF6E8] font-georgia text-[#594423]">
    <div class="flex min-h-screen w-full">
        <?php include('includes/sidebar.php'); ?>
        <div class="main">
            <?php include('includes/navbar.php'); ?>
            <main class="px-4 sm:px-6 lg:px-8 py-6">
                <!-- Info Cards Container -->
                <div class="bg-white p-4 sm:p-6 rounded-lg shadow-md border-2 border-[#F7E6CA] mb-6">
                    <h2 class="text-xl sm:text-2xl text-[#4E3B2A] font-header mb-4 sm:mb-6 text-center">Dashboard Overview</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Sales Report Card -->
                        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-md text-center border-2 border-[#F7E6CA]">
                            <h3 class="text-lg sm:text-xl text-[#4E3B2A] font-header mb-2">Sales Report</h3>
                            <p class="text-2xl sm:text-3xl font-bold text-[#594423] mb-2"><?php echo htmlspecialchars($sales_percent); ?></p>
                            <p class="text-xs sm:text-sm text-[#594423] mb-1">Today: ₱<?php echo htmlspecialchars($today_sales); ?></p>
                            <p class="text-xs sm:text-sm text-[#594423] mb-1">Monthly: ₱<?php echo htmlspecialchars($monthly_sales); ?></p>
                            <p class="text-xs sm:text-sm text-[#594423] mb-4">Last Year: ₱<?php echo htmlspecialchars($last_year_sales); ?></p>
                            <div class="chart-container">
                                <canvas id="lineChart"></canvas>
                            </div>
                        </div>
                        <!-- Customer Preference Card -->
                        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-md text-center border-2 border-[#F7E6CA]">
                            <h3 class="text-lg sm:text-xl text-[#4E3B2A] font-header mb-2">Customer Preference</h3>
                            <p class="text-2xl sm:text-3xl font-bold text-[#594423] mb-4"><?php echo htmlspecialchars($pref_count); ?></p>
                            <div class="chart-container">
                                <canvas id="barChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Best-Seller Food Section -->
                <div class="bg-white p-4 sm:p-6 rounded-lg shadow-md border-2 border-[#F7E6CA]">
                    <h2 class="text-xl sm:text-2xl text-[#4E3B2A] font-header mb-4 sm:mb-6 text-center">Best-Seller Food</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-6">
                        <?php foreach ($best_sellers as $index => $dish): ?>
                            <div class="bg-white p-3 sm:p-4 rounded-lg shadow-md border-2 border-[#F7E6CA] text-center relative best-seller-card">
                                <span class="best-seller-tag">Top <?php echo $index + 1; ?> Best Seller</span>
                                <div class="image-container image-overlay">
                                    <img src="<?php echo htmlspecialchars($dish['ImagePath'] ?? 'image/placeholder.jpg'); ?>" 
                                         alt="<?php echo htmlspecialchars($dish['MenuName']); ?>" 
                                         class="rounded-lg h-24 sm:h-32 object-contain w-auto max-w-full mb-2 sm:mb-3 mx-auto"
                                         onerror="this.src='image/placeholder.jpg'">
                                </div>
                                <div class="card-content">
                                    <h4 class="text-sm sm:text-base text-[#4E3B2A] font-header"><?php echo htmlspecialchars($dish['MenuName']); ?></h4>
                                    <p class="text-xs sm:text-sm text-[#594423]">Sold: <?php echo htmlspecialchars($dish['total_quantity']); ?> units</p>
                                    <div class="stars">
                                        <?php 
                                        $rating = $default_ratings[$index] ?? 3;
                                        for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="star"><?php echo $i <= $rating ? '★' : '☆'; ?></span>
                                        <?php endfor; ?>
                                    </div>
                                    <p class="text-xs sm:text-sm text-[#594423]">Today: <?php echo htmlspecialchars($modal_sales_data[$dish['MenuName']]['today']); ?> units</p>
                                    <p class="text-xs sm:text-sm text-[#594423]">Last Month: <?php echo htmlspecialchars($modal_sales_data[$dish['MenuName']]['last_month']); ?> units</p>
                                    <p class="text-xs sm:text-sm text-[#594423]">Last Year: <?php echo htmlspecialchars($modal_sales_data[$dish['MenuName']]['last_year']); ?> units</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Best-Seller Sales Chart -->
                    <div class="mt-6 bg-white p-4 sm:p-6 rounded-lg shadow-md border-2 border-[#F7E6CA]">
                        <h3 class="text-lg sm:text-xl text-[#4E3B2A] font-header text-center mb-4">Best-Seller Sales Analysis</h3>
                        <div class="chart-container">
                            <canvas id="bestSellerSalesChart"></canvas>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main');

            if (sidebarToggle && sidebar && mainContent) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    if (sidebar.classList.contains('collapsed')) {
                        if (window.innerWidth >= 768) {
                            mainContent.style.marginLeft = '80px';
                            mainContent.style.width = 'calc(100% - 80px)';
                        } else {
                            mainContent.style.marginLeft = '0';
                            mainContent.style.width = '100%';
                        }
                    } else {
                        if (window.innerWidth >= 768) {
                            mainContent.style.marginLeft = '320px';
                            mainContent.style.width = 'calc(100% - 320px)';
                        } else {
                            mainContent.style.marginLeft = '0';
                            mainContent.style.width = '100%';
                        }
                    }
                });

                // Adjust on window resize
                window.addEventListener('resize', function() {
                    if (window.innerWidth < 768) {
                        mainContent.style.marginLeft = '0';
                        mainContent.style.width = '100%';
                    } else {
                        if (sidebar.classList.contains('collapsed')) {
                            mainContent.style.marginLeft = '80px';
                            mainContent.style.width = 'calc(100% - 80px)';
                        } else {
                            mainContent.style.marginLeft = '320px';
                            mainContent.style.width = 'calc(100% - 320px)';
                        }
                    }
                });

                // Initial adjustment
                if (window.innerWidth < 768) {
                    mainContent.style.marginLeft = '0';
                    mainContent.style.width = '100%';
                } else if (sidebar.classList.contains('collapsed')) {
                    mainContent.style.marginLeft = '80px';
                    mainContent.style.width = 'calc(100% - 80px)';
                }
            }
        });

        // Line Chart (Sales Report)
        const lineData = {
            labels: <?php echo json_encode($line_labels); ?>,
            datasets: [{
                label: 'Sales Over Time',
                data: <?php echo json_encode($line_sales); ?>,
                fill: false,
                borderColor: '#594423',
                backgroundColor: '#F7E6CA',
                tension: 0.1
            }]
        };

        const lineConfig = {
            type: 'line',
            data: lineData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { color: '#4E3B2A', font: { size: 12 } } },
                    title: { display: true, text: 'Monthly Sales', color: '#4E3B2A', font: { size: 12 } }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { color: '#4E3B2A', font: { size: 10 } } },
                    x: { ticks: { color: '#4E3B2A', font: { size: 10 } } }
                }
            }
        };

        const lineChart = new Chart(document.getElementById('lineChart'), lineConfig);

        // Bar Chart (Customer Preference)
        const barData = {
            labels: ['Today', 'Last Month', 'Last Year'],
            datasets: [{
                label: 'Sales Comparison',
                data: <?php echo json_encode($bar_data); ?>,
                backgroundColor: '#F7E6CA',
                borderColor: '#594423',
                borderWidth: 1
            }]
        };

        const barConfig = {
            type: 'bar',
            data: barData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { color: '#4E3B2A', font: { size: 12 } } },
                    title: { display: true, text: 'Sales Comparison', color: '#4E3B2A', font: { size: 12 } }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { color: '#4E3B2A', font: { size: 10 } } },
                    x: { ticks: { color: '#4E3B2A', font: { size: 10 } } }
                }
            }
        };

        const barChart = new Chart(document.getElementById('barChart'), barConfig);

        // Best-Seller Sales Chart
        const bestSellerSalesData = {
            labels: <?php echo json_encode(array_column($best_sellers, 'MenuName')); ?>,
            datasets: [
                {
                    label: 'Today',
                    data: <?php echo json_encode(array_values(array_map(function($dish) use ($modal_sales_data) { return $modal_sales_data[$dish['MenuName']]['today']; }, $best_sellers))); ?>,
                    backgroundColor: '#594423'
                },
                {
                    label: 'Last Month',
                    data: <?php echo json_encode(array_values(array_map(function($dish) use ($modal_sales_data) { return $modal_sales_data[$dish['MenuName']]['last_month']; }, $best_sellers))); ?>,
                    backgroundColor: '#F7E6CA'
                },
                {
                    label: 'Last Year',
                    data: <?php echo json_encode(array_values(array_map(function($dish) use ($modal_sales_data) { return $modal_sales_data[$dish['MenuName']]['last_year']; }, $best_sellers))); ?>,
                    backgroundColor: '#FFF1E8'
                }
            ]
        };

        const bestSellerSalesConfig = {
            type: 'bar',
            data: bestSellerSalesData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { color: '#4E3B2A', font: { size: 12 } } },
                    title: { display: true, text: 'Best-Seller Sales Analysis', color: '#4E3B2A', font: { size: 14 } }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { color: '#4E3B2A', font: { size: 10 } } },
                    x: { ticks: { color: '#4E3B2A', font: { size: 10 } } }
                }
            }
        };

        const bestSellerSalesChart = new Chart(document.getElementById('bestSellerSalesChart'), bestSellerSalesConfig);
    </script>
    <script src="assets/scripts.js"></script>
</body>
</html>