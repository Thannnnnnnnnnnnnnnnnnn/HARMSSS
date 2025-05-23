<?php

session_start();

$heading = 'Job offer';
$config = require 'config.php';
$db = new Database($config['database']);
// dd($_SESSION);
$applicants = $db->query(
    'SELECT 
applicants.*,
applicationstatus.status,
jobpostings.job_title,
jobpostings.posted_by,
job_offer.user_decision
FROM applicants 
INNER JOIN applicationstatus ON applicants.applicant_id = applicationstatus.applicant_id 
INNER JOIN jobpostings ON applicants.posting_id = jobpostings.posting_id
INNER JOIN job_offer ON applicants.applicant_id = job_offer.applicant_id
ORDER BY created_at ASC',
)->fetchAll();

// dd($applicants);

require 'views/hr_hiring/applicants-offered.view.php';
