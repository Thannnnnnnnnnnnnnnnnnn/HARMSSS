<?php
include 'staff.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        $employee = $_POST['employee'];
        $review_date = $_POST['review_date'];
        $rating = $_POST['rating'];
        $comments = $_POST['comments'];
        $reviewer = $_POST['reviewer'];
        $feedback = $_POST['feedback'];

        $sql = "INSERT INTO performancereviews (EmployeeID, ReviewDate, Rating, Comments, Reviewer) 
                VALUES ('$employee', '$review_date', '$rating', '$comments', '$reviewer')";
        $conn->query($sql);

        if (!empty($feedback)) {
            $sql_feedback = "INSERT INTO feedback (EmployeeID, Feedback) VALUES ('$employee', '$feedback')";
            $conn->query($sql_feedback);
        }
    }

    if (isset($_POST['delete'])) {
        $id = $_POST['id'];

        $emp_query = $conn->query("SELECT EmployeeID FROM performancereviews WHERE ReviewID='$id'");
        if ($emp_query->num_rows > 0) {
            $emp_row = $emp_query->fetch_assoc();
            $employee_id = $emp_row['EmployeeID'];
            $conn->query("DELETE FROM feedback WHERE EmployeeID='$employee_id'");
        }

        $conn->query("DELETE FROM performancereviews WHERE ReviewID='$id'");
    }
}

$reviewers = ["The Employee (Self-evaluation)", "Peer"];
$reviews_by_reviewer = [];

foreach ($reviewers as $rev) {
    $result = $conn->query("SELECT * FROM performancereviews WHERE Reviewer='$rev'");
    $reviews_by_reviewer[$rev] = $result;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Performance Reviews</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

        function confirmReviewSubmission() {
            return confirm("Are you sure you want to add this review?");
        }

        function confirmDelete() {
            return confirm("Are you sure you want to delete this review?");
        }

        function openModal(id, employee, date, rating, comments, reviewer, feedback) {
            document.getElementById('modalReviewID').textContent = id;
            document.getElementById('modalEmployeeID').textContent = employee;
            document.getElementById('modalReviewDate').textContent = date;
            document.getElementById('modalRating').textContent = rating;
            document.getElementById('modalComments').textContent = comments;
            document.getElementById('modalReviewer').textContent = reviewer;
            document.getElementById('modalFeedback').textContent = feedback;
            document.getElementById('reviewModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('reviewModal').classList.add('hidden');
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen font-sans flex">
    <main class="flex-1 p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Performance Reviews</h1>

        <!-- Search Bar -->
        <div class="mb-6">
            <input 
                type="text" 
                id="searchInput" 
                placeholder="🔍 Search by Employee ID..." 
                class="border border-gray-300 rounded-md p-3 w-full shadow-sm focus:outline-none focus:ring focus:border-blue-300"
                onkeyup="filterTables()">
        </div>

        <!-- Form -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-10">
            <form method="POST" class="space-y-4" onsubmit="return confirmReviewSubmission()">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" name="employee" placeholder="Employee ID" required class="border border-gray-300 rounded-md p-3 w-full">
                    <input type="date" name="review_date" required class="border border-gray-300 rounded-md p-3 w-full">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <select name="rating" required class="border border-gray-300 rounded-md p-3 w-full">
                        <option value="" disabled selected>Select Rating</option>
                        <?php for ($i = 1; $i <= 10; $i++): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                    <select name="comments" required class="border border-gray-300 rounded-md p-3 w-full">
                        <option value="" disabled selected>Select Comment</option>
                        <option value="Needs Improvement">Needs Improvement</option>
                        <option value="Meets Expectations">Meets Expectations</option>
                        <option value="Exceeds Expectations">Exceeds Expectations</option>
                    </select>
                    <select name="reviewer" required class="border border-gray-300 rounded-md p-3 w-full">
                        <option value="" disabled selected>Select Reviewer</option>
                        <option value="The Employee (Self-evaluation)">The Employee (Self-evaluation)</option>
                        <option value="Peer">Peer</option>
                    </select>
                </div>
                <textarea name="feedback" placeholder="Enter Feedback (Optional)" class="border border-gray-300 rounded-md p-3 w-full h-24 resize-none"></textarea>
                <button type="submit" name="add" class="bg-amber-800 hover:bg-amber-700 text-white px-6 py-3 rounded-md transition">➕ Add Review & Feedback</button>
            </form>
        </div>

        <!-- Modal -->
        <div id="reviewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white p-6 rounded-lg max-w-lg w-full space-y-2 shadow-lg">
                <h2 class="text-xl font-bold mb-4">Review Details</h2>
                <p><strong>Review ID:</strong> <span id="modalReviewID"></span></p>
                <p><strong>Employee ID:</strong> <span id="modalEmployeeID"></span></p>
                <p><strong>Review Date:</strong> <span id="modalReviewDate"></span></p>
                <p><strong>Rating:</strong> <span id="modalRating"></span></p>
                <p><strong>Comments:</strong> <span id="modalComments"></span></p>
                <p><strong>Reviewer:</strong> <span id="modalReviewer"></span></p>
                <p><strong>Feedback:</strong> <span id="modalFeedback"></span></p>
                <button onclick="closeModal()" class="mt-4 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Close</button>
            </div>
        </div>

        <!-- Tables per Reviewer -->
        <?php foreach ($reviews_by_reviewer as $reviewer => $reviews): ?>
            <h2 class="text-xl font-semibold text-gray-700 mb-3"><?php echo $reviewer; ?> Reviews</h2>
            <div class="overflow-x-auto mb-10 bg-white shadow-md rounded-lg feedback-table">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-200 text-gray-600">
                        <tr>
                            <th class="border px-4 py-3">ReviewID</th>
                            <th class="border px-4 py-3">Employee</th>
                            <th class="border px-4 py-3">Review Date</th>
                            <th class="border px-4 py-3">Rating</th>
                            <th class="border px-4 py-3">Comments</th>
                            <th class="border px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php while ($row = $reviews->fetch_assoc()): ?>
                            <?php
                            $emp_id = $row['EmployeeID'];
                            $feedback_text = "";
                            $feedback_query = $conn->query("SELECT Feedback FROM feedback WHERE EmployeeID='$emp_id'");
                            while ($feedback_row = $feedback_query->fetch_assoc()) {
                                $feedback_text .= $feedback_row['Feedback'] . "\\n";
                            }
                            ?>
                            <tr class="hover:bg-gray-100 transition">
                                <td class="border px-4 py-2"><?php echo $row['ReviewID']; ?></td>
                                <td class="border px-4 py-2 employee-id"><?php echo $row['EmployeeID']; ?></td>
                                <td class="border px-4 py-2"><?php echo $row['ReviewDate']; ?></td>
                                <td class="border px-4 py-2"><?php echo $row['Rating']; ?></td>
                                <td class="border px-4 py-2"><?php echo $row['Comments']; ?></td>
                                <td class="border px-4 py-2 space-x-2 text-center">
                                    <button 
                                        type="button" 
                                        onclick="openModal('<?php echo $row['ReviewID']; ?>', '<?php echo $row['EmployeeID']; ?>', '<?php echo $row['ReviewDate']; ?>', '<?php echo $row['Rating']; ?>', '<?php echo addslashes($row['Comments']); ?>', '<?php echo addslashes($row['Reviewer']); ?>', `<?php echo addslashes($feedback_text); ?>`)" 
                                        class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-md transition">👁️ View</button>
                                    <form method="POST" onsubmit="return confirmDelete()" class="inline">
                                        <input type="hidden" name="id" value="<?php echo $row['ReviewID']; ?>">

                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </main>
</body>
</html>

<?php $conn->close(); ?>
