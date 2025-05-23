<?php
include '../conn.php';
include '../user.php';
$bookingDb = $connections["cr3_re"];
$usmDb = $connections["cr3_re_usm"];
$notif = $connections["cr3_re"];
$confirmed = false;
$error = '';
$guestName = '';
$reservationId = '';
$checkIn = '';
$checkOut = '';

if (isset($_GET['id']) && isset($_GET['token'])) {
    $reservation_id = intval($_GET['id']);
    $token = $_GET['token'];


    $stmt = $bookingDb->prepare("SELECT CancelToken, CheckinDate, CheckOutDate, GuestID, ReservationStatus FROM booking WHERE ReservationID = ?");
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $stmt->bind_result($stored_token, $checkInRaw, $checkOutRaw, $guestID, $reservationStatus);

    $resultFound = $stmt->fetch();
    $stmt->close(); 

    if ($resultFound) {

        $transac = $usmDb->prepare("INSERT INTO department_transaction (department_id , transaction_type, description)
                           VALUES (?,'Confirmed Booking', 'Guest Confirmed Booking')");
         $transac->bind_param("i", $department_id);
        $transac->execute();
        
            $message = "Booking #{$reservation_id} has been Confirmed.";
            $notificationStmt = $notif->prepare("INSERT INTO notifications (notifType, message, status, Department, date_sent, User_ID) VALUES ('Confirmed', ?, 'Unread', 'Core 3', NOW(), NULL)");
            $notificationStmt->bind_param("s", $message);
            $notificationStmt->execute();
            $notificationStmt->close();
    // audit d2
            $audit = $usmDb->prepare("INSERT INTO department_audit_trail 
             (department_id, action, department_affected, module_affected, description)
             VALUES (?,?, ?, ?, ?)");
             $action = 'Confirmed Booking';
            $module = 'booking';
            $description = 'Guest Confirmed Booking';
            $department = 'Core 3';
             $audit->bind_param("issss", $department_id,$action, $department, $module, $description);

             $audit->execute();
        if ($stored_token === $token) {
            if ($reservationStatus === 'Cancelled') {
              
                $error = "Your Booking has already been cancelled. To secure your stay, please make a new booking.";
            } else {
               
                $update = $bookingDb->prepare("UPDATE booking SET ReservationStatus = 'Confirmed' WHERE ReservationID = ?");
                $update->bind_param("i", $reservation_id);
                if ($update->execute()) {
                    $confirmed = true;
                    $reservationId = $reservation_id;
                    $checkIn = date("F j, Y", strtotime($checkInRaw));
                    $checkOut = date("F j, Y", strtotime($checkOutRaw));

                 
                    $guestStmt = $connections["cr3_re"]->prepare("SELECT guest_name FROM guests WHERE GuestID = ?");
                    $guestStmt->bind_param("i", $guestID);
                    $guestStmt->execute();
                    $guestStmt->bind_result($guestName);
                    $guestStmt->fetch();
                    $guestStmt->close();
                } else {
                    $error = 'Failed to confirm Booking. Please try again.';
                }
                $update->close();
            }
        } else {
            $error = 'Invalid confirmation token.';
        }
    } else {
        $error = 'Booking not found.';
    }
} else {
    $error = 'Missing confirmation parameters.';
}
?>



<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8' />
    <meta name='viewport' content='width=device-width, initial-scale=1.0' />
    <title>Booking Confirmed</title>
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

      
       <?php if ($confirmed): ?>
    <div class='bg-white border border-[#e0d6c6] shadow-xl rounded-3xl p-10 max-w-xl w-full text-center'>
         <div class='mb-6'>
            <svg class='mx-auto h-16 w-16 text-green-600' fill='none' stroke='currentColor' stroke-width='1.5' viewBox='0 0 24 24'>
                <path stroke-linecap='round' stroke-linejoin='round' d='M4.5 12.75l6 6 9-13.5' />
            </svg>
        </div>
        <h1 class='text-3xl text-green-700 font-bold mb-4'>Booking Confirmed</h1>
        <p class='text-[#5c4b37] text-lg mb-2'>Thank you, <span class='font-semibold'><?= htmlspecialchars($guestName) ?></span>!</p>
        <p class='text-[#5c4b37] text-lg mb-2'>Your Booking <span class='font-semibold'>#<?= $reservationId ?></span> has been confirmed.</p>
        <p class='text-[#5c4b37] text-lg mb-6'>Stay Dates: <span class='font-semibold'><?= $checkIn ?></span> to <span class='font-semibold'><?= $checkOut ?></span></p>
             <!--     ilagay to sa website ng hotel para masya -->
        <a href='Website.php' class='inline-block mt-4 px-6 py-3 rounded-lg text-white bg-[#8B5C29] hover:bg-[#a46a32] transition duration-300'>
            Go To Avalon Hotel & Resort Website
        </a>
        <div class='mt-6 text-sm text-gray-500'>
            We look forward to welcoming you to <span class='italic'>Avalon Hotel & Resort</span>.
        </div>
    </div>
<?php else: ?>
    <div class='bg-white border border-red-400 text-red-700 shadow-xl rounded-3xl p-10 max-w-xl w-full text-center'>
        <h1 class='text-3xl font-bold mb-4'>Booking Not Confirmed</h1>
        <p class='mb-6'><?= htmlspecialchars($error) ?></p>
        <!--     ilagay to sa website ng hotel para masya -->
        <a href='Website.php' class='inline-block mt-4 px-6 py-3 rounded-lg text-white bg-[#8B5C29] hover:bg-[#a46a32] transition duration-300'>
            Return to Avalon Hotel & Resort Website
        </a>
    </div> <div class='mt-6 text-sm text-gray-500'>
                We hope to welcome you again soon at <span class='italic'>Avalon Hotel & Resort</span>.
            </div>
        </div>
<?php endif; ?>
    
           

</body>
</html>

