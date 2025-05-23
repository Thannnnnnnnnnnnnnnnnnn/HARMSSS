<?php
// Include the database connection
include 'connection.php';

// Check if the 'id' is set in the URL
if (isset($_GET['id'])) {
    $review_id = $_GET['id'];

    // First, we need to get the EmployeeID associated with this review
    $emp_query = $conn->query("SELECT EmployeeID FROM performancereviews WHERE ReviewID='$review_id'");

    if ($emp_query->num_rows > 0) {
        // Get the EmployeeID
        $emp_row = $emp_query->fetch_assoc();
        $employee_id = $emp_row['EmployeeID'];

        // Delete the feedback associated with this employee
        if ($conn->query("DELETE FROM feedback WHERE EmployeeID='$employee_id'") === TRUE) {
            // Delete the review from the performancereviews table
            if ($conn->query("DELETE FROM performancereviews WHERE ReviewID='$review_id'") === TRUE) {
                // Redirect back to the main page with a success message
                header("Location: dashboard.php?status=success");
                exit();
            } else {
                // If there is an error deleting the review
                header("Location: dashboard.php?status=error_deleting_review");
                exit();
            }
        } else {
            // If there is an error deleting the feedback
            header("Location: dashboard.php?status=error_deleting_feedback");
            exit();
        }
    } else {
        // If no review is found with the given ID
        header("Location: dashboard.php?status=error_review_not_found");
        exit();
    }
} else {
    // If no ID is passed
    header("Location: dashboard.php?status=error_no_id");
    exit();
}

$conn->close();
?>
