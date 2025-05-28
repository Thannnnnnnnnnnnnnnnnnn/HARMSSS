<?php
session_start();
$config = require '../config.php';
// $usm = new Database($config['usm']);

// $usm->query('INSERT INTO department_log_history(department_id, email, event_type, failure_reason, ip_address, user_agent, login_type) VALUES(:department_id, :email, :event_type, :failure_reason, :ip_address, :user_agent, :login_type)', [
//     'department_id' => 1,
//     'email' => $_SESSION['email'],
//     'event_type' => 'session timeout',
//     'failure_reason' => 'Session Timeout, User Inactive',
//     'ip_address' => $_SERVER['REMOTE_ADDR'],
//     'user_agent' => $_SERVER['HTTP_USER_AGENT'],
//     'login_type' => 'standard',
// ]);

session_unset();

session_destroy();

header('Location: ../../../USM/login.php');

exit();
