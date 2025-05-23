<?php
require 'vendor/autoload.php';
session_start();
$heading = 'HOME';
$config = require 'config.php';
$db = new Database($config['database']);

$postings = $db->query('SELECT * FROM jobpostings ORDER BY created_at desc')->fetchAll();

$applications = $db->query('SELECT
applicants.*,
interviewschedules.*,
applicationstatus.status
FROM applicants 
INNER JOIN interviewschedules on interviewschedules.applicant_id = applicants.applicant_id
INNER JOIN applicationstatus on applicants.applicant_id = applicationstatus.applicant_id
where user_id = :user_id', [
    'user_id' => $_SESSION['user_id']
])->fetchAll();

require 'views/home.view.php';
