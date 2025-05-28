<?php
session_start();
$heading = 'Dashboard';
require '../../Database.php';
$config = require '../../config.php';
$db = new Database($config['database']);

$totalApplicants = $db->query("SELECT COUNT(*) AS total FROM applicants")->fetch()['total'];

$jobs = $db->query("
    SELECT 
        jobpostings.*, 
        (SELECT COUNT(*) FROM applicants WHERE applicants.posting_id = jobpostings.posting_id) AS applicant_count
    FROM jobpostings
")->fetchAll();

$recentApplicants = $db->query("
    SELECT applicants.*, 
    jobpostings.job_title AS job_title
    FROM applicants
    JOIN jobpostings ON applicants.posting_id = jobpostings.posting_id
    ORDER BY created_at DESC
    LIMIT 5
")->fetchAll();
$totalJobPostings = $db->query("SELECT COUNT(*) AS total FROM jobpostings")->fetch()['total'];

$totalOngoingInterviews = $db->query("
    SELECT COUNT(*) AS total 
    FROM interviewschedules
    WHERE interview_status = 'ongoing' 
")->fetch()['total'];

$totalDoneInterviews = $db->query("
    SELECT COUNT(*) AS total 
    FROM interviewschedules
    WHERE interview_status = 'done'
")->fetch()['total'];

$totalNewHireInterviews = $db->query("
    SELECT
    a.*,
    s.status
    FROM applicants a
    INNER JOIN applicationstatus s ON s.applicant_id = a.applicant_id
    WHERE s.status = 'hired'
")->fetchAll();
// dd($_SESSION);
require '../../views/admin/index.view.php';
