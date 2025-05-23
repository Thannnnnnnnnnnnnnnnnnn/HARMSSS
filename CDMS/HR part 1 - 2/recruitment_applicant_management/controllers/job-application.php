<?php

session_start();

$config = require 'config.php';
$heading = 'JOB-APPLICATION';
$db = new Database($config['database']);
$usm = new Database($config['usm']);

$applications = $db->query("SELECT
 applicants.*,
 applicationstatus.status
 FROM applicants
 inner join applicationstatus on applicants.applicant_id = applicationstatus.applicant_id
 WHERE user_id = :user_id", [
    ':user_id' => $_SESSION['user_id']
])->fetchAll();
// dd($applications);
$user_info = $usm->query("SELECT first_name, last_name, email FROM user_account WHERE user_id = :user_id", [
    ':user_id' => $_SESSION['user_id']
])->fetch();
// dd($user_info);
if (count($applications) >= 1) {
    $currentApplication;
    foreach ($applications as $application) {
        if ($application['status'] == 'hired') {
            $currentApplication = 2;
            break;
        } elseif ($application['status'] == 'declined') {
        } elseif ($application['status'] != 'rejected') {
            $currentApplication = 1;
        }
    }
    // dd($application);
    if ($currentApplication ?? '' == 1) {
        // dd($currentApplication);
        $_SESSION['unfinished_application'] = true;
        header('Location: /application');
        exit();
    } elseif ($currentApplication ?? '' == 2) {
        // dd($currentApplication);
        $_SESSION['already_hired'] = true;
        header('Location: /application');
    }
}

$success = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [];
    $uploadDir = "uploads/documents/" . $_SESSION['user_id'] . "/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $fileFields = ['resume', 'philhealth', 'sss', 'pagibig'];
    $filePaths = [];
    foreach ($fileFields as $field) {
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES[$field];
            $allowedExtensions = ['pdf', 'doc', 'docx'];
            $fileExtension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedExtensions)) {
                $errors[$field] = ucfirst($field) . " must be a PDF, DOC, or DOCX file.";
                continue;
            }
            if ($file["size"] > 2 * 1024 * 1024) {
                $errors[$field] = ucfirst($field) . " must be less than 2MB.";
                continue;
            }
            $fileName = $_SESSION['user_id'] . "_" . $field . "_" . time() . "." . $fileExtension;
            $filePath = $uploadDir . $fileName;
            if (move_uploaded_file($file["tmp_name"], $filePath)) {
                $filePaths[$field] = $filePath;
            } else {
                $errors[$field] = "Error uploading " . ucfirst($field) . ".";
            }
        } else {
            $errors[$field] = ucfirst($field) . " is required.";
        }
    }
    validate('first_name', $errors);
    validate('last_name', $errors);
    validate('contact_number', $errors);
    validate('address', $errors);
    validate('email', $errors);
    if ($_POST['age'] <= 17) {
        $errors['age'] = "You must be at least 18 years old to apply.";
    }
    if (empty($errors)) {
        $db->query("INSERT INTO applicants 
                            (user_id, first_name, last_name, contact_number, age, date_of_birth, address, email, resume, posting_id) 
                            VALUES (:user_id, :first_name, :last_name, :contact_number, :age, :date_of_birth, :address, :email, :resume, :posting_id)", [
            ':user_id' => $_SESSION['user_id'],
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

        $db->query("INSERT INTO documents (applicant_id, philhealth, sss, pagibig) 
                    VALUES (:applicant_id, :philhealth, :sss, :pagibig)", [
            ':applicant_id' => $applicant_id,
            ':philhealth' => $filePaths['philhealth'] ?? null,
            ':sss' => $filePaths['sss'] ?? null,
            ':pagibig' => $filePaths['pagibig'] ?? null,
        ]);

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

        $db->query("INSERT INTO notifications (title, message, status, applicant_id, type, `for`) VALUES (:title, :message, :status, :applicant_id, :type, :for)", [
            ':title' => "New application for {$job_posting['job_title']}",
            ':message' => "Mr/mrs. {$_POST['first_name']} {$_POST['last_name']} applied for the position of {$job_posting['job_title']} in {$job_posting['location']}. The employment type is {$job_posting['employment_type']} with a salary of {$job_posting['salary']}.",
            ':status' => 'unread',
            ':applicant_id' => $applicant_id,
            ':type' => 'application',
            ':for' => 'admin',
        ]);

        $db->query("INSERT INTO notifications (title, message, status, applicant_id, type, `for`) VALUES (:title, :message, :status, :applicant_id, :type, :for)", [
            ':title' => "New application for {$job_posting['job_title']}",
            ':message' => "Mr/mrs. {$_POST['first_name']} {$_POST['last_name']} applied for the position of {$job_posting['job_title']} in {$job_posting['location']}. The employment type is {$job_posting['employment_type']} with a salary of {$job_posting['salary']}.",
            ':status' => 'unread',
            ':applicant_id' => $applicant_id,
            ':type' => 'application',
            ':for' => 'hr',
        ]);

        $usm->query("INSERT INTO department_transaction (department_id, user_id, transaction_type, description, department_affected, module_affected) VALUES (:department_id, :user_id, :transaction_type, :description, :department_affected, :module_affected)", [
            "department_id" => 1,
            "user_id" => $_SESSION['user_id'],
            "transaction_type" => 'application submission',
            "description" => "UserID: {$_SESSION['user_id']} Submitted an application for job: {$recruiter['job_title']}",
            "department_affected" => 'HR part 1&2',
            "module_affected" => 'recruitment and applicant management',
        ]);

        $usm->query("INSERT INTO department_audit_trail (department_id, user_id, action, description, department_affected, module_affected) VALUES (:department_id, :user_id, :action, :description, :department_affected, :module_affected)", [
            "department_id" => 1,
            "user_id" => $_SESSION['user_id'],
            "action" => 'create',
            "description" => "UserID: {$_SESSION['user_id']} added an application for job: {$recruiter['job_title']}",
            "department_affected" => 'HR part 1&2',
            "module_affected" => 'recruitment and applicant management',
        ]);

        header('Location: /application');
        exit();
    }
}

require 'views/job-application.view.php';
