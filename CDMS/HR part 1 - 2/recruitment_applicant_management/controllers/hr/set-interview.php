<?php
session_start();
$heading = 'APPLICANTS';
$config = require 'config.php';
$db = new Database($config['database']);
$usm = new Database($config['usm']);

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
            ':status' => 'initial-interview',
            ':updated_by' => $_SESSION['user_id'],
            ':applicant_id' =>  $_POST['applicant_id'],
        ]);

        $db->query("INSERT INTO notifications (applicant_id, message, type, `for`, title) VALUES (:applicant_id, :message, :type, :for, :title)", [
            ':applicant_id' => $_POST['applicant_id'],
            ':title' => 'Initial interview Scheduled !',
            ':message' => "Dear Applicant, We are pleased to inform you that your initial interview has been scheduled on " . $_POST['date'] . " at " . $_POST['time'] . ". Please ensure you are prepared and available at the specified time. We look forward to discussing your qualifications and potential with us.
            Thank you, ",
            ':type' => 'Initial',
            ':for' => 'applicant',
        ]);

        $applicant = $db->query('SELECT first_name,last_name FROM applicants WHERE applicant_id = :applicant_id', [
            'applicant_id' => $_GET['id'],
        ])->fetch();

        $db->query("INSERT INTO notifications (applicant_id, message, type, `for`, title) VALUES (:applicant_id, :message, :type, :for, :title)", [
            ':applicant_id' => $_POST['applicant_id'],
            ':title' => 'Initial interview Scheduled !',
            ':message' => "An initial interview was scheduled by {$_SESSION['username']} for applicant: {$applicant['first_name']} {$applicant['last_name']} on  {$_POST['date']} at {$_POST['time']}.",
            ':type' => 'Initial',
            ':for' => 'admin',
        ]);

        $usm->query("INSERT INTO department_transaction (department_id, user_id, transaction_type, description, department_affected, module_affected) VALUES (:department_id, :user_id, :transaction_type, :description, :department_affected, :module_affected)", [
            "department_id" => 1,
            "user_id" => $_SESSION['user_id'],
            "transaction_type" => 'initial interview',
            "description" => "Recruiter: {$_SESSION['username']} scheduled an initial interview for applicant: {$applicant['first_name']} {$applicant['last_name']} on {$_POST['date']} at {$_POST['time']}",
            "department_affected" => 'HR part 1&2',
            "module_affected" => 'recruitment and applicant management',
        ]);

        $usm->query("INSERT INTO department_audit_trail (department_id, user_id, action, description, department_affected, module_affected) VALUES (:department_id, :user_id, :action, :description, :department_affected, :module_affected)", [
            "department_id" => 1,
            "user_id" => $_SESSION['user_id'],
            "action" => 'create',
            "description" => "An initial interview was scheduled by {$_SESSION['username']} for applicant: {$applicant['first_name']} {$applicant['last_name']}",
            "department_affected" => 'HR part 1&2',
            "module_affected" => 'recruitment and applicant management',
        ]);

        header('Location: /hr/applicants-interview');
        exit();
    }
}

$applicant = $db->query('SELECT * FROM applicants WHERE applicant_id = :applicant_id', [
    'applicant_id' => $_GET['id'],
])->fetch();

require 'views/hr/set-interview.view.php';
