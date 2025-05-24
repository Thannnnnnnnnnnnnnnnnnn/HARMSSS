<?php
session_start();
$heading = 'Update  Applicant Details';
$config = require '../../config.php';
require '../../Database.php';
$db = new Database($config['database']);
$usm = new Database($config['usm']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        validate('first_name', $errors);
        validate('last_name', $errors);
        validate('age', $errors);
        if ($_POST['age'] <= 0) {
            $errors['age'] = 'Oops! Age needs to be a positive number greater than zero.';
        }
        validate('date_of_birth', $errors);
        validate('contact_number', $errors);
        validate('email', $errors);

        if (!empty($errors)) {
            throw new Exception('Validation failed: Please check the entered information and try again.');
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
    if (empty($errors)) {
        try {
            $db->query("UPDATE applicants SET first_name = :first_name, last_name = :last_name, age = :age, date_of_birth = :date_of_birth, contact_number = :contact_number, email = :email WHERE applicant_id = :applicant_id", [
                ':first_name' => trim($_POST['first_name']),
                ':last_name' => rtrim($_POST['last_name']),
                ':age' => $_POST['age'],
                ':date_of_birth' => $_POST['date_of_birth'],
                ':contact_number' => $_POST['contact_number'],
                ':email' => $_POST['email'],
                ':applicant_id' => $_POST['applicant_id'],
            ]);

            $usm->query("INSERT INTO department_audit_trail (department_id, user_id, action, description, department_affected, module_affected) VALUES (:department_id, :user_id, :action, :description, :department_affected, :module_affected)", [
                ':department_id' => 1,
                ':user_id' => $_SESSION['user_id'],
                ':action' => 'update',
                ':description' => "admin: {$_SESSION['username']} just Updated applicant {$_POST['first_name']} information with the applicant ID: ' . {$_POST['applicant_id']}",
                ':department_affected' => 'HR part 1&2',
                ':module_affected' => 'recruitment and applicant management',
            ]);

            header('location: /admin/applicants');
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = 'Error: The action could not be completed due to a data validation problem. Please ensure all related data is correct.';
            }
        }
    }
}

$applicant = $db->query("SELECT applicant_id, first_name, last_name, contact_number, age, date_of_birth, email FROM applicants WHERE applicant_id = :applicant_id
", [
    ':applicant_id' => $_GET['id']
])->fetch();

require 'views/admin/applicant-update.view.php';
