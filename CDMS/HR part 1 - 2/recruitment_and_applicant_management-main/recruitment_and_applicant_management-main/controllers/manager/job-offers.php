<?php
session_start();
$heading = 'Job-Offers';
$config = require 'config.php';
$db = new Database($config['database']);

$offers = $db->query("SELECT
j.*,
a.first_name,
a.last_name
FROM job_offer j INNER JOIN applicants a on j.applicant_id = a.applicant_id ")->fetchAll();
// dd($offers);
require 'views/manager/job-offers.view.php';
