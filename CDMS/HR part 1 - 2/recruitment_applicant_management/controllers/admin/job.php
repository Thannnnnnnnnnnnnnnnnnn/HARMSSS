<?php
session_start();
$heading = 'Job Posting';
$config = require '../../config.php';
require '../../Database.php';
$db = new Database($config['database']);
$usm = new Database($config['usm']);

$errors = [];
// $delete = true;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete']) && $_POST['delete'] == true) {
        $jobposting = $db->query("SELECT * FROM jobpostings WHERE posting_id = :posting_id", [
            ':posting_id' => $_POST['id']
        ])->fetch();

        $db->query("DELETE FROM jobpostings WHERE posting_id = :posting_id", [
            ':posting_id' => $_POST['id']
        ]);

        $usm->query("INSERT INTO department_audit_trail (department_id, user_id, action, description, department_affected, module_affected) VALUES (:department_id, :user_id, :action, :description, :department_affected, :module_affected)", [
            ':department_id' => 1,
            ':user_id' => $_SESSION['user_id'],
            ':action' => 'delete',
            ':description' => "admin: {$_SESSION['username']} just deleted a job posting with the ID of: {$_POST['id']}",
            ':department_affected' => 'HR part 1&2',
            ':module_affected' => 'recruitement and applicant management',
        ]);

        $usm->query("INSERT INTO department_transaction (department_id, user_id, transaction_type, description, department_affected, module_affected) VALUES (:department_id, :user_id, :transaction_type, :description, :department_affected, :module_affected)", [
            ':department_id' => 1,
            ':user_id' => $_SESSION['user_id'],
            ':transaction_type' => 'job posting deletion',
            ':description' => "admin: {$_SESSION['username']} deleted a job posting. Position: {$_POST['job_title']}, Location: {$_POST['location']}, Employment type: {$_POST['employment_type']}",
            ':department_affected' => 'HR part 1&2',
            ':module_affected' => 'recruitement and applicant management',
        ]);
        header('location: /admin/jobs');
        exit();
    }

    try {
        validate('job_title', $errors);
        validate('company', $errors);
        validate('description', $errors);
        validate('location', $errors);
        validate('salary', $errors);
        validate('requirements', $errors);
        validate('employment_type', $errors);
        if (!empty($errors)) {
            dd($errors);
            throw new Exception('Validation errors occurred.');
        }
        if (empty($errors)) {
            $db->query('UPDATE jobpostings SET job_title = :job_title, company = :company, location = :location, salary = :salary, employment_type = :employment_type WHERE posting_id = :posting_id', [
                ':job_title' => $_POST['job_title'],
                ':company' => $_POST['company'],
                ':location' => $_POST['location'],
                ':salary' => $_POST['salary'],
                ':employment_type' => $_POST['employment_type'],
                ':posting_id' => $_GET['id']
            ]);

            $db->query('UPDATE prerequisites SET description = :description, requirements = :requirements WHERE posting_id = :posting_id', [
                ':description' => $_POST['description'],
                ':requirements' => $_POST['requirements'],
                ':posting_id' => $_GET['id']
            ]);

            $usm->query("INSERT INTO department_audit_trail (department_id, user_id, action, description, department_affected, module_affected) VALUES (:department_id, :user_id, :action, :description, :department_affected, :module_affected)", [
                ':department_id' => 1,
                ':user_id' => $_SESSION['user_id'],
                ':action' => 'update',
                ':description' => "admin: {$_SESSION['username']} updated a job posting",
                ':department_affected' => 'HR part 1&2',
                ':module_affected' => 'recruitement and applicant management',
            ]);

            $usm->query("INSERT INTO department_transaction (department_id, user_id, transaction_type, description, department_affected, module_affected) VALUES (:department_id, :user_id, :transaction_type, :description, :department_affected, :module_affected)", [
                ':department_id' => 1,
                ':user_id' => $_SESSION['user_id'],
                ':transaction_type' => 'job posting update',
                ':description' => "admin: {$_SESSION['username']} updated a job posting. Position: {$_POST['job_title']}, Location: {$_POST['location']}",
                ':department_affected' => 'HR part 1&2',
                ':module_affected' => 'recruitement and applicant management',
            ]);

            $success = true;
        }
    } catch (Exception $e) {
        $errors[] = 'An error occurred while updating the job posting. Please try again later.';
    }
}

$job = $db->query('SELECT 
j.*,
u.username,
u.user_id,
p.*
FROM jobpostings j INNER JOIN user_accounts u on u.user_id = j.posted_by 
INNER JOIN prerequisites p on p.posting_id = j.posting_id
WHERE j.posting_id = :posting_id', [
    ':posting_id' => $_GET['id'],
])->fetch();
require 'views/admin/job.view.php';
