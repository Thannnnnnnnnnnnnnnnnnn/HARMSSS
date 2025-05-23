<?php

session_start();

$heading = 'Interview History';
$config = require 'config.php';
$db = new Database($config['database']);

$applicants = $db->query(
    'SELECT 
a.*,
i.*,
s.status,
j.job_title,
j.posted_by
FROM applicants a
INNER JOIN applicationstatus s ON a.applicant_id = s.applicant_id
INNER JOIN interviewschedules i ON a.applicant_id = i.applicant_id
INNER JOIN jobpostings j ON a.posting_id = j.posting_id
WHERE i.interview_status = "done" AND i.interview_type = "final"
-- AND s.status = "final interview passed" OR s.status = "rejected"
ORDER BY i.created_at desc',
)->fetchAll();
require 'views/hr_hiring/interview-history.view.php';
