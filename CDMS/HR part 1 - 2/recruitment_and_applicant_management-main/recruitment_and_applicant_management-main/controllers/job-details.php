<?php

session_start();

$heading = 'Job';
$config = require 'config.php';
$db = new Database($config['database']);
$usm = new Database($config['usm']);

$job = $db->query('SELECT
j.*,
p.*
FROM jobpostings j INNER JOIN prerequisites p on p.posting_id = j.posting_id
WHERE j.posting_id = :posting_id', [
    ':posting_id' => $_GET['id'],
])->fetch();

// dd($job);
$dept = $usm->query("SELECT * FROM departments WHERE department_id = :department_id", [
    ':department_id' => $job['department_id'],
])->fetch();
// dd($dept);

$postings = $db->query('SELECT * FROM jobpostings ORDER BY created_at desc')->fetchAll();
$applications = $db->query('SELECT * from applicants where user_id = :user_id', [
    'user_id' => $_SESSION['user_id']
])->fetchAll();

$applications = $db->query("SELECT * FROM applicants")->fetchAll();

// dd($applications);

require 'views/job-details.view.php';
