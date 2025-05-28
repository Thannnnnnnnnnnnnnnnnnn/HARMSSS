<?php
session_start();
$heading = 'MY APPLICATIONS';
require '../functions.php';
$config = require '../config.php';
require '../Database.php';
$db = new Database($config['database']);
// $usm = new Database($config['usm']);
$updated = false;
// dd($_SESSION['pending']);
$applications = $db->query('SELECT
a.applicant_id,
a.first_name,
a.last_name,
a.contact_number,
a.age,
a.date_of_birth,
a.address,
a.email,
a.resume,
a.posting_id,
a.created_at,
j.job_title,
COALESCE(s.status, "Applied") AS status 
FROM applicants a
LEFT JOIN applicationstatus s ON a.applicant_id = s.applicant_id
INNER JOIN jobpostings j ON a.posting_id = j.posting_id
AND status != :status
WHERE a.email = :email
ORDER BY created_at desc', [
  ':status' => 'rejected',
  ':email' => $_SESSION['email'] ?? '',
])->fetchAll();

$h_applications = $db->query('SELECT
a.applicant_id,
a.first_name,
a.last_name,
a.contact_number,
a.age,
a.date_of_birth,
a.address,
a.email,
a.resume,
a.posting_id,
a.created_at,
j.job_title,
COALESCE(s.status, "Applied") AS status 
FROM applicants a
LEFT JOIN applicationstatus s ON a.applicant_id = s.applicant_id
INNER JOIN jobpostings j ON a.posting_id = j.posting_id
AND status = :status
WHERE a.email = :email
ORDER BY created_at desc', [
  ':status' => 'rejected',
  ':email' => $_SESSION['email'] ?? '',
])->fetchAll();

$interview = $db->query("SELECT 
  a.applicant_id,
  i.date,
  i.mode,
  i.location,
  j.job_title,
  s.status
FROM jobpostings j
INNER JOIN applicants a ON j.posting_id = a.posting_id
INNER JOIN interviewschedules i ON a.applicant_id = i.applicant_id
INNER JOIN applicationstatus s ON a.applicant_id = s.applicant_id
WHERE status = :status", [
  ':status' => 'initial-interview'
])->fetch();

require '../views/application.view.php';
