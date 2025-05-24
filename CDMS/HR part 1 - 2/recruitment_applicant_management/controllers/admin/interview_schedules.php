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
    // validate('date', $errors);
    // validate('time', $errors);
    // validate('location', $errors);
    // validate('mode', $errors);
    // validate('interview_type', $errors);
    // validate('interview_status', $errors);
    // dd($errors);
    if (empty($errors)) {
        // dd($_POST);

        if ($_POST['pass'] ?? '' === true) {
            $db->query("UPDATE interviewschedules SET interview_status = :interview_status WHERE schedule_id = :schedule_id", [
                ':interview_status' => 'passed',
                ':schedule_id' => $_POST['schedule_id'],
            ]);
            if ($_POST['interview_type'] === 'initial') {
                // dd('initial interview passed');
                $db->query("UPDATE applicationstatus SET status = :status WHERE applicant_id = :applicant_id", [
                    ':status' => 'initial interview passed',
                    ':applicant_id' => $_POST['applicant_id'],
                ]);
            } else {
                // dd('final interview passed');
                $db->query("UPDATE applicationstatus SET status = :status WHERE applicant_id = :applicant_id", [
                    ':status' => 'final interview passed',
                    ':applicant_id' => $_POST['applicant_id'],
                ]);
            }
            // $updated = true;
        }
    }
    if ($_POST['fail'] ?? '' === true) {
        $db->query("UPDATE interviewschedules SET interview_status = :interview_status WHERE schedule_id = :schedule_id", [
            ':interview_status' => 'failed',
            ':schedule_id' => $_POST['schedule_id'],
        ]);
        if ($_POST['interview_type'] === 'initial') {
            $db->query("UPDATE applicationstatus SET status = :status WHERE applicant_id = :applicant_id", [
                ':status' => 'initial interview failed',
                ':applicant_id' => $_POST['applicant_id'],
            ]);
        } else {
            $db->query("UPDATE applicationstatus SET status = :status WHERE applicant_id = :applicant_id", [
                ':status' => 'final interview failed',
                ':applicant_id' => $_POST['applicant_id'],
            ]);
        }
        // $deleted = true;
    }
}

$initial_schedules = $db->query("SELECT
i.*,
a.first_name
FROM interviewschedules i INNER JOIN applicants a on i.applicant_id = a.applicant_id
WHERE i.interview_status = 'pending'
AND i.interview_type = 'initial'
ORDER BY i.created_at DESC
")->fetchAll();

$final_schedules = $db->query("SELECT
i.*,
a.first_name
FROM interviewschedules i INNER JOIN applicants a on i.applicant_id = a.applicant_id
INNER JOIN applicationstatus s on i.applicant_id = s.applicant_id
WHERE i.interview_status = 'pending'
AND i.interview_type = 'final'
ORDER BY created_at DESC
")->fetchAll();

$done_schedules = $db->query("SELECT
i.*,
a.first_name
FROM interviewschedules i INNER JOIN applicants a on i.applicant_id = a.applicant_id
INNER JOIN applicationstatus s on i.applicant_id = s.applicant_id
WHERE i.interview_status != 'pending'
ORDER BY created_at DESC
")->fetchAll();
// dd(count($done_schedules));
// dd($initial_schedules);

require '../../views/admin/interview_schedules.view.php';
