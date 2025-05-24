<?php
session_start();
$heading = 'MY APPLICATIONS';
require '../functions.php';
$config = require '../config.php';
require '../Database.php';
$db = new Database($config['database']);
// $usm = new Database($config['usm']);

// $rejectApplication = isset($_SESSION['unfinished_application']) && $_SESSION['unfinished_application'] === true;
// unset($_SESSION['unfinished_application']);
// $hiredApplication = isset($_SESSION['already_hired']) && $_SESSION['already_hired'] === true;
// unset($_SESSION['already_hired']);

$updated = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  dd($_POST);
  $errors = [];
  $resume = $_POST['old_resume'];

  if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/changed_documents/';
    $resume =  $_SESSION['user_id'] . '_alt_' . time() . '_' . basename($_FILES['resume']['name']);
    $resumefile = $uploadDir . $resume;
    if (move_uploaded_file($_FILES['resume']['tmp_name'], $resumefile)) {
      $resumePath = $resumefile;
    }
    // if (isset($_FILES[$field]) && $_FILES[$field]['error'] !== UPLOAD_ERR_NO_FILE) {
    //   // dd($_FILES[$field]);
    //   $file = $_FILES[$field];
    //   $allowedExtensions = ['pdf', 'doc', 'docx'];
    //   $fileExtension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    //   if (!in_array($fileExtension, $allowedExtensions)) {
    //     $errors[$field] = ucfirst($field) . " must be a PDF, DOC, or DOCX file.";
    //     // continue;
    //   }
    //   if ($file["size"] > 2 * 1024 * 1024) {
    //     $errors[$field] = ucfirst($field) . " must be less than 2MB.";
    //     // continue;
    //   }
    //   $fileName = $_POST['first_name'] . "_" . $_POST['last_name'] . $field . "_" . time() . "." . $fileExtension;
    //   $filePath = $uploadDir . $fileName;
    //   if (move_uploaded_file($file["tmp_name"], $filePath)) {
    //     $filePaths[$field] = $filePath;
    //   } else {
    //     $errors[$field] = "Error uploading " . ucfirst($field) . ".";
    //   }
    // } else {
    //   $errors[$field] = ucfirst($field) . " is required.";
    // }
  }

  validate('first_name', $errors);
  validate('last_name', $errors);
  validate('contact_number', $errors);
  validate('address', $errors);
  validate('email', $errors);
  if (empty($errors)) {
    $db->query("UPDATE applicants SET first_name = :first_name, last_name = :last_name, contact_number = :contact_number, address = :address, email = :email WHERE applicant_id = :applicant_id", [
      ':first_name' => $_POST['first_name'],
      ':last_name' => $_POST['last_name'],
      ':contact_number' => $_POST['contact_number'],
      ':address' => $_POST['address'],
      ':email' => $_POST['email'],
      ':applicant_id' => $_POST['applicant_id'],
    ]);
    if (!empty($_FILES['resume']['name'] ?? '')) {
      $db->query("UPDATE applicants SET resume = :resume WHERE applicant_id = :applicant_id", [
        ':resume' => $resumePath,
        ':applicant_id' => $_POST['applicant_id'],
      ]);
    }
    if (!empty($_FILES['philhealth']['name'] ?? '')) {
      $db->query("UPDATE documents SET philhealth = :philhealth WHERE applicant_id = :applicant_id", [
        ':philhealth' => $philhealthPath,
        ':applicant_id' => $_POST['applicant_id'],
      ]);
    }
    if (!empty($_FILES['sss']['name'] ?? '')) {
      $db->query("UPDATE documents SET sss = :sss WHERE applicant_id = :applicant_id", [
        ':sss' => $sssPath,
        ':applicant_id' => $_POST['applicant_id'],
      ]);
    }
    if (!empty($_FILES['pagibig']['name'] ?? '')) {
      $db->query("UPDATE documents SET pagibig = :pagibig WHERE applicant_id = :applicant_id", [
        ':pagibig' => $pagibigPath,
        ':applicant_id' => $_POST['applicant_id'],
      ]);
    }

    $usm->query("INSERT INTO department_audit_trail (department_id, user_id, action, description, department_affected, module_affected) VALUES (:department_id, :user_id, :action, :description, :department_affected, :module_affected)", [
      ':department_id' => 1,
      ':user_id' => $_SESSION['user_id'],
      ':action' => 'Update',
      ':description' => "applicant: {$_POST['applicant_id']} has updated their application information.",
      ':department_affected' => 'HR part 1&2',
      ':module_affected' => 'recruitment and applicant management'
    ]);

    $updated = true;
  }
}

$applications = $db->query('SELECT
a.applicant_id,
a.user_id,
a.first_name,
a.last_name,
a.contact_number,
a.age,
a.date_of_birth,
a.address,
a.email,
a.resume,
a.posting_id,
a.created_at,
j.job_title,
COALESCE(s.status, "Applied") AS status 
FROM applicants a
LEFT JOIN applicationstatus s ON a.applicant_id = s.applicant_id
INNER JOIN jobpostings j ON a.posting_id = j.posting_id
AND status != :status
ORDER BY created_at desc', [
  ':status' => 'rejected',
])->fetchAll();

$h_applications = $db->query('SELECT
a.applicant_id,
a.user_id,
a.first_name,
a.last_name,
a.contact_number,
a.age,
a.date_of_birth,
a.address,
a.email,
a.resume,
a.posting_id,
a.created_at,
j.job_title,
COALESCE(s.status, "Applied") AS status 
FROM applicants a
LEFT JOIN applicationstatus s ON a.applicant_id = s.applicant_id
INNER JOIN jobpostings j ON a.posting_id = j.posting_id
AND status = :status
ORDER BY created_at desc', [
  ':status' => 'rejected',
])->fetchAll();

$interview = $db->query("SELECT 
  a.applicant_id,
  i.date,
  i.mode,
  i.location,
  j.job_title,
  s.status
FROM jobpostings j
INNER JOIN applicants a ON j.posting_id = a.posting_id
INNER JOIN interviewschedules i ON a.applicant_id = i.applicant_id
INNER JOIN applicationstatus s ON a.applicant_id = s.applicant_id
WHERE status = :status", [
  ':status' => 'initial-interview'
])->fetch();

// foreach ($applications as $application) {
//   $documents = $db->query("SELECT * FROM documents WHERE applicant_id = :applicant_id", [
//     ':applicant_id' => $application['applicant_id'],
//   ])->fetch();
// }

require '../views/application.view.php';
