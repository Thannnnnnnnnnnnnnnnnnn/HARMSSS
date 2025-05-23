<?php

session_start();

$heading = 'Interview schedule';
$config = require 'config.php';
$db = new Database($config['database']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];

    validate('date', $errors);
    validate('time', $errors);
    validate('location', $errors);
    validate('mode', $errors);
    validate('interview_type', $errors);

    if (empty($errors)) {
        $db->query("INSERT INTO interviewschedules(applicant_id, interviewer_id, date, time, location, mode, interview_type, interview_status) VALUES(:applicant_id, :interviewer_id, :date, :time, :location, :mode, :interview_type, :interview_status)", [
            ':applicant_id' => $_POST['applicant_id'],
            ':interviewer_id' => $_SESSION['user_id'],
            ':date' => $_POST['date'],
            ':time' => $_POST['time'],
            ':location' => $_POST['location'],
            ':mode' => $_POST['mode'],
            ':interview_type' => $_POST['interview_type'],
            ':interview_status' => 'ongoing',
        ]);

        $db->query('UPDATE applicationstatus SET status = :status, updated_by =:updated_by WHERE applicant_id = :applicant_id', [
            ':status' => 'final-interview',
            ':updated_by' => $_SESSION['user_id'],
            ':applicant_id' =>  $_POST['applicant_id'],
        ]);

        $db->query("INSERT INTO notifications (applicant_id, message, type, `for`, title) VALUES (:applicant_id, :message, :type, :for, :title)", [
            ':applicant_id' => $_POST['applicant_id'],
            ':type' => 'final',
            ':for' => 'applicant',
            ':title' => 'final interview Scheduled !',
            ':message' => "Dear Applicant, We are pleased to inform you that your final interview has been scheduled on " . $_POST['date'] . " at " . $_POST['time'] . ". Please ensure you are prepared and available at the specified time. We look forward to discussing your qualifications and potential with us.
Thank you, "
        ]);
        header('Location: /hr_hiring/applicants-interview');
        exit();
    }
}

$applicant = $db->query('SELECT * FROM applicants WHERE applicant_id = :applicant_id', [
    ':applicant_id' => $_GET['id'],
])->fetch();

$posting = $db->query('SELECT * FROM jobpostings WHERE posting_id = :posting_id', [
    ':posting_id' => $applicant['posting_id'],
])->fetch();

require 'views/hr_hiring/set-interview.view.php';
