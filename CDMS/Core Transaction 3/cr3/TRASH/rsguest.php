<?php
session_start();
require 'Database.php';
require 'functions.php';
$config = require 'config.php';
$db = new Database($config['database']);
$roomTypes = $db->query('SELECT room_id, room_name FROM rooms')->fetchAll();
$reservations = $db->query('SELECT * FROM reservations')->fetchAll();
$errors = [];


if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $room_id =   trim($_POST['room_id'] ?? '');
    $checkin_date = trim($_POST['checkin_date'] ?? '');
    $checkout_date = trim($_POST['checkout_date'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $guests = trim($_POST['guests'] ?? '');
    //  dd($_POST);
    // dd($errors);
    //     try {
    //         $stmt = $db->query("INSERT INTO reservations ( first_name, last_name, room_id, checkin_date, checkout_date, phone, guests) VALUES (:first_name, :last_name, :room_id, :checkin_date, :checkout_date, :phone, :guests)", [

    //             ':first_name' => $first_name,
    //             ':last_name' => $last_name,
    //             ':room_id' => $room_id,
    //             ':checkin_date' => $checkin_date,
    //             ':checkout_date' => $checkout_date,
    //             ':phone' => $phone,
    //             ':guests' => $guests
    //         ]);
    //         // dd($stmt->errorInfo());
    //     } catch (PDOException $e) {
    //         if ($e->errorInfo[1] == 1062) {
    //             $errors['chechin_date'] = "Reservation already exists";
    //         }
    //     }
    // }

    // IF ERRORS IS EMPTY THIS BLOCK WILL RUN
    if (empty($errors)) {
        $db->query('UPDATE reservations SET first_name = :first_name, last_name = :last_name, room_id = :room_id, checkin_date = :checkin_date, checkout_date = :checkout_date, phone = :phone, guests = :guests', [

            // UPDATE SQL STATEMENT FOR UPDATING RECORDS. ADD THE WHERE CONDITION IF NECESSARY.
            ':first_name' => $_POST['first_name'],
            ':last_name' => $_POST['last_name'],
            ':room_id' => $_POST['room_id'],
            ':checkin_date' => $_POST['chekin_date'],
            ':checkout_date' => $_POST['checkout_date'],
            ':phone' => $_POST['phone'],
            ':guests' => $_POST['guests'],
        ]);
        $success = true;
    }
}

// // FETCHING THE DATA FROM THE DATABASE.
// $job = $db->query('SELECT j.*,
// u.username,
// u.user_id,
// p.*
// FROM jobpostings j INNER JOIN user_accounts u on u.user_id = j.posted_by 
// INNER JOIN prerequisites p on p.posting_id = j.posting_id
// WHERE j.posting_id = :posting_id', [
//     ':posting_id' => $_GET['id'],
// ])->fetch();
// // dd($job);
?>



<?php require 'partials/head.php'; ?>


<header class="h-17 font-semibold bg-gradient-to-t from-[#4E3B2A]-700 to-[#F5E1C8] text-2xl text-center text-black py-5 sticky top-0 flex items-center justify-between px-10 shadow-md ">
    <div class="flex items-center gap-2">
        <img src="logo.png" alt="Avalon Hotel Logo" class="w-20 h-auto">
        <img src="room.png" alt="Avalon Hotel" class="w-8 h-8">
    </div>
    <h1 class="flex-grow text-center ml-42 ">Check Availability for Reservation</h1>
    <div>
        <nav class="flex gap-2 ">
            <a href="home.php" class="text-sm hover:text-[#4E3B2A] hover:bg-[#F7E6CA] rounded-full py-2 px-4 border-[#F7E6CA]">Home</a>
            <a href="contact.php" class="text-sm hover:text-[#4E3B2A] hover:bg-[#F7E6CA] rounded-full py-2 px-4 border-[#F7E6CA]">Contact</a>
            <a href="about.php" class="text-sm hover:text-[#4E3B2A] hover:bg-[#F7E6CA] rounded-full py-2 px-4 border-[#F7E6CA]">About</a>
        </nav>
    </div>

</header>

<main class="w-full h-full mx-auto py-5">
    <div class="flex justify-center items-center flex-col">
        <!-- Image that changes dynamically -->
        <div class="flex justify-center items-center gap-2">
            <button id="prevBtn" class="text-white px-3 py-4 rounded-full hover:bg-[#594423]">‹</button>
            <img id="roomImage" src="room.png" alt="Room Image" class="w-[550px] h-[550px] object-cover rounded-lg">
            <button id="nextBtn" class="text-white px-3 py-4 rounded-full hover:bg-[#594423]">›</button>
        </div>


        <label for="Rooms" class="block border border-[#F7E6CA] bg-[#F7E6CA] rounded-sm mb-2 text-lg text-[#4E3B2A] font-medium text-center mt-3.5">
            Select a Room
        </label>
        <select id="room_id" class="bg-[#F7E6CA] border-[#4E3B2A] text-[#4E3B2A] text-sm rounded-lg block w-78 p-3 text-center hover:cursor-pointer focus:ring-2 focus:ring-[#4E3B2A]">
            <?php foreach ($roomTypes as $roomType) : ?>
                <option value="<?= ($roomType['room_id']) ?>"><?= ($roomType['room_name']) ?></option>

            <?php endforeach;
            ?>
        </select>


    </div>



    <!-- Modal toggle -->
    <div class="flex justify-center items-center mt-5 ">
        <button data-modal-target="crud-modal" data-modal-toggle="crud-modal" class="block text-[#4E3B2A] bg-[#F7E6CA] hover:bg-[#594423] hover:text-[#F7E6CA] border-[#4E3B2A] font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700  block w-58" type="button">
            Check Availability
        </button>
    </div>
    <!-- Main modal -->

    <div class="w-200 p-5 dark:bg-gray-700 flex items-center justify-center min-h-120">
        <form method="POST" action="">

            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <div>
                    <label for="first_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">First Name</label>
                    <input type="text" id="first_name" name="first_name" class="bg-[#F7E6CA] border-[#4E3B2A] text-[#4E3B2A] text-sm rounded-lg block w-78 p-3 text-center hover:cursor-pointer focus:ring-2 focus:ring-[#4E3B2A]" required>
                </div>
                <div>
                    <label for="last_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Last Name</label>
                    <input type="text" id="last_name" name="last_name" class="bg-[#F7E6CA] border-[#4E3B2A] text-[#4E3B2A] text-sm rounded-lg block w-78 p-3 text-center hover:cursor-pointer focus:ring-2 focus:ring-[#4E3B2A]" required>
                </div>
                <div>
                    <label for="phone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="bg-[#F7E6CA] border-[#4E3B2A] text-[#4E3B2A] text-sm rounded-lg block w-78 p-3 text-center hover:cursor-pointer focus:ring-2 focus:ring-[#4E3B2A]" required>
                </div>
                <div>
                    <label for="guests" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Number of Guests</label>
                    <input type="number" id="guests" name="guests" class="bg-[#F7E6CA] border-[#4E3B2A] text-[#4E3B2A] text-sm rounded-lg block w-78 p-3 text-center hover:cursor-pointer focus:ring-2 focus:ring-[#4E3B2A]" required>
                </div>
                <div>
                    <label for="checkin_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Check-in Date</label>
                    <input type="date" id="checkin_date" name="checkin_date" class="bg-[#F7E6CA] border-[#4E3B2A] text-[#4E3B2A] text-sm rounded-lg block w-78 p-3 text-center hover:cursor-pointer focus:ring-2 focus:ring-[#4E3B2A]" required>
                </div>
                <div>
                    <label for="checkout_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Check-out Date</label>
                    <input type="date" id="checkout_date" name="checkout_date" class="bg-[#F7E6CA] border-[#4E3B2A] text-[#4E3B2A] text-sm rounded-lg block w-78 p-3 text-center hover:cursor-pointer focus:ring-2 focus:ring-[#4E3B2A]" required>
                </div>
                <div>
                    <label for="room_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Room</label>
                    <select id="room_id" class="bg-[#F7E6CA] border-[#4E3B2A] text-[#4E3B2A] text-sm rounded-lg block w-78 p-3 text-center hover:cursor-pointer focus:ring-2 focus:ring-[#4E3B2A]">
                        <?php foreach ($roomTypes as $roomType) : ?>
                            <option value="<?= ($roomType['room_id']) ?>"><?= ($roomType['room_name']) ?></option>

                        <?php endforeach;
                        ?>
                    </select>

                </div>
            </div>

            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Submit</button>
        </form>

    </div>
    </div>
    </div>
    </div>

</main>



<script>
    document.addEventListener("DOMContentLoaded", function() {
        const roomImages = {
            "Junior Suite": ["assets/j1.jpg", "assets/j2.jpg", "assets/j3.jpg"],
            "Executive Suite": ["assets/1.jpg", "assets/executive2.jpg", "assets/executive3.jpg"],
            "Presidential Suite": ["assets/presidential1.jpg", "assets/presidential2.jpg", "assets/presidential3.jpg"],
            "Royal_Suite": ["assets/royal1.jpg", "assets/royal2.jpg", "assets/royal3.jpg"],
            "Penthhouse_Suite": ["assets/penth1.jpg", "assets/penth2.jpg", "assets/penth3.jpg"]

        };

        const roomSelect = document.getElementById("room_id");
        const roomImage = document.getElementById("roomImage");
        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");

        let currentImages = [];
        let currentIndex = 0;

        // Function to update image
        function updateImage() {
            roomImage.src = currentImages[currentIndex];
        }

        // Change images based on selected room
        roomSelect.addEventListener("change", function() {
            const selectedRoom = roomSelect.value;
            if (roomImages[selectedRoom]) {
                currentImages = roomImages[selectedRoom];
                currentIndex = 0;
                updateImage();
            } else {
                roomImage.src = "room.png";
                currentImages = [];
            }
        });

        // Next button functionality
        nextBtn.addEventListener("click", function() {
            if (currentImages.length > 0) {
                currentIndex = (currentIndex + 1) % currentImages.length;
                updateImage();
            }
        });

        // Previous button functionality
        prevBtn.addEventListener("click", function() {
            if (currentImages.length > 0) {
                currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
                updateImage();
            }
        });

        // Modal toggle

    });
</script>


<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
<?php require 'partials/footer.php'; ?>

</body>

</html>