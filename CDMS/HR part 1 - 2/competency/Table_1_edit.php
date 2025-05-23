<?php
include("../../connection.php");

//DB mo yannn
$db_name = "hr_1&2_competency_management";

if (!isset($connections) || !isset($connections[$db_name])) {
    die("Database connection not found for $db_name");
}

$connection = $connections[$db_name];

// PK this ng table mo
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid competency ID.");
}

$competencyId = intval($_GET['id']);

// Fetchingggggggggg
$query = "SELECT CompetencyName, Description FROM competencies WHERE CompetencyID = ?";
$stmt = mysqli_prepare($connection, $query);

if (!$stmt) {
    die("Query preparation failed: " . mysqli_error($connection));
}

mysqli_stmt_bind_param($stmt, "i", $competencyId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    ?>
    <form id="editCompetencyForm">
        <input type="hidden" name="competency_id" value="<?php echo htmlspecialchars($competencyId); ?>">

        <label class="form-label">Competency Name:</label>
        <input type="text" name="competency_name" value="<?php echo htmlspecialchars($row['CompetencyName']); ?>" 
               class="form-control mb-3" required>

        <label class="form-label">Description:</label>
        <textarea name="description" class="form-control mb-3" required><?php echo htmlspecialchars($row['Description']); ?></textarea>

        

        <button type="submit" class="btn btn-success w-100">Save Changes</button>
    </form>
    <?php
} else {
    echo "<p>No record found for the provided competency ID.</p>";
}

mysqli_stmt_close($stmt);
mysqli_close($connection);
?>
