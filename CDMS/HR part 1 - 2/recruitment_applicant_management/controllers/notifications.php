<?php

session_start();

$heading = 'notification';
$config = require 'config.php';
$db = new Database($config['database']);

$accepted = false;
$declined = false;

$applicant = $db->query("SELECT
a.first_name,
a.applicant_id,
n.applicant_id,
n.id,
j.position
FROM applicants a 
INNER JOIN notifications n ON n.applicant_id = a.applicant_id
INNER JOIN job_offer j ON a.applicant_id = j.applicant_id
WHERE n.id = :id", [
    ':id' => $_GET['id']
])->fetch();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    if ($_POST['read'] ?? '' == 'on') {
        $db->query("UPDATE notifications SET status = 'read' WHERE id = :id", [
            ':id' => $_POST['id'],
        ]);
    }

    if ($_POST['accepted'] ?? '' == true) {
        $db->query("UPDATE job_offer SET user_decision = :user_decision WHERE offer_id = :offer_id", [
            ':user_decision' =>  'accepted',
            ':offer_id' => $_POST['id'],
        ]);
        $db->query("INSERT INTO notifications (title, message, status, type, applicant_id, `for`) VALUES (:title, :message, :status, :type, :applicant_id, :for)", [
            ':title' => 'Job offer accepted by the applicant!',
            ':message' => $applicant['first_name'] . ' with the Applicant ID of ' . $applicant['applicant_id'] . ' just accepted the job offer for the position of' . $applicant['position'],
            ':status' => 'unread',
            ':applicant_id' => $applicant['applicant_id'],
            ':type' => 'job offer accepted',
            ':for' => 'hiring manager',
        ]);
        $db->query("UPDATE applicationstatus SET status = :status WHERE status_id = :status_id", [
            ':status' =>  'job offer accepted',
            ':status_id' => $_POST['status_id'],
        ]);

        $accepted = true;
    }

    if ($_POST['declined'] ?? '' == true) {
        $db->query("UPDATE job_offer SET user_decision = :user_decision WHERE offer_id = :offer_id", [
            ':user_decision' =>  'declined',
            ':offer_id' => $_POST['id'],
        ]);
        $db->query("UPDATE applicationstatus SET status = :status WHERE status_id = :status_id", [
            ':status' =>  'declined',
            ':status_id' => $_POST['status_id'],
        ]);
        $db->query("INSERT INTO notifications (message, status, type, applicant_id, `for`) VALUES (:message, :status, :type, :applicant_id, :for)", [
            ':message' => $applicant['first_name'] . ' with the Applicant ID of ' . $applicant['applicant_id'] . ' Declined your job offer for the position of ' . $applicant['position'],
            ':status' => 'unread',
            ':applicant_id' => $applicant['applicant_id'],
            ':type' => 'job offer declined',
            ':for' => 'hiring manager',
        ]);

        $declined = true;
    }
}


$notif = $db->query('SELECT * FROM notifications WHERE id = :id', [
    ':id' => $_GET['id']
])->fetch();

$offer = $db->query('SELECT
notifications.*,
job_offer.*,
applicationstatus.*
FROM notifications INNER JOIN job_offer ON job_offer.applicant_id = notifications.applicant_id
INNER JOIN applicationstatus ON  applicationstatus.applicant_id = job_offer.applicant_id
WHERE id = :id', [
    ':id' => $_GET['id']
])->fetch();

require 'views/notifications.view.php';
