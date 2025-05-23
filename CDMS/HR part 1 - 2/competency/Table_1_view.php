<?php

include("../../connection.php");

// Define the correct database name
$db_name = "hr_1&2_competency_management"; 

// Check if $connections array is defined and contains the requested DB
if (!isset($connections) || !isset($connections[$db_name])) {
    die("Database connection not found for $db_name");
}

// Assign the correct connection
$connection = $connections[$db_name]; 

// Validate that ID is provided
if (!isset($_GET['id'])) {
    die("No competency ID provided.");
}

// Ensure competency ID is a valid integer
$competencyId = intval($_GET['id']);

if ($competencyId <= 0) {
    die("Invalid competency ID.");
}

// Prepare the query to select competency details
$query = "SELECT CompetencyID, CompetencyName, Description FROM competencies WHERE CompetencyID = ?";
$stmt = mysqli_prepare($connection, $query);

if (!$stmt) {
    die("Query preparation failed: " . mysqli_error($connection));
}

mysqli_stmt_bind_param($stmt, "i", $competencyId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    echo "<p><b>ID:</b> " . htmlspecialchars($row['CompetencyID']) . "</p>";
    echo "<p><b>Competency Name:</b> " . htmlspecialchars($row['CompetencyName']) . "</p>";
    echo "<p><b>Description:</b> " . nl2br(htmlspecialchars($row['Description'])) . "</p>";

} else {
    echo "No record found for the provided competency ID.";
}

// Close resources
mysqli_stmt_close($stmt);
mysqli_close($connection);

?>
