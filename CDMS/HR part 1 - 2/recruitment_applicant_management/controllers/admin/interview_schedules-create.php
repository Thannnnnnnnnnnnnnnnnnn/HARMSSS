<?php
session_start();
$heading = 'Interview Schedules Create';
$config = require 'config.php';
$db = new Database($config['database']);

$applicants = $db->query("
    SELECT a.applicant_id, a.first_name, a.last_name
    FROM applicants a
    LEFT JOIN interviewschedules i ON a.applicant_id = i.applicant_id
    WHERE i.applicant_id IS NULL
")->fetchAll();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    validate('date', $errors);
    validate('time', $errors);
    validate('location', $errors);
    validate('mode', $errors);
    validate('interview_type', $errors);
    validate('interview_status', $errors);
    validate('applicant_id', $errors);

    if (!empty($errors)) {
        $error = true;
    }
    if (empty($errors)) {
        $db->query("INSERT INTO interviewschedules (date, time, location, mode, interview_type, interview_status, applicant_id, interviewer_id)
                VALUES (:date, :time, :location, :mode, :interview_type, :interview_status, :applicant_id, :interviewer_id)
            ", [
            'date' => $_POST['date'],
            'time' => $_POST['time'],
            'location' => $_POST['location'],
            'mode' => $_POST['mode'],
            'interview_type' => $_POST['interview_type'],
            'interview_status' => $_POST['interview_status'],
            'applicant_id' => $_POST['applicant_id'],
            'interviewer_id' => $_POST['interviewer_id']
        ]);
        $usm->query("INSERT INTO department_transaction (department_id, user_id, transaction_type, description, department_affected, module_affected) VALUES (:department_id, :user_id, :transaction_type, :description, :department_affected, :module_affected)", [
            ':department_id' => 1,
            ':user_id' => $_SESSION['user_id'],
            ':transaction_type' => 'set interview schedule',
            ':description' => "admin: {$_SESSION['username']} set an interview schedule dated on {$_POST['date']} at {$_POST['time']} for applicant: {$_POST['applicant_id']}",
            ':department_affected' => 'HR part 1&2',
            ':module_affected' => 'recruitment and applicant management',
        ]);
        $usm->query("INSERT INTO department_audit_trail (department_id, user_id, action, description, department_affected, module_affected) VALUES (:department_id, :user_id, :action, :description, :department_affected, :module_affected)", [
            ':department_id' => 1,
            ':user_id' => $_SESSION['user_id'],
            ':action' => 'create',
            ':description' => "admin: {$_SESSION['username']} created an interview schedule for applicant: {$_POST['applicant_id']}",
            ':department_affected' => 'HR part 1&2',
            ':module_affected' => 'recruitment and applicant management',
        ]);
        $success = true;
    }
}

require 'views/admin/interview_schedules-create.view.php';
