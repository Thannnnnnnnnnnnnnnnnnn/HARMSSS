<?php
include '../conn.php';
include '../user.php';
$bookingDb = $connections["cr3_re"];
$usmDb = $connections["cr3_re_usm"];
$notif = $connections["cr3_re"];

function renderPage($title, $iconSvg, $headline, $message, $color, $button = true) {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8' />
        <meta name='viewport' content='width=device-width, initial-scale=1.0' />
        <title>{$title}</title>
        <script src='https://cdn.tailwindcss.com'></script>
        <link href='https://fonts.googleapis.com/css2?family=Cinzel&display=swap' rel='stylesheet'>
        <style>
            body {
                font-family: 'Cinzel', serif;
            }
        </style>
    </head>
    <body class='min-h-screen bg-gradient-to-br from-[#f7f4ef] to-[#fff] flex items-center justify-center px-4'>
        <div class='bg-white border border-[#e0d6c6] shadow-xl rounded-3xl p-10 max-w-xl w-full text-center'>
            <img src='../Logo.png' alt='Logo' class='mx-auto h-16 mb-4'>
            <img src='../Logo-Name.png' alt='Logo Name' class='mx-auto h-10 mb-6'>

            <div class='mb-6'>{$iconSvg}</div>
            <h1 class='text-3xl text-{$color}-700 font-bold mb-4'>{$headline}</h1>
            <p class='text-[#5c4b37] text-lg mb-6'>{$message}</p>";
         
            if ($button) { 
                  // ilagay to sa website ng hotel para masaya
                echo "<a href='Website.php' class='inline-block mt-4 px-6 py-3 rounded-lg text-white bg-[#8B5C29] hover:bg-[#a46a32] transition duration-300'>
                    Go To Avalon Hotel & Resort Website
                </a>";
            }

    echo "  <div class='mt-6 text-sm text-gray-500'>
                We hope to welcome you again soon at <span class='italic'>Avalon Hotel & Resort</span>.
            </div>
        </div>
    </body>
    </html>";
}

if (isset($_GET['id']) && isset($_GET['token'])) {
    $reservation_id = $_GET['id'];
    $cancel_token = $_GET['token'];

    $stmt = $bookingDb->prepare("SELECT ReservationID, CancelToken, ReservationStatus FROM booking WHERE ReservationID = ? AND CancelToken = ?");
    $stmt->bind_param("is", $reservation_id, $cancel_token);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservation = $result->fetch_assoc();
    $stmt->close();

    if ($reservation && $reservation['ReservationStatus'] !== 'Cancelled') {
        $stmt = $bookingDb->prepare("UPDATE booking SET ReservationStatus = 'Cancelled' WHERE ReservationID = ? AND CancelToken = ?");
        $stmt->bind_param("is", $reservation_id, $cancel_token);
        if ($stmt->execute()) {
              $message = "Booking #{$reservation_id} has been cancelled.";
            $notificationStmt = $notif->prepare("INSERT INTO notifications (notifType, message, status, Department, date_sent, User_ID) VALUES ('cancel', ?, 'Unread', 'Core 3', NOW(), NULL)");
            $notificationStmt->bind_param("s", $message);
            $notificationStmt->execute();
            $notificationStmt->close();
                // transaction d2
         $transac = $usmDb->prepare("INSERT INTO department_transaction (department_id , transaction_type, description)
                           VALUES (?,'Cancelled Booking', 'Guest Cancelled Booking')");
         $transac->bind_param("i", $department_id);
        $transac->execute();
         // audit d2
            $audit = $usmDb->prepare("INSERT INTO department_audit_trail 
             (department_id, action, department_affected, module_affected, description)
             VALUES (?, ?, ?, ?, ?)");
             $action = 'Cancelled Booking';
            $module = 'Booking';
            $description = 'Guest Cancelled Booking';
            $department = 'Core 3';
             $audit->bind_param("issss", $department_id,$action, $department, $module, $description);
             $audit->execute();
            renderPage(
                "Booking Cancelled",
                "<svg class='mx-auto h-16 w-16 text-red-600' fill='none' stroke='currentColor' stroke-width='1.5' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' d='M12 9v2m0 4h.01m-6.938 4h13.856c1.054 0 1.635-1.14 1.077-2.05L13.077 4.95c-.527-.91-1.86-.91-2.387 0L3.005 17.95C2.447 18.86 3.028 20 4.082 20z' /></svg>",
                "Booking Cancelled",
                "Your Booking <span class='font-semibold'>#{$reservation_id}</span> has been successfully cancelled.",
                "red"
            );
        } else {
            renderPage(
                "Error Cancelling",
                "<svg class='mx-auto h-16 w-16 text-red-600' fill='none' stroke='currentColor' stroke-width='1.5' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' d='M12 9v2m0 4h.01m-6.938 4h13.856c1.054 0 1.635-1.14 1.077-2.05L13.077 4.95c-.527-.91-1.86-.91-2.387 0L3.005 17.95C2.447 18.86 3.028 20 4.082 20z' /></svg>",
                "Error",
                "There was a problem cancelling the Booking. Please try again.",
                "red"
            );
        }
        $stmt->close();
    } elseif ($reservation && $reservation['ReservationStatus'] === 'Cancelled') {
        renderPage(
            "Already Cancelled",
            "<svg class='mx-auto h-16 w-16 text-yellow-500' fill='none' stroke='currentColor' stroke-width='1.5' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' d='M12 9v2m0 4h.01m-6.938 4h13.856c1.054 0 1.635-1.14 1.077-2.05L13.077 4.95c-.527-.91-1.86-.91-2.387 0L3.005 17.95C2.447 18.86 3.028 20 4.082 20z' /></svg>",
            "Already Cancelled",
            "This Booking has already been cancelled.",
            "yellow"
        );
    } else {
        renderPage(
            "Invalid Cancellation",
            "<svg class='mx-auto h-16 w-16 text-red-600' fill='none' stroke='currentColor' stroke-width='1.5' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' d='M12 9v2m0 4h.01m-6.938 4h13.856c1.054 0 1.635-1.14 1.077-2.05L13.077 4.95c-.527-.91-1.86-.91-2.387 0L3.005 17.95C2.447 18.86 3.028 20 4.082 20z' /></svg>",
            "Invalid Request",
            "Invalid cancellation request. The link may be incorrect or expired.",
            "red"
        );
    }
} else {
    renderPage(
        "Missing Info",
        "<svg class='mx-auto h-16 w-16 text-red-600' fill='none' stroke='currentColor' stroke-width='1.5' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' d='M12 9v2m0 4h.01m-6.938 4h13.856c1.054 0 1.635-1.14 1.077-2.05L13.077 4.95c-.527-.91-1.86-.91-2.387 0L3.005 17.95C2.447 18.86 3.028 20 4.082 20z' /></svg>",
        "Missing Parameters",
        "Booking ID or cancellation token is missing.",
        "red"
    );
}
?>
