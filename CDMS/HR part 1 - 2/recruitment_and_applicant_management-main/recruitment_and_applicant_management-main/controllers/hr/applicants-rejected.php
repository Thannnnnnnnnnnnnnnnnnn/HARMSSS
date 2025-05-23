<?php
session_start();
$heading = 'APPLICANTS REJECTED';
$config = require 'config.php';
$db = new Database($config['database']);

$applicants = $db->query("SELECT 
a.*,
s.status,
j.job_title
from applicants a INNER JOIN applicationstatus s ON a.applicant_id = s.applicant_id
INNER JOIN jobpostings j ON a.posting_id = j.posting_id
WHERE s.status = :status", [
    ':status' => 'rejected'
])->fetchAll();
// dd($applicants);

require 'views/hr/applicants-rejected.view.php';
