<?php

session_start();

$heading = 'Job';
$config = require 'config.php';
$db = new Database($config['database']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    $updated = false;

    validate('job_title', $errors);
    validate('description', $errors);
    validate('location', $errors);
    validate('employment_type', $errors);
    validate('requirements', $errors);
    validate('salary', $errors);
    validate('company', $errors);
    if (empty($errors)) {
        $db->query("UPDATE jobpostings SET job_title = :job_title, description = :description, location = :location, employment_type = :employment_type, requirements = :requirements, salary = :salary, company = :company WHERE posting_id = :posting_id", [
            ':job_title' => $_POST['job_title'],
            ':description' => $_POST['description'],
            ':location' => $_POST['location'],
            ':employment_type' => $_POST['employment_type'],
            ':requirements' => $_POST['requirements'],
            ':salary' => $_POST['salary'],
            ':company' => $_POST['company'],
            ':posting_id' => $_GET['id'],
        ]);
        $updated = true;
    }
}

$job = $db->query('SELECT * from jobpostings WHERE posting_id = :posting_id', [
    ':posting_id' => $_GET['id'],
])->fetch();

require 'views/hr_hiring/job-details.view.php';
