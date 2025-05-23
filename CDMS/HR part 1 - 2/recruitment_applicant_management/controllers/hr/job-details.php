<?php

session_start();

$heading = 'Job';
$config = require 'config.php';
$db = new Database($config['database']);
$usm = new Database($config['usm']);

$user = $db->query("SELECT posted_by FROM jobpostings WHERE posting_id = :posting_id", [
    ':posting_id' => $_GET['id'],
])->fetch();

// dd($user);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate('job_title', $errors);
    validate('description', $errors);
    validate('location', $errors);
    validate('employment_type', $errors);
    validate('requirements', $errors);
    validate('salary', $errors);
    validate('company', $errors);

    if (empty($errors)) {
        $db->query("UPDATE jobpostings SET job_title = :job_title,  location = :location, employment_type = :employment_type, salary = :salary, company = :company, posted_by = :posted_by WHERE posting_id = :posting_id", [
            ':job_title' => $_POST['job_title'],
            ':location' => $_POST['location'],
            ':employment_type' => $_POST['employment_type'],
            ':salary' => $_POST['salary'],
            ':company' => $_POST['company'],
            ':posted_by' => $user['posted_by'],
            ':posting_id' => $_GET['id'],
        ]);
        $db->query("UPDATE prerequisites SET description = :description, requirements = :requirements WHERE posting_id = :posting_id", [
            ':description' => $_POST['description'],
            ':requirements' => $_POST['requirements'],
            ':posting_id' => $_GET['id'],
        ]);
    }
}

$job = $db->query('SELECT
j.*,
p.*
FROM jobpostings j INNER JOIN prerequisites p on p.posting_id = j.posting_id
WHERE p.posting_id = :posting_id', [
    ':posting_id' => $_GET['id'],
])->fetch();

$dept = $usm->query("SELECT * FROM departments WHERE department_id = :department_id", [
    ':department_id' => $job['department_id'],
])->fetch();

// dd($dept);

$applications = $db->query("SELECT * FROM applicants")->fetchAll();

require 'views/hr/job-details.view.php';
