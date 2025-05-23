<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

include 'connection.php'; // Your PDO DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $GuestID = $_POST['GuestID'];

    try {
        // Update guest checkout time
        $stmt = $conn->prepare("UPDATE guests SET check_out = CURRENT_TIMESTAMP WHERE GuestID = :guestId");
        $stmt->bindParam(':guestId', $GuestID, PDO::PARAM_INT);

        if ($stmt->execute()) {

            // Get guest email and name
            $getEmail = $conn->prepare("SELECT guest_name, email FROM guests WHERE GuestID = :id");
            $getEmail->execute([':id' => $GuestID]);
            $guest = $getEmail->fetch(PDO::FETCH_ASSOC);

            if ($guest) {
                $guestEmail = $guest['email'];
                $guestName = htmlspecialchars($guest['guest_name']);
                $feedbackLink = "http://localhost/manager/guest_management/feedbacklink.php" . $GuestID;

                // Send feedback email via PHPMailer
                $mail = new PHPMailer(true);

                try {
                    // SMTP Settings
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'jerichomaghilom08@gmail.com'; // Sender email
                    $mail->Password   = 'fona jyny mxmf fcqw'; // App password from Gmail
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;

                    // Email content
                    $mail->setFrom('jerichomaghilom08@gmail.com', 'Avalon Hotel');
                    $mail->addAddress($guestEmail, $guestName);
                    $mail->isHTML(true);
                    $mail->Subject = "We’d love your feedback!";
                    $mail->Body    = "
<html>
  <body>
    <p>Hi $guestName,</p>
    <p>Thank you for staying with us! We'd really appreciate it if you could share your experience.</p>
    <p>
      <a href='http://localhost/manager/guest_management/feedbacklink.php?guest_id=$GuestID&name=" . urlencode($guestName) . "'
         style='background:#594423;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>
         Leave Feedback
      </a>
    </p>
    <p>Warm regards,<br><strong>Hotel Team</strong></p>
  </body>
</html>";

                    $mail->send();
                } catch (Exception $e) {
                    error_log("Email error: " . $mail->ErrorInfo);
                }
            }

            // Redirect with success
            header("Location: ../guest.php?message=Checkout successful");
            exit();
        } else {
            header("Location: ../guest.php?message=Not check-out");
        }

    } catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}