<?php
session_start();
$heading = 'Department Accounts';
$config = require 'config.php';
$usm = new Database($config['usm']);
$errors = [];
$logs = $usm->query('SELECT * FROM department_log_history')->fetchAll();
// dd($logs);
require 'views/um/dept_log_history.php';
