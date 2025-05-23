<?php
include 'db_connection.php';

$action = $_POST['action'];

if ($action == 'add') {
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $stmt = $conn->prepare("INSERT INTO employees (FullName, Email) VALUES (?, ?)");
    $stmt->bind_param("ss", $fullName, $email);
    if (!$stmt->execute()) {
        die("Error: " . $stmt->error);
    }
    $stmt->close();
} elseif ($action == 'edit') {
    $employeeID = $_POST['employeeID'];
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $stmt = $conn->prepare("UPDATE employees SET FullName = ?, Email = ? WHERE EmployeeID = ?");
    $stmt->bind_param("ssi", $fullName, $email, $employeeID);
    if (!$stmt->execute()) {
        die("Error: " . $stmt->error);
    }
    $stmt->close();
} elseif ($action == 'delete') {
    $employeeID = $_POST['employeeID'];
    $stmt = $conn->prepare("DELETE FROM employees WHERE EmployeeID = ?");
    $stmt->bind_param("i", $employeeID);
    if (!$stmt->execute()) {
        die("Error: " . $stmt->error);
    }
    $stmt->close();
}

header("Location: employees.php");
?>