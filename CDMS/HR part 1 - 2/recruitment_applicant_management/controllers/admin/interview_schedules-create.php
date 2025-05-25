<?php
session_start();
$heading = 'Interview Schedules Create';
$config = require '../../config.php';
require '../../Database.php';
require '../../functions.php';
$db = new Database($config['database']);

$applicants = $db->query(" SELECT applicant_id, first_name, last_name FROM applicants ")->fetchAll();
// dd($applicants);
$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $db->query("SELECT first_name, email, posting_id FROM applicants WHERE applicant_id = :applicant_id", [
        'applicant_id' => $_POST['applicant_id']
    ])->fetch();
    // dd($email);
    $job = $db->query("SELECT job_title , company FROM jobpostings WHERE posting_id = :posting_id", [
        'posting_id' => $email['posting_id']
    ])->fetch();
    // dd($job);
    validate('date', $errors);
    validate('time', $errors);
    validate('location', $errors);
    validate('mode', $errors);
    validate('interview_type', $errors);
    validate('applicant_id', $errors);

    if (!empty($errors)) {
        $error = true;
    }
    if (empty($errors)) {
        $db->query("INSERT INTO interviewschedules (date, time, location, mode, interview_type, interview_status, applicant_id, interviewer_id)
                VALUES (:date, :time, :location, :mode, :interview_type, :interview_status, :applicant_id, :interviewer_id)
            ", [
            'date' => $_POST['date'],
            'time' => $_POST['time'],
            'location' => $_POST['location'],
            'mode' => $_POST['mode'],
            'interview_type' => $_POST['interview_type'],
            'interview_status' => 'pending',
            'applicant_id' => $_POST['applicant_id'],
            'interviewer_id' => $_SESSION['User_ID']
        ]);
        if ($_POST['interview_type'] === 'initial') {
            sendMail(
                $email['email'],
                "Interview Invitation: {$job['job_title']} at {$job['company']}",
                "Dear {$email['first_name']},
    
                Thank you for your interest in the {$job['job_title']} position at {$job['company']}. We were very impressed with your application and would like to invite you for an initial interview to discuss your qualifications further.
    
                Your interview will be conducted virtually via Google Meet. Please find the details below:
                
Date: {$_POST['date']}
Time: {$_POST['time']}
Link: {$_POST['location']}

We look forward to speaking with you!

Sincerely,
{$job['company']}"
            );
        } else {
            sendMail(
                $email['email'],
                "Invitation for Final Interview: {$job['job_title']} at {$job['company']}",
                "Dear , {$email['first_name']}

                Following your successful previous interview(s) for the {$job['job_title']} position at {$job['company']}, we are delighted to invite you for a final interview to discuss your candidacy further.

                We have been very impressed with your qualifications and believe you could be a great fit for our team. This final interview will provide an opportunity for you to meet with [mention who they will meet, e.g., senior leadership, the hiring manager and a team member, key stakeholders] and delve deeper into [mention key areas, e.g., the strategic vision for the role, specific project challenges, your long-term career aspirations].

                The interview will be conducted in-person.  Please find the details below:

Date: {$_POST['date']}
Time: {$_POST['time']}
Link: {$_POST['location']}

We look forward to this final conversation and potentially welcoming you to our team!

Sincerely,

The HR Team
{$job['company']}"
            );
        }
        header('location: interview_schedules.php');
        exit;
    }
}

require '../../views/admin/interview_schedules-create.view.php';
