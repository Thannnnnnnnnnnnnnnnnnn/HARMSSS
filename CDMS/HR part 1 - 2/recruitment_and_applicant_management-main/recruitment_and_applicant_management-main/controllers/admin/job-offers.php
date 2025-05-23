<?php
session_start();
$heading = 'Job offers';
$config = require 'config.php';
$db = new Database($config['database']);

$offers = $db->query('SELECT offer_id, position, applicant_id FROM job_offer WHERE user_decision = :user_decision', [
    ':user_decision' => 'offer-sent'
])->fetchAll();

$offers_accepted = $db->query('SELECT
o.offer_id, 
o.position, 
o.applicant_id,
s.status
FROM job_offer o INNER JOIN applicationstatus s ON o.applicant_id = s.applicant_id
WHERE user_decision = :user_decision', [
    ':user_decision' => 'accepted'
])->fetchAll();

require 'views/admin/job-offers.view.php';
