<?php
// require 'vendor/autoload.php';
session_start();
$heading = 'HOME';
require 'function.php';
$config = require '../config.php';
require '../Database.php';
$db = new Database($config['database']);
// $usm = new Database($config['usm']);

// dd($_SESSION);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // dd($_POST);
    $users = $db->query('SELECT email FROM user_account')->fetchAll();
    $errors = [];
    $success = false;
    $email = $_POST['email'];
    $password = $_POST['password'];
    // $username = $_POST['username'];
    // $first_name = $_POST['first_name'];
    // $last_name = $_POST['last_name'];

    validate('email', $errors);
    if ($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format.';
        } elseif (strlen($email) > 255) {
            $errors['email'] = 'Email is too long.';
        }
    }
    validate('password', $errors);
    // validate('username', $errors);
    // validate('first_name', $errors);
    // validate('last_name', $errors);
    foreach ($users as $user) {
        if ($user['email'] == $_POST['email']) {
            $errors['email'] = 'email already taken.';
        }
    }
    // dd($errors);
    if (empty($errors)) {
        if ($email && $password) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $db->query("INSERT INTO user_account (email, password, role) VALUES (:email, :password ,:role)", [
                'email' => $email,
                'password' => $hashedPassword,
                'role' => 'applicant',
            ]);

            sendMail(
                $_POST['email'],
                'Registration Successful',
                "Great news! Your registration is complete. Welcome to our team! You're all set to log in and begin exploring career opportunities."
            );
            // dd('test');
            header('Location: index.php');
            exit();
        }
    }
}

require '../views/register.view.php';
