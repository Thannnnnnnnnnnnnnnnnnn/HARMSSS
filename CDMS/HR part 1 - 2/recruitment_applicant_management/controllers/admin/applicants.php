<?php
session_start();
$heading = 'Applicants';
$config = require '../../config.php';
require '../../Database.php';
require '../../functions.php';
$db = new Database($config['database']);
$nhoes = new Database($config['nhoes']);
$hr12_usm = new Database($config['hr12_usm']);
$hr34_usm = new Database($config['hr34_usm']);
$logs1_usm = new Database($config['logs1_usm']);
$logs2_usm = new Database($config['logs2_usm']);
$cr1_usm = new Database($config['cr1_usm']);
$cr2_usm = new Database($config['cr2_usm']);
$cr3_usm = new Database($config['cr3_usm']);
$fin_usm = new Database($config['fin_usm']);

$errors = [];
function randomize(): int
{
    $randomNumber = mt_rand(0, 99999);
    return sprintf('%05d', $randomNumber);
}
function firstTwo($surname)
{
    return strtoupper(substr($surname, 0, 2));
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['approve'] ?? '' === 'true') {
        $db->query("UPDATE applicationstatus SET status = :status WHERE applicant_id = :applicant_id", [
            ':status' => 'approved',
            ':applicant_id' => $_POST['applicant_id'],
        ]);

        header('location: interview_schedules-create.php');
        exit();
    }
    if ($_POST['reject'] ?? '' === 'true') {
        $db->query("UPDATE applicationstatus SET status = :status WHERE applicant_id = :applicant_id", [
            ':status' => 'rejected',
            ':applicant_id' => $_POST['applicant_id'],
        ]);
    }
    if ($_POST['hire'] ?? '' === 'true') {
        $applicant = $db->query("SELECT
        a.*,
        s.status,
        j.department_id,
        j.company,
        j.job_title
        FROM applicants a inner join applicationstatus s on a.applicant_id = s.applicant_id
        INNER JOIN jobpostings j ON a.posting_id = j.posting_id
        WHERE a.applicant_id = :applicant_id", [
            ':applicant_id' => $_POST['applicant_id'],
        ])->fetch();
        $job = $nhoes->query("SELECT department_name FROM departments WHERE dept_id = :dept_id", [
            ':dept_id' => $applicant['department_id'],
        ])->fetch();
        // dd($applicant);
        $fiveRandom = randomize();
        $employee_id = "S225{$fiveRandom}0204";
        $surname = firstTwo($applicant['last_name']);
        $password = "#{$surname}2258080";
        $uri = "{$_SERVER['HTTP_HOST']}HARMS/CDMS/USM/login.php";
        switch ($applicant['department_id']) {
            case 1:
                $hr12_usm->query("INSERT INTO department_accounts (Department_ID, User_ID, Name, Password, Role, Status, Email) VALUES (:Department_ID, :User_ID, :Name, :Password, :Role, :Status, :Email)", [
                    ':Department_ID' => "HR120302",
                    ':User_ID' => "{$employee_id}",
                    ':Name' => $applicant['first_name'] . ' ' . $applicant['last_name'],
                    ':Password' => $password,
                    ':Role' => 'Staff',
                    ':Status' => 'Active',
                    ':Email' => $applicant['email'],
                ]);
                sendMail(
                    $applicant['email'],
                    "Welcome to {$applicant['company']}! Your Employee Credentials",
                    "Dear {$applicant['first_name']} {$applicant['last_name']},

Welcome to the team at {$applicant['company']}! We are thrilled to have you join us as a {$applicant['job_title']}.

To help you get started, here are your initial login credentials for our internal systems:

Employee ID: {$employee_id}
Temporary Password: {$password}

You can log in to your account at {$uri}.

We are excited for you to begin your journey with us!

Sincerely,

The HR Team
{$applicant['company']}"
                );

                break;
            case 2:
                $hr34_usm->query("INSERT INTO department_accounts (Department_ID, User_ID, Name, Password, Role, Status, Email) VALUES (:Department_ID, :User_ID, :Name, :Password, :Role, :Status, :Email)", [
                    ':Department_ID' => "HR120303",
                    ':User_ID' => "{$employee_id}",
                    ':Name' => $applicant['first_name'] . ' ' . $applicant['last_name'],
                    ':Password' => $password,
                    ':Role' => 'Staff',
                    ':Status' => 'Active',
                    ':Email' => $applicant['email'],
                ]);
                sendMail(
                    $applicant['email'],
                    "Welcome to {$applicant['company']}! Your Employee Credentials",
                    "Dear {$applicant['first_name']} {$applicant['last_name']},

Welcome to the team at {$applicant['company']}! We are thrilled to have you join us as a {$applicant['job_title']}.

To help you get started, here are your initial login credentials for our internal systems:

Employee ID: {$employee_id}
Temporary Password: {$password}

You can log in to your account at {$uri}.

We are excited for you to begin your journey with us!

Sincerely,

The HR Team
{$applicant['company']}"
                );
                // dd("HR 3&4");
                break;
            case 3:
                $logs1_usm->query("INSERT INTO department_accounts (Department_ID, User_ID, Name, Password, Role, Status, Email) VALUES (:Department_ID, :User_ID, :Name, :Password, :Role, :Status, :Email)", [
                    ':Department_ID' => "L120304",
                    ':User_ID' => "{$employee_id}",
                    ':Name' => $applicant['first_name'] . ' ' . $applicant['last_name'],
                    ':Password' => $password,
                    ':Role' => 'Staff',
                    ':Status' => 'Active',
                    ':Email' => $applicant['email'],
                ]);
                sendMail(
                    $applicant['email'],
                    "Welcome to {$applicant['company']}! Your Employee Credentials",
                    "Dear {$applicant['first_name']} {$applicant['last_name']},

Welcome to the team at {$applicant['company']}! We are thrilled to have you join us as a {$applicant['job_title']}.

To help you get started, here are your initial login credentials for our internal systems:

Employee ID: {$employee_id}
Temporary Password: {$password}

You can log in to your account at {$uri}.

We are excited for you to begin your journey with us!

Sincerely,

The HR Team
{$applicant['company']}"
                );
                // dd("Logistic 1");
                break;
            case 4:
                $logs2_usm->query("INSERT INTO department_accounts (Department_ID, User_ID, Name, Password, Role, Status, Email) VALUES (:Department_ID, :User_ID, :Name, :Password, :Role, :Status, :Email)", [
                    ':Department_ID' => "L220305",
                    ':User_ID' => "{$employee_id}",
                    ':Name' => $applicant['first_name'] . ' ' . $applicant['last_name'],
                    ':Password' => $password,
                    ':Role' => 'Staff',
                    ':Status' => 'Active',
                    ':Email' => $applicant['email'],
                ]);
                sendMail(
                    $applicant['email'],
                    "Welcome to {$applicant['company']}! Your Employee Credentials",
                    "Dear {$applicant['first_name']} {$applicant['last_name']},

Welcome to the team at {$applicant['company']}! We are thrilled to have you join us as a {$applicant['job_title']}.

To help you get started, here are your initial login credentials for our internal systems:

Employee ID: {$employee_id}
Temporary Password: {$password}

You can log in to your account at {$uri}.

We are excited for you to begin your journey with us!

Sincerely,

The HR Team
{$applicant['company']}"
                );
                // dd("Logistic 2");
                break;
            case 5:
                $cr1_usm->query("INSERT INTO department_accounts (Department_ID, User_ID, Name, Password, Role, Status, Email) VALUES (:Department_ID, :User_ID, :Name, :Password, :Role, :Status, :Email)", [
                    ':Department_ID' => "C120306",
                    ':User_ID' => "{$employee_id}",
                    ':Name' => $applicant['first_name'] . ' ' . $applicant['last_name'],
                    ':Password' => $password,
                    ':Role' => 'Staff',
                    ':Status' => 'Active',
                    ':Email' => $applicant['email'],
                ]);
                sendMail(
                    $applicant['email'],
                    "Welcome to {$applicant['company']}! Your Employee Credentials",
                    "Dear {$applicant['first_name']} {$applicant['last_name']},

Welcome to the team at {$applicant['company']}! We are thrilled to have you join us as a {$applicant['job_title']}.

To help you get started, here are your initial login credentials for our internal systems:

Employee ID: {$employee_id}
Temporary Password: {$password}

You can log in to your account at {$uri}.

We are excited for you to begin your journey with us!

Sincerely,

The HR Team
{$applicant['company']}"
                );
                // dd("Core transaction 1");
                break;
            case 6:
                $cr2_usm->query("INSERT INTO department_accounts (Department_ID, User_ID, Name, Password, Role, Status, Email) VALUES (:Department_ID, :User_ID, :Name, :Password, :Role, :Status, :Email)", [
                    ':Department_ID' => "C220307",
                    ':User_ID' => "{$employee_id}",
                    ':Name' => $applicant['first_name'] . ' ' . $applicant['last_name'],
                    ':Password' => $password,
                    ':Role' => 'Staff',
                    ':Status' => 'Active',
                    ':Email' => $applicant['email'],
                ]);
                sendMail(
                    $applicant['email'],
                    "Welcome to {$applicant['company']}! Your Employee Credentials",
                    "Dear {$applicant['first_name']} {$applicant['last_name']},

Welcome to the team at {$applicant['company']}! We are thrilled to have you join us as a {$applicant['job_title']}.

To help you get started, here are your initial login credentials for our internal systems:

Employee ID: {$employee_id}
Temporary Password: {$password}

You can log in to your account at {$uri}.

We are excited for you to begin your journey with us!

Sincerely,

The HR Team
{$applicant['company']}"
                );
                // dd("Core transaction 2");
                break;
            case 7:
                $cr3_usm->query("INSERT INTO department_accounts (Department_ID, User_ID, Name, Password, Role, Status, Email) VALUES (:Department_ID, :User_ID, :Name, :Password, :Role, :Status, :Email)", [
                    ':Department_ID' => "C320308",
                    ':User_ID' => "{$employee_id}",
                    ':Name' => $applicant['first_name'] . ' ' . $applicant['last_name'],
                    ':Password' => $password,
                    ':Role' => 'Staff',
                    ':Status' => 'Active',
                    ':Email' => $applicant['email'],
                ]);
                sendMail(
                    $applicant['email'],
                    "Welcome to {$applicant['company']}! Your Employee Credentials",
                    "Dear {$applicant['first_name']} {$applicant['last_name']},

Welcome to the team at {$applicant['company']}! We are thrilled to have you join us as a {$applicant['job_title']}.

To help you get started, here are your initial login credentials for our internal systems:

Employee ID: {$employee_id}
Temporary Password: {$password}

You can log in to your account at {$uri}.

We are excited for you to begin your journey with us!

Sincerely,

The HR Team
{$applicant['company']}"
                );
                // dd("Core transaction 3");
                break;
            case 8:
                $fin_usm->query("INSERT INTO department_accounts (Department_ID, User_ID, Name, Password, Role, Status, Email) VALUES (:Department_ID, :User_ID, :Name, :Password, :Role, :Status, :Email)", [
                    ':Department_ID' => "F20309",
                    ':User_ID' => "{$employee_id}",
                    ':Name' => $applicant['first_name'] . ' ' . $applicant['last_name'],
                    ':Password' => $password,
                    ':Role' => 'Staff',
                    ':Status' => 'Active',
                    ':Email' => $applicant['email'],
                ]);
                sendMail(
                    $applicant['email'],
                    "Welcome to {$applicant['company']}! Your Employee Credentials",
                    "Dear {$applicant['first_name']} {$applicant['last_name']},

Welcome to the team at {$applicant['company']}! We are thrilled to have you join us as a {$applicant['job_title']}.

To help you get started, here are your initial login credentials for our internal systems:

Employee ID: {$employee_id}
Temporary Password: {$password}

You can log in to your account at {$uri}.

We are excited for you to begin your journey with us!

Sincerely,

The HR Team
{$applicant['company']}"
                );
                // dd("Financials");
                break;
            default:
                // Handle cases where the department ID is not recognized
                dd("Unknown Department");
                break;
        }



        // $db->query("UPDATE applicationstatus SET status = :status WHERE applicant_id = :applicant_id", [
        //     ':status' => 'hired',
        //     ':applicant_id' => $_POST['applicant_id'],
        // ]);

        // $nhoes->query("INSERT INTO employees (first_name, last_name, email, phone_number, DateOfBirth, AddressLine1, department, hire_date)
        // VALUES (:first_name, :last_name, :email, :phone_number, :DateOfBirth, :AddressLine1, :department, :hire_date)", [
        //     ':first_name' => $applicants['first_name'],
        //     ':last_name' => $applicants['last_name'],
        //     ':email' => $applicants['email'],
        //     ':phone_number' => $applicants['phone_number'],
        //     ':DateOfBirth' => $applicants['date_of_birth'],
        //     ':AddressLine1' => $applicants['address'],
        //     ':department' => $applicants['department'],
        //     ':hire_date' => $applicants['updated_at'],
        // ]);

        // $nhoes->query("INSERT INTO documents(document_type, employee_id, file_path) VALUES
        // (:document_type, :employee_id, :file_path)", [
        //     ':document_type' => 'Resume',
        //     ':employee_id' => $nhoes->pdo->lastInsertId(),
        //     ':file_path' => $applicant['resume'],
        // ]);
    }
}

$applicants = $db->query("SELECT
a.*,
s.status,
j.department_id
FROM applicants a inner join applicationstatus s on a.applicant_id = s.applicant_id
INNER JOIN jobpostings j ON a.posting_id = j.posting_id
WHERE s.status != 'hired'
ORDER BY created_at DESC 
")->fetchAll();
// dd($applicants);
$newhires = $db->query("SELECT
a.*,
s.status
FROM applicants a inner join applicationstatus s on a.applicant_id = s.applicant_id
WHERE s.status = 'hired'
ORDER BY created_at DESC 
")->fetchAll();

require '../../views/admin/applicants.view.php';
