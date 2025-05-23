<?php
require 'vendor/autoload.php';
session_start();
// dd($usm);
$config = require 'config.php';
$db = new Database($config['database']);
$usm = new Database($config['usm']);

// $client = new Google\Client();
// $client->setClientId($config['google']['client_id']);
// $client->setClientSecret($config['google']['client_secret']);
// $client->setRedirectUri($config['google']['redirect_uri']);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['login'] ?? '' == true) {

        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $errors = [];

        validate('email', $errors);
        validate('password', $errors);

        if (!empty($errors)) {
            header('location: /');
            exit();
        }

        try {
            $user = $usm->query('SELECT * FROM user_account WHERE email = :email', [
                ':email' => $email,
            ])->fetch();
            if (!$user) {
                $errors['email'] = 'Email or password is incorrect';
                $usm->query("INSERT INTO department_log_history(department_id, email, event_type, failure_reason, ip_address, user_agent, login_type) VALUES(:department_id, :email, :event_type, :failure_reason, :ip_address, :user_agent, :login_type)", [
                    ':department_id' => 1,
                    ':email' => $_POST['email'],
                    ':event_type' => 'login failed',
                    ':failure_reason' => $errors['email'],
                    ':ip_address' => $_SERVER['REMOTE_ADDR'],
                    ':user_agent' => $_SERVER['HTTP_USER_AGENT'],
                    ':login_type' => 'standard',
                ]);
            } elseif (!password_verify($password, $user['password'])) {
                $errors['password'] = 'Password is incorrect';
                $usm->query("INSERT INTO department_log_history(department_id, email, event_type, failure_reason, ip_address, user_agent, login_type) VALUES(:department_id, :email, :event_type, :failure_reason, :ip_address, :user_agent, :login_type)", [
                    ':department_id' => 1,
                    ':email' => $_POST['email'],
                    ':event_type' => 'login failed',
                    ':failure_reason' => $errors['password'],
                    ':ip_address' => $_SERVER['REMOTE_ADDR'],
                    ':user_agent' => $_SERVER['HTTP_USER_AGENT'],
                    ':login_type' => 'standard',
                ]);
            }

            if (empty($errors) && $user) {
                $usm->query("INSERT INTO department_log_history(department_id, email, event_type, ip_address, user_agent, login_type) VALUES(:department_id, :email, :event_type, :ip_address, :user_agent, :login_type)", [
                    ':department_id' => 1,
                    ':email' => $user['email'],
                    ':event_type' => 'login',
                    ':ip_address' => $_SERVER['REMOTE_ADDR'],
                    ':user_agent' => $_SERVER['HTTP_USER_AGENT'],
                    ':login_type' => 'standard',
                ]);
                if ($user['role'] === 'admin') {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    header("Location: /admin/");
                    exit();
                } elseif ($user['role'] === 'manager') {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    header('Location: /manager/');
                    exit();
                } elseif ($user['role'] === 'recruiter') {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    header("Location: /hr/");
                    exit();
                } elseif ($user['role'] === 'hiring manager') {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    header('Location: /hr_hiring/');
                    exit();
                } elseif ($user['role'] === 'applicant') {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    header('Location: /home');
                    exit();
                }
            }
        } catch (Exception $e) {
            error_log('Database Error: ' . $e->getMessage());
            $errors['database'] = 'An unexpected error occurred. Please try again later.';
        }
    }
    // if ($_POST['google'] ?? '' == true) {
    //     $client = new Google\Client();
    //     $client->setClientId($config['google']['client_id']);
    //     $client->setClientSecret($config['google']['client_secret']);
    //     $client->setRedirectUri($config['google']['redirect_uri']);
    //     $client->addScope("email");
    //     $client->addScope("profile");
    //     $authUrl = $client->createAuthUrl();
    //     header('Location: ' . $authUrl);
    //     exit();
    // }
}

require 'views/index.view.php';
