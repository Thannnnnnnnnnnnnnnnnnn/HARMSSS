<?php

session_start();
$heading = 'Interview Schedules';
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
    validate('interview_status', $errors);
    if (empty($errors)) {
        if ($_POST['update'] ?? '' == true) {
            $db->query("UPDATE interviewschedules SET date = :date, time = :time, location = :location, mode = :mode, interview_type = :interview_type, interview_status = :interview_status WHERE schedule_id = :schedule_id", [
                ':date' => $_POST['date'],
                ':time' => $_POST['time'],
                ':location' => $_POST['location'],
                ':mode' => $_POST['mode'],
                ':interview_type' => $_POST['interview_type'],
                ':interview_status' => $_POST['interview_status'],
                ':schedule_id' => $_POST['schedule_id'],
            ]);

            $usm->query("INSERT INTO department_audit_trail (department_id, user_id, action, description, department_affected, module_affected) VALUES (:department_id, :user_id, :action, :description, :department_affected, :module_affected)", [
                ':department_id' => 1,
                ':user_id' => $_SESSION['user_id'],
                ':action' => 'update',
                ':description' => "Updated interview schedule with the schedule ID: {$_POST['schedule_id']} for applicant: {$_POST['first_name']}",
                ':department_affected' => 'HR part 1&2',
                ':module_affected' => 'recruitment and applicant management',
            ]);
            $updated = true;
        }
    }
    if ($_POST['delete'] ?? '' == true) {
        $db->query("DELETE FROM interviewschedules WHERE schedule_id = :schedule_id", [
            ':schedule_id' => $_POST['id'],
        ]);

        $usm->query("INSERT INTO department_audit_trail (department_id, user_id, action, description, department_affected, module_affected) VALUES (:department_id, :user_id, :action, :description, :department_affected, :module_affected)", [
            ':department_id' => 1,
            ':user_id' => $_SESSION['user_id'],
            ':action' => 'delete',
            ':description' => "admin: {$_SESSION['username']} Deleted an applicant with the applicant ID: {$_POST['applicant_id']}",
            ':department_affected' => 'HR part 1&2',
            ':module_affected' => 'recruitment and applicant management',
        ]);
        $deleted = true;
    }
}

$schedules = $db->query("SELECT
s.*,
a.first_name,
i.username
FROM interviewschedules s INNER JOIN applicants a on s.applicant_id = a.applicant_id
INNER JOIN user_accounts i on i.user_id = s.interviewer_id
ORDER BY created_at DESC
")->fetchAll();

require 'views/admin/interview_schedules.view.php';
