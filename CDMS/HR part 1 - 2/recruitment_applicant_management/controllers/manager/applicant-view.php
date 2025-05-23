<?php

session_start();
$heading = 'APPLICANT';
$config = require 'config.php';
$db = new Database($config['database']);

$applicant = $db->query('SELECT 
applicants.*,
documents.*
FROM applicants INNER JOIN documents on documents.applicant_id = applicants.applicant_id
WHERE applicants.applicant_id = :applicant_id', [
    'applicant_id' => $_GET['id'],
])->fetch();

$interviews = $db->query('SELECT * FROM interviewschedules WHERE applicant_id = :applicant_id', [
    'applicant_id' => $_GET['id'],
])->fetchAll();
// dd($interviews);
require 'views/manager/applicant-view.view.php';
