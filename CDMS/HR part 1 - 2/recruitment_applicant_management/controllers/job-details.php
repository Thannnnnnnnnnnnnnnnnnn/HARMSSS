<?php

session_start();

$heading = 'Job';
require '../functions.php';
$config = require '../config.php';
require '../Database.php';
$db = new Database($config['database']);
$nhoes = new Database($config['nhoes']);

$job = $db->query('SELECT
j.*,
p.*
FROM jobpostings j INNER JOIN prerequisites p on p.posting_id = j.posting_id
WHERE j.posting_id = :posting_id', [
    ':posting_id' => $_GET['id'],
])->fetch();

// dd($job);
$dept = $nhoes->query("SELECT * FROM departments WHERE dept_id = :dept_id", [
    ':dept_id' => $job['department_id'],
])->fetch();
// dd($dept);

$postings = $db->query('SELECT * FROM jobpostings ORDER BY created_at desc')->fetchAll();
// $applications = $db->query('SELECT * from applicants where user_id = :user_id', [
//     'user_id' => $_SESSION['user_id']
// ])->fetchAll();

$applications = $db->query("SELECT * FROM applicants")->fetchAll();

// dd($applications);

require '../views/job-details.view.php';
