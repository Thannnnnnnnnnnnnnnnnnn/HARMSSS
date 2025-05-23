<?php

session_start();
$heading = 'NOTIFICATION';
$config = require 'config.php';
$db = new Database($config['database']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['read'] ?? '' == 'on') {
        $db->query("UPDATE notifications SET status = 'read' WHERE id = :id", [
            ':id' => $_POST['id'],
        ]);
    }
}

$application = $db->query("SELECT
a.*,
n.*
FROM applicants a INNER JOIN notifications n ON a.applicant_id = n.applicant_id 
WHERE n.id = :id", [
    ':id' => $_GET['id']
])->fetch();
// dd($application);

$offer = $db->query("SELECT
a.applicant_id,
a.first_name,
a.last_name,
o.*
FROM applicants a INNER JOIN job_offer o ON a.applicant_id = o.applicant_id 
")->fetch();
// dd($offer);

require 'views/hr_hiring/notifications.view.php';
