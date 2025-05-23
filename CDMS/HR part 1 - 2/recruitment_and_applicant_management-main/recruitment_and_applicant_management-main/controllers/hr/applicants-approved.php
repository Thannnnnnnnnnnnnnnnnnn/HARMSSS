<?php
session_start();

$heading = 'APPROVED APPLICANTS';
$config = require 'config.php';
$db = new Database($config['database']);

$applications = $db->query("SELECT * FROM applicants")->fetchAll();

$applicants = $db->query("SELECT  
a.applicant_id,
a.first_name,
a.last_name,
a.contact_number,
a.address,
a.email,
a.resume,
s.status,
s.updated_by
FROM applicants a INNER JOIN applicationstatus s 
ON a.applicant_id = s.applicant_id
WHERE s.status = 'approved' 
AND s.updated_by = :updated_by
ORDER BY a.applicant_id;
", [
    ':updated_by' => $_SESSION['user_id']
])->fetchAll();


require 'views/hr/applicants-approved.view.php';
