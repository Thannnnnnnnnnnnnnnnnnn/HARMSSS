<?php
session_start();
$heading = 'Applicants';
$config = require '../../config.php';
require '../../Database.php';
require '../../functions.php';
$db = new Database($config['database']);
// $usm = new Database($config['usm']);
$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['approve'] === 'true') {
        // dd($_POST);
        $db->query("UPDATE applicationstatus SET status = :status WHERE applicant_id = :applicant_id", [
            ':status' => 'approved',
            ':applicant_id' => $_POST['applicant_id'],
        ]);

        header('location: interview_schedules-create.php');
        exit();
    }
    if ($_POST['reject'] === 'true') {
        // dd($_POST);
        $db->query("UPDATE applicationstatus SET status = :status WHERE applicant_id = :applicant_id", [
            ':status' => 'rejected',
            ':applicant_id' => $_POST['applicant_id'],
        ]);
    }
}

$applicants = $db->query("SELECT
a.*,
s.status
FROM applicants a inner join applicationstatus s on a.applicant_id = s.applicant_id
WHERE s.status != 'hired'
ORDER BY created_at DESC 
")->fetchAll();
// dd($applicants);
$newhires = $db->query("SELECT
a.*,
s.status
FROM applicants a inner join applicationstatus s on a.applicant_id = s.applicant_id
WHERE s.status = 'hired'
ORDER BY created_at DESC 
")->fetchAll();
// dd($newhires);
require '../../views/admin/applicants.view.php';
