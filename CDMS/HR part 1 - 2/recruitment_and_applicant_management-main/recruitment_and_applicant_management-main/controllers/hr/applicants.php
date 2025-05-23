<?php
session_start();
$heading = 'APPLICANTS';
$config = require 'config.php';
$db = new Database($config['database']);
$usm = new Database($config['usm']);

$approved = false;
$rejected = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $status = $db->query('SELECT
   s.status,
   a.applicant_id,
   a.first_name,
   a.last_name
   FROM applicants a INNER JOIN applicationstatus s ON a.applicant_id = s.applicant_id 
   WHERE a.applicant_id = :applicant_id', [
      'applicant_id' => $_POST['applicant_id'],
   ])->fetch();
   if (!empty($_POST['approve']) && $status['status'] == 'applied') {
      $db->query('UPDATE applicationstatus SET status = :status, updated_by =:updated_by WHERE applicant_id = :applicant_id', [
         ':status' => 'approved',
         ':updated_by' => $_SESSION['user_id'],
         ':applicant_id' => $_POST['applicant_id'],
      ]);
      $db->query('INSERT INTO notifications (title,message,status,applicant_id,type,`for`) VALUES (:title,:message,:status,:applicant_id,:type,:for)', [
         ':title' => 'Application Has been Approved',
         ':message' => "Hello Mr/Ms {$status['first_name']} {$status['last_name']} Your application have been Approved",
         ':status' => 'unread',
         ':applicant_id' => $status['applicant_id'],
         ':type' => 'application',
         ':for' => 'applicant',
      ]);
      $db->query('INSERT INTO notifications (title,message,status,applicant_id,type,`for`) VALUES (:title,:message,:status,:applicant_id,:type,:for)', [
         ':title' => 'Application Has been Approved',
         ':message' => "Application ID: {$status['applicant_id']} - {$status['first_name']} {$status['last_name']} has been approved and requires further action.",
         ':status' => 'unread',
         ':applicant_id' => $status['applicant_id'],
         ':type' => 'application',
         ':for' => 'admin',
      ]);
      $date = date("Y-m-d");
      $time = date("h:i:sa");
      $usm->query("INSERT INTO department_transaction (department_id, user_id, transaction_type, description, department_affected, module_affected) VALUES (:department_id, :user_id, :transaction_type, :description, :department_affected, :module_affected)", [
         "department_id" => 1,
         "user_id" => $_SESSION['user_id'],
         "transaction_type" => 'approving application',
         "description" => "Application ID {$_POST['applicant_id']} was approved by Recruiter {$_SESSION['username']} on {$date} at {$time}.",
         "department_affected" => 'HR part 1&2',
         "module_affected" => 'recruitment and applicant management',
      ]);

      $usm->query("INSERT INTO department_audit_trail (department_id, user_id, action, description, department_affected, module_affected) VALUES (:department_id, :user_id, :action, :description, :department_affected, :module_affected)", [
         "department_id" => 1,
         "user_id" => $_SESSION['user_id'],
         "action" => 'update',
         "description" => "On {$date} at {$time}, User {$_SESSION['username']} performed an 'approve application' action on Application ID {$_POST['applicant_id']}.",
         "department_affected" => 'HR part 1&2',
         "module_affected" => 'recruitment and applicant management',
      ]);
      $approved = true;
   } elseif (!empty($_POST['reject']) && $status['status'] == 'applied') {
      $db->query('UPDATE applicationstatus SET status = :status, updated_by =:updated_by WHERE applicant_id = :applicant_id', [
         ':status' => 'rejected',
         ':updated_by' => $_SESSION['user_id'],
         ':applicant_id' => $_POST['applicant_id'],
      ]);
      $db->query('INSERT INTO notifications (title,message,status,applicant_id,type,`for`) VALUES (:title,:message,:status,:applicant_id,:type,:for)', [
         ':title' => 'application rejected',
         ':message' => 'Hello Mr/Ms' . $status['first_name'] . ' ' . $status['last_name'] . 'Sorry to say that your application have been rejected due to some reasons. Thank you for applying, we wish you all the best for your future endeavors',
         ':status' => 'unread',
         ':applicant_id' => $status['applicant_id'],
         ':type' => 'application',
         ':for' => 'applicant',
      ]);
      $db->query('INSERT INTO notifications (title,message,status,applicant_id,type,`for`) VALUES (:title,:message,:status,:applicant_id,:type,:for)', [
         ':title' => 'Application Rejected',
         ':message' => "Applicant: {$status['first_name']} {$status['last_name']} ,Applicant ID: {$status['applicant_id']} Application has been marked as rejected.",
         ':status' => 'unread',
         ':applicant_id' => $status['applicant_id'],
         ':type' => 'application',
         ':for' => 'admin',
      ]);
      $usm->query("INSERT INTO department_transaction (department_id, user_id, transaction_type, description, department_affected, module_affected) VALUES (:department_id, :user_id, :transaction_type, :description, :department_affected, :module_affected)", [
         "department_id" => 1,
         "user_id" => $_SESSION['user_id'],
         "transaction_type" => 'rejecting application',
         "description" => "Application ID {$_POST['applicant_id']} was rejected by Recruiter {$_SESSION['username']} on {$date} at {$time}.",
         "department_affected" => 'HR part 1&2',
         "module_affected" => 'recruitment and applicant management',
      ]);

      $usm->query("INSERT INTO department_audit_trail (department_id, user_id, action, description, department_affected, module_affected) VALUES (:department_id, :user_id, :action, :description, :department_affected, :module_affected)", [
         "department_id" => 1,
         "user_id" => $_SESSION['user_id'],
         "action" => 'update',
         "description" => "On {$date} at {$time}, User {$_SESSION['username']} performed an 'application rejection' action on Application ID {$_POST['applicant_id']}.",
         "department_affected" => 'HR part 1&2',
         "module_affected" => 'recruitment and applicant management',
      ]);
      $rejected = true;
   }
}

$applicants = $db->query(
   'SELECT 
applicants.*,
applicationstatus.status,
jobpostings.job_title,
jobpostings.posted_by
FROM applicants 
INNER JOIN applicationstatus ON applicants.applicant_id = applicationstatus.applicant_id 
INNER JOIN jobpostings ON applicants.posting_id = jobpostings.posting_id
where status = "applied"
ORDER BY created_at desc',
)->fetchAll();

require 'views/hr/applicants.view.php';
