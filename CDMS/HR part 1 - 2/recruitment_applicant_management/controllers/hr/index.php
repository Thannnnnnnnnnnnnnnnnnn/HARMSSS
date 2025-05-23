<?php

session_start();

$heading = 'HOME';
$config = require 'config.php';
$db = new Database($config['database']);
$postings = $db->query('SELECT * FROM jobpostings ORDER BY created_at desc')->fetchAll();

$applications = $db->query("SELECT * FROM applicants")->fetchAll();
require 'views/hr/index.view.php';
