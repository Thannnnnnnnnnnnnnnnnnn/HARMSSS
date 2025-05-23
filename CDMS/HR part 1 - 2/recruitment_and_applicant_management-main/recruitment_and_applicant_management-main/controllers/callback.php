<?php
session_start();
require 'vendor/autoload.php';

$config = require 'config.php';

$client = new Google\Client();
$client->setClientId($config['google']['client_id']);
$client->setClientSecret($config['google']['client_secret']);
$client->setRedirectUri($config['google']['redirect_uri']);

if (isset($_GET['code'])) {
    $db = new Database($config['usm']);
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    $oauth = new Google\Service\Oauth2($client);
    $userInfo = $oauth->userinfo->get();
    // dd($userInfo);
    $user = $db->query('SELECT * FROM user_account WHERE email = :email', [
        ':email' => $userInfo->email,
    ])->fetch();

    if (!$user) {
        $db->query("INSERT INTO user_account (department_id, first_name, last_name, username, email, role, register_type) VALUES (:department_id, :first_name, :last_name, :username, :email, :role, :register_type)", [
            "department_id" => 1,
            ":first_name" => $userInfo->given_name,
            ":last_name" => $userInfo->family_name,
            ":username" => $userInfo->name,
            ":email" => $userInfo->email,
            ":role" => 'applicant',
            ":register_type" => 'google',
        ]);
    }

    $user_role = $db->query('SELECT * FROM user_account WHERE email = :email', [
        ':email' => $userInfo->email,
    ])->fetch();

    if ($user_role['role'] === 'admin') {
        $_SESSION['user_id'] = $user_role['user_id'];
        $_SESSION['username'] = $user_role['username'];
        $_SESSION['role'] = $user_role['role'];
        header("Location: /admin/");
        exit();
    } elseif ($user_role['role'] === 'manager') {
        $_SESSION['user_id'] = $user_role['user_id'];
        $_SESSION['username'] = $user_role['username'];
        $_SESSION['role'] = $user_role['role'];
        header("Location: /manager/");
        exit();
    } elseif ($user_role['role'] === 'recruiter') {
        $_SESSION['user_id'] = $user_role['user_id'];
        $_SESSION['username'] = $user_role['username'];
        $_SESSION['role'] = $user_role['role'];
        header("Location: /hr/");
        exit();
    } elseif ($user_role['role'] === 'hiring manager') {
        $_SESSION['user_id'] = $user_role['user_id'];
        $_SESSION['username'] = $user_role['username'];
        $_SESSION['role'] = $user_role['role'];
        header('Location: /hr_hiring/');
        exit();
    } elseif ($user_role['role'] === 'applicant') {
        $_SESSION['user_id'] = $user_role['user_id'];
        $_SESSION['username'] = $user_role['username'];
        $_SESSION['role'] = $user_role['role'];
        header('Location: /home');
        exit();
    }
} else {
    echo "Error: Authorization code not received.";
}
