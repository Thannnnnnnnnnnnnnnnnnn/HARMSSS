<?php
session_start();
$heading = 'MY APPLICATIONS';
require '../functions.php';
$config = require '../config.php';
require '../Database.php';
$db = new Database($config['database']);
// $usm = new Database($config['usm']);
$updated = false;
// dd($_SESSION);
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
    $updated = true;
  }
}

$applications = $db->query('SELECT
a.applicant_id,
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
WHERE a.email = :email
ORDER BY created_at desc', [
  ':status' => 'rejected',
  ':email' => $_SESSION['email'] ?? '',
])->fetchAll();

$h_applications = $db->query('SELECT
a.applicant_id,
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
WHERE a.email = :email
ORDER BY created_at desc', [
  ':status' => 'rejected',
  ':email' => $_SESSION['email'] ?? '',
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

require '../views/application.view.php';
