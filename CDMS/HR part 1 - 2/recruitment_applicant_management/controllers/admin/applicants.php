<?php
session_start();
$heading = 'Applicants';
$config = require '../../config.php';
require '../../Database.php';
require '../../functions.php';
$db = new Database($config['database']);
$nhoes = new Database($config['nhoes']);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // dd($applicants);
    if ($_POST['approve'] ?? '' === 'true') {
        // dd($_POST);
        $db->query("UPDATE applicationstatus SET status = :status WHERE applicant_id = :applicant_id", [
            ':status' => 'approved',
            ':applicant_id' => $_POST['applicant_id'],
        ]);

        header('location: interview_schedules-create.php');
        exit();
    }
    if ($_POST['reject'] ?? '' === 'true') {
        // dd($_POST);
        $db->query("UPDATE applicationstatus SET status = :status WHERE applicant_id = :applicant_id", [
            ':status' => 'rejected',
            ':applicant_id' => $_POST['applicant_id'],
        ]);
    }
    if ($_POST['hire'] ?? '' === 'true') {
        // dd($_POST);
        $applicant = $db->query("SELECT
        a.*,
        s.status,
        j.department_id
        FROM applicants a inner join applicationstatus s on a.applicant_id = s.applicant_id
        INNER JOIN jobpostings j ON a.posting_id = j.posting_id
        WHERE a.applicant_id = :applicant_id", [
            ':applicant_id' => $_POST['applicant_id'],
        ])->fetch();
        dd($applicant);
        $db->query("UPDATE applicationstatus SET status = :status WHERE applicant_id = :applicant_id", [
            ':status' => 'hired',
            ':applicant_id' => $_POST['applicant_id'],
        ]);

        $nhoes->query("INSERT INTO employees (first_name, last_name, email, phone_number, DateOfBirth, AddressLine1, department, hire_date)
        VALUES (:first_name, :last_name, :email, :phone_number, :DateOfBirth, :AddressLine1, :department, :hire_date)", [
            ':first_name' => $applicants['first_name'],
            ':last_name' => $applicants['last_name'],
            ':email' => $applicants['email'],
            ':phone_number' => $applicants['phone_number'],
            ':DateOfBirth' => $applicants['date_of_birth'],
            ':AddressLine1' => $applicants['address'],
            ':department' => $applicants['department'],
            ':hire_date' => $applicants['updated_at'],
        ]);
    }
}

$applicants = $db->query("SELECT
a.*,
s.status,
j.department_id
FROM applicants a inner join applicationstatus s on a.applicant_id = s.applicant_id
INNER JOIN jobpostings j ON a.posting_id = j.posting_id
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
