<?php
include '../../connection.php'; // Ensure database connection

header('Content-Type: application/json'); // Set response type to JSON

// Database name
$db_name = "hr_1&2_competency_management";

// Validate database connection
if (!isset($connections) || !isset($connections[$db_name])) {
    echo json_encode(["status" => "error", "message" => "Database connection not found for $db_name"]);
    exit;
}

// Use the correct database connection
$conn = $connections[$db_name];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $competency_name = trim($_POST['competency_name']);
    $description = trim($_POST['description']);

    // Check for empty fields
    if (empty($competency_name) || empty($description)) {
        echo json_encode(["status" => "error", "message" => "All fields are required!"]);
        exit;
    }

   // Generate the first 8 digits based on the current date (YYYYMMDD)
$competency_prefix = date("Ymd");

// Generate the last 2 digits as random numbers (00 - 99)
$random_suffix = str_pad(mt_rand(0, 99), 2, '0', STR_PAD_LEFT);

// Combine both parts
$competency_id = $competency_prefix . $random_suffix;


    // Prepare insert query
    $sql = "INSERT INTO competencies (CompetencyID, CompetencyName, Description) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("iss", $competency_id, $competency_name, $description);

    // ✅ Execute the query
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Inserted successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Insert failed: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
