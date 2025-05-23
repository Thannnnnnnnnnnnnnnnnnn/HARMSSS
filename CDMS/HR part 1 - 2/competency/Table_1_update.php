<?php
include("../../connection.php");

header('Content-Type: application/json'); 

//db mo yannnnnn
$db_name = "hr_1&2_competency_management";

if (!isset($connections) || !isset($connections[$db_name])) {
    echo json_encode(["status" => "error", "message" => "Database connection not found"]);
    exit;
}

$connection = $connections[$db_name];

//columns yan ng table muuuuuuuuuu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $competencyId = intval($_POST['competency_id']);
    $competencyName = trim($_POST['competency_name']);
    $description = trim($_POST['description']);

    if ($competencyId <= 0 || empty($competencyName) || empty($description)) {
        echo json_encode(["status" => "error", "message" => "Invalid input data"]);
        exit;
    }

    // Update the tableeeeee
    $query = "UPDATE competencies SET CompetencyName = ?, Description = ? WHERE CompetencyID = ?";
    $stmt = mysqli_prepare($connection, $query);

    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Query preparation failed"]);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "ssi", $competencyName, $description, $competencyId);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo json_encode(["status" => "success", "message" => "Competency updated successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "No changes made or update failed."]);
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($connection);
?>
