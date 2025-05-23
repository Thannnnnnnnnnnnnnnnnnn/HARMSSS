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

// dd($applicant);

require 'views/hr_hiring/applicant-view.view.php';
