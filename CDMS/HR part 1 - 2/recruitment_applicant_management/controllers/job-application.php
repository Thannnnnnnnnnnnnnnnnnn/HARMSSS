<?php

session_start();
require 'function.php';
$config = require '../config.php';
require '../Database.php';
// dd($_SESSION);

$heading = 'JOB-APPLICATION';
$db = new Database($config['database']);

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'true';
    header('location: register.php');
    exit();
}

$postingId = $_GET['id'] ?? null;
if (!$postingId) {
    die('Job posting ID is required.');
}

$recruiter = $db->query(
    'SELECT posted_by, job_title FROM jobpostings WHERE posting_id = :posting_id',
    ['posting_id' => $postingId]
)->fetch();

$applications = $db->query(
    "SELECT applicants.*, applicationstatus.status
     FROM applicants
     INNER JOIN applicationstatus ON applicants.applicant_id = applicationstatus.applicant_id"
)->fetchAll();

$my_application = $db->query(
    "SELECT email FROM applicants WHERE email = :email",
    [':email' => $_SESSION['email']]
)->fetch();

// dd($my_application);

if ($my_application) {
    // dd($applications);
    $_SESSION['pending'] = 'You have an Unfinished application.';
    header('location: application.php');
    exit();
}
function calcuAge($dob)
{
    $dob = new DateTime($dob);
    $today = new DateTime();
    $age = $today->diff($dob)->y;
    return $age;
}

$success = false;
$errors = [];
$filePaths = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // dd($_POST);

    $requiredFields = ['first_name', 'last_name', 'contact_number', 'address', 'email', 'date_of_birth'];
    foreach ($requiredFields as $field) {
        validate($field, $errors);
    }

    $field = 'resume';
    if (isset($_FILES[$field]) && $_FILES[$field]['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES[$field];
        $allowedExtensions = ['pdf', 'doc', 'docx'];
        $fileExtension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            $errors[$field] = "Resume must be a PDF, DOC, or DOCX file.";
        }
        if ($file["size"] > 5 * 1024 * 1024) {
            $errors[$field] = "Resume must be less than 2MB.";
        }
        if (empty($errors)) {
            $uploadDir = "uploads/documents/{$_POST['first_name']}_{$_POST['last_name']}/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = "{$_POST['first_name']}_{$_POST['last_name']}_resume_" . time() . ".{$fileExtension}";
            $filePath = $uploadDir . $fileName;
            if (move_uploaded_file($file["tmp_name"], $filePath)) {
                $filePaths[$field] = $filePath;
            } else {
                $errors[$field] = "Error uploadiang Resume.";
            }
        }
    } else {
        $errors[$field] = "Resume is required.";
    }
    // dd($errors);
    if (empty($errors)) {
        $db->query(
            "INSERT INTO applicants 
                (first_name, last_name, contact_number, age, date_of_birth, address, email, resume, posting_id) 
             VALUES 
                (:first_name, :last_name, :contact_number, :age, :date_of_birth, :address, :email, :resume, :posting_id)",
            [
                ':first_name' => $_POST['first_name'],
                ':last_name' => $_POST['last_name'],
                ':contact_number' => $_POST['contact_number'],
                ':age' => calcuAge($_POST['date_of_birth']),
                ':date_of_birth' => $_POST['date_of_birth'],
                ':address' => $_POST['address'],
                ':email' => $_POST['email'],
                ':resume' => $filePaths['resume'] ?? null,
                ':posting_id' => $postingId,
            ]
        );

        $applicant_id = $db->pdo->lastInsertId();

        $db->query(
            "INSERT INTO applicationstatus (applicant_id, status) VALUES (:applicant_id, :status)",
            [
                ':applicant_id' => $applicant_id,
                ':status' => 'applied',
            ]
        );
        // dd($_POST);
        $job_posting = $db->query(
            "SELECT job_title, location, employment_type, salary, company FROM jobpostings WHERE posting_id = :posting_id",
            [':posting_id' => $postingId]
        )->fetch();

        sendMail(
            $_POST['email'],
            "Subject: Your Application to Avalon Has Been Received!",
            "Dear {$_POST['first_name']} {$_POST['last_name']},\n\n" .
                "Thank you for applying to the {$job_posting['job_title']} position at {$job_posting['company']}. We have successfully received your application.\n\n" .
                "We appreciate your interest in joining our team. Our HR team will review your qualifications and experience carefully. If your profile aligns with our requirements, we will contact you for the next steps in the hiring process.\n\n" .
                "We appreciate your patience as we review all applications.\n\n" .
                "Sincerely,\n\nThe HR Team\n{$job_posting['company']}"
        );

        header('Location: application.php');
        exit();
    }
}

require '../views/job-application.view.php';
