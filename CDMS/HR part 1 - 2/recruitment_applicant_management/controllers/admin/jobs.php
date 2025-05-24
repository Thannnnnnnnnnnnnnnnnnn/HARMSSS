<?php

session_start();
$heading = 'Job Postings';
$config = require '../../config.php';
require '../../Database.php';
$db = new Database($config['database']);
// $usm = new Database($config['usm']);

$errors = [];
$success = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        validate('job_title', $errors);
        validate('location', $errors);
        validate('employment_type', $errors);
        validate('salary', $errors);
        validate('company', $errors);
        if ($errors) {
            throw new Exception('all fields are required !');
        } else {
            $db->query("INSERT INTO jobpostings (job_title,location,employment_type,salary,company,posted_by) VALUES (:job_title,:location,:employment_type,:salary,:company,:posted_by)", [
                ':job_title' => $_POST['job_title'],
                ':location' => $_POST['location'],
                ':employment_type' => $_POST['employment_type'],
                ':salary' => $_POST['salary'],
                ':company' => $_POST['company'],
                ':posted_by' => $_POST['posted_by'],
            ]);
            $job_id = $db->pdo->lastInsertId();
            $db->query("INSERT INTO prerequisites (posting_id,description,requirements) VALUES (:posting_id,:description,:requirements)", [
                ':posting_id' => $job_id,
                ':description' => $_POST['description'],
                ':requirements' => $_POST['requirements'],
            ]);
            // $usm->query("INSERT INTO department_audit_trail (department_id, user_id, action, description, department_affected, module_affected) VALUES (:department_id, :user_id, :action, :description, :department_affected, :module_affected)", [
            //     ':department_id' => 1,
            //     ':user_id' => $_SESSION['user_id'],
            //     ':action' => 'create',
            //     ':description' => "admin: {$_SESSION['username']} created a new job posting",
            //     ':department_affected' => 'HR part 1&2',
            //     ':module_affected' => 'recruitement and applicant management',
            // ]);
            // $usm->query("INSERT INTO department_transaction (department_id, user_id, transaction_type, description, department_affected, module_affected) VALUES (:department_id, :user_id, :transaction_type, :description, :department_affected, :module_affected)", [
            //     ':department_id' => 1,
            //     ':user_id' => $_SESSION['user_id'],
            //     ':transaction_type' => 'job posting creation',
            //     ':description' => "admin: {$_SESSION['username']} created a new job posting. Position: {$_POST['job_title']}, Location: {$_POST['location']}",
            //     ':department_affected' => 'HR part 1&2',
            //     ':module_affected' => 'recruitement and applicant management',
            // ]);
            $success = true;
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}


$postings = $db->query('SELECT * FROM jobpostings
-- u.username,
-- u.user_id
-- FROM jobpostings j INNER JOIN user_accounts u on u.user_id = j.posted_by 
ORDER BY created_at desc')->fetchAll();

require '../../views/admin/jobs.view.php';
