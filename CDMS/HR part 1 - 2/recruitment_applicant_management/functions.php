<?php

function dd($value)
{
    echo '<pre>';
    var_dump($value);
    echo '</pre>';

    die();
}

function abort($code = 404)
{
    http_response_code($code);

    require "views/error/{$code}.php";

    die();
}

function routeToController($uri, $routes)
{
    if (array_key_exists($uri, $routes)) {
        require $routes[$uri];
    } else {
        abort();
    }
}

function validate($value, &$errors)
{
    if (empty(trim($_POST[$value] ?? ''))) {
        $errors[$value] = "{$value} field is required.";
    }
}


function sendMail($email, $subject, $message)
{
    require '../../../../PHPMailer/PHPMailerAutoload.php';
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'VehicleReservationManagement@gmail.com';
    $mail->Password = 'fzja ezgo ojdu fobc'; // 
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('AVALON@gmail.com', 'AVALON System Authenticator');
    $mail->addAddress($email);
    $mail->Subject = $subject;
    $mail->Body = $message;
    return $mail->send();
}
