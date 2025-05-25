<?php
// Corrected SQL query to fetch program descriptions
if (isset($_GET['ProgramName'])) {
    $programName = $_GET['ProgramName'];

    $stmt = $conn->prepare("SELECT ProgramDescriptions FROM trainingprograms WHERE ProgramName = ?");
    $stmt->bind_param('s', $programName);
    $stmt->execute();
    $result = $stmt->get_result();

    $descriptions = [];
    while ($row = $result->fetch_assoc()) {
        $descriptions = json_decode($row['ProgramDescriptions']);
    }

    echo json_encode($descriptions);
    exit;
}
?>