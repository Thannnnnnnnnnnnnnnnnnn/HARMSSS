<?php
session_start();
$heading = 'Job offer';
$config = require '../../config.php';
require '../../Database.php';
$db = new Database($config['database']);
$usm = new Database($config['usm']);
$nhoes = new Database($config['nhoes']);

$offer = $db->query('SELECT * FROM job_offer WHERE offer_id = :offer_id', [
    'offer_id' => $_GET['id']
])->fetch();

$applicant = $db->query('SELECT
a.*,
d.*,
s.status,
j.job_title,
j.location,
j.employment_type,
j.department_id,
j.role_id
FROM applicants a INNER JOIN documents d ON a.applicant_id = d.applicant_id
INNER JOIN applicationstatus s ON a.applicant_id = s.applicant_id
INNER JOIN jobpostings j ON a.posting_id = j.posting_id
WHERE a.applicant_id = :applicant_id', [
    'applicant_id' => $offer['applicant_id']
])->fetch();
// dd($applicant);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // dd($_POST);
    $errors = [];
    if ($_POST['hire'] ?? '' == true) {
        $db->query('UPDATE applicationstatus SET status = :status WHERE applicant_id = :applicant_id', [
            'status' => 'Hired',
            'applicant_id' => $applicant['applicant_id']
        ]);

        $db->query("INSERT INTO notifications (title, message, status, applicant_id, type, `for`) VALUES (:title, :message, :status, :applicant_id, :type, :for)", [
            ':title' => 'Congratulations!',
            ':message' => 'You have been hired for the position of ' . $offer['position'],
            'status' => 'unread',
            'applicant_id' => $offer['applicant_id'],
            ':type' => 'hired',
            ':for' => 'applicant'
        ]);

        $usm->query("INSERT INTO department_transaction (department_id, user_id, transaction_type, description, department_affected, module_affected) VALUES (:department_id, :user_id, :transaction_type, :description, :department_affected, :module_affected)", [
            'department_id' => 1,
            'user_id' => $_SESSION['user_id'],
            'transaction_type' => 'hried an applicant',
            'description' => "admin: {$_SESSION['username']} hired applicant {$applicant['first_name']} {$applicant['last_name']}",
            'department_affected' => 'HR part 1&2',
            'module_affected' => 'recruitment and applcant management'
        ]);

        $nhoes->query("INSERT INTO employees(first_name, last_name, email, department_id, role_id) VALUES(:first_name, :last_name, :email, :department_id, :role_id)", [
            ':first_name' => $applicant['first_name'],
            ':last_name' => $applicant['last_name'],
            ':email' => $applicant['email'],
            ':department_id' => $applicant['department_id'],
            ':role_id' => $applicant['role_id'],
        ]);
        $id = $nhoes->pdo->lastInsertId();
        $resume_extension = pathinfo($applicant['resume'], PATHINFO_EXTENSION);
        $nhoes->query("INSERT INTO documents(document_type, file_path, employee_id) VALUES(:document_type, :file_path, :employee_id)", [
            ':document_type' => $resume_extension,
            ':file_path' => $applicant['resume'],
            ':employee_id' => $id
        ]);
        $sss_extension = pathinfo($applicant['sss'], PATHINFO_EXTENSION);
        $nhoes->query("INSERT INTO documents(document_type, file_path, employee_id) VALUES(:document_type, :file_path, :employee_id)", [
            ':document_type' => $sss_extension,
            ':file_path' => $applicant['sss'],
            ':employee_id' => $id
        ]);
        $philhealth_extension = pathinfo($applicant['philhealth'], PATHINFO_EXTENSION);
        $nhoes->query("INSERT INTO documents(document_type, file_path, employee_id) VALUES(:document_type, :file_path, :employee_id)", [
            ':document_type' => $philhealth_extension,
            ':file_path' => $applicant['philhealth'],
            ':employee_id' => $id
        ]);
        $pagibig_extension = pathinfo($applicant['pagibig'], PATHINFO_EXTENSION);
        $nhoes->query("INSERT INTO documents(document_type, file_path, employee_id) VALUES(:document_type, :file_path, :employee_id)", [
            ':document_type' => $pagibig_extension,
            ':file_path' => $applicant['pagibig'],
            ':employee_id' => $id
        ]);

        header('Location: /admin/job-offers');
        exit;
    }
    if ($_POST['delete'] ?? '' == true) {
        $db->query('DELETE FROM job_offer WHERE offer_id = :offer_id', [
            'offer_id' => $_POST['offer_id']
        ]);
        $usm->query("INSERT INTO department_audit_trail (department_id, user_id, action, description, department_affected, module_affected) VALUES (:department_id, :user_id, :action, :description, :department_affected, :module_affected)", [
            'department_id' => $_SESSION['user_data']['department_id'],
            'user_id' => $_SESSION['user_data']['user_id'],
            'action' => 'delete',
            'description' => "admin: {$_SESSION['username']} deleted a job offer with id: {$_POST['offer_id']} that belongs to applicant: {$applicant['first_name']} {$applicant['last_name']}",
            'department_affected' => 'Job Offer',
            'module_affected' => 'Job Offer'
        ]);
        header('Location: /admin/job-offers');
        exit;
    }
}
require 'views/admin/job-offers-view.view.php';
