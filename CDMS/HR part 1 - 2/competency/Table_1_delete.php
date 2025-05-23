<?php
include("../../connection.php");

// Ensure the correct database is selected
mysqli_select_db($connection, 'hr_1&2_competency_management');

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $CompetencyID = $_GET['id'];

    // List of tables where CompetencyID needs to be deleted
    $tables = ['employeecompetencies', 'competencies', 'another_table']; // Add more table names here

    foreach ($tables as $table) {
        // Check if the table exists before attempting deletion
        $checkTable = "SHOW TABLES LIKE '$table'";
        $tableResult = mysqli_query($connection, $checkTable);

        if (mysqli_num_rows($tableResult) == 1) { // Table exists
            // Prepare delete query
            $deleteQuery = "DELETE FROM `$table` WHERE `CompetencyID` = ?";
            if ($stmtDelete = mysqli_prepare($connection, $deleteQuery)) {
                mysqli_stmt_bind_param($stmtDelete, "i", $CompetencyID);
                mysqli_stmt_execute($stmtDelete);
                mysqli_stmt_close($stmtDelete);
            } else {
                echo "❌ Error preparing delete query for $table: " . mysqli_error($connection);
            }
        }
    }

    // Redirect after deletion
    mysqli_close($connection);
    header("Location: competencies.php");
    exit();
} else {
    header("Location: competencies.php");
    exit();
}

?>
