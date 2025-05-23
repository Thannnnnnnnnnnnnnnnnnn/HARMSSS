<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

include '../conn.php';


$department_id = 7; 

$query = "
    SELECT 
        u.user_id, 
        u.first_name, 
        u.last_name, 
        CONCAT(u.first_name, ' ', u.last_name) AS Name,
        u.email, 
        u.role, 
        u.department_id , 
        d.department_id , 
        d.dept_name 
    FROM user_account u 
    INNER JOIN departments d ON u.department_id = d.department_id  
    WHERE d.department_id = ?
";

$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $department_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $user_name = $row['Name'];
        $role = $row['role'];
        $department = $row['dept_name'];
        $email = $row['email'];
        $department_id = $row['department_id'];
    }
} else {
    die("Error fetching user data: " . mysqli_error($connection));
}

mysqli_stmt_close($stmt);

$bookingDb = $connections["cr3_re"];
$guestDb = $connections["cr3_re"];
$notif = $connections["cr3_re"];
$usmDb = $connections["cr3_re"];
$roomDb = $connections["cr3_re"];

$showBookingComplete = false;
$reservationData = [];
$hasConfirmedBooking = false;

$rooms = [];
$roomQuery = $roomDb->query("SELECT room_name, RoomNumber, Capacity, price, Description, image FROM rooms WHERE status = 'Available'");
if ($roomQuery) {
    while ($row = $roomQuery->fetch_assoc()) {
        $rooms[] = $row;
    }
    $roomQuery->close();
} else {
    error_log("Error fetching rooms: " . $roomDb->error);
    echo "<div class='bg-red-100 text-red-800 p-2 mb-4 rounded'>Error fetching rooms: " . htmlspecialchars($roomDb->error) . "</div>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_guest_reservation'])) {
    $guest_name = trim($_POST['guest_name'] ?? '');
    $check_in = date("Y-m-d", strtotime($_POST['check_in'] ?? ''));
    $check_out = date("Y-m-d", strtotime($_POST['check_out'] ?? ''));
    $special_requests = trim($_POST['special_requests'] ?? '');
    $guest_preference = trim($_POST['Guest_preference'] ?? '');
    $preference_details = trim($_POST['preference_details'] ?? '');
    $payment_method = trim($_POST['payment_method'] ?? '');
    $payment_status = trim($_POST['payment_status'] ?? 'Pending');
    $room_number = intval($_POST['room_number'] ?? 101);
    $num_guests = intval($_POST['num_guests'] ?? 1);
    $emails = trim($_POST['Email'] ?? '');
    $phone = trim($_POST['Phone'] ?? '');


    if (empty($guest_name) || empty($check_in) || empty($check_out) || empty($guest_preference) || empty($preference_details) || empty($emails) || empty($phone)) {
        echo "<div class='bg-red-100 text-red-800 p-2 mb-4 rounded'>Error: All required fields must be filled.</div>";
        exit;
    }


    $checkStmt = $bookingDb->prepare("
        SELECT b.ReservationID, b.ReservationStatus 
        FROM `cr3_re`.guests g 
        JOIN `cr3_re`.booking b ON g.GuestID = b.GuestID 
        WHERE g.email = ? AND b.ReservationStatus IN ('Confirmed', 'Pending')
    ");
    if ($checkStmt === false) {
        error_log("Prepare failed for booking check: " . $bookingDb->error);
        die("Prepare failed: " . htmlspecialchars($bookingDb->error));
    }

    $checkStmt->bind_param("s", $emails);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    if ($checkResult === false) {
        error_log("Execute failed for booking check: " . $checkStmt->error);
        die("Execute failed: " . htmlspecialchars($checkStmt->error));
    }

    if ($checkResult->num_rows > 0) {
        $hasConfirmedBooking = true;
        error_log("Existing booking found for email: $emails");
    }
    $checkStmt->close();

    if (!$hasConfirmedBooking) {

        $stmt = $guestDb->prepare("INSERT INTO guests (guest_name, email, phone) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $guest_name, $emails, $phone);
        if (!$stmt->execute()) {
            error_log("Error inserting guest: " . $stmt->error);
            echo "<div class='bg-red-100 text-red-800 p-2 mb-4 rounded'>Error inserting guest: " . htmlspecialchars($stmt->error) . "</div>";
            $stmt->close();
            exit;
        }
        $guest_id = $stmt->insert_id;
        $stmt->close();


        $stmt = $bookingDb->prepare("INSERT INTO guest_preferences (PreferenceType, PreferenceDetail, GuestID) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $guest_preference, $preference_details, $guest_id);
        if (!$stmt->execute()) {
            error_log("Error inserting preference: " . $stmt->error);
            echo "<div class='bg-red-100 text-red-800 p-2 mb-4 rounded'>Error inserting preference: " . htmlspecialchars($stmt->error) . "</div>";
            $stmt->close();
            exit;
        }
        $stmt->close();

        
        $default_channel = 7;
        $cancel_token = bin2hex(random_bytes(32));
        $reservation_status = 'Pending';
        $stmt = $bookingDb->prepare("
            INSERT INTO booking (GuestID, CheckinDate, CheckOutDate, RoomNumber, ReservationStatus, BookingChannelID, SpecialRequests, NumberOfGuests, CancelToken)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("issssssis", $guest_id, $check_in, $check_out, $room_number, $reservation_status, $default_channel, $special_requests, $num_guests, $cancel_token);

        if ($stmt->execute()) {
            $showBookingComplete = true;
            $reservationData = [
                'check_in' => $check_in,
                'check_out' => $check_out,
                'room_number' => $room_number,
                'num_guests' => $num_guests
            ];
            $id = $stmt->insert_id;


            $check_in_formatted = date("F j, Y", strtotime($check_in));
            $check_out_formatted = date("F j, Y", strtotime($check_out));
           
            $cancel_link = "http://localhost/cr3/cr3bo/Guest/cancel.php?id=$id&token=$cancel_token";
            $confirm_link = "http://localhost/cr3/cr3bo/Guest/confirm.php?id=$id&token=$cancel_token";
            $subject = "Your Luxurious Avalon Booking - Action Required";
                $messageBody = <<<EOD
<html>
<head>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Cinzel&display=swap');
    body {
      font-family: 'Cinzel', Georgia, serif;
      margin: 0;
      padding: 0;
      background-color: #f4f4f4;
    }
    .email-container {
      background-color: #ffffff;
      max-width: 600px;
      margin: 40px auto;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }
    .header {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 20px;
      padding-bottom: 20px;
      margin-bottom: 30px;
    }
    .header img {
      height: 50px;
      display: block;
    }
    h2 {
      color: #8B5C29;
      text-align: center;
      margin-bottom: 25px;
      font-size: 24px;
    }
    .message {
      font-size: 16px;
      color: #444;
      text-align: center;
      line-height: 1.6;
      margin-bottom: 30px;
    }
    .details {
      background-color: #fdf8f3;
      padding: 25px;
      border-radius: 12px;
      font-size: 15px;
      color: #333;
      margin-bottom: 35px;
      line-height: 1.7;
    }
    .details strong {
      color: #8B5C29;
    }
    .footer {
      margin-top: 50px;
      font-size: 14px;
      text-align: center;
      color: #777;
      border-top: 1px solid #e0e0e0;
      padding-top: 25px;
      font-family: 'Cinzel', Georgia, serif;
    }
  </style>
</head>
<body>
  <div class="email-container">
    <div class="header">
      <img src="cid:logo1" alt="Avalon Logo">
      <img src="cid:logo2" alt="Avalon Logo Name">
    </div>
    <h2>Your Booking is Confirmed</h2>
    <div class="message">
      Dear <strong>$guest_name</strong>,<br><br>
      It is our pleasure to welcome you to <strong>Avalon Hotel & Restaurant Resort</strong>.<br>
      Below are the luxurious details of your upcoming stay.
    </div>
    <div class="details">
      <p>We are pleased to confirm your reservation under the <strong>ID #$id</strong>.</p>
      <p>You have selected <strong>Room $room_number</strong>, tailored for your comfort and relaxation.</p>
      <p>This reservation accommodates <strong>$num_guests guest(s)</strong>, ensuring an exquisite experience for all.</p>
      <p>Your scheduled arrival is on <strong>$check_in_formatted</strong>, and your departure will be on <strong>$check_out_formatted</strong>.</p>
      <p>We are committed to making your stay truly memorable.</p>
      <p>If you have any special requests or preferences, please do not hesitate to let us know. Our concierge is at your service 24/7.</p>
      <p><strong>Cancellation Policy:</strong> Cancellations are accepted without charges up to 24 hours before your check-in. A fee may apply after this period.</p>
      <p>To proceed with a cancellation, please click the button below:</p>
    </div>
    <div style="text-align: center; margin-top: 30px;">
      <a href="$cancel_link" style="display: inline-block;padding: 12px 24px; background-color: #b91c1c; color: #fff; font-family: 'Cinzel', serif; font-size: 16px; text-decoration: none; border-radius: 10px; border: 2px solid #9f1239; box-shadow: 0 4px 10px rgba(0,0,0,0.15); transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#991b1b'" onmouseout="this.style.backgroundColor='#b91c1c'">
        Cancel Reservation
      </a>
    </div>
    <div style="text-align: center; margin-top: 20px;">
      <a href="$confirm_link" style="display: inline-block; padding: 12px 24px; background-color: #8B5C29; color: #fff; font-family: 'Cinzel', serif; font-size: 16px; text-decoration: none; border-radius: 10px; border: 2px solid #734a1e; box-shadow: 0 4px 10px rgba(0,0,0,0.15); transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#a46a32'" onmouseout="this.style.backgroundColor='#8B5C29'">
        Confirm Reservation
      </a>
    </div>
    <div class="footer">
      We look forward to offering you a luxurious and relaxing experience.<br>
      Warmest regards,<br><br>
      <strong>The Avalon Team</strong>
    </div>
  </div>
</body>
</html>
EOD;


            $mail = new PHPMailer(true);
            try {
         
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'Loureymarkmondido12@gmail.com';
                $mail->Password = 'Konissbfcmkybxsd';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;
                $mail->setFrom('Loureymarkmondido12@gmail.com', 'Avalon Hotel');
                $mail->addAddress($emails, $guest_name);
                $mail->AddEmbeddedImage('../Logo.png', 'logo1');
                $mail->AddEmbeddedImage('../Logo-Name.png', 'logo2');
                $mail->Subject = $subject;
                $mail->Body = $messageBody;
                $mail->isHTML(true);
                $mail->send();
                error_log("Email sent successfully to $emails for reservation ID $id");
            } catch (Exception $e) {
                error_log("PHPMailer Error for reservation ID $id: {$mail->ErrorInfo}");
                echo "<div class='bg-red-100 text-red-800 p-2 mb-4 rounded'>Failed to send confirmation email: " . htmlspecialchars($mail->ErrorInfo) . "</div>";
            }

            // Insert notification
            $message = "Reservation #{$id} has been Created.";
            $notificationStmt = $notif->prepare("INSERT INTO notifications (notifType, message, status, Department, date_sent, User_ID) VALUES ('create', ?, 'Unread', 'Core 3', NOW(), NULL)");
            $notificationStmt->bind_param("s", $message) ;
            if (!$notificationStmt->execute()) {
                error_log("Error inserting notification: " . $notificationStmt->error);
            }
            $notificationStmt->close();


            $transac = $usmDb->prepare("INSERT INTO department_transaction (department_id, transaction_type, description) VALUES (?, 'Guest Book a room', 'Create New Booking')");
            $transac->bind_param("i", $department_id);
            if (!$transac->execute()) {
                error_log("Error inserting transaction: " . $transac->error);
            }
            $transac->close();

            // Insert audit trail
            $audit = $usmDb->prepare("INSERT INTO department_audit_trail (department_id, action, department_affected, module_affected, description) VALUES (?, 'Create','Core 3', 'booking', 'Create New Booking')");
            $audit->bind_param("i", $department_id);
            if (!$audit->execute()) {
                error_log("Error inserting audit trail: " . $audit->error);
            }
            $audit->close();
        } else {
            error_log("Error inserting booking: " . $stmt->error);
            echo "<div class='bg-red-100 text-red-800 p-2 mb-4 rounded'>Error creating booking: " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>AVALON</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    .luxury-bg {
      background: linear-gradient(to bottom, #f5e8c7, #fffaf0);
    }
    .gold-text {
      color: #d4af37;
    }
    .elegant-border {
      border: 2px solid #d4af37;
    }
    .btn {
      transition: all 0.3s ease;
    }
    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    }
    .payment-option {
      display: flex;
      align-items: center;
      padding: 10px;
      border: 2px solid #d4af37;
      border-radius: 8px;
      margin-bottom: 10px;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .payment-option:hover {
      background-color: #f7e6ca;
      transform: translateY(-2px);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    }
    .payment-option.unavailable {
      opacity: 0.5;
      cursor: not-allowed;
    }
    .payment-option.unavailable:hover {
      background-color: transparent;
      transform: none;
      box-shadow: none;
    }
    .paypal-img {
      width: 30px;
      height: 20px;
      margin-left: 10px;
    }
  </style>
</head>
<body class="luxury-bg font-serif text-gray-800">
  <div class="flex items-center justify-between p-4">
    <div class="flex justify-center flex-1">
      <div class="flex items-center">
        <img src="../Logo.png" alt="Company Logo" class="h-24 w-auto p-3">
        <img src="../Logo-Name.png" alt="Company Name" class="h-12 w-auto p-3">
      </div>
    </div>
    <div class="button-container flex space-x-4">
      <a href="Website.php" class="btn bg-[#F7E6CA] hover:bg-[#FFF6E8] font-semibold py-2 px-4 rounded-lg flex items-center">
        <i class="fas fa-home text-lg"></i>
      </a>
    </div>
  </div>
<main class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-6 border-t-2 elegant-border">
  <div class="max-w-4xl mx-auto px-4">
    <div class="mb-6">
      <ol class="flex items-center justify-between text-base font-medium text-gray-600">
        <li class="flex items-center <?php echo $showBookingComplete ? 'text-green-700' : (isset($_POST['room_number']) ? 'text-green-700' : 'text-yellow-700'); ?>">
          <div class="flex items-center justify-center w-8 h-8 rounded-full <?php echo $showBookingComplete ? 'bg-green-100 text-green-700' : (isset($_POST['room_number']) ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'); ?> mr-2">1</div>
          <span>Room Selected</span>
        </li>
        <li class="flex-auto border-t-2 border-<?php echo $showBookingComplete || isset($_POST['room_number']) ? 'green-400' : 'gray-400'; ?> mx-3"></li>
        <li class="flex items-center <?php echo $showBookingComplete ? 'text-green-700' : (isset($_POST['guest_name']) ? 'text-yellow-700' : 'text-gray-500'); ?>">
          <div class="flex items-center justify-center w-8 h-8 rounded-full <?php echo $showBookingComplete ? 'bg-green-100 text-green-700' : (isset($_POST['guest_name']) ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-200 text-gray-500'); ?> mr-2">2</div>
          <span>Guest Info</span>
        </li>
        <li class="flex-auto border-t-2 border-<?php echo $showBookingComplete || isset($_POST['guest_name']) ? 'green-400' : 'gray-400'; ?> mx-3"></li>
        <li class="flex items-center <?php echo $showBookingComplete ? 'text-green-700' : (isset($_POST['show_payment_form']) ? 'text-yellow-700' : 'text-gray-500'); ?>" id="payment-step">
          <div class="flex items-center justify-center w-8 h-8 rounded-full <?php echo $showBookingComplete ? 'bg-green-100 text-green-700' : (isset($_POST['show_payment_form']) ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-200 text-gray-500'); ?> mr-2">3</div>
          <span>Confirm Payment</span>
        </li>
        <li class="flex-auto border-t-2 border-<?php echo $showBookingComplete ? 'green-400' : 'gray-400'; ?> mx-3"></li>
        <li class="flex items-center <?php echo $showBookingComplete ? 'text-green-700' : 'text-gray-500'; ?>" id="complete-step">
          <div class="flex items-center justify-center w-8 h-8 rounded-full <?php echo $showBookingComplete ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500'; ?> mr-2">4</div>
          <span>Booking Complete</span>
        </li>
      </ol>
    </div>

<div id="room-form" class="bg-[#fdf9f3] py-12 px-4 sm:px-10 rounded-xl shadow-xl max-w-5xl mx-auto" style="<?php echo isset($_POST['room_number']) || $showBookingComplete ? 'display: none;' : 'display: block;'; ?>">
  <h1 class="text-4xl font-extrabold mb-12 text-center text-[#BFA76F] tracking-wide">Choose Your Room</h1>

  <div class="mb-10 max-w-md mx-auto">
    <label class="block text-lg font-semibold text-gray-700 mb-3">Number of Guests</label>
    <input type="number" id="num_guests" name="num_guests" min="1" max="20" value="1"
           oninput="filterRooms(); if (this.value < 1) this.value = 1;"
           class="w-full border border-[#d4c2a8] p-4 rounded-lg shadow-inner focus:outline-none focus:ring-2 focus:ring-[#BFA76F] bg-[#fffdf8] placeholder:text-gray-400"
           placeholder="e.g. 2" required/>
  </div>

  <div id="room-list" class="space-y-10">
    <?php if (empty($rooms)): ?>
      <div class="bg-white text-center p-10 rounded-2xl shadow-lg border border-[#e7dbc8]">
        <h2 class="text-2xl font-bold text-[#BFA76F] mb-4">No Rooms Available</h2>
        <p class="text-gray-600 text-base">We're sorry, but there are no rooms available at the moment that match your criteria. Please try adjusting your selection or check back later.</p>
      </div>
    <?php else: ?>
      <?php foreach ($rooms as $room): ?>
        <div class="room-card bg-white border border-[#e7dbc8] rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-transform duration-300 hover:scale-[1.01]" data-capacity="<?php echo $room['Capacity']; ?>">
          <div class="md:flex">
            <div class="md:w-1/2">
              <!-- location ng image ng room bosss -->
             <img src="../../cr3CGM/roomPhoto/<?php echo htmlspecialchars($room['image']); ?>" alt="<?php echo htmlspecialchars($room['room_name']); ?>" class="w-full h-64 object-cover md:rounded-l-2xl">
            </div>
            <div class="md:w-1/2 p-6 flex flex-col justify-between">
              <div>
                <h2 class="text-2xl font-bold text-[#BFA76F] mb-2"><?php echo htmlspecialchars($room['room_name']); ?></h2>
                <p class="text-gray-600 italic mb-4"><?php echo htmlspecialchars($room['Description']); ?></p>
                <div class="flex justify-between text-sm text-gray-600 mb-3">
                  <span>Capacity: <?php echo htmlspecialchars($room['Capacity']); ?> guests</span>
                  <span>Price: <span class="text-[#BFA76F] font-semibold">₱<?php echo number_format($room['price'], 2); ?></span></span>
                </div>
              </div>
              <div>
                <input type="hidden" name="room_number" value="<?php echo $room['RoomNumber']; ?>">
                <button type="button" onclick="showGuestForm(<?php echo $room['RoomNumber']; ?>)"
                        class="mt-4 bg-[#BFA76F] hover:bg-[#d2bc94] text-white px-6 py-3 rounded-full font-semibold tracking-wide w-full shadow-md transition duration-300">
                  Pick This Room
                </button>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

    <div id="guest-form" style="<?php echo isset($_POST['room_number']) && !isset($_POST['show_payment_form']) && !$showBookingComplete ? 'display: block;' : 'display: none;'; ?>">
      <h1 class="text-3xl font-bold mb-6 text-center gold-text">Enter Your Details</h1>
      <form method="POST" class="space-y-6">
        <input type="hidden" name="room_number" value="<?php echo htmlspecialchars($_POST['room_number'] ?? ''); ?>">
        <input type="hidden" name="num_guests" value="<?php echo htmlspecialchars($_POST['num_guests'] ?? ''); ?>">
        <div>
          <label class="block text-base font-medium text-gray-700">Full Name</label>
          <input type="text" name="guest_name" required placeholder="e.g. Juan Dela Cruz"
                 class="w-full border border-gray-300 p-3 rounded-md shadow-sm focus:border-gold-text"/>
        </div>
        <div class="flex flex-col md:flex-row gap-4">
          <div class="w-full">
            <label class="block text-base font-medium text-gray-700">Check-In</label>
            <input id="check-in" name="check_in" type="text" required
                   class="w-full border border-gray-300 p-3 rounded-md shadow-sm focus:border-gold-text"
                   placeholder="Select check-in date"/>
          </div>
          <div class="w-full">
            <label class="block text-base font-medium text-gray-700">Check-Out</label>
            <input id="check-out" name="check_out" type="text" required
                   class="w-full border border-gray-300 p-3 rounded-md shadow-sm focus:border-gold-text"
                   placeholder="Select check-out date"/>
          </div>
        </div>
        <div>
          <label class="block text-base font-medium text-gray-700">Guest Preference</label>
          <select name="Guest_preference" required class="w-full border border-gray-300 p-3 rounded-md shadow-sm focus:border-gold-text">
            <option value="">Select Preference</option>
            <option value="Room">Room</option>
            <option value="Food">Food</option>
            <option value="Services">Services</option>
            <option value="Other">Other</option>
          </select>
        </div>
        <div>
          <label class="block text-base font-medium text-gray-700">Preference Details</label>
          <input type="text" name="preference_details" required placeholder="e.g. Ocean view, Vegetarian meals..."
                 class="w-full border border-gray-300 p-3 rounded-md shadow-sm focus:border-gold-text"/>
        </div>
        <div>
          <label class="block text-base font-medium text-gray-700">Special Requests</label>
          <input type="text" name="special_requests" placeholder="e.g. Late check-in, Allergy needs..."
                 class="w-full border border-gray-300 p-3 rounded-md shadow-sm focus:border-gold-text"/>
        </div>
        <div class="flex flex-wrap gap-4">
          <div class="flex-1 min-w-[200px]">
            <label class="block text-base font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="Email" placeholder="example@gmail.com" required
                   class="w-full border border-gray-300 p-3 rounded-md shadow-sm focus:border-gold-text focus:outline-none"/>
          </div>
          <div class="flex-1 min-w-[200px]">
            <label class="block text-base font-medium text-gray-700 mb-1">Phone Number</label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-700">+63</span>
              <input type="tel" name="Phone" id="phone" value="9" required
                     class="w-full border border-gray-300 p-3 pl-10 rounded-md shadow-sm focus:border-gold-text focus:outline-none"
                     placeholder="09XXXXXXXXX" maxlength="10" pattern="09[0-9]{9}"/>
            </div>
          </div>
        </div>
        <div class="text-center">
          <button type="button" onclick="showPaymentForm()"
                  class="bg-[#F7E6CA] hover:bg-[#FFF6E8] text-[#594423] px-6 py-3 rounded-md font-medium text-base shadow-md transition duration-300">
            COMPLETE PAYMENT
          </button>
          <button type="button" onclick="goBackToRoom()"
                  class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-md font-medium text-base shadow-md transition duration-300">
            Back
          </button>
        </div>
      </form>
    </div>

    <div id="payment-form" style="<?php echo isset($_POST['show_payment_form']) && !$showBookingComplete ? 'display: block;' : 'display: none;'; ?>">
      <h1 class="text-3xl font-bold mb-6 text-center gold-text">Select Payment Method</h1>
      <form method="POST" class="space-y-6">
        <input type="hidden" name="room_number" value="<?php echo htmlspecialchars($_POST['room_number'] ?? ''); ?>">
        <input type="hidden" name="num_guests" value="<?php echo htmlspecialchars($_POST['num_guests'] ?? ''); ?>">
        <input type="hidden" name="guest_name" value="<?php echo htmlspecialchars($_POST['guest_name'] ?? ''); ?>">
        <input type="hidden" name="check_in" value="<?php echo htmlspecialchars($_POST['check_in'] ?? ''); ?>">
        <input type="hidden" name="check_out" value="<?php echo htmlspecialchars($_POST['check_out'] ?? ''); ?>">
        <input type="hidden" name="Guest_preference" value="<?php echo htmlspecialchars($_POST['Guest_preference'] ?? ''); ?>">
        <input type="hidden" name="preference_details" value="<?php echo htmlspecialchars($_POST['preference_details'] ?? ''); ?>">
        <input type="hidden" name="special_requests" value="<?php echo htmlspecialchars($_POST['special_requests'] ?? ''); ?>">
        <input type="hidden" name="Email" value="<?php echo htmlspecialchars($_POST['Email'] ?? ''); ?>">
        <input type="hidden" name="Phone" value="<?php echo htmlspecialchars($_POST['Phone'] ?? ''); ?>">
        <input type="hidden" name="show_payment_form" value="1">
        <div>
          <label class="block text-base font-medium text-gray-700">Payment Option</label>
          <div class="space-y-4">
            <div class="payment-option unavailable" onclick="event.preventDefault();">
              <input type="radio" name="payment_status" value="Paid" class="mr-2" disabled>
              <span class="text-lg font-semibold">Pay Now</span>
              <img src="https://cdn.pixabay.com/photo/2015/05/26/09/37/paypal-784404_1280.png" alt="PayPal" class="paypal-img">
              <span class="text-gray-500 ml-2">This payment method is unavailable for now</span>
            </div>
         <div class="payment-option border rounded-xl p-5 mb-4 shadow-sm cursor-pointer transition hover:shadow-md <?php echo isset($_POST['payment_status']) && $_POST['payment_status'] == 'Pending' ? 'border-[#BFA76F]' : 'border-gray-300'; ?>" 
     onclick="selectPaymentOption('Pending', 'Pay Later')">
       <label class="flex items-start space-x-3 cursor-pointer">
           <input type="radio" name="payment_status" value="Pending"
           class="mt-1 accent-[#BFA76F]" 
           <?php echo isset($_POST['payment_status']) && $_POST['payment_status'] == 'Pending' ? 'checked' : 'checked'; ?>>

                 <div>
                 <span class="block text-lg font-semibold text-[#594423]">Pay Later</span>
                 <span class="block text-sm text-gray-600">No advance payment — settle your bill when you arrive at the hotel.</span>
                 <ul class="text-sm text-gray-500 list-disc list-inside mt-2">
                <li>Free cancellation before check-in</li>
               <li>Pay in cash or card at front desk</li>
               <li>Reservation fully secured</li>
              </ul>
             </div>
            </label>
             </div>


          </div>
        </div>
        <div id="payment-method-section" style="display: none;">
          <label class="block text-base font-medium text-gray-700 mb-3">Select Payment Method</label>
          <div class="grid grid-cols-3 gap-3">
            <div class="payment-option" onclick="selectPaymentMethod('Credit Card')">
              <img src="https://www.visa.com.ph/dam/VCOM/regional/ap/philippines/global-elements/images/ph-visa-platinum-card-498x280.png" alt="Credit Card" class="w-full h-16 object-contain rounded-md">
              <p class="text-center text-gray-700 mt-1">Credit Card</p>
            </div>
            <div class="payment-option" onclick="selectPaymentMethod('Debit Card')">
              <img src="https://www.visa.co.in/dam/VCOM/regional/ap/india/global-elements/images/in-visa-gold-card-498x280.png" alt="Debit Card" class="w-full h-16 object-contain rounded-md">
              <p class="text-center text-gray-700 mt-1">Debit Card</p>
            </div>
            <div class="payment-option" onclick="selectPaymentMethod('Gcash')">
              <img src="https://www.bworldonline.com/wp-content/uploads/2021/09/GCash_Horizontal-Full-Blue-Transparent.png" alt="Gcash" class="w-full h-16 object-contain rounded-md">
              <p class="text-center text-gray-700 mt-1">Gcash</p>
            </div>
            <div class="payment-option" onclick="selectPaymentMethod('Paymaya')">
              <img src="https://www.bworldonline.com/wp-content/uploads/2021/09/Paymaya-logo.jpg" alt="Paymaya" class="w-full h-16 object-contain rounded-md">
              <p class="text-center text-gray-700 mt-1">PayMaya</p>
            </div>
            <div class="payment-option" onclick="selectPaymentMethod('PayPal')">
              <img src="https://cdn.pixabay.com/photo/2015/05/26/09/37/paypal-784404_1280.png" alt="PayPal" class="w-full h-16 object-contain rounded-md">
              <p class="text-center text-gray-700 mt-1">PayPal</p>
            </div>
          </div>
          <input type="hidden" name="payment_method" id="payment_method" value="<?php echo htmlspecialchars($_POST['payment_method'] ?? ''); ?>">
        </div>
        <div class="text-center">
          <button type="submit" name="submit_guest_reservation"
                  class="bg-[#F7E6CA] hover:bg-[#FFF6E8] text-[#594423] px-6 py-3 rounded-md font-medium text-base shadow-md transition duration-300">
            BOOK NOW
          </button>
          <button type="button" onclick="goBackToGuestInfo()"
                  class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-md font-medium text-base shadow-md transition duration-300">
            Back
          </button>
        </div>
      </form>
    </div>

    <div id="booking-complete" style="<?php echo $showBookingComplete ? 'display: block;' : 'display: none;'; ?>">
      <h1 class="text-3xl font-bold mb-6 text-center gold-text">Booking Confirmed</h1>
      <div class="text-center">
        <svg class="w-24 h-24 mx-auto mb-4 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <p class="text-lg text-gray-700 mb-3">Thank you for choosing Avalon Hotel And Restaurant Resort!</p>
        <p class="text-sm text-gray-600 mb-4">Your booking details have been sent to your email. We look forward to welcoming you.</p>
        <p class="text-sm text-gray-600 mb-4">If you have any questions, please contact us at <a href="mailto:Loureymarkmondido12@gmail.com" class="text-blue-500">Loureymarkmondido12@gmail.com</a></p>
        <div class="bg-gold-text text-white p-4 rounded-md shadow-md">
          <p class="text-base">Reservation Number: <span class="font-bold">AVL-<?php echo date('Ymd') . '-' . str_pad($id, 3, '0', STR_PAD_LEFT); ?></span></p>
          <p class="text-base">Room Number: <span class="font-bold"><?php echo htmlspecialchars($reservationData['room_number'] ?? ''); ?></span></p>
          <p class="text-base">Number of Guests: <span class="font-bold"><?php echo htmlspecialchars($reservationData['num_guests'] ?? ''); ?></span></p>
          <p class="text-base">Check-In: <span class="font-bold"><?php echo htmlspecialchars($reservationData['check_in'] ?? ''); ?></span></p>
          <p class="text-base">Check-Out: <span class="font-bold"><?php echo htmlspecialchars($reservationData['check_out'] ?? ''); ?></span></p>
        </div>
        <button onclick="window.location.href='Website.php'" class="mt-6 bg-green-700 hover:bg-green-800 text-white px-6 py-3 rounded-md font-medium text-base shadow-md transition duration-300">
          Return to Home
        </button>
      </div>
    </div>
  </div>
</main>

<script>
  let formData={
    guestName:'',
    checkIn:'',
    checkOut:'',
    guestPreference:'',
    preferenceDetails:'',
    specialRequests:'',
    roomNumber:'',
    email:'',
    phon:'',
  }
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($hasConfirmedBooking): ?>
       formData.guestName = <?php echo json_encode($_POST['guest_name'] ?? ''); ?>;
        formData.checkIn = <?php echo json_encode($_POST['check_in'] ?? ''); ?>;
        formData.checkOut = <?php echo json_encode($_POST['check_out'] ?? ''); ?>;
        formData.guestPreference = <?php echo json_encode($_POST['Guest_preference'] ?? ''); ?>;
        formData.preferenceDetails = <?php echo json_encode($_POST['preference_details'] ?? ''); ?>;
        formData.specialRequests = <?php echo json_encode($_POST['special_requests'] ?? ''); ?>;
        formData.roomNumber = <?php echo json_encode($_POST['room_number'] ?? ''); ?>;
        formData.numGuests = <?php echo json_encode($_POST['num_guests'] ?? ''); ?>;
        formData.email = <?php echo json_encode($_POST['Email'] ?? ''); ?>;
        formData.phone = <?php echo json_encode($_POST['Phone'] ?? ''); ?>;

        Swal.fire({
            icon: 'warning',
            title: 'Confirmed Booking Exists',
            text: 'You have a confirmed booking. Need to cancel first.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#8B5C29'
        }).then(() => {
            document.getElementById('room-form').style.display = 'none';
            document.getElementById('payment-form').style.display = 'none';
            document.getElementById('guest-form').style.display = 'block';

            
            document.querySelector('li:nth-child(1)').classList.remove('text-yellow-700');
            document.querySelector('li:nth-child(1)').classList.add('text-green-700');
            document.querySelector('li:nth-child(1) div').classList.remove('bg-yellow-100');
            document.querySelector('li:nth-child(1) div').classList.add('bg-green-100');
            document.querySelector('li:nth-child(3)').classList.remove('text-gray-500');
            document.querySelector('li:nth-child(3)').classList.add('text-yellow-700');
            document.querySelector('li:nth-child(3) div').classList.remove('bg-gray-200');
            document.querySelector('li:nth-child(3) div').classList.add('bg-yellow-100');
            document.getElementById('payment-step').classList.remove('text-yellow-700');
            document.getElementById('payment-step').classList.add('text-gray-500');
            document.getElementById('payment-step').querySelector('div').classList.remove('bg-yellow-100');
            document.getElementById('payment-step').querySelector('div').classList.add('bg-gray-200');


            document.querySelector('#guest-form [name="guest_name"]').value = formData.guestName;
            document.querySelector('#guest-form [name="check_in"]').value = formData.checkIn;
            document.querySelector('#guest-form [name="check_out"]').value = formData.checkOut;
            document.querySelector('#guest-form [name="Guest_preference"]').value = formData.guestPreference;
            document.querySelector('#guest-form [name="preference_details"]').value = formData.preferenceDetails;
            document.querySelector('#guest-form [name="special_requests"]').value = formData.specialRequests;
            document.querySelector('#guest-form [name="room_number"]').value = formData.roomNumber;
            document.querySelector('#guest-form [name="num_guests"]').value = formData.numGuests;
            document.querySelector('#guest-form [name="Email"]').value = formData.email;
            document.querySelector('#guest-form [name="Phone"]').value = formData.phone;
        });
    <?php endif; ?>
});
  flatpickr("#check-in", {
    dateFormat: "Y-m-d",
    minDate: "today"
  });
  flatpickr("#check-out", {
    dateFormat: "Y-m-d",
    minDate: new Date().fp_incr(1)
  });

  function filterRooms() {
    const numGuests = parseInt(document.getElementById('num_guests').value) || 1;
    const roomCards = document.querySelectorAll('.room-card');
    
    roomCards.forEach(card => {
      const capacity = parseInt(card.getAttribute('data-capacity'));
      if (capacity >= numGuests) {
        card.style.display = 'block';
      } else {
        card.style.display = 'none';
      }
    });
  }

  function showGuestForm(roomNumber) {
    const guestCount = document.getElementById('num_guests').value;
    document.querySelector('#guest-form [name="room_number"]').value = roomNumber;
    document.querySelector('#guest-form [name="num_guests"]').value = guestCount;
    document.getElementById('room-form').style.display = 'none';
    document.getElementById('guest-form').style.display = 'block';
    document.querySelector('li:nth-child(1)').classList.remove('text-yellow-700');
    document.querySelector('li:nth-child(1)').classList.add('text-green-700');
    document.querySelector('li:nth-child(1) div').classList.remove('bg-yellow-100');
    document.querySelector('li:nth-child(1) div').classList.add('bg-green-100');
    document.querySelector('li:nth-child(3)').classList.remove('text-gray-500');
    document.querySelector('li:nth-child(3)').classList.add('text-yellow-700');
    document.querySelector('li:nth-child(3) div').classList.remove('bg-gray-200');
    document.querySelector('li:nth-child(3) div').classList.add('bg-yellow-100');
  }

  function showPaymentForm() {
    const guestName = document.querySelector('#guest-form [name="guest_name"]').value;
    const checkIn = document.querySelector('#guest-form [name="check_in"]').value;
    const checkOut = document.querySelector('#guest-form [name="check_out"]').value;
    const guestPreference = document.querySelector('#guest-form [name="Guest_preference"]').value;
    const preferenceDetails = document.querySelector('#guest-form [name="preference_details"]').value;
    const roomNumber = document.querySelector('#guest-form [name="room_number"]').value;
    const numGuests = document.querySelector('#guest-form [name="num_guests"]').value;
    const email = document.querySelector('#guest-form [name="Email"]').value;
    const phone = document.querySelector('#guest-form [name="Phone"]').value;

    if (!guestName || !checkIn || !checkOut || !guestPreference || !preferenceDetails || !email || !phone) {
      Swal.fire({
        icon: 'warning',
        title: 'Oops!',
        html: '<strong>Please complete all required fields</strong><br>Full Name, Check-In, Check-Out, Guest Preference, Details, Email, and Phone Number are needed.',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Got it'
      });
      return;
    }

    const specialRequests = document.querySelector('#guest-form [name="special_requests"]').value;

    document.querySelector('#payment-form [name="guest_name"]').value = guestName;
    document.querySelector('#payment-form [name="check_in"]').value = checkIn;
    document.querySelector('#payment-form [name="check_out"]').value = checkOut;
    document.querySelector('#payment-form [name="Guest_preference"]').value = guestPreference;
    document.querySelector('#payment-form [name="preference_details"]').value = preferenceDetails;
    document.querySelector('#payment-form [name="special_requests"]').value = specialRequests;
    document.querySelector('#payment-form [name="room_number"]').value = roomNumber;
    document.querySelector('#payment-form [name="num_guests"]').value = numGuests;
    document.querySelector('#payment-form [name="Email"]').value = email;
    document.querySelector('#payment-form [name="Phone"]').value = phone;

    document.getElementById('guest-form').style.display = 'none';
    document.getElementById('payment-form').style.display = 'block';
    document.getElementById('payment-step').classList.remove('text-gray-500');
    document.getElementById('payment-step').classList.add('text-yellow-700');
    document.getElementById('payment-step').querySelector('div').classList.remove('bg-gray-200');
    document.getElementById('payment-step').querySelector('div').classList.add('bg-yellow-100');
  }

  function goBackToRoom() {
    document.getElementById('payment-form').style.display = 'none';
    document.getElementById('guest-form').style.display = 'none';
    document.getElementById('room-form').style.display = 'block';
    document.querySelector('li:nth-child(3)').classList.remove('text-yellow-700');
    document.querySelector('li:nth-child(3)').classList.add('text-gray-500');
    document.querySelector('li:nth-child(3) div').classList.remove('bg-yellow-100');
    document.querySelector('li:nth-child(3) div').classList.add('bg-gray-200');
    document.getElementById('payment-step').classList.remove('text-yellow-700');
    document.getElementById('payment-step').classList.add('text-gray-500');
    document.getElementById('payment-step').querySelector('div').classList.remove('bg-yellow-100');
    document.getElementById('payment-step').querySelector('div').classList.add('bg-gray-200');
  }

  function goBackToGuestInfo() {
    document.getElementById('payment-form').style.display = 'none';
    document.getElementById('room-form').style.display = 'none';
    document.getElementById('guest-form').style.display = 'block';
    document.getElementById('payment-step').classList.remove('text-yellow-700');
    document.getElementById('payment-step').classList.add('text-gray-500');
    document.getElementById('payment-step').querySelector('div').classList.remove('bg-yellow-100');
    document.getElementById('payment-step').querySelector('div').classList.add('bg-gray-200');
  }

  function selectPaymentOption(status, method) {
    if (status === 'Paid') return;
    document.querySelector(`input[name="payment_status"][value="${status}"]`).checked = true;
    const paymentMethodSection = document.getElementById('payment-method-section');
    paymentMethodSection.style.display = status === 'Paid' ? 'block' : 'none';
    document.getElementById('payment_method').value = method;
  }

  function selectPaymentMethod(method) {
    document.getElementById('payment_method').value = method;
    document.querySelectorAll('.payment-option').forEach(option => {
      option.classList.remove('border-gold-text');
      option.classList.add('border-gray-300');
    });
    event.currentTarget.classList.remove('border-gray-300');
    event.currentTarget.classList.add('border-gold-text');
  }

  document.getElementById('phone').addEventListener('input', function(e) {
    let value = e.target.value;
    if (!value.startsWith('9')) {
      e.target.value = '9';
      return;
    }
    value = value.replace(/[^0-9]/g, '');
    if (value.length > 11) {
      value = value.slice(0, 11);
    }
    e.target.value = value;
  });
  function filterRooms() {
  const numGuests = parseInt(document.getElementById('num_guests').value) || 1;
  const roomCards = document.querySelectorAll('.room-card');
  let visibleCount = 0;

  roomCards.forEach(card => {
    const capacity = parseInt(card.getAttribute('data-capacity')) || 0;
    if (capacity >= numGuests) {
      card.style.display = 'block';
      visibleCount++;
    } else {
      card.style.display = 'none';
    }
  });


  let noRoomsMessage = document.getElementById('no-room-message');
  if (!noRoomsMessage) {

    noRoomsMessage = document.createElement('div');
    noRoomsMessage.id = 'no-room-message';
    noRoomsMessage.className = 'bg-white text-center p-10 rounded-2xl shadow-lg border border-[#e7dbc8] text-gray-600 text-base';
    noRoomsMessage.innerHTML = `
      <h2 class="text-2xl font-bold text-[#BFA76F] mb-4">No Suitable Rooms</h2>
      <p>Sorry, there are no rooms available that can accommodate Thist Total of guest(s). Please try a smaller number or contact us for group booking options.</p>
      <p class="text-sm text-gray-500 mt-2">If you have any questions, please contact us at <a href="mailto:Loureymarkmondido12@gmail.com" class="text-blue-500">
    `;
    document.getElementById('room-list').appendChild(noRoomsMessage);
  }

  noRoomsMessage.style.display = (visibleCount === 0) ? 'block' : 'none';
}

  document.getElementById('phone').addEventListener('keydown', function(e) {
    const value = e.target.value;
    if ((e.key === 'Backspace' || e.key === 'Delete') && value === '9') {
      e.preventDefault();
    }
    if (value.length >= 11 && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(e.key)) {
      e.preventDefault();
    }
  });


  window.onload = filterRooms;
</script>
</body>
</html>