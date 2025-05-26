<?php
session_start();
$config = require '../config.php';
require '../Database.php';
require '../functions.php';
$db = new Database($config['database']);
// $usm = new Database($config['usm']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['login'] ?? '' == true) {

        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $errors = [];

        validate('email', $errors);
        validate('password', $errors);

        if (!empty($errors)) {
            header('location: index.php');
            exit();
        }

        try {
            $user = $db->query('SELECT * FROM user_account WHERE email = :email', [
                ':email' => $email,
            ])->fetch();
            if (!$user) {
                $errors['email'] = 'Email or password is incorrect';
            } elseif (!password_verify($password, $user['password'])) {
                $errors['password'] = 'Password is incorrect';
            }

            if (empty($errors) && $user) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                header('Location: home.php');
                exit();
            }
        } catch (Exception $e) {
            error_log('Database Error: ' . $e->getMessage());
            $errors['database'] = 'An unexpected error occurred. Please try again later.';
        }
    }
}

require '../views/index.view.php';
