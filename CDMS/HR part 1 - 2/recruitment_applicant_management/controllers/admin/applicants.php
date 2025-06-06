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

        $db->query("UPDATE applicationstatus SET status = :status WHERE applicant_id = :applicant_id", [
            ':status' => 'hired',
            ':applicant_id' => $applicant['applicant_id'],
        ]);

        $job = $nhoes->query("SELECT department_name FROM departments WHERE dept_id = :dept_id", [
            ':dept_id' => $applicant['department_id'],
        ])->fetch();

        $fiveRandom = randomize();
        $employee_id = "S225{$fiveRandom}0204";
        $surname = firstTwo($applicant['last_name']);
        $password = "#{$surname}2258080";
        $uri = "{$_SERVER['HTTP_HOST']}/HARMS/CDMS/USM/login.php";
        $extension = pathinfo($applicant['resume']);

        switch ($applicant['department_id']) {
            case 1:
                $nhoes->query("INSERT INTO employees (EmployeeID, FirstName, LastName, Email, PhoneNumber, DateOfBirth, AddressLine1, DepartmentID, HireDate, JobTitle)
                    VALUES (:EmployeeID, :FirstName, :LastName, :Email, :PhoneNumber, :DateOfBirth, :AddressLine1, :DepartmentID, :HireDate, :JobTitle)", [
                    ':EmployeeID' => $applicant['applicant_id'],
                    ':FirstName' => $applicant['first_name'],
                    ':LastName' => $applicant['last_name'],
                    ':Email' => $applicant['email'],
                    ':PhoneNumber' => $applicant['contact_number'],
                    ':DateOfBirth' => $applicant['date_of_birth'],
                    ':AddressLine1' => $applicant['address'],
                    ':DepartmentID' => $applicant['department_id'],
                    ':HireDate' => $applicant['updated_at'],
                    ':JobTitle' => $applicant['job_title'],
                ]);
                $nhoes->query("INSERT INTO documents (document_type, employee_id, file_path) VALUES (:DocumentType, :EmployeeID, :FilePath)", [
                    ':DocumentType' => $extension['extension'],
                    ':EmployeeID' => $nhoes->pdo->lastInsertId(),
                    ':FilePath' => $applicant['resume'],
                ]);
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
Password: {$password}

You can log in to your account at {$uri}.

We are excited for you to begin your journey with us!

Sincerely,

