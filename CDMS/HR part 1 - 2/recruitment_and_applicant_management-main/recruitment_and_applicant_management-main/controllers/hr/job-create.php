<?php
session_start();

$heading = 'JOB-CREATE';
$config = require 'config.php';
$db = new Database($config['database']);
$usm = new Database($config['usm']);
$nhoes = new Database($config['nhoes']);
// dd($_SESSION);
$departments = $usm->query("SELECT * FROM departments")->fetchAll();
// dd($departments);
$roles = $nhoes->query("SELECT role_id,role_title FROM roles")->fetchAll();
// dd($roles);
$applications = $db->query("SELECT * FROM applicants")->fetchAll();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // dd($_POST);
    validate('job_title', $errors);
    validate('location', $errors);
    validate('description', $errors);
    validate('requirements', $errors);
    validate('salary', $errors);

    if (empty($errors)) {
        // dd($_POST);
        $db->query('INSERT INTO jobpostings(job_title, location, salary, company, employment_type, department_id, role_id, posted_by) VALUES(:job_title, :location, :salary, :company, :employment_type, :department_id, :role_id, :posted_by)', [
            ':job_title' => $_POST['job_title'],
            ':location' => $_POST['location'],
            ':salary' => $_POST['salary'],
            ':company' => $_POST['company'],
            ':employment_type' => $_POST['employment_type'],
            ':role_id' => $_POST['role_id'],
            ':department_id' => $_POST['department_id'],
            ':posted_by' => $_SESSION['user_id'],
        ]);

        $posting_id = $db->pdo->lastInsertId();
        $db->query("INSERT INTO prerequisites (description, requirements, posting_id) VALUES (:description, :requirements, :posting_id)", [
            ':description' => $_POST['description'],
            ':requirements' => $_POST['requirements'],
            ':posting_id' => $posting_id,
        ]);

        header('Location: /hr/');
        exit();
    }
}

require 'views/hr/job-create.view.php';
