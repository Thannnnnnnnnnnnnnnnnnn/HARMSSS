<?php
session_start();
$heading = 'Department Accounts';
$config = require 'config.php';
$usm = new Database($config['usm']);
$errors = [];
$account = $usm->query('SELECT * FROM department_accounts WHERE dept_accounts_id = :dept_accounts_id', [
    ':dept_accounts_id' => $_GET['id']
])->fetch();
require 'views/um/dept_accounts-view.php';
