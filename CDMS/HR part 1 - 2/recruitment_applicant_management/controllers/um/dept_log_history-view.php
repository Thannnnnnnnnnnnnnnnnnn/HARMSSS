<?php
session_start();
$heading = 'Department Accounts';
$config = require 'config.php';
$usm = new Database($config['usm']);
$errors = [];
$log = $usm->query('SELECT * FROM department_log_history WHERE dept_log_id = :dept_log_id', [
    ':dept_log_id' => $_GET['id']
])->fetch();
// dd($log);
require 'views/um/dept_log_history-view.php';
