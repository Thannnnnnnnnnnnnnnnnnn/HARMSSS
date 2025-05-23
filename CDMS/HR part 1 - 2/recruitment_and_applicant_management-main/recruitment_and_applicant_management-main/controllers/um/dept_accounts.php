<?php
session_start();
$heading = 'Department Accounts';
$config = require 'config.php';
$usm = new Database($config['usm']);
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $validate = $usm->query("SELECT username, email FROM user_account")->fetchAll();
        $isValid = true;
        foreach ($validate as $val) {
            if ($val['username'] == $_POST['username']) {
                $isValid = false;
                throw new Exception('Error: The username already exists. Please choose a different username.');
            } else if ($val['email'] == $_POST['email']) {
                $isValid = false;
                $isValid = false;
                throw new Exception('Error: The email already exists. Please choose a different email.');
            }
        }
        if ($isValid) {
            validate('first_name', $errors);
            validate('last_name', $errors);
            validate('username', $errors);
            validate('email', $errors);
            validate('password', $errors);
            validate('role', $errors);
            validate('module', $errors);
            if (empty($errors)) {
                $usm->query("INSERT INTO department_accounts(department_id, first_name, last_name, username, email, password, status, role, module) VALUES (:department_id,:first_name, :last_name, :username, :email, :password, :status, :role, :module)", [
                    ':department_id' => 1,
                    ':first_name' => $_POST['first_name'],
                    ':last_name' => $_POST['last_name'],
                    ':username' => $_POST['username'],
                    ':email' => $_POST['email'],
                    ':password' => password_hash($_POST['password'], PASSWORD_ARGON2I),
                    ':status' => $_POST['status'],
                    ':role' => $_POST['role'],
                    ':module' => $_POST['module'],
                ]);
            }
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
$accounts = $usm->query('SELECT * FROM department_accounts')->fetchAll();
require 'views/um/dept_accounts.view.php';
