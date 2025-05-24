<?php

session_start();
$heading = 'Job Postings';
$config = require '../../config.php';
require '../../Database.php';
require '../../functions.php';
$db = new Database($config['database']);
$nhoes = new Database($config['nhoes']);
// dd($nhoes);
// $usm = new Database($config['usm']);
$errors = [];
$success = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // dd($_POST);
    try {
        validate('job_title', $errors);
        validate('location', $errors);
        validate('employment_type', $errors);
        validate('salary', $errors);
        validate('company', $errors);
        if ($errors) {
            throw new Exception('all fields are required !');
        } else {
            $db->query("INSERT INTO jobpostings (job_title,location,employment_type,salary,company, department_id, posted_by) VALUES (:job_title,:location,:employment_type,:salary,:company, :department_id, :posted_by)", [
                ':job_title' => $_POST['job_title'],
                ':location' => $_POST['location'],
                ':employment_type' => $_POST['employment_type'],
                ':salary' => $_POST['salary'],
                ':company' => $_POST['company'],
                ':department_id' => $_POST['department'],
                ':posted_by' => $_POST['posted_by'],
            ]);
            $job_id = $db->pdo->lastInsertId();
            $db->query("INSERT INTO prerequisites (posting_id,description,requirements) VALUES (:posting_id,:description,:requirements)", [
                ':posting_id' => $job_id,
                ':description' => $_POST['description'],
                ':requirements' => $_POST['requirements'],
            ]);
            $success = true;
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}


$postings = $db->query('SELECT * FROM jobpostings ORDER BY created_at desc')->fetchAll();
$departments = $nhoes->query('SELECT * FROM departments')->fetchAll();
require '../../views/admin/jobs.view.php';
