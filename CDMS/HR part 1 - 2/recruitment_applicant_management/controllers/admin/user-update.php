<?php
session_start();
$heading = 'Update User Account';
$config = require '../../config.php';
require '../../Database.php';
$usm = new Database($config['usm']);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    validate('first_name', $errors);
    validate('last_name', $errors);
    validate('username', $errors);
    validate('email', $errors);
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }
    validate('role', $errors);
    if (empty($errors)) {
        $usm->query("UPDATE user_account SET first_name = :first_name, last_name = :last_name, username = :username, email = :email, role = :role WHERE user_id = :user_id", [
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'role' => $_POST['role'],
            'user_id' => $_POST['user_id']
        ]);

        $usm->query("INSERT INTO department_audit_trail (department_id, user_id, action, description, department_affected, module_affected) VALUES (:department_id, :user_id, :action, :description, :department_affected, :module_affected)", [
            'department_id' => 1,
            'user_id' => $_SESSION['user_id'],
            'action' => 'update',
            'description' => "admin: {$_SESSION['username']} just updated a User account with the user_id: {$_POST['user_id']}",
            'department_affected' => 'HR part 1&2',
            'module_affected' => 'recruitment and applicant management'
        ]);

        header('Location: /admin/users');
        exit;
    }
}

$user = $usm->query("SELECT user_id, first_name, last_name, username, email, role FROM user_account WHERE user_id = :user_id", [
    'user_id' => $_GET['id']
])->fetch();
// dd($user);
require 'views/admin/user-update.view.php';
