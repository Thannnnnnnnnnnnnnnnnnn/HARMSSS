<?php

session_start();
$heading = 'NOTIFICATION';
$config = require 'config.php';
$db = new Database($config['database']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $applicant = $db->query("SELECT applicant_id, first_name FROM applicants WHERE applicant_id = :applicant_id", [
        ':applicant_id' => $_POST['applicant_id'],
    ])->fetch();
    // dd($applicant);
    if ($_POST['read'] ?? '' == 'on') {
        $db->query("UPDATE notifications SET status = 'read' WHERE id = :id", [
            ':id' => $_POST['id'],
        ]);
    }
    if ($_POST['approve'] ?? '' == 'true') {
        $db->query("UPDATE job_offer SET status = 'approved' WHERE offer_id = :offer_id", [
            ':offer_id' => $_POST['offer_id'],
        ]);
        $db->query("INSERT INTO notifications (title, message, status, applicant_id, type, `for`) VALUES (:title, :message, :status, :applicant_id, :type, :for)", [
            ':title' => 'Job offer',
            ':message' => 'Good day, ' . $applicant['first_name'] . ' ' . 'You have a new job offer.',
            ':status' => 'unread',
            'applicant_id' => $applicant['applicant_id'],
            ':type' => 'job offer',
            ':for' => 'applicant',
        ]);
        header('location: /manager/job-offers');
        exit();
    }
    if ($_POST['reject'] ?? '' == 'true') {
        $db->query("UPDATE job_offer SET status = 'rejected' WHERE offer_id = :offer_id", [
            ':offer_id' => $_POST['offer_id'],
        ]);
        $db->query("INSERT INTO notifications (title, message, status, applicant_id, type, `for`) VALUES (:title, :message, :status, :applicant_id, :type, :for)", [
            ':title' => 'Job offer rejected',
            ':message' => $_POST['remarks'],
            ':status' => 'unread',
            'applicant_id' => $applicant['applicant_id'],
            ':type' => 'job offer',
            ':for' => 'hiring manager',
        ]);
        header('location: /manager/job-offers');
        exit();
    }
}

$offer = $db->query("SELECT
a.applicant_id,
a.first_name,
a.last_name,
o.*
FROM applicants a INNER JOIN job_offer o ON a.applicant_id = o.applicant_id 
")->fetch();

$notification = $db->query("SELECT * FROM notifications WHERE id = :id", [
    ':id' => $_GET['id']
])->fetch();

require 'views/manager/applicant-notification.view.php';
