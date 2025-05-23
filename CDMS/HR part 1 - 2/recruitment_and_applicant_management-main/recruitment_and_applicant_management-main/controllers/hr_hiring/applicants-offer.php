<?php

session_start();

$heading = 'Job offer';
$config = require 'config.php';
$db = new Database($config['database']);

$errors = [];
$success = false;
$update = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // dd($_POST);
    validate('position', $errors);
    validate('work_location', $errors);
    validate('salary', $errors);
    validate('schedule', $errors);
    validate('time_in', $errors);
    validate('time_out', $errors);
    validate('benefits', $errors);
    validate('responsibilities', $errors);

    if (empty($errors) && ($_POST['submit'] ?? '') == true) {
        $db->query('INSERT INTO job_offer (position, work_location, salary, schedule, time_in, time_out, benefits, responsibilities, user_decision, applicant_id, created_by, status) VALUES (:position, :work_location, :salary, :schedule, :time_in, :time_out, :benefits, :responsibilities, :user_decision, :applicant_id, :created_by, :status)', [
            ':position' => $_POST['position'],
            ':work_location' => $_POST['work_location'],
            ':salary' => $_POST['salary'],
            ':schedule' => $_POST['schedule'],
            ':time_in' => $_POST['time_in'],
            ':time_out' => $_POST['time_out'],
            ':benefits' => $_POST['benefits'],
            ':responsibilities' => $_POST['responsibilities'],
            ':user_decision' => 'offer-sent',
            ':applicant_id' => $_GET['id'],
            ':created_by' => $_SESSION['user_id'],
            ':status' => 'pending',
        ]);
        $db->query('UPDATE applicationstatus SET status = :status WHERE applicant_id = :applicant_id', [
            ':status' => 'job offered',
            ':applicant_id' => $_GET['id'],
        ]);
        $db->query("INSERT INTO notifications (message, status, type, `for`, title, applicant_id) VALUES(:message, :status, :type, :for, :title, :applicant_id)", [
            ':title' => 'Job Offer Request Confirmation!',
            ':message' => 'New Job request has been sent to you.',
            ':status' => 'unread',
            ':type' => 'job offer',
            ':for' => 'manager',
            ':applicant_id' => $_GET['id']
        ]);
        header('location: /hr_hiring/applicants-offered');
        exit();
    }
    if (empty($errors) && ($_POST['update'] ?? '') == true) {
        $db->query('UPDATE job_offer 
            SET position = :position, 
                work_location = :work_location, 
                salary = :salary, 
                schedule = :schedule, 
                time_in = :time_in, 
                time_out = :time_out, 
                benefits = :benefits, 
                responsibilities = :responsibilities 
            WHERE applicant_id = :applicant_id', [
            ':position' => $_POST['position'],
            ':work_location' => $_POST['work_location'],
            ':salary' => $_POST['salary'],
            ':schedule' => $_POST['schedule'],
            ':time_in' => $_POST['time_in'],
            ':time_out' => $_POST['time_out'],
            ':benefits' => $_POST['benefits'],
            ':responsibilities' => $_POST['responsibilities'],
            ':applicant_id' => $_GET['id'],
        ]);
    }
}

$applicant = $db->query(
    'SELECT 
a.*,
s.status,
j.job_title,
j.posted_by,
j.location,
j.salary
FROM applicants a
INNER JOIN applicationstatus s ON a.applicant_id = s.applicant_id 
INNER JOIN jobpostings j ON a.posting_id = j.posting_id
WHERE status = "final interview passed"
OR status = "job offered"
AND a.applicant_id = :applicant_id
ORDER BY created_at desc',
    [
        ':applicant_id' => $_GET['id']
    ]
)->fetch();

$offer = $db->query('SELECT * FROM job_offer WHERE applicant_id = :applicant_id', [
    ':applicant_id' => $_GET['id']
])->fetch();

require 'views/hr_hiring/applicants-offer.view.php';
