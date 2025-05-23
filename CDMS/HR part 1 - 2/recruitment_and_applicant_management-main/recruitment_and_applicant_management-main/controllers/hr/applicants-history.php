<?php

session_start();

$heading = 'INTERVIEWS';
$config = require 'config.php';
$db = new Database($config['database']);

// for notification
$applications = $db->query("SELECT * FROM applicants")->fetchAll();

$interviews = $db->query("SELECT 
i.*, 
a.applicant_id,
a.first_name, 
a.last_name, 
s.status
FROM interviewschedules i
INNER JOIN applicants a ON i.applicant_id = a.applicant_id
INNER JOIN applicationstatus s ON a.applicant_id = s.applicant_id
WHERE i.interview_type = 'initial'
ORDER BY created_at DESC")->fetchAll();

require 'views/hr/applicants-history.view.php';
