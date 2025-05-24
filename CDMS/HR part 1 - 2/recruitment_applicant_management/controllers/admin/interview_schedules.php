<?php

session_start();
$heading = 'Interview Schedules';
$config = require '../../config.php';
require '../../functions.php';
require '../../Database.php';
$db = new Database($config['database']);
// $usm = new Database($config['usm']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    validate('date', $errors);
    validate('time', $errors);
    validate('location', $errors);
    validate('mode', $errors);
    validate('interview_type', $errors);
    validate('interview_status', $errors);
    if (empty($errors)) {
        if ($_POST['pass'] ?? '' === true) {
            $db->query("UPDATE interviewschedules SET interview_status = :interview_status WHERE schedule_id = :schedule_id", [
                ':interview_status' => 'passed',
                ':schedule_id' => $_POST['schedule_id'],
            ]);
            $db->query("UPDATE applicationstatus SET status = :status WHERE schedule_id = :schedule_id", [
                ':interview_status' => 'initial interview passed',
                ':schedule_id' => $_POST['schedule_id'],
            ]);
            // $updated = true;
        }
    }
    if ($_POST['fail'] ?? '' === true) {
        $db->query("UPDATE interviewschedules SET interview_status = :interview_status WHERE schedule_id = :schedule_id", [
            ':interview_status' => 'failed',
            ':schedule_id' => $_POST['schedule_id'],
        ]);

        $db->query("UPDATE applicationstatus SET status = :status WHERE schedule_id = :schedule_id", [
            ':interview_status' => 'initial interview failed',
            ':schedule_id' => $_POST['schedule_id'],
        ]);
        // $deleted = true;
    }
}

$initial_schedules = $db->query("SELECT
s.*,
a.first_name
FROM interviewschedules s INNER JOIN applicants a on s.applicant_id = a.applicant_id
WHERE s.interview_status = 'pending'
AND s.interview_type = 'initial'
ORDER BY created_at DESC
")->fetchAll();

$final_schedules = $db->query("SELECT
s.*,
a.first_name
FROM interviewschedules s INNER JOIN applicants a on s.applicant_id = a.applicant_id
WHERE s.interview_status = 'pending'
AND s.interview_type = 'final'
ORDER BY created_at DESC
")->fetchAll();
// dd($initial_schedules);

require '../../views/admin/interview_schedules.view.php';