The HR Team
{$applicant['company']}"
                );
                break;
            case 2:
                $nhoes->query("INSERT INTO employees (EmployeeID, FirstName, LastName, Email, PhoneNumber, DateOfBirth, AddressLine1, DepartmentID, HireDate, JobTitle)
                    VALUES (:EmployeeID, :FirstName, :LastName, :Email, :PhoneNumber, :DateOfBirth, :AddressLine1, :DepartmentID, :HireDate, :JobTitle)", [
                    ':EmployeeID' => $applicant['applicant_id'],
                    ':FirstName' => $applicant['first_name'],
                    ':LastName' => $applicant['last_name'],
                    ':Email' => $applicant['email'],
                    ':PhoneNumber' => $applicant['contact_number'],
                    ':DateOfBirth' => $applicant['date_of_birth'],
                    ':AddressLine1' => $applicant['address'],
                    ':DepartmentID' => $applicant['department_id'],
                    ':HireDate' => $applicant['updated_at'],
                    ':JobTitle' => $applicant['job_title'],
                ]);
                $nhoes->query("INSERT INTO documents (document_type, employee_id, file_path) VALUES (:DocumentType, :EmployeeID, :FilePath)", [
                    ':DocumentType' => $extension['extension'],
                    ':EmployeeID' => $nhoes->pdo->lastInsertId(),
                    ':FilePath' => $applicant['resume'],
                ]);
                $hr34_usm->query("INSERT INTO department_accounts (Department_ID, User_ID, Name, Password, Role, Status, Email) VALUES (:Department_ID, :User_ID, :Name, :Password, :Role, :Status, :Email)", [
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
Password: {$password}

                You can log in to your account at {$uri}.


We are excited for you to begin your journey with us!

                Sincerely,

                The HR Team
                {$applicant['company']}"
                );
                break;
            case 3:
                $nhoes->query("INSERT INTO employees (EmployeeID, FirstName, LastName, Email, PhoneNumber, DateOfBirth, AddressLine1, DepartmentID, HireDate, JobTitle)
                    VALUES (:EmployeeID, :FirstName, :LastName, :Email, :PhoneNumber, :DateOfBirth, :AddressLine1, :DepartmentID, :HireDate, :JobTitle)", [
                    ':EmployeeID' => $applicant['applicant_id'],
                    ':FirstName' => $applicant['first_name'],
                    ':LastName' => $applicant['last_name'],
                    ':Email' => $applicant['email'],
                    ':PhoneNumber' => $applicant['contact_number'],
                    ':DateOfBirth' => $applicant['date_of_birth'],
                    ':AddressLine1' => $applicant['address'],
                    ':DepartmentID' => $applicant['department_id'],
                    ':HireDate' => $applicant['updated_at'],
                    ':JobTitle' => $applicant['job_title'],
                ]);
                $nhoes->query("INSERT INTO documents (document_type, employee_id, file_path) VALUES (:DocumentType, :EmployeeID, :FilePath)", [
                    ':DocumentType' => $extension['extension'],
                    ':EmployeeID' => $nhoes->pdo->lastInsertId(),
                    ':FilePath' => $applicant['resume'],
                ]);
                $logs1_usm->query("INSERT INTO department_accounts (Department_ID, User_ID, Name, Password, Role, Status, Email) VALUES (:Department_ID, :User_ID, :Name, :Password, :Role, :Status, :Email)", [
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
Password: {$password}

                You can log in to your account at {$uri}.

                We are excited for you to begin your journey with us!

                Sincerely,

                The HR Team
                {$applicant['company']}"
                );

                break;
            case 2:
                $nhoes->query("INSERT INTO employees (EmployeeID, FirstName, LastName, Email, PhoneNumber, DateOfBirth, AddressLine1, DepartmentID, HireDate, JobTitle)
                                VALUES (:EmployeeID, :FirstName, :LastName, :Email, :PhoneNumber, :DateOfBirth, :AddressLine1, :DepartmentID, :HireDate, :JobTitle)", [
                    ':EmployeeID' => $applicant['applicant_id'],
                    ':FirstName' => $applicant['first_name'],
                    ':LastName' => $applicant['last_name'],
                    ':Email' => $applicant['email'],
                    ':PhoneNumber' => $applicant['contact_number'],
                    ':DateOfBirth' => $applicant['date_of_birth'],
                    ':AddressLine1' => $applicant['address'],
                    ':DepartmentID' => $applicant['department_id'],
                    ':HireDate' => $applicant['updated_at'],
                    ':JobTitle' => $applicant['job_title'],
                ]);
                $nhoes->query("INSERT INTO documents (document_type, employee_id, file_path) VALUES (:DocumentType, :EmployeeID, :FilePath)", [
                    ':DocumentType' => $extension['extension'],
                    ':EmployeeID' => $nhoes->pdo->lastInsertId(),
                    ':FilePath' => $applicant['resume'],
                ]);
                $hr34_usm->query("INSERT INTO department_accounts (Department_ID, User_ID, Name, Password, Role, Status, Email) VALUES (:Department_ID, :User_ID, :Name, :Password, :Role, :Status, :Email)", [
                    ':Department_ID' => "HR220303",
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
            case 3:
                $nhoes->query("INSERT INTO employees (EmployeeID, FirstName, LastName, Email, PhoneNumber, DateOfBirth, AddressLine1, DepartmentID, HireDate)
                                VALUES (:EmployeeID, :FirstName, :LastName, :Email, :PhoneNumber, :DateOfBirth, :AddressLine1, :DepartmentID, :HireDate)", [
                    ':EmployeeID' => $applicant['applicant_id'],
                    ':FirstName' => $applicant['first_name'],
                    ':LastName' => $applicant['last_name'],
                    ':Email' => $applicant['email'],
                    ':PhoneNumber' => $applicant['contact_number'],
                    ':DateOfBirth' => $applicant['date_of_birth'],
                    ':AddressLine1' => $applicant['address'],
                    ':DepartmentID' => $applicant['department_id'],
                    ':HireDate' => $applicant['updated_at'],
                ]);
                $nhoes->query("INSERT INTO documents (document_type, employee_id, file_path) VALUES (:DocumentType, :EmployeeID, :FilePath)", [
                    ':DocumentType' => $extension['extension'],
                    ':EmployeeID' => $nhoes->pdo->lastInsertId(),
                    ':FilePath' => $applicant['resume'],
                ]);
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
                break;
            case 4:
                $nhoes->query("INSERT INTO employees (EmployeeID, FirstName, LastName, Email, PhoneNumber, DateOfBirth, AddressLine1, DepartmentID, HireDate, JobTitle)
                    VALUES (:EmployeeID, :FirstName, :LastName, :Email, :PhoneNumber, :DateOfBirth, :AddressLine1, :DepartmentID, :HireDate, :JobTitle)", [
                    ':EmployeeID' => $applicant['applicant_id'],
                    ':FirstName' => $applicant['first_name'],
                    ':LastName' => $applicant['last_name'],
                    ':Email' => $applicant['email'],
                    ':PhoneNumber' => $applicant['contact_number'],
                    ':DateOfBirth' => $applicant['date_of_birth'],
                    ':AddressLine1' => $applicant['address'],
                    ':DepartmentID' => $applicant['department_id'],
                    ':HireDate' => $applicant['updated_at'],
                    ':JobTitle' => $applicant['job_title'],
                ]);
                $nhoes->query("INSERT INTO documents (document_type, employee_id, file_path) VALUES (:DocumentType, :EmployeeID, :FilePath)", [
                    ':DocumentType' => $extension['extension'],
                    ':EmployeeID' => $nhoes->pdo->lastInsertId(),
                    ':FilePath' => $applicant['resume'],
                ]);
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
Password: {$password}

                You can log in to your account at {$uri}.

                We are excited for you to begin your journey with us!

                Sincerely,

                The HR Team
                {$applicant['company']}"
                );
                break;
            case 5:
                $nhoes->query("INSERT INTO employees (EmployeeID, FirstName, LastName, Email, PhoneNumber, DateOfBirth, AddressLine1, DepartmentID, HireDate, JobTitle)
                    VALUES (:EmployeeID, :FirstName, :LastName, :Email, :PhoneNumber, :DateOfBirth, :AddressLine1, :DepartmentID, :HireDate, :JobTitle)", [
                    ':EmployeeID' => $applicant['applicant_id'],
                    ':FirstName' => $applicant['first_name'],
                    ':LastName' => $applicant['last_name'],
                    ':Email' => $applicant['email'],
                    ':PhoneNumber' => $applicant['contact_number'],
                    ':DateOfBirth' => $applicant['date_of_birth'],
                    ':AddressLine1' => $applicant['address'],
                    ':DepartmentID' => $applicant['department_id'],
                    ':HireDate' => $applicant['updated_at'],
                    ':JobTitle' => $applicant['job_title'],
                ]);
                $nhoes->query("INSERT INTO documents (document_type, employee_id, file_path) VALUES (:DocumentType, :EmployeeID, :FilePath)", [
                    ':DocumentType' => $extension['extension'],
                    ':EmployeeID' => $nhoes->pdo->lastInsertId(),
                    ':FilePath' => $applicant['resume'],
                ]);
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
Password: {$password}

                You can log in to your account at {$uri}.

                We are excited for you to begin your journey with us!

                Sincerely,

                The HR Team
                {$applicant['company']}"
                );
                break;
            case 6:
                $nhoes->query("INSERT INTO employees (EmployeeID, FirstName, LastName, Email, PhoneNumber, DateOfBirth, AddressLine1, DepartmentID, HireDate, JobTitle)
                    VALUES (:EmployeeID, :FirstName, :LastName, :Email, :PhoneNumber, :DateOfBirth, :AddressLine1, :DepartmentID, :HireDate, :JobTitle)", [
                    ':EmployeeID' => $applicant['applicant_id'],
                    ':FirstName' => $applicant['first_name'],
                    ':LastName' => $applicant['last_name'],
                    ':Email' => $applicant['email'],
                    ':PhoneNumber' => $applicant['contact_number'],
                    ':DateOfBirth' => $applicant['date_of_birth'],
                    ':AddressLine1' => $applicant['address'],
                    ':DepartmentID' => $applicant['department_id'],
                    ':HireDate' => $applicant['updated_at'],
                    ':JobTitle' => $applicant['job_title'],
                ]);
                $nhoes->query("INSERT INTO documents (document_type, employee_id, file_path) VALUES (:DocumentType, :EmployeeID, :FilePath)", [
                    ':DocumentType' => $extension['extension'],
                    ':EmployeeID' => $nhoes->pdo->lastInsertId(),
                    ':FilePath' => $applicant['resume'],
                ]);
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
Password: {$password}

                You can log in to your account at {$uri}.

                We are excited for you to begin your journey with us!

                Sincerely,

                The HR Team
                {$applicant['company']}"
                );
                break;
            case 7:
                $nhoes->query("INSERT INTO employees (EmployeeID, FirstName, LastName, Email, PhoneNumber, DateOfBirth, AddressLine1, DepartmentID, HireDate, JobTitle)
                    VALUES (:EmployeeID, :FirstName, :LastName, :Email, :PhoneNumber, :DateOfBirth, :AddressLine1, :DepartmentID, :HireDate, :JobTitle)", [
                    ':EmployeeID' => $applicant['applicant_id'],
                    ':FirstName' => $applicant['first_name'],
                    ':LastName' => $applicant['last_name'],
                    ':Email' => $applicant['email'],
                    ':PhoneNumber' => $applicant['contact_number'],
                    ':DateOfBirth' => $applicant['date_of_birth'],
                    ':AddressLine1' => $applicant['address'],
                    ':DepartmentID' => $applicant['department_id'],
                    ':HireDate' => $applicant['updated_at'],
                    ':JobTitle' => $applicant['job_title'],
                ]);
                $nhoes->query("INSERT INTO documents (document_type, employee_id, file_path) VALUES (:DocumentType, :EmployeeID, :FilePath)", [
                    ':DocumentType' => $extension['extension'],
                    ':EmployeeID' => $nhoes->pdo->lastInsertId(),
                    ':FilePath' => $applicant['resume'],
                ]);
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
Password: {$password}

                You can log in to your account at {$uri}.

                We are excited for you to begin your journey with us!

                Sincerely,

                The HR Team
                {$applicant['company']}"
                );
                break;
            case 8:
                $nhoes->query("INSERT INTO employees (EmployeeID, FirstName, LastName, Email, PhoneNumber, DateOfBirth, AddressLine1, DepartmentID, HireDate, JobTitle)
                    VALUES (:EmployeeID, :FirstName, :LastName, :Email, :PhoneNumber, :DateOfBirth, :AddressLine1, :DepartmentID, :HireDate, :JobTitle)", [
                    ':EmployeeID' => $applicant['applicant_id'],
                    ':FirstName' => $applicant['first_name'],
                    ':LastName' => $applicant['last_name'],
                    ':Email' => $applicant['email'],
                    ':PhoneNumber' => $applicant['contact_number'],
                    ':DateOfBirth' => $applicant['date_of_birth'],
                    ':AddressLine1' => $applicant['address'],
                    ':DepartmentID' => $applicant['department_id'],
                    ':HireDate' => $applicant['updated_at'],
                    ':JobTitle' => $applicant['job_title'],
                ]);
                $nhoes->query("INSERT INTO documents (document_type, employee_id, file_path) VALUES (:DocumentType, :EmployeeID, :FilePath)", [
                    ':DocumentType' => $extension['extension'],
                    ':EmployeeID' => $nhoes->pdo->lastInsertId(),
                    ':FilePath' => $applicant['resume'],
                ]);
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
Password: {$password}

                You can log in to your account at {$uri}.

                We are excited for you to begin your journey with us!

                Sincerely,

                The HR Team
                {$applicant['company']}"
                );
                break;

            default:
                $errors[] = "Invalid department ID: {$applicant['department_id']}. Unable to insert employee data.";
                break;
        }
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
$newhires = $db->query("SELECT
a.*,
s.status
FROM applicants a inner join applicationstatus s on a.applicant_id = s.applicant_id
WHERE s.status = 'hired'
ORDER BY created_at DESC 
")->fetchAll();

require '../../views/admin/applicants.view.php';
