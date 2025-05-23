<?php
session_start();

$heading = 'HOME';
$config = require 'config.php';
$db = new Database($config['database']);

$postings = $db->query('SELECT * FROM jobpostings ORDER BY created_at desc')->fetchAll();

require 'views/hr_hiring/index.view.php';
