<?php  include "../../partials/admin/sidebar.php";
include "../../partials/admin/head.php";
include "../../partials/admin/navbar.php";
include "../../partials/admin/footer.php";
include "../user.php";
$db_name = "cr3_re"; 
$connection = $connections[$db_name];

$itemsPerPage = isset($_GET['items']) ? (int)$_GET['items'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;
$totalResults = $connection->query("SELECT COUNT(*) AS total FROM department_audit_trail");
$totalRow = $totalResults->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $itemsPerPage);

$query = "SELECT dat.dept_audit_trail_id, d.dept_name, CONCAT(u.first_name, ' ', u.last_name) AS Name, 
          dat.action, dat.description, dat.department_affected, dat.module_affected, dat.timestamp 
          FROM department_audit_trail dat 
          LEFT JOIN departments d ON dat.department_id = d.department_id 
          LEFT JOIN user_account u ON dat.user_id = u.user_id 
          ORDER BY dat.Dept_Audit_Trail_ID ASC 
          LIMIT $offset, $itemsPerPage";
$result = mysqli_query($connection, $query);    
?>

<main class="px-4 py-4">
  <h2 class="text-center text-[#594423] text-2xl font-bold mb-4">Department Audit Trail</h2>
  <div class="bg-gradient-to-b from-white to-orange-100 min-h-screen p-8">
    <div class="overflow-x-auto bg-white shadow-lg rounded-md">
      <table class="min-w-full text-[12px] text-center text-[#594423]">
        <thead class="bg-[#F7E6CA] text-[#594423] uppercase">
          <tr>
            <th class="p-2">Dept Audit Trail ID</th>
            <th class="p-2">Department Name</th>
            <th class="p-2">Name</th>
            <th class="p-2">Action</th>
            <th class="p-2">Description</th>
            <th class="p-2">Department Affected</th>
            <th class="p-2">Module Affected</th>
            <th class="p-2">Date</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): 
                $formattedTimestamp = date('F j, Y, g:i A', strtotime($row['timestamp']));?>
              <tr class='hover:bg-[#FFF6E8] text-[11px] cursor-pointer' onclick="openAuditModal(
                '<?php echo $row['dept_audit_trail_id']; ?>',
                '<?php echo $row['dept_name']; ?>',
                '<?php echo $row['Name']??'Guest'; ?>',
                '<?php echo $row['action']; ?>',
                '<?php echo $row['description']; ?>',
                '<?php echo $row['department_affected']; ?>',
                '<?php echo $row['module_affected']; ?>',
                '<?php echo $formattedTimestamp; ?>')">
                <td class='border'><?php echo $row['dept_audit_trail_id']; ?></td>
                <td class='border'><?php echo $row['dept_name']; ?></td>
                <td class='border'><?php echo $row['Name']??'Guest'; ?></td>
                <td class='border'><?php echo $row['action']; ?></td>
                <td class='border'><?php echo $row['description']; ?></td>
                <td class='border'><?php echo $row['department_affected']; ?></td>
                <td class='border'><?php echo $row['module_affected']; ?></td>
                <td class='border'><?php echo $row['timestamp']; ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan='8' class='text-center py-4'>No Audit Trail Found.</td></tr>
          <?php endif; ?>
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
</main>

<!-- Modal -->
<div id="auditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50" onclick="closeAuditModal()">
  <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg relative animate__animated animate__fadeInDown" onclick="event.stopPropagation()">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-2xl font-bold text-[#594423] flex items-center gap-2">
        <i class="fas fa-info-circle"></i> Audit Trail Details
      </h2>
      <button onclick="closeAuditModal()" class="text-gray-400 hover:text-gray-600 text-2xl">×</button>
    </div>

    <div class="space-y-4 text-gray-700 text-sm">
      <div class="flex"><span class="w-48 font-semibold">Audit Trail ID:</span> <span id="modalAuditID"></span></div>
      <div class="flex"><span class="w-48 font-semibold">Department Name:</span> <span id="modalDeptName"></span></div>
      <div class="flex"><span class="w-48 font-semibold">Name:</span> <span id="modalName"></span></div>
      <div class="flex"><span class="w-48 font-semibold">Action:</span> <span id="modalAction"></span></div>
      <div class="flex"><span class="w-48 font-semibold">Description:</span> <span id="modalDescription"></span></div>
      <div class="flex"><span class="w-48 font-semibold">Department Affected:</span> <span id="modalDeptAffected"></span></div>
      <div class="flex"><span class="w-48 font-semibold">Module Affected:</span> <span id="modalModuleAffected"></span></div>
      <div class="flex"><span class="w-48 font-semibold">Timestamp:</span> <span id="modalTimestamp"></span></div>
    </div>

    <div class="flex justify-end mt-6">
      <button onclick="closeAuditModal()" class="px-6 py-2 bg-[#594423] hover:bg-[#4a3820] text-white rounded-lg font-medium transition">Close</button>
    </div>
  </div>
</div>

<script>
function changeItemsPerPage(select) {
  const items = select.value;
  window.location.href = "?page=1&items=" + items;
}

function openAuditModal(id, deptName, name, action, description, deptAffected, moduleAffected, timestamp) {
  document.getElementById('modalAuditID').textContent = id;
  document.getElementById('modalDeptName').textContent = deptName;
  document.getElementById('modalName').textContent = name;
  document.getElementById('modalAction').textContent = action;
  document.getElementById('modalDescription').textContent = description;
  document.getElementById('modalDeptAffected').textContent = deptAffected;
  document.getElementById('modalModuleAffected').textContent = moduleAffected;
  document.getElementById('modalTimestamp').textContent = timestamp;
  document.getElementById('auditModal').classList.remove('hidden');
}

function closeAuditModal() {
  document.getElementById('auditModal').classList.add('hidden');
}
</script>