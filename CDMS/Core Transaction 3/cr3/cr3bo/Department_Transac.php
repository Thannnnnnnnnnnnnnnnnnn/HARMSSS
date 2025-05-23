<?php include "../Sidebar.php";
include "../../partials/admin/sidebar.php";
include "../../partials/admin/head.php";
include "../../partials/admin/navbar.php";
include "../../partials/admin/footer.php";
include "../user.php";
$db_name = "cr3_re"; 
$connection = $connections[$db_name];

$itemsPerPage = isset($_GET['items']) ? (int)$_GET['items'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;
$totalResults = $connection->query("SELECT COUNT(*) AS total FROM department_transaction");
$totalRow = $totalResults->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $itemsPerPage);


$query = "SELECT dt.dept_transc_id, d.dept_name, CONCAT(u.first_name, ' ', u.last_name) AS Name, 
          dt.transaction_type, dt.description, dt.timestamp 
          FROM department_transaction dt 
          LEFT JOIN departments d ON dt.department_id = d.department_id 
          LEFT JOIN user_account u ON dt.user_id = u.user_id 
          ORDER BY dt.dept_transc_id ASC 
          LIMIT $offset, $itemsPerPage";
$result = mysqli_query($connection, $query);    
?>
<main class="px-4 py-4">
  <h2 class="text-center text-[#594423] text-2xl font-bold mb-4">Department Transaction</h2>
  <div class="bg-gradient-to-b from-white to-orange-100 min-h-screen p-8">
    <div class="overflow-x-auto bg-white shadow-lg rounded-md">
      <table class="min-w-full text-[12px] text-center text-[#594423]">
        <thead class="bg-[#F7E6CA] text-[#594423] uppercase">
          <tr>
            <th class="p-2">Dept Transaction ID</th>
            <th class="p-2">Department Name</th>
            <th class="p-2">Name</th>
            <th class="p-2">Transaction Type</th>
            <th class="p-2">Description</th>
            <th class="p-2">Date</th>
          </tr>
        </thead>
        <tbody>
          <?php
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
              $formattedTimestamp = date('F j, Y, g:i A', strtotime($row['timestamp']));
                echo "<tr class='hover:bg-[#FFF6E8] text-[11px]' onclick=\"openViewModal(
                  '{$row['dept_transc_id']}',
                  '{$row['dept_name']}',
                  '" . ($row['Name'] ?? 'Guest') . "',

                  '{$row['transaction_type']}',
                  '{$row['description']}',
                  '$formattedTimestamp')\">
                  <td class='border'>{$row['dept_transc_id']}</td>
                  <td class='border'>{$row['dept_name']}</td>
                  <td class='border'>" . ($row['Name'] ?? 'Guest') . "</td>
                  <td class='border'>{$row['transaction_type']}</td>
                  <td class='border'>{$row['description']}</td>
                  <td class='border'>{$row['timestamp']}</td>
                </tr>";
              }
            } else {
              echo "<tr><td colspan='6' class='text-center py-4'>No Transaction Found.</td></tr>";
            }
          ?>
        </tbody>
      </table>
    </div>
    <div class="flex items-center justify-between mt-4">
      <div class="text-sm text-gray-700">
        Showing <?= min($itemsPerPage, $totalItems) ?> to <?= $itemsPerPage ?> Items
      </div>
      <div class="flex items-center space-x-1">
        <?php if ($page > 1): ?>
          <a href="?page=<?= $page-1 ?>&items=<?= $itemsPerPage ?>" class="px-3 py-1 bg-[#f1ddbe] hover:bg-gray-300 rounded-md"><</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <a href="?page=<?= $i ?>&items=<?= $itemsPerPage ?>" class="px-3 py-1 <?= $i == $page ? 'bg-[#f1ddbe]' : 'bg-[#f1ddbe]' ?> hover:bg-gray-300 rounded-md"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
          <a href="?page=<?= $page+1 ?>&items=<?= $itemsPerPage ?>" class="px-3 py-1 bg-[#f1ddbe] hover:bg-gray-300 rounded-md">></a>
        <?php endif; ?>
      </div>
      <div class="text-sm text-gray-700 flex items-center">
        Items per page:
        <select onchange="changeItemsPerPage(this)" class="bg-[#f1ddbe] border border-gray-300 rounded-md py-1 px-2 ml-2">
          <option value="10" <?= $itemsPerPage == 10 ? 'selected' : '' ?>>10</option>
          <option value="20" <?= $itemsPerPage == 20 ? 'selected' : '' ?>>20</option>
          <option value="50" <?= $itemsPerPage == 50 ? 'selected' : '' ?>>50</option>
        </select>
      </div>
    </div>
  </div>

  <div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50" onclick="closeViewModal()">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg relative animate__animated animate__fadeInDown" onclick="event.stopPropagation()">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-[#594423] flex items-center gap-2">
          <i class="fas fa-info-circle"></i> Transaction Details
        </h2>
        <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600 text-2xl">×</button>
      </div>
      <div class="space-y-4 text-gray-700 text-sm">
        <div class="flex items-center">
          <span class="font-semibold w-48">Transaction ID:</span>
          <span id="modalreserve" class="text-gray-900"></span>
        </div>
        <div class="flex items-center">
          <span class="font-semibold w-48">Department Name:</span>
          <span id="modalDepartmentName" class="text-gray-900"></span>
        </div>
        <div class="flex items-center">
          <span class="font-semibold w-48">Name:</span>
          <span id="modalName" class="text-gray-900"></span>
        </div>
        <div class="flex items-center">
          <span class="font-semibold w-48">Transaction Type:</span>
          <span id="modalTransactionType" class="text-gray-900"></span>
        </div>
        <div class="flex items-center">
          <span class="font-semibold w-48">Description:</span>
          <span id="modalDescription" class="text-gray-900"></span>
        </div>
        <div class="flex items-center">
          <span class="font-semibold w-48">Timestamp:</span>
          <span id="modalTimestamp" class="text-gray-900"></span>
        </div>
      </div>
      <div class="flex justify-end mt-8">
        <button onclick="closeViewModal()" class="px-6 py-2 bg-[#594423] hover:bg-[#4a3820] text-white rounded-lg font-medium transition">
          Close
        </button>
      </div>
    </div>
  </div>
</main>
<script>
  function openViewModal(id, departmentName, name, type, description, timestamp) {
    document.getElementById('modalreserve').innerText = id;
    document.getElementById('modalDepartmentName').innerText = departmentName;
    document.getElementById('modalName').innerText = name;
    document.getElementById('modalTransactionType').innerText = type;
    document.getElementById('modalDescription').innerText = description;
    document.getElementById('modalTimestamp').innerText = timestamp;
    document.getElementById('viewModal').classList.remove('hidden');
  }
  function closeViewModal() {
    document.getElementById('viewModal').classList.add('hidden');
  }
  function changeItemsPerPage(select) {
    const items = select.value;
    window.location.href = "?page=1&items=" + items;
  }
</script>