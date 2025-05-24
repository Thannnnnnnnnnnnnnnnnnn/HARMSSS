<?php
session_start();
$heading = 'Applicants';
$config = require '../../config.php';
require '../../Database.php';
$db = new Database($config['database']);
$usm = new Database($config['usm']);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $db->query("DELETE FROM applicants WHERE applicant_id = :applicant_id", [
            ':applicant_id' => $_POST['id'],
        ]);

        $usm->query("INSERT INTO department_audit_trail (department_id, user_id, action, description, department_affected, module_affected) VALUES (:department_id, :user_id, :action, :description, :department_affected, :module_affected)", [
            ':department_id' => 1,
            ':user_id' => $_SESSION['user_id'],
            ':action' => 'delete',
            ':description' => "admin: {$_SESSION['username']} Deleted an applicant with the applicant ID: {$_POST['applicant_id']}",
            ':department_affected' => 'HR part 1&2',
            ':module_affected' => 'recruitment and applicant management',
        ]);
        $delete = true;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $error = 'Error: Action could not be completed. Please contact support for assistance.';
        }
    }
}

$applicants = $db->query("SELECT
a.*,
s.status
FROM applicants a inner join applicationstatus s on a.applicant_id = s.applicant_id
WHERE s.status != 'hired'
ORDER BY created_at DESC 
")->fetchAll();

$newhires = $db->query("SELECT
a.*,
s.status
FROM applicants a inner join applicationstatus s on a.applicant_id = s.applicant_id
WHERE s.status = 'hired'
ORDER BY created_at DESC 
")->fetchAll();
require '../../views/admin/applicants.view.php';
