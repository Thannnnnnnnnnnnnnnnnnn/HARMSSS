<?php
session_start();
$heading = 'Applicant Details';
$config = require '../../config.php';
require '../../Database.php';
$db = new Database($config['database']);


$applicant = $db->query("SELECT 
a.*,
d.*,
s.status,
j.job_title
from applicants a INNER JOIN documents d on d.applicant_id = a.applicant_id
INNER JOIN applicationstatus s on s.applicant_id = a.applicant_id
INNER JOIN jobpostings j on j.posting_id = a.posting_id
WHERE a.applicant_id = :applicant_id
", [
    ':applicant_id' => $_GET['id']
])->fetch();

require '../../views/admin/applicant.view.php';
