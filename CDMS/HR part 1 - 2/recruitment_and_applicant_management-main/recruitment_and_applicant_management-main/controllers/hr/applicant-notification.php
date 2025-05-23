<?php

session_start();
$heading = 'NOTIFICATION';
$config = require 'config.php';
$db = new Database($config['database']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // dd($_POST);
    if ($_POST['read'] ?? '' == 'on') {
        // dd('read');
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


require 'views/hr/applicant-notification.view.php';
