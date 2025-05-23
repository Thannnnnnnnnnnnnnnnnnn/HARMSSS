<?php

session_start();

$heading = 'INTERVIEWS';
$config = require 'config.php';
$db = new Database($config['database']);

$success = false;
$rejected = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['pass'] ?? '' == true) {
        // dd($_POST);
        $db->query('UPDATE applicationstatus SET status = :status, updated_by = :updated_by WHERE applicant_id = :applicant_id', [
            ':status' => 'final interview passed',
            ':updated_by' => $_SESSION['user_id'],
            ':applicant_id' => $_POST['applicant_id'],
        ]);
        $db->query('UPDATE interviewschedules SET interview_status = :interview_status WHERE schedule_id = :schedule_id', [
            ':interview_status' => 'done',
            ':schedule_id' => $_POST['schedule_id'],
        ]);
        header('location: /hr_hiring/interview-history');
    } elseif ($_POST['reject'] ?? '' == true) {
        $db->query('UPDATE applicationstatus SET status = :status, updated_by = :updated_by WHERE applicant_id = :applicant_id', [
            ':status' => 'rejected',
            ':updated_by' => $_SESSION['user_id'],
            ':applicant_id' => $_POST['applicant_id'],
        ]);
        $db->query('UPDATE interviewschedules SET interview_status = :interview_status WHERE schedule_id = :schedule_id', [
            ':interview_status' => 'done',
            ':schedule_id' => $_POST['schedule_id'],
        ]);
        header('location: /hr_hiring/interview-history');
    }
};

$interviews = $db->query("SELECT 
interviewschedules.*, 
applicants.applicant_id,
applicants.first_name, 
applicants.last_name, 
applicationstatus.status
FROM interviewschedules 
INNER JOIN applicants ON interviewschedules.applicant_id = applicants.applicant_id
INNER JOIN applicationstatus ON applicants.applicant_id = applicationstatus.applicant_id
WHERE applicationstatus.status = :status
AND interviewschedules.interview_type = :interview_type
AND interviewschedules.interview_status = :interview_status
ORDER BY created_at DESC", [
    ':status' => 'final-interview',
    ':interview_type' => 'final',
    ':interview_status' => 'ongoing'
])->fetchAll();

// dd($interviews);

require 'views/hr_hiring/applicants-interview.view.php';
