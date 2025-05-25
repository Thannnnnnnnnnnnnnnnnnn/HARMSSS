<?php

session_start();

require '../functions.php';
$config = require '../config.php';
require '../Database.php';
$heading = 'JOB-APPLICATION';
$db = new Database($config['database']);
// $usm = new Database($config['usm']);

$applications = $db->query("SELECT
 applicants.*,
 applicationstatus.status
 FROM applicants
 inner join applicationstatus on applicants.applicant_id = applicationstatus.applicant_id")->fetchAll();
// dd($applications);
// if (count($applications) >= 1) {
//     $currentApplication;
//     foreach ($applications as $application) {
//         if ($application['status'] == 'hired') {
//             $currentApplication = 2;
//             break;
//         } elseif ($application['status'] == 'declined') {
//         } elseif ($application['status'] != 'rejected') {
//             $currentApplication = 1;
//         }
//     }
//     // dd($application);
//     // if ($currentApplication ?? '' == 1) {
//     //     // dd($currentApplication);
//     //     $_SESSION['unfinished_application'] = true;
//     //     header('Location: /application');
//     //     exit();
//     // } elseif ($currentApplication ?? '' == 2) {
//     //     // dd($currentApplication);
//     //     $_SESSION['already_hired'] = true;
//     //     header('Location: /application');
//     // }
// }
$recruiter = $db->query('SELECT posted_by, job_title FROM jobpostings WHERE posting_id = :posting_id', [
    'posting_id' => $_GET['id'],
])->fetch();
// dd($recruiter);
$success = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // dd($_FILES);
    $errors = [];
    $uploadDir = "uploads/documents/{$_POST['first_name']}_{$_POST['last_name']}/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $field = 'resume';
    $filePaths = [];
    if (isset($_FILES[$field]) && $_FILES[$field]['error'] !== UPLOAD_ERR_NO_FILE) {
        // dd($_FILES[$field]);
        $file = $_FILES[$field];
        $allowedExtensions = ['pdf', 'doc', 'docx'];
        $fileExtension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            $errors[$field] = ucfirst($field) . " must be a PDF, DOC, or DOCX file.";
            // continue;
        }
        if ($file["size"] > 2 * 1024 * 1024) {
            $errors[$field] = ucfirst($field) . " must be less than 2MB.";
            // continue;
        }
        $fileName = $_POST['first_name'] . "_" . $_POST['last_name'] . $field . "_" . time() . "." . $fileExtension;
        $filePath = $uploadDir . $fileName;
        if (move_uploaded_file($file["tmp_name"], $filePath)) {
            $filePaths[$field] = $filePath;
        } else {
            $errors[$field] = "Error uploading " . ucfirst($field) . ".";
        }
    } else {
        $errors[$field] = ucfirst($field) . " is required.";
    }
    validate('first_name', $errors);
    validate('last_name', $errors);
    validate('contact_number', $errors);
    validate('address', $errors);
    validate('email', $errors);
    if ($_POST['age'] <= 17 || $_POST['age'] > 60) {
        $errors['age'] = "age not qualified.";
    }
    // dd($errors);
    if (empty($errors)) {
        $db->query("INSERT INTO applicants 
                            (first_name, last_name, contact_number, age, date_of_birth, address, email, resume, posting_id) 
                            VALUES (:first_name, :last_name, :contact_number, :age, :date_of_birth, :address, :email, :resume, :posting_id)", [
            ':first_name' => $_POST['first_name'],
            ':last_name' => $_POST['last_name'],
            ':contact_number' => $_POST['contact_number'],
            ':age' => $_POST['age'],
            ':date_of_birth' => $_POST['date_of_birth'],
            ':address' => $_POST['address'],
            ':email' => $_POST['email'],
            ':resume' => $filePaths['resume'] ?? null,
            ':posting_id' => $_GET['id'],
        ]);

        $applicant_id = $db->pdo->lastInsertId();

        // $db->query("INSERT INTO documents (applicant_id, philhealth, sss, pagibig) 
        //             VALUES (:applicant_id, :philhealth, :sss, :pagibig)", [
        //     ':applicant_id' => $applicant_id,
        //     ':philhealth' => $filePaths['philhealth'] ?? null,
        //     ':sss' => $filePaths['sss'] ?? null,
        //     ':pagibig' => $filePaths['pagibig'] ?? null,
        // ]);

        $recruiter = $db->query('SELECT posted_by, job_title FROM jobpostings WHERE posting_id = :posting_id', [
            'posting_id' => $_GET['id'],
        ])->fetch();

        $db->query("INSERT INTO applicationstatus (applicant_id, status, updated_by) VALUES (:applicant_id, :status, :updated_by)", [
            ':applicant_id' => $applicant_id,
            ':status' => 'applied',
            ':updated_by' => $recruiter['posted_by'],
        ]);

        $job_posting = $db->query("SELECT job_title, location, employment_type, salary FROM jobpostings WHERE posting_id = :posting_id", [
            ':posting_id' => $_GET['id'],
        ])->fetch();

        header('Location: application.php');
        exit();
    }
}

require '../views/job-application.view.php';
