<?php
session_start();
$heading = 'Department Accounts';
$config = require 'config.php';
$usm = new Database($config['usm']);
$errors = [];
$transactions = $usm->query('SELECT * FROM department_transaction')->fetchAll();

require 'views/um/dept_transaction.php';
