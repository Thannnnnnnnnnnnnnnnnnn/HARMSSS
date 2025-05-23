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
include('../../includes/head.php'); 
require_once __DIR__ . "/../../includes/Database.php";

// Connect to orders database
$dbOrders = new Database();
$connOrders = $dbOrders->connect("orders");

if (!$connOrders) {
    die("❌ Database connection failed.");
}

// Pagination setup
$limit = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

// Fetch paginated payments
$stmt = $connOrders->prepare("SELECT p.TransactionID, p.OrderID, p.Amount AS PaymentAmount, p.TransactionDate, o.CustomerName 
                              FROM payment_transactions p
                              JOIN orders o ON p.OrderID = o.Order_ID
                              LIMIT ?, ?");
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$resultPayments = $stmt->get_result();

// Fetch total payment count
$resultTotal = $connOrders->query("SELECT COUNT(*) AS total FROM payment_transactions");
$totalPayments = $resultTotal->fetch_assoc()['total'] ?? 0;
$totalPages = max(1, ceil($totalPayments / $limit));
?>

<body>
<div class="flex min-h-screen w-full bg-[#FFF6E8] font-[Georgia] text-[#4E3B2A]">
    <?php include('../../includes/sidebar.php'); ?>

    <div class="main w-full md:ml-[320px]">
        <?php include('../../includes/navbar.php'); ?>

        <main class="px-8 py-8">
            <h1 class="text-2xl font-[Cinzel] font-bold text-[#594423] mb-10">Billing Payment Transactions Record</h1>

            <div class="overflow-x-auto bg-594423 rounded-2xl shadow-md border-2 border-[#594423]">
                <table id="paymentsTable" class="min-w-full">
                    <thead class="bg-[#4E3B2A] text-white">
                        <tr>
                            <th class="px-6 py-4 border-b border-[#594423] text-left cursor-pointer" onclick="sortTable(0)">Transaction ID</th>
                            <th class="px-6 py-4 border-b border-[#594423] text-left cursor-pointer" onclick="sortTable(1)">Order ID</th>
                            <th class="px-6 py-4 border-b border-[#594423] text-left cursor-pointer" onclick="sortTable(2)">Customer Name</th>
                            <th class="px-6 py-4 border-b border-[#594423] text-left cursor-pointer" onclick="sortTable(3)"> Amount</th>
                            <th class="px-6 py-4 border-b border-[#594423] text-left cursor-pointer" onclick="sortTable(4)">Transaction Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $resultPayments->fetch_assoc()): ?>
                            <tr class="hover:bg-[#FFF6E8] transition">
                                <td class="px-6 py-4 border-b border-[#594423]"><?php echo htmlspecialchars($row['TransactionID']); ?></td>
                                <td class="px-6 py-4 border-b border-[#594423]"><?php echo htmlspecialchars($row['OrderID']); ?></td>
                                <td class="px-6 py-4 border-b border-[#594423]"><?php echo htmlspecialchars($row['CustomerName']); ?></td>
                                <td class="px-6 py-4 border-b border-[#594423]"><?php echo htmlspecialchars(number_format($row['PaymentAmount'], 2)); ?></td>
                                <td class="px-6 py-4 border-b border-[#594423]"><?php echo htmlspecialchars($row['TransactionDate']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <div class="flex justify-between items-center px-6 py-4 border-t-2 border-[#594423]">
                    <a href="?page=<?php echo max(1, $page - 1); ?>"
                        class="bg-[#F7E6CA] hover:bg-[#594423] text-[#594423] hover:text-[#F7E6CA] font-semibold px-6 py-3 rounded-lg border-2 border-[#594423] transition">
                        Previous
                    </a>
                    <a href="?page=<?php echo min($totalPages, $page + 1); ?>"
                        class="bg-[#F7E6CA] hover:bg-[#594423] text-[#594423] hover:text-[#F7E6CA] font-semibold px-6 py-3 rounded-lg border-2 border-[#594423] transition">
                        Next
                    </a>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="../../assets/scripts.js"></script>

<script>
    function sortTable(columnIndex) {
        const table = document.getElementById("paymentsTable");
        let switching = true;
        let dir = "asc";
        let rows, shouldSwitch, x, y, i;

        while (switching) {
            switching = false;
            rows = table.rows;
            for (i = 1; i < rows.length - 1; i++) {
                shouldSwitch = false;
                x = rows[i].getElementsByTagName("TD")[columnIndex];
                y = rows[i + 1].getElementsByTagName("TD")[columnIndex];

                if (dir === "asc" ? (x.innerText.toLowerCase() > y.innerText.toLowerCase()) : (x.innerText.toLowerCase() < y.innerText.toLowerCase())) {
                    shouldSwitch = true;
                    break;
                }
            }
            if (shouldSwitch) {
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
            } else if (dir === "asc") {
                dir = "desc";
                switching = true;
            }
        }
    }
</script>

</body>
</html>