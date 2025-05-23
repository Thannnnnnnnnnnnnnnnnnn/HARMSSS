<?php

include '../conn.php';


session_start();



$user = $_SESSION['user'];
$_SESSION['user_id'] = $user['user_id'];

$role = $user['role'] ?? null;

// Use global variables set by user.php
$user_id = $GLOBALS['user_id'] ?? $_SESSION['user_id'];
$department_id = $GLOBALS['department_id'] ?? null;
$user_name = $GLOBALS['user_name'] ?? $user['username'] ?? 'Unknown User';
$department = $GLOBALS['department'] ?? 'Unknown Department';
$email = $GLOBALS['email'] ?? $user['email'] ?? 'Unknown Email';

$notif = $connections["cr3_re"];
$usmDb = $connections["cr3_re"];
$db_name = "cr3_re"; 
$connection = $connections[$db_name];




if (!$user_id || !$department_id) {
    $_SESSION['error_message'] = "User authentication required";
    header("Location: login.php");
    exit();
}
// d2 audit trail tapos transaction baguhin dine

?>