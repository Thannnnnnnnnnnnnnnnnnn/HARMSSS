<?php

session_start();

$heading = 'Job offer';
$config = require 'config.php';
$db = new Database($config['database']);

$offer = $db->query('SELECT
o.*,
a.first_name,
a.last_name 
FROM job_offer o INNER JOIN applicants a ON o.applicant_id = a.applicant_id
WHERE offer_id = :offer_id', [
    'offer_id' => $_GET['id']
])->fetch();
// dd($offer);
require 'views/manager/job-offer.view.php';
