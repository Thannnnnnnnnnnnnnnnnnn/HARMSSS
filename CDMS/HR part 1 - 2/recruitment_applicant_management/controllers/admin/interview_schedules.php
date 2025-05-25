<?php

session_start();
$heading = 'Interview Schedules';
$config = require '../../config.php';
require '../../functions.php';
require '../../Database.php';
$db = new Database($config['database']);
// $usm = new Database($config['usm']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    $applicant = $db->query("SELECT * FROM applicants WHERE applicant_id = :applicant_id", [
        ':applicant_id' => $_POST['applicant_id'],
    ])->fetch();
    $job = $db->query("SELECT * FROM jobpostings WHERE posting_id = :posting_id", [
        ':posting_id' => $applicant['posting_id'],
    ])->fetch();
    // dd($job);
    if (empty($errors)) {

        if ($_POST['pass'] ?? '' === true) {
            $db->query("UPDATE interviewschedules SET interview_status = :interview_status WHERE schedule_id = :schedule_id", [
                ':interview_status' => 'passed',
                ':schedule_id' => $_POST['schedule_id'],
            ]);
            if ($_POST['interview_type'] === 'initial') {
                // dd($_POST);
                $db->query("UPDATE applicationstatus SET status = :status WHERE applicant_id = :applicant_id", [
                    ':status' => 'initial interview passed',
                    ':applicant_id' => $_POST['applicant_id'],
                ]);
                sendMail(
                    $applicant['email'],
                    "Congratulations! Next Steps for {$job['job_title']} at {$job['company']}",
                    "Dear {$applicant['first_name']} {$applicant['last_name']},

                    Following your recent initial interview for the {$job['job_title']} position at {$job['company']}, we are pleased to inform you that you have successfully moved forward to the next stage of our hiring process!

In the meantime, please let us know if you have any questions.

We look forward to continuing the conversation!

Sincerely,

The HR Team
{$job['company']}"
                );
            } else {
                $db->query("UPDATE applicationstatus SET status = :status WHERE applicant_id = :applicant_id", [
                    ':status' => 'final interview passed',
                    ':applicant_id' => $_POST['applicant_id'],
                ]);
                sendMail(
                    $applicant['email'],
                    "Congratulations! Offer for {$job['job_title']} at {$job['company']}",
                    "Dear {$applicant['first_name']} {$applicant['last_name']},

                    Following your final interview for the {$job['job_title']} position at {$job['company']}, we are thrilled to inform you that you have successfully completed our hiring process!

We were very impressed with your qualifications and believe you would be an excellent addition to our team.

We will be in touch very soon with an official offer letter and details regarding the next steps. In the meantime, please let us know if you have any questions.

We are excited about the possibility of you joining us!

Sincerely,

The HR Team
{$job['company']}"
                );
                header('location: applicants.php');
                exit();
            }
        }
    }
    if ($_POST['fail'] ?? '' === true) {
        $db->query("UPDATE interviewschedules SET interview_status = :interview_status WHERE schedule_id = :schedule_id", [
            ':interview_status' => 'failed',
            ':schedule_id' => $_POST['schedule_id'],
        ]);
        if ($_POST['interview_type'] === 'initial') {
            $db->query("UPDATE applicationstatus SET status = :status WHERE applicant_id = :applicant_id", [
                ':status' => 'initial interview failed',
                ':applicant_id' => $_POST['applicant_id'],
            ]);
        } else {
            $db->query("UPDATE applicationstatus SET status = :status WHERE applicant_id = :applicant_id", [
                ':status' => 'final interview failed',
                ':applicant_id' => $_POST['applicant_id'],
            ]);
        }
    }
}

$initial_schedules = $db->query("SELECT
i.*,
a.first_name
FROM interviewschedules i INNER JOIN applicants a on i.applicant_id = a.applicant_id
WHERE i.interview_status = 'pending'
AND i.interview_type = 'initial'
ORDER BY i.created_at DESC
")->fetchAll();

$final_schedules = $db->query("SELECT
i.*,
a.first_name
FROM interviewschedules i INNER JOIN applicants a on i.applicant_id = a.applicant_id
INNER JOIN applicationstatus s on i.applicant_id = s.applicant_id
WHERE i.interview_status = 'pending'
AND i.interview_type = 'final'
ORDER BY created_at DESC
")->fetchAll();

$done_schedules = $db->query("SELECT
i.*,
a.first_name
FROM interviewschedules i INNER JOIN applicants a on i.applicant_id = a.applicant_id
INNER JOIN applicationstatus s on i.applicant_id = s.applicant_id
WHERE i.interview_status != 'pending'
ORDER BY created_at DESC
")->fetchAll();

require '../../views/admin/interview_schedules.view.php';
