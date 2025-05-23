<?php

session_start();

$heading = 'User Profile';
$config = require 'config.php';
$db = new Database($config['database']);


$usernames = $db->query('SELECT username FROM user_accounts WHERE user_id != :user_id', [
    'user_id' => $_SESSION['user_id'],
])->fetchAll();
$errors = [];
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate('first_name', $errors);
    validate('last_name', $errors);
    validate('username', $errors);
    // dd($_POST);
    foreach ($usernames as $username) {
        if ($username['username'] === $_POST['username']) {
            $errors['username'] = 'Username is already taken.';
        }
    }
    if (empty($errors) && !empty($_POST['first_name']) && !empty($_POST['last_name'])) {
        $db->query('UPDATE user_accounts SET first_name = :first_name, last_name = :last_name, username = :username WHERE user_id = :user_id', [
            ':first_name' => $_POST['first_name'],
            ':last_name' => $_POST['last_name'],
            ':username' => $_POST['username'],
            ':user_id' => $_SESSION['user_id'],
        ]);
        $success = true;
    }
}

$user = $db->query('SELECT first_name, last_name, username, email FROM user_accounts WHERE user_id = :user_id', [
    'user_id' => $_SESSION['user_id'],
])->fetch();

require 'views/hr/profile.view.php';
