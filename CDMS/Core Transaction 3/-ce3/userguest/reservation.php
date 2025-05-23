<?php
session_start();
require '../Database.php';
require '../functions.php';
$config = require '../config.php';
$db = new Database($config['database']);
$roomTypes = $db->query('SELECT room_id, room_name FROM rooms')->fetchAll();

// Fetching all reservations might be inefficient if the table grows large. Consider fetching only relevant data if needed.
// $reservations = $db->query('SELECT * FROM reservations')->fetchAll();
$errors = [];


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $stmt = $db->query("INSERT INTO reservations (user_id, first_name, last_name, phone, room_id, checkin_date, checkout_date, guests) VALUES (:user_id, :first_name, :last_name, :phone, :room_id, :checkin_date, :checkout_date, :guests)", [
        'user_ids' => $_POST['user_ids'],
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'phone' => $_POST['phone'],
        'room_id' => $_POST['room_id'],
        'checkin_date' => $_POST['checkin_date'],
        'checkout_date' => $_POST['checkout_date'],
        'guests' => $_POST['guests']
    ]);
    // dd($db);
    // $first_name = trim($_POST['first_name'] ?? '');
    // $last_name = trim($_POST['last_name'] ?? '');
    // $room_id =   trim($_POST['room_id'] ?? '');
    // $checkin_date = trim($_POST['checkin_date'] ?? '');
    // $checkout_date = trim($_POST['checkout_date'] ?? '');
    // $phone = trim($_POST['phone'] ?? '');
    // $guests = trim($_POST['guests'] ?? '');
}

?>

<?php require '../partials/head.php';
?>

<div id="targetup"></div>

