<?php
require 'Database.php';

$config = require 'config.php';
$db = new Database($config['database']);

if (isset($_POST['submit'])) {
    $date = !empty($_POST['date']) ? $_POST['date'] : date('Y-m-d');

    $db->query(
        "INSERT INTO roomreservations (full_name, email, phone, date, time, guest) 
         VALUES (:full_name, :email, :phone, :date, :time, :guest)",
        [
            ':full_name' => $_POST['full_name'],
            ':email' => $_POST['email'],
            ':phone' => $_POST['phone'],
            ':date' => $date,
            ':time' => $_POST['time'],
            ':guest' => $_POST['guest'],
        ]
    );

    // Redirect to the next page
    header("Location: ________________________________________.php"); // Change 'confirmation.php' to your desired page
    exit(); // Ensure no further code executes after redirect
}
?>

<!DOCTYPE html>
<html lang="en">
<script src="script.js" defer></script>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Reservation Room Selection</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://unpkg.com/lucide-icons/dist/umd/lucide.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide-icons"></script>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>

<body>
    <div class="container">
        <header>
            <div class="header-content">
                <h1>Check Availability for Reservation</h1>
            </div>
        </header>

        <main>
            <div class="form-container">
                <div class="form-grid">
                    <!-- Reservation Form -->
                    <div class="form-column">
                        <form id="reservationForm" method="post">
                            <label class="form-label">
                                <span class="label-text">Full Name</span>
                                <input type="text" placeholder="Enter your name" name="full_name" required>
                            </label>

                            <label class="form-label">
                                <span class="label-text">Email</span>
                                <input type="email" placeholder="Enter your email" name="email" required>
                            </label>

                            <label class="form-label">
                                <span class="label-text">Phone</span>
                                <input type="number" placeholder="Enter your phone number" name="phone" required>
                            </label>

                            <label class="form-label">
                                <span class="label-text">From</span>
                                <input type="date" name="date" required>
                            </label>

                            <label class="form-label">
                                <span class="label-text">To</span>
                                <input type="date" name="checkout_date" required>
                            </label>

                            <label class="form-label">
                                <span class="label-text">Number of Guests</span>
                                <select name="guest" required>
                                    <option value="" disabled selected>How many Guests?</option>
                                    <option value="1">1 Guest</option>
                                    <option value="2">2 Guests</option>
                                    <option value="3">3 Guests</option>
                                    <option value="4">4 Guests</option>
                                    <option value="5">5 Guests</option>
                                </select>
                            </label>

                            <div class="button-container">
                                <button type="submit" name="submit">
                                    Check Availability <i data-lucide="chevron-right"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>







    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const selectElement = document.querySelector("select[name='room']");
            const roomImage = document.getElementById("roomImage");

            // Map room types to images

            const roomImages = {
                "1": "deluxe-king-room.jpg",
                "2": "executive-club-suite.jpg",
                "3": "presidential-suite.jpg",
                "4": "overwater-villa.jpg",
                "5": "presidential-suite.jpg"

            };

            selectElement.addEventListener("change", function() {
                // Change image based on selected room
                if (roomImages[this.value]) {
                    roomImage.src = roomImages[this.value];
                }
            });
        });
    </script>






</body>

</html>