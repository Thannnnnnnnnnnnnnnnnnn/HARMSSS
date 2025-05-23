<?php
session_start();
$config = require 'config.php';
$heading = 'Department Audit Trail';
$usm = new Database($config['usm']);
$socreg = new Database($config['socreg']);
$pm = new Database($config['pm']);
// $audit = [];
$audit = $usm->query('SELECT * FROM department_audit_trail')->fetchAll();
$pm = $pm->query('SELECT * FROM user_audit_trail')->fetchAll();

// dd($pm); 
// $audit['ram_audit'] = $ram_audit;
// $socreg_audit = $socreg->query('SELECT * FROM department_audit_trail')->fetchAll();
// $audit['socreg_audit'] = $socreg_audit;
// dd($audit['ram_audit']);
// dd($socreg_audit);
// dd($audit);
require 'views/um/dept_audit_trail.view.php';