<header class="sticky top-0 z-10 bg-gradient-to-t from-[#4E3B2A]-700 to-[#F5E1C8] text-black shadow-md">
    <div class="container mx-auto flex flex-wrap items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
        <div class="flex items-center gap-2">
            <img src="../room.png" alt="Avalon Hotel Logo" class="hidden sm:block h-8 w-8 sm:h-8 sm:w-8">
            <img src="../logo.png" alt="Avalon Hotel" class="h-5 w-20">
        </div>

        <h1 class="mt-2 w-full text-center text-xl font-semibold sm:w-auto sm:flex-grow sm:text-center sm:text-2xl py-2 sm:py-0">
            Check Availability for Reservation
        </h1>

        <nav class="flex items-center justify-center w-full sm:w-auto">
            <ul class="flex item-center justify-center  gap-2 text-sm mt-2 sm:mt-0">
                <li><a href="home.php" class="block rounded-full px-3 py-2 hover:bg-[#F7E6CA] hover:text-[#4E3B2A]">Home</a></li>
                <li><a href="contact.php" class="block rounded-full px-3 py-2 hover:bg-[#F7E6CA] hover:text-[#4E3B2A]">Contact</a></li>
                <li><a href="about.php" class="block rounded-full px-3 py-2 hover:bg-[#F7E6CA] hover:text-[#4E3B2A]">About</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="bg-[#F7E6CA] container mx-auto mt-2 px-4 py-5 sm:mt-10">
    <div class="flex flex-col items-center">
        <div class="mb-10 flex w-full max-w-2xl items-center">
            <button id="prevBtn" class="rounded-full bg-[#F7E6CA] px-4 py-3 text-2xl font-bold text-[#4E3B2A] hover:bg-[#594423] hover:text-[#F7E6CA] mr-4">‹</button>
            <img id="roomImage" src="../room.png" alt="Room Image" class="mb-FULL w-full max-w-2xl relative overflow-hidden" style="--aspect-ratio: 16 / 9;">
            <button id="nextBtn" class="rounded-full bg-[#F7E6CA] px-4 py-3 text-2xl font-bold text-[#4E3B2A] hover:bg-[#594423] hover:text-[#F7E6CA] ml-4">›</button>
        </div>

        <div class="flex justify-center mt-9">
            <label for="firstSelect" class="mr-4 text-lg font-medium text-[#4E3B2A]">
                Select a Room
            </label>
            <select onchange="passValue()" id="firstSelect" class="w-auto cursor-pointer rounded-lg border-[#4E3B2A] bg-[#F7E6CA] p-3 text-sm text-[#4E3B2A] focus:ring-2 focus:ring-[#4E3B2A]">
                <?php foreach ($roomTypes as $roomType) : ?>
                    <option value="<?= htmlspecialchars($roomType['room_id']) ?>"><?= htmlspecialchars($roomType['room_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="mt-5 flex justify-center items-center">

        <button onclick="slideDown()" class="block rounded-lg border border-[#4E3B2A] bg-[#F7E6CA] px-5 py-2.5 text-center text-sm font-medium text-[#4E3B2A] hover:bg-[#594423] hover:text-[#F7E6CA]">
            Scroll Down to Form
        </button>
    </div>

    <!------------------------------------------------------------- Form Section ---------------------------------------------------------------->

    <div class="mt-16 sm:mt-24 flex items-center justify-center">
        <div class="w-full max-w-2xl rounded-2xl border border-[#594423] bg-white p-4 dark:bg-gray-700 sm:p-6">
            <?php if (!empty($errors)) : ?>
                <div class="mb-4 rounded border border-red-400 bg-red-100 p-3 text-red-700">
                    <ul>
                        <?php foreach ($errors as $error) : ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php if (isset($success) && $success) : ?>
                <div class="mb-4 rounded border border-green-400 bg-green-100 p-3 text-green-700">
                    Reservation submitted successfully!
                </div>
            <?php endif; ?>

            <form class="mt-10" method="POST" action="">
                <div class="mb-6 grid grid-cols-1 gap-4 sm:gap-6 md:grid-cols-2">
                    <div>
                        <label for="first_name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">First Name</label>
                        <input type="text" id="first_name" name="first_name" class="block w-full rounded-lg border-[#4E3B2A] bg-[#F7E6CA] p-3 text-center text-sm text-[#4E3B2A] hover:cursor-pointer focus:ring-2 focus:ring-[#4E3B2A] dark:text-gray-800" required value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="last_name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Last Name</label>
                        <input type="text" id="last_name" name="last_name" class="block w-full rounded-lg border-[#4E3B2A] bg-[#F7E6CA] p-3 text-center text-sm text-[#4E3B2A] hover:cursor-pointer focus:ring-2 focus:ring-[#4E3B2A] dark:text-gray-800" required value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="phone" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="block w-full rounded-lg border-[#4E3B2A] bg-[#F7E6CA] p-3 text-center text-sm text-[#4E3B2A] hover:cursor-pointer focus:ring-2 focus:ring-[#4E3B2A] dark:text-gray-800" required pattern="[0-9\s\-+()]*" title="Enter a valid phone number" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="guests" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Number of Guests</label>
                        <input type="number" id="guests" name="guests" min="1" class="block w-full rounded-lg border-[#4E3B2A] bg-[#F7E6CA] p-3 text-center text-sm text-[#4E3B2A] hover:cursor-pointer focus:ring-2 focus:ring-[#4E3B2A] dark:text-gray-800" required value="<?= htmlspecialchars($_POST['guests'] ?? '1') ?>">
                    </div>
                    <div>
                        <label for="checkin_date" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Check-in Date</label>
                        <input type="date" id="checkin_date" name="checkin_date" class="block w-full rounded-lg border-[#4E3B2A] bg-[#F7E6CA] p-3 text-center text-sm text-[#4E3B2A] hover:cursor-pointer focus:ring-2 focus:ring-[#4E3B2A] dark:text-gray-800" required min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($_POST['checkin_date'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="checkout_date" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Check-out Date</label>
                        <input type="date" id="checkout_date" name="checkout_date" class="block w-full rounded-lg border-[#4E3B2A] bg-[#F7E6CA] p-3 text-center text-sm text-[#4E3B2A] hover:cursor-pointer focus:ring-2 focus:ring-[#4E3B2A] dark:text-gray-800" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>" value="<?= htmlspecialchars($_POST['checkout_date'] ?? '') ?>">
                    </div>
                    <div class="md:col-span-2">
                        <label for="secondSelect" class="mb-2 block text-center text-sm font-medium text-gray-900 dark:text-white">Selected Room</label>
                        <select onchange="passValue2()" id="secondSelect" name="room_id" class="block w-full cursor-pointer rounded-lg border-[#4E3B2A] bg-[#F7E6CA] p-3 text-center text-sm text-[#4E3B2A] focus:ring-2 focus:ring-[#4E3B2A] dark:text-gray-800">
                            <?php foreach ($roomTypes as $roomType) : ?>
                                <option value="<?= htmlspecialchars($roomType['room_id']) ?>" <?= (isset($_POST['room_id']) && $_POST['room_id'] == $roomType['room_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($roomType['room_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="flex justify-center">
                    <button type="submit" class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300">Submit Reservation</button>
                </div>
            </form>

        </div>
    </div>

    <div class="mt-10 flex justify-center">
        <button onclick="slideUp()" class="block rounded-lg border border-[#4E3B2A] bg-[#F7E6CA] px-5 py-2.5 text-center text-sm font-medium text-[#4E3B2A] hover:bg-[#594423] hover:text-[#F7E6CA]">
            Scroll to Top
        </button>
        <div id="targetdown"></div>
    </div>
</main>

<script>
    function slideDown() {

        document.querySelector('.mt-10').scrollIntoView({
            behavior: "smooth",
            block: "start"
        });
    }

    function slideUp() {
        document.getElementById("targetup").scrollIntoView({
            behavior: "smooth"
        });
    }

    function passValue() {
        const firstSelect = document.getElementById("firstSelect");
        const secondSelect = document.getElementById("secondSelect");
        if (firstSelect && secondSelect) {
            secondSelect.value = firstSelect.value;
            secondSelect.dispatchEvent(new Event('change'));
        }
    }

    function passValue2() {
        const firstSelect = document.getElementById("firstSelect");
        const secondSelect = document.getElementById("secondSelect");
        if (firstSelect && secondSelect) {
            firstSelect.value = secondSelect.value;
            firstSelect.dispatchEvent(new Event('change'));
        }
    }

    //----------------------------------------------------------------------------- IMAGE SLIDER LOGIC-----------------------------------------------------------------------//
    document.addEventListener("DOMContentLoaded", function() {

        const roomImages = {
            "Junior Suite": ["../assets/j1.jpg", "../assets/j2.jpg", "../assets/j3.jpg"],
            "Executive Suite": ["../assets/1.jpg", "../assets/executive2.jpg", "../assets/executive3.jpg"],
            "Presidential Suite": ["../assets/presidential1.jpg", "../assets/presidential2.jpg", "../assets/presidential3.jpg"],
            "Royal Suite": ["../assets/royal1.jpg", "../assets/royal2.jpg", "../assets/royal3.jpg"],
            "Penthouse Suite": ["../assets/penth1.jpg", "../assets/penth2.jpg", "../assets/penth3.jpg"]

        };
        const defaultImage = "../room.png";

        const roomSelect = document.getElementById("firstSelect");
        const roomSelect2 = document.getElementById("secondSelect");
        const roomImage = document.getElementById("roomImage");
        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");

        let currentImages = [];
        let currentIndex = 0;

        function updateImage() {
            if (roomImage && currentImages.length > 0) {
                roomImage.src = currentImages[currentIndex];
                roomImage.alt = `Image ${currentIndex + 1} of ${currentImages.length} for selected room`;
            } else if (roomImage) {
                roomImage.src = defaultImage;
                roomImage.alt = "Default room image";
            }
        }

        function handleRoomSelectionChange(event) {
            const selectElement = event.target;
            const selectedRoomText = selectElement.options[selectElement.selectedIndex]?.text;

            if (selectedRoomText && roomImages[selectedRoomText]) {
                currentImages = roomImages[selectedRoomText];
            } else {
                currentImages = [];
            }
            currentIndex = 0;
            updateImage();
        }


        const initialSelectedRoomText = roomSelect.options[roomSelect.selectedIndex]?.text;
        if (initialSelectedRoomText && roomImages[initialSelectedRoomText]) {
            currentImages = roomImages[initialSelectedRoomText];
        } else {
            currentImages = [];
        }
        updateImage();



        if (roomSelect) roomSelect.addEventListener("change", handleRoomSelectionChange);
        if (roomSelect2) roomSelect2.addEventListener("change", handleRoomSelectionChange); // Sync changes from second select

        if (nextBtn) {
            nextBtn.addEventListener("click", function() {
                if (currentImages.length > 0) {
                    currentIndex = (currentIndex + 1) % currentImages.length;
                    updateImage();
                }
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener("click", function() {
                if (currentImages.length > 0) {
                    currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
                    updateImage();
                }
            });
        }
    });


    document.addEventListener('DOMContentLoaded', function() {
        const checkinDateInput = document.getElementById('checkin_date');
        const checkoutDateInput = document.getElementById('checkout_date');

        function validateDates() {
            if (checkinDateInput.value && checkoutDateInput.value) {
                const checkinDate = new Date(checkinDateInput.value);
                const checkoutDate = new Date(checkoutDateInput.value);

                if (checkoutDate <= checkinDate) {
                    checkoutDateInput.setCustomValidity('Check-out date must be after check-in date.');
                } else {
                    checkoutDateInput.setCustomValidity('');
                }

                let minCheckout = new Date(checkinDate);
                minCheckout.setDate(minCheckout.getDate() + 1);
                checkoutDateInput.min = minCheckout.toISOString().split('T')[0];
            }
        }
        if (checkinDateInput) checkinDateInput.addEventListener('change', validateDates);
        if (checkoutDateInput) checkoutDateInput.addEventListener('change', validateDates);

        validateDates();
    });
</script>


<?php require '../partials/footer.php'; ?>

</body>

</html>