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
include_once('../../includes/head.php'); 
require_once __DIR__ . "/../../includes/Database.php";

$dbOrders = new Database();
$connOrders = $dbOrders->connect("orders");

if (!$connOrders) {
    die("❌ Database connection failed.");
}

$limit = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

if (isset($_GET['delete'])) {
    $orderId = intval($_GET['delete']);

    // 1. Delete from payment_transactions first
    $stmt = $connOrders->prepare("DELETE FROM payment_transactions WHERE OrderID = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $stmt->close();

    // 2. Delete from orderitems
    $stmt = $connOrders->prepare("DELETE FROM orderitems WHERE OrderID = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $stmt->close();

    // 3. Delete from orders last
    $stmt = $connOrders->prepare("DELETE FROM orders WHERE Order_ID = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $stmt->close();

    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Order deleted successfully',
                confirmButtonColor: '#594423',
                background: '#FFF6E8',
                customClass: {
                    popup: 'rounded-2xl',
                    title: 'text-[#594423] font-[Cinzel]',
                    content: 'text-[#4E3B2A]'
                }
            }).then(() => {
                window.location.href = 'orders.php';
            });
        });
    </script>";
}

if (isset($_POST['update'])) {
    $orderId = (int)$_POST['Order_ID'];
    $customerName = $_POST['CustomerName'];
    $totalAmount = (float)$_POST['TotalAmount'];
    $orderDate = $_POST['OrderDate'];

    $stmt = $connOrders->prepare("UPDATE orders SET CustomerName = ?, TotalAmount = ?, OrderDate = ? WHERE Order_ID = ?");
    $stmt->bind_param("sdsi", $customerName, $totalAmount, $orderDate, $orderId);
    if ($stmt->execute()) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Order updated successfully',
                    confirmButtonColor: '#594423',
                    background: '#FFF6E8',
                    customClass: {
                        popup: 'rounded-2xl',
                        title: 'text-[#594423] font-[Cinzel]',
                        content: 'text-[#4E3B2A]'
                    }
                }).then(() => {
                    window.location.href = 'orders.php';
                });
            });
        </script>";
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error updating order: {$stmt->error}',
                    confirmButtonColor: '#594423',
                    background: '#FFF6E8',
                    customClass: {
                        popup: 'rounded-2xl',
                        title: 'text-[#594423] font-[Cinzel]',
                        content: 'text-[#4E3B2A]'
                    }
                });
            });
        </script>";
    }
}

$stmt = $connOrders->prepare("SELECT Order_ID, CustomerName, TotalAmount, OrderDate FROM orders LIMIT ?, ?");
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$resultOrders = $stmt->get_result();

$resultTotal = $connOrders->query("SELECT COUNT(*) AS total FROM orders");
$totalOrders = $resultTotal->fetch_assoc()['total'] ?? 0;
$totalPages = max(1, ceil($totalOrders / $limit));
?>

