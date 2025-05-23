<?php
require 'vendor/autoload.php';
session_start();
$heading = 'HOME';
$config = require 'config.php';
$db = new Database($config['database']);
$usm = new Database($config['usm']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $users = $usm->query('SELECT username,email FROM user_account')->fetchAll();
    $errors = [];
    $success = false;
    $email = $_POST['email'];
    $password = $_POST['password'];
    $username = $_POST['username'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];

    validate('email', $errors);
    if ($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format.';
        } elseif (strlen($email) > 255) {
            $errors['email'] = 'Email is too long.';
        }
    }
    validate('password', $errors);
    validate('username', $errors);
    validate('first_name', $errors);
    validate('last_name', $errors);
    foreach ($users as $user) {
        if ($user['username'] == $_POST['username']) {
            $errors['username'] = 'username already taken.';
        } elseif ($user['email'] == $_POST['email']) {
            $errors['email'] = 'email already taken.';
        }
    }
    if (empty($errors)) {
        if ($email && $password && $username) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $usm->query("INSERT INTO user_account (department_id, first_name, last_name, username, email, password, role, register_type) VALUES (:department_id, :first_name, :last_name, :username, :email, :password ,:role ,:register_type)", [
                'department_id' => 1,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => 'applicant',
                'register_type' => 'standard',
            ]);

            $user_id = $usm->pdo->lastInsertId();
            $usm->query("INSERT INTO department_audit_trail (department_id, user_id, action, description, department_affected, module_affected) VALUES (:department_id, :user_id, :action, :description, :department_affected, :module_affected)", [
                ':department_id' => 1,
                ':user_id' => $user_id,
                ':action' => 'create',
                ':description' => "Mr/mrs $first_name $last_name has registered as an applicant.",
                ':department_affected' => 'HR part 1&2',
                ':module_affected' => 'recruitment and applicant management'
            ]);

            $success = true;
            header('Location: /');
        }
    }
}

require 'views/register.view.php';
