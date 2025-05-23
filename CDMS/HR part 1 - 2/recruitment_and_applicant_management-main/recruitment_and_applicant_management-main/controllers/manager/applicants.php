<?php
session_start();
$heading = 'APPLICANTS';
$config = require 'config.php';
$db = new Database($config['database']);

$applicants = $db->query(
   'SELECT 
applicants.*,
applicationstatus.status,
jobpostings.job_title,
jobpostings.posted_by
FROM applicants 
INNER JOIN applicationstatus ON applicants.applicant_id = applicationstatus.applicant_id 
INNER JOIN jobpostings ON applicants.posting_id = jobpostings.posting_id
ORDER BY created_at desc',
)->fetchAll();


require 'views/manager/applicants.view.php';