<body>
<div class="flex min-h-screen w-full bg-[#FFF6E8] font-[Georgia] text-[#4E3B2A]">
    <?php include('../../includes/sidebar.php'); ?>

    <div class="main w-full md:ml-[320px]">
        <?php include('../../includes/navbar.php'); ?>

        <main class="px-8 py-8">
            <h1 class="text-2xl font-[Cinzel] font-bold text-[#594423] mb-10">Orders Record</h1>

            <div class="overflow-x-auto bg-white rounded-2xl shadow-md border-2 border-[#594423]">
                <table id="ordersTable" class="min-w-full bg-white">
                    <thead class="bg-[#4E3B2A] text-white">
                        <tr>
                            <th class="px-6 py-4 border-b border-[#594423] text-left cursor-pointer" onclick="sortTable(0)">Order ID</th>
                            <th class="px-6 py-4 border-b border-[#594423] text-left cursor-pointer" onclick="sortTable(1)">Customer Name</th>
                            <th class="px-6 py-4 border-b border-[#594423] text-left cursor-pointer" onclick="sortTable(2)">Total Amount</th>
                            <th class="px-6 py-4 border-b border-[#594423] text-left cursor-pointer" onclick="sortTable(3)">Order Date</th>
                            <th class="px-6 py-4 border-b border-[#594423] text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $resultOrders->fetch_assoc()): ?>
                            <tr class="hover:bg-[#F7E6CA] transition">
                                <td class="px-6 py-4 border-b border-[#594423]"><?php echo htmlspecialchars($row['Order_ID']); ?></td>
                                <td class="px-6 py-4 border-b border-[#594423]"><?php echo htmlspecialchars($row['CustomerName']); ?></td>
                                <td class="px-6 py-4 border-b border-[#594423]"><?php echo htmlspecialchars(number_format($row['TotalAmount'], 2)); ?></td>
                                <td class="px-6 py-4 border-b border-[#594423]"><?php echo htmlspecialchars($row['OrderDate']); ?></td>
                                <td class="px-6 py-4 border-b border-[#594423] flex space-x-2">
                                    <button 
                                        class="delete-button flex items-center space-x-2 bg-red-100 hover:bg-red-600 text-red-600 hover:text-white border-2 border-red-600 px-4 py-2 rounded-lg transition"
                                        data-order-id="<?php echo htmlspecialchars($row['Order_ID']); ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4m-4 0a1 1 0 00-1 1v1h6V4a1 1 0 00-1-1m-4 0h4" />
                                        </svg>
                                        <span>Delete</span>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <div class="flex justify-between items-center px-6 py-4 border-t-2 border-[#594423] bg-white">
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

            <?php if (isset($_GET['update'])): 
                $orderIdToUpdate = (int)$_GET['update'];
                $stmt = $connOrders->prepare("SELECT * FROM orders WHERE Order_ID = ?");
                $stmt->bind_param("i", $orderIdToUpdate);
                $stmt->execute();
                $orderData = $stmt->get_result()->fetch_assoc();
                if ($orderData):
            ?>
                <div class="mt-10 p-6 bg-white rounded-2xl shadow-lg border-2 border-[#594423]">
                    <h2 class="text-xl font-[Cinzel] font-bold mb-6 text-[#594423]">Update Order</h2>

                    <form action="orders.php" method="POST" class="space-y-6">
                        <input type="hidden" name="Order_ID" value="<?php echo htmlspecialchars($orderData['Order_ID']); ?>">

                        <div>
                            <label for="CustomerName" class="block text-sm font-bold mb-2 text-[#4E3B2A]">Customer Name</label>
                            <input type="text" id="CustomerName" name="CustomerName" value="<?php echo htmlspecialchars($orderData['CustomerName']); ?>" class="w-full p-3 border-2 border-[#594423] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#F7E6CA]" required>
                        </div>

                        <div>
                            <label for="TotalAmount" class="block text-sm font-bold mb-2 text-[#4E3B2A]">Total Amount</label>
                            <input type="number" step="0.01" id="TotalAmount" name="TotalAmount" value="<?php echo htmlspecialchars($orderData['TotalAmount']); ?>" class="w-full p-3 border-2 border-[#594423] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#F7E6CA]" required>
                        </div>

                        <div>
                            <label for="OrderDate" class="block text-sm font-bold mb-2 text-[#4E3B2A]">Order Date</label>
                            <input type="date" id="OrderDate" name="OrderDate" value="<?php echo htmlspecialchars($orderData['OrderDate']); ?>" class="w-full p-3 border-2 border-[#594423] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#F7E6CA]" required>
                        </div>

                        <div>
                            <button type="submit" name="update" class="bg-[#F7E6CA] hover:bg-[#594423] text-[#594423] hover:text-[#F7E6CA] font-bold px-6 py-3 rounded-lg border-2 border-[#594423] transition">
                                Update Order
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; endif; ?>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../assets/scripts.js"></script>

<script>
    function sortTable(columnIndex) {
        const table = document.getElementById("ordersTable");
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

    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.delete-button');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const orderId = this.getAttribute('data-order-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `Do you want to delete Order ID ${orderId}? This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#594423',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                    background: '#FFF6E8',
                    customClass: {
                        popup: 'rounded-2xl',
                        title: 'text-[#594423] font-[Cinzel]',
                        content: 'text-[#4E3B2A]'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `?delete=${orderId}`;
                    }
                });
            });
        });
    });
</script>
</body>
</html>