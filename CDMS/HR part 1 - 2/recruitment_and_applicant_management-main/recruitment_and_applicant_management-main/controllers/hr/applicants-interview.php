<?php

session_start();

$heading = 'INTERVIEWS';
$config = require 'config.php';
$db = new Database($config['database']);

$success = false;
$rejected = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $applicant = $db->query("SELECT 
    a.first_name,
    a.last_name
    FROM applicants a
    WHERE applicant_id = :applicant_id", [
        ':applicant_id' => $_POST['applicant_id']
    ])->fetch();
    if ($_POST['pass'] ?? '' == true) {
        $db->query("UPDATE applicationstatus SET status = :status, updated_by = :updated_by WHERE applicant_id = :applicant_id ", [
            ':status' => 'initial-interview passed',
            ':updated_by' => $_SESSION['user_id'],
            ':applicant_id' => $_POST['applicant_id']
        ]);
        $db->query('UPDATE interviewschedules SET interview_status = :interview_status WHERE schedule_id =:schedule_id', [
            ':interview_status' => 'done',
            ':schedule_id' => $_POST['schedule_id'],
        ]);
        $db->query("INSERT INTO notifications (title, message, status, applicant_id, `for`) VALUES (:title, :message, :status, :applicant_id, :for)", [
            'title' => 'New applicant alert!',
            'message' => $applicant['first_name'] . ' ' . $applicant['last_name'] . ' has passed the initial interview. Remarks: ' . $_POST['remarks'],
            ':status' => 'unread',
            ':applicant_id' => $_POST['applicant_id'],
            'for' => 'hiring manager'
        ]);

        header('Location: /hr/applicants-history');
        exit;
    }

    if ($_POST['reject'] ?? '' == true) {
        $db->query('UPDATE applicationstatus SET status = :status, updated_by = :updated_by WHERE applicant_id = :applicant_id', [
            ':status' => 'rejected',
            ':updated_by' => $_SESSION['user_id'],
            ':applicant_id' => $_POST['applicant_id'],
        ]);
        $db->query('UPDATE interviewschedules SET interview_status = :interview_status WHERE schedule_id =:schedule_id', [
            ':interview_status' => 'done',
            ':schedule_id' => $_POST['schedule_id'],
        ]);
        $db->query("INSERT INTO notifications (title, message, status, applicant_id, `for`) VALUES (:title, :message, :status, :applicant_id, :for)", [
            'title' => 'Application rejected',
            'message' => $_POST['remarks'],
            ':status' => 'unread',
            ':applicant_id' => $_POST['applicant_id'],
            'for' => 'applicant'
        ]);
        header('Location: /hr/applicants-history');
        exit;
    }
};

$applications = $db->query("SELECT * FROM applicants")->fetchAll();

$interviews = $db->query("SELECT 
i.*, 
a.applicant_id,
a.first_name, 
a.last_name, 
s.status
FROM interviewschedules i
INNER JOIN applicants a ON i.applicant_id = a.applicant_id
INNER JOIN applicationstatus s ON a.applicant_id = s.applicant_id
WHERE s.status = :status
ORDER BY created_at DESC", [
    ':status' => 'initial-interview'
])->fetchAll();

require 'views/hr/applicants-interview.view.php';
