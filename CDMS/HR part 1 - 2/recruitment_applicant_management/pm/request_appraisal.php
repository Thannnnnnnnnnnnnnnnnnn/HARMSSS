<?php
include 'connection.php';

// Check if the request method is POST and the 'employee_id' is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['employee_id'])) {
    // Get the employee ID from the POST data
    $employee_id = $_POST['employee_id'];

    // Fetch the most recent KPIID for the employee from the kpis table
    $result = $conn->query("SELECT KPIID, EmployeeID, DateCreated FROM kpis WHERE EmployeeID = '$employee_id' ORDER BY DateCreated DESC LIMIT 1");

    // Check if there are any results
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $review_id = $row['KPIID'];

        // Insert the appraisal with the ReviewID
        $sql = "INSERT INTO appraisals (ReviewID) VALUES ('$review_id')";
        if ($conn->query($sql) === TRUE) {
            // Successful insertion
            // Optionally, you could redirect to a page or display a success message
            header("Location: appraisals.php?status=success");
            exit;
        } else {
            // If there was an issue with the insert query
            echo "Error: " . $conn->error;
        }
    } else {
        // If no KPI data found for the given employee_id
        echo "No recent KPI data found for this employee.";
    }
}

header("Location: appraisals.php");
exit;
?>
