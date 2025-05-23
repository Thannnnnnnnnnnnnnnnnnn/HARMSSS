<?php
session_start();
$heading = 'APPLICANTS';
$config = require 'config.php';
$db = new Database($config['database']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    validate('date', $errors);
    validate('time', $errors);
    validate('location', $errors);
    validate('mode', $errors);

    if (empty($errors)) {
        $db->query("INSERT INTO interviewschedules(applicant_id, interviewer_id, date, time, location, mode, interview_status, interview_type) VALUES(:applicant_id, :interviewer_id, :date, :time, :location, :mode, :interview_status, :interview_type)", [
            ':applicant_id' => $_POST['applicant_id'],
            ':interviewer_id' => $_POST['user_id'],
            ':date' => $_POST['date'],
            ':time' => $_POST['time'],
            ':location' => $_POST['location'],
            ':mode' => $_POST['mode'],
            ':interview_status' => 'ongoing',
            ':interview_type' => 'final',
        ]);

        $db->query('UPDATE applicationstatus SET status = :status, updated_by =:updated_by WHERE applicant_id = :applicant_id', [
            ':status' => 'final-interview',
            ':updated_by' => $_SESSION['user_id'],
            ':applicant_id' =>  $_POST['applicant_id'],
        ]);

        $db->query("INSERT INTO notifications (applicant_id, message, title, type, `for`) VALUES (:applicant_id, :message, :title, :type ,:for)", [
            ':applicant_id' => $_POST['applicant_id'],
            ':title' => 'final interview scheduled!',
            ':type' => 'final',
            ':message' => "Your final interview is scheduled for " . $_POST['date'] . ' at ' . $_POST['time'],
            ':for' => 'applicant'
        ]);

        header('Location: /hr_hiring/applicants-interview');
        exit();
    }
}

$applicants = $db->query(
    'SELECT 
applicants.*,
applicationstatus.status,
jobpostings.job_title,
jobpostings.posted_by
FROM applicants 
INNER JOIN applicationstatus ON applicants.applicant_id = applicationstatus.applicant_id 
INNER JOIN jobpostings ON applicants.posting_id = jobpostings.posting_id
WHERE status = "initial-interview passed"
ORDER BY created_at desc',
)->fetchAll();


require 'views/hr_hiring/applicants.view.php';
