<?php
session_start();
$heading = 'User Accounts';
$config = require 'config.php';
$db = new Database($config['database']);
$usm = new Database($config['usm']);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['create'] ?? '' === true) {
        validate('first_name', $errors);
        validate('last_name', $errors);
        validate('username', $errors);
        validate('email', $errors);
        validate('password', $errors);
        validate('role', $errors);
        if (empty($errors)) {
            $sweetalert = null;
            try {
                $validate = $usm->query("SELECT username, email FROM user_account")->fetchAll();
                $isValid = true;
                foreach ($validate as $val) {
                    if ($val['username'] == $_POST['username']) {
                        $isvalid = false;
                        throw new Exception('Error: The username already exists. Please choose a different username.');
                    } else if ($val['email'] == $_POST['email']) {
                        $isvalid = false;
                        throw new Exception('Error: The email already exists. Please choose a different email.');
                    }
                }
                if ($isValid) {
                    if ($_POST['create'] ?? '' === true) {
                        $usm->query("INSERT INTO department_accounts (department_id, first_name, last_name, username, email, password, role status, module) VALUES (:department_id, :first_name, :last_name, :username, :email, :password, :role, :status, :module)", [
                            ':department_id' => 1,
                            ':first_name' => trim($_POST['first_name']),
                            ':last_name' => trim($_POST['last_name']),
                            ':username' => trim($_POST['username']),
                            ':email' => rtrim($_POST['email']),
                            ':password' => password_hash(trim($_POST['password']), PASSWORD_DEFAULT),
                            ':role' => $_POST['role'],
                            ':status' => 'active',
                            ':module' => 'recruitment and applicant management',
                        ]);
                        $id = $usm->pdo->lastInsertId();
                        $usm->query("INSERT INTO department_audit_trail(department_id,user_id,action,description,department_affected,module_affected) VALUES (:department_id,:user_id,:action,:description,:department_affected,:module_affected)", [
                            ':department_id' => 1,
                            ':user_id' => $_SESSION['user_id'],
                            ':action' => 'create',
                            ':description' => "admin: {$_SESSION['username']} just created a new user account with the Department Account ID: {$id}",
                            ':department_affected' => 'HR part 1&2',
                            ':module_affected' => 'recruitment and applicant management',
                        ]);
                    }
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    }

    if ($_POST['delete'] ?? '' === true) {
        try {
            $usm->query("DELETE FROM user_account WHERE user_id = :user_id", [
                ':user_id' => $_POST['id'],
            ]);
            $usm->query("INSERT INTO department_audit_trail(department_id,user_id,action,description,department_affected,module_affected) VALUES (:department_id,:user_id,:action,:description,:department_affected,:module_affected)", [
                ':department_id' => 1,
                ':user_id' => $_SESSION['user_id'],
                ':action' => 'delete',
                ':description' => "admin: {$_SESSION['username']} Deleted a user account with the user ID: {$_POST['id']}",
                ':department_affected' => 'HR part 1&2',
                ':module_affected' => 'recruitment and applicant management',
            ]);
            $delete = true;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = 'Error: Action could not be completed. Please contact support for assistance.';
            }
        }
    }
}

$users = $usm->query("SELECT * FROM user_account ORDER BY created_at DESC")->fetchAll();
require 'views/admin/users.view.php';
