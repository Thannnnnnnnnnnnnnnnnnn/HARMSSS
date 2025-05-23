</nav>
<?php include "staff.php" ?>

        <!-- Main Content -->
        <main class="px-8 py-8">


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Employee Feedback by Reviewer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    
  </head>
  <body class="flex min-h-screen bg-gray-50 text-gray-800">
    <main class="flex-1 p-6">
      <!-- Page Title -->
      <h1 class="text-3xl font-bold mb-8 text-yellow-800 flex items-center gap-2">
        <i class="fa-solid fa-comments"></i> Employee Feedback by Reviewer
      </h1>

      <!-- Search Bar -->
      <div class="mb-8">
        <input
          type="text"
          id="searchInput"
          placeholder="🔍 Search by Employee ID..."
          class="w-full md:w-1/3 border border-yellow-300 p-3 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"
          onkeyup="filterTables()"
        />
      </div>

      <?php
        $reviewers = [
          'The Employee (Self-evaluation)'
        ];

        foreach ($reviewers as $reviewer):
          $query = "
            SELECT f.EmployeeID, f.Feedback, pr.Reviewer
            FROM feedback f
            JOIN performancereviews pr ON f.EmployeeID = pr.EmployeeID
            WHERE pr.Reviewer = '$reviewer'
            ORDER BY f.EmployeeID
          ";
          $result = $conn->query($query);
      ?>

      <!-- Reviewer Feedback Section -->
      <div class="mb-12">
        <h2 class="text-2xl font-semibold text-yellow-700 mb-4">
          <?php echo $reviewer; ?> Feedback
        </h2>
        <div class="bg-white p-5 shadow-lg rounded-lg border border-yellow-200">
          <table class="w-full text-sm feedback-table">
            <thead>
              <tr class="bg-yellow-100 text-gray-700">
                <th class="px-5 py-3 text-left border-b border-yellow-200">👤 Employee ID</th>
                <th class="px-5 py-3 text-left border-b border-yellow-200">💬 Feedback</th>
                <th class="px-5 py-3 text-left border-b border-yellow-200">Actions</th> <!-- Added Actions column -->
              </tr>
            </thead>
            <tbody>
              <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr class="hover:bg-yellow-50 transition">
                    <td class="px-5 py-3 border-b border-gray-200 font-medium employee-id">
                      <?php echo $row['EmployeeID']; ?>
                    </td>
                    <td class="px-5 py-3 border-b border-gray-200 text-gray-700">
                      <?php echo htmlspecialchars($row['Feedback']); ?>
                    </td>
                    <td class="px-5 py-3 border-b border-gray-200">
                      <button 
                        class="text-yellow-500 hover:text-yellow-700 font-semibold" 
                        onclick="openModal('<?php echo $row['EmployeeID']; ?>', '<?php echo addslashes($row['Feedback']); ?>')">
                        View
                      </button> <!-- View Button -->
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="3" class="px-5 py-4 text-center text-gray-500">
                    No feedback available.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <?php endforeach; ?>
    </main>

    <!-- Modal -->
    <div id="feedbackModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center hidden">
      <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
        <h3 class="text-2xl font-semibold text-yellow-700 mb-4">Feedback Details</h3>
        <p id="modalEmployeeID" class="text-gray-600 mb-4"><strong>Employee ID:</strong> <span></span></p>
        <p id="modalFeedback" class="text-gray-600"><strong>Feedback:</strong> <span></span></p>
        <button onclick="closeModal()" class="mt-4 text-yellow-500 hover:text-yellow-700 font-semibold">Close</button>
      </div>
    </div>

    <!-- Filter Script -->
    <script>
      function filterTables() {
        const input = document.getElementById("searchInput").value.toLowerCase();
        const tables = document.querySelectorAll(".feedback-table");

        tables.forEach((table) => {
          const rows = table.querySelectorAll("tbody tr");
          rows.forEach((row) => {
            const empIDCell = row.querySelector(".employee-id");
            if (empIDCell) {
              const empID = empIDCell.textContent.toLowerCase();
              row.style.display = empID.includes(input) ? "" : "none";
            }
          });
        });
      }

      function openModal(empID, feedback) {
        // Fill in the modal with the feedback data
        document.getElementById("modalEmployeeID").children[0].textContent = empID;
        document.getElementById("modalFeedback").children[0].textContent = feedback;

        // Show the modal
        document.getElementById("feedbackModal").classList.remove("hidden");
      }

      function closeModal() {
        // Hide the modal
        document.getElementById("feedbackModal").classList.add("hidden");
      }
    </script>
  </body>
</html>







         
       