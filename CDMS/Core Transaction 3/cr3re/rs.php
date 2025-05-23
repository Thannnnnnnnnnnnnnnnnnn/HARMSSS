<?php

session_start();
require '../Database.php';
require '../functions.php';
$config = require '../config.php';

$conn = new Database($config['database']);


$rooms = $conn->query('SELECT * FROM rooms')->fetchAll();
// dd($_SESSION);

$user = $_SESSION['user'];
// dd($_SERVER);


// $rs = $conn->query('SELECT * FROM reservationstatus')->fetchAll();
// $rooms = $conn->query('SELECT * FROM rooms')->fetchAll();

$status = $conn->query('SELECT r.*, g.*, m.*, s.* 
FROM reservations r 
INNER JOIN guests g 
ON r.guests = g.GuestID  
INNER JOIN rooms m 
ON m.room_id = r.room_id 
INNER JOIN reservationstatus s 
ON r.reservation_id = s.reservation_id')->fetchAll();


// dd($status);
// dd($status);



//dd($_SESSION['first_name']);
//dd($users);
require '../partials/admin/head.php';
require '../partials/admin/sidebar.php';
require '../partials/admin/navbar.php';

?>
<!-- Main Content -->
<!--------------------------------------------------------------------------ADMIN---------------------------------------------------------------------->
<?php switch ($user['role']):
    case 'admin': ?>
        <main class="px-8 py-8">
            <div class="text-center p-1 text-lg font-bold rounded-lg mb-6 bg-[#F7E6CA] shadow-2xl ">
                <h1>RESERVATION STATUS</h1>
            </div>
            <!--MAIN TABLE FORMAT0-->
            <div class="relative overflow-x-auto shadow-2xl sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-[#F7E6CA] dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Guest</th>
                            <th class="px-6 py-3">Reservation ID</th>
                            <th scope="col" class="px-6 py-3">Room name</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Check_in</th>
                            <th class="px-6 py-3">Check_out</th>
                            <th class="px-6 py-3">Action</th>

                        </tr>
                    </thead>
                    <tbody>

                        <?php

                        foreach ($status as $stats):
                        ?>



                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <?= $stats['guest_name'] ?></th>
                                <td class="px-6 py-4"><?= $stats['reservation_id'] ?></td>
                                <td class="px-6 py-4"><?= $stats['room_name'] ?></td>
                                <td class="px-6 py-4"><?= $stats['status'] ?></td>
                                <td class="px-6 py-4"><?= $stats['check_in'] ?></td>
                                <td class="px-6 py-4"><?= $stats['check_out'] ?></td>


                                <td class="space-x-2 flex mt-3">
    <button 
        onclick="openViewModal(<?= htmlspecialchars(json_encode($stats), ENT_QUOTES, 'UTF-8') ?>)" 
        class="bg-blue-500 text-white px-2 py-1 rounded"
    >View</button>
                                <!---------------------------------------------------------------------------- DElETE MODAL ---------------------------------------------------------------------------->
    <form action="../crud/rscrud.php" method="POST" class="inline">
        <input type="hidden" name="delete" value="true">
        <input type="hidden" name="StatusID" value="<?= htmlspecialchars($stats['status_id']) ?>">
        <input type="hidden" name="reservationID" value="<?= htmlspecialchars($stats['reservation_id']) ?>">
        <input type="hidden" name="Status" value="<?= htmlspecialchars($stats['status']) ?>">
        <input type="hidden" name="UpdatedAt" value="<?= htmlspecialchars($stats['updated_at']) ?>">
        <button type="submit" onclick="return confirm('Delete this item?')" class="bg-red-600 text-white px-2 py-1 rounded">
            Delete
        </button>
    </form>
</td>
                                <!---------------------------------------------------------------------------- VIEW MODAL ---------------------------------------------------------------------------->
                                <div id="iewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
                                    <div class="bg-[#F7E6CA] p-6 rounded w-96">
                                        <h2 class="text-xl mb-4 font-bold">RESERVATION DETAILS</h2>
                                        <p><strong>Guest ID:</strong> <span id="viewGuestID"></span></p>
                                        <p><strong>Reservation ID:</strong> <span id="viewreservationID"></span></p>
                                        <p><strong>Guests:</strong> <span id="viewGuest"></span></p>
                                        <p><strong>Status:</strong> <span id="viewStatus"></span></p>
                                        <p><strong>Updated at:</strong> <span id="viewUpdatedAt"></span></p>
                                        <button onclick="closeModal('viewModal')" class="mt-4 bg-gray-600 text-white px-4 py-2 rounded">Close</button>
                                    </div>
                                </div>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>
        </main>
        <!------------------------------------------------------------------------ manager --------------------------------------------------------------------->
    <?php break;
    case 'manager': ?>
        <main class="px-8 py-8">
            <div class="text-center p-1 text-lg font-bold rounded-lg mb-6 bg-[#F7E6CA] shadow-2xl ">
                <h1>RESERVATION STATUS</h1>
            </div>
            <!--MAIN TABLE FORMAT0-->
            <div class="relative overflow-x-auto shadow-2xl sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-[#F7E6CA] dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Reservation ID</th>
                            <th class="px-6 py-3">Guest name</th>
                            <th scope="col" class="px-6 py-3">Room name</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Check_in</th>
                            <th class="px-6 py-3">Check_out</th>
                            <th class="px-6 py-3">Action</th>

                        </tr>
                    </thead>
                    <tbody>

                        <?php

                        foreach ($status as $stats):
                        ?>



                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <?= $stats['reservation_id'] ?> </th>
                                <td class="px-6 py-4"><?= $stats['guest_name'] ?></td>
                                <td class="px-6 py-4"><?= $stats['room_name'] ?></td>
                                <td class="px-6 py-4"><?= $stats['status'] ?></td>
                                <td class="px-6 py-4"><?= $stats['check_in'] ?></td>
                                <td class="px-6 py-4"><?= $stats['check_out'] ?></td>

                                <td class="space-x-2 flex mt-3  ">
                                    <button onclick='openViewModal(<?= json_encode($stats) ?>)' class=" bg-blue-500 text-white px-2 py-1 rounded">View</button>
                                    <button onclick='openEditModal(<?= json_encode($stats) ?>)' class=" bg-green-800 text-white px-2 py-1 rounded">Edit</button>
                                    <form action="rs.php" method="POST" class="inline">
                                        <input type="hidden" name="delete" value="true">
                                        <input type="hidden" name="GuestID" value="<?= $stats['GuestID'] ?>">
                                        <input type="hidden" name="reservationID" value="<?= $stats['reservation_id'] ?>">

                                        <button type="submit" onclick="return confirm('Delete this item?')" class="bg-red-600 text-white px-2 py-1 rounded">Delete</button>
                                    </form>
                                </td>

                            </tr>
                        <?php endforeach; ?>

                    </tbody>

                </table>
                <!---------------------------------------------------------------------------- View Modal ---------------------------------------------------------------------------->
                <div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
                    <div class="bg-[#F7E6CA] p-6 rounded w-96">
                        <h2 class="text-xl mb-4 font-bold">RESERVATION DETAILS</h2>

                        <p><strong>Guest ID:</strong> <span id="viewGuestID"></span></p>
                        <p><strong>Reservation ID:</strong> <span id="viewreservationID"></span></p>
                        <p><strong>Guest Name</strong> <span id="viewGuestName"></span></p>
                        <p><strong>Room Name:</strong> <span id="viewRoomname"></span></p>
                        <p><strong>Status:</strong> <span id="viewStatus"></span></p>

                        <p><strong>Updated at:</strong> <span id="viewUpdatedAt"></span></p>
                        <button onclick="closeModal('viewModal')" class="mt-4 bg-gray-600 text-white px-4 py-2 rounded">Close</button>
                    </div>
                </div>


                <!------------------------------------------------------------------------ Add Reservation Modal --------------------------------------------------------------------->
                <div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
                    <div class="max-w-3xl mx-auto mt-10 bg-white border border-[#594423] rounded-[12px] shadow-md p-6">
                        <h2 class="text-xl font-semibold text-[#4E3B2A] mb-4 text-center">ADD RESERVATION</h2>
                        <form action="../crud/rscrud.php" method="POST" class="space-y-4 p-6 bg-white shadow-md rounded-md">
                            <input type="hidden" name="add" value="true">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Guest Name -->
                                <div>
                                    <label for="guest_name" class="block font-semibold">Guest Name:</label>
                                    <input type="text" name="guest_name" id="addguest_name"
                                        class="w-full px-3 py-2 border rounded" placeholder="Enter Guest Name" required>
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label for="phone" class="block font-semibold">Phone:</label>
                                    <input type="tel" name="phone" id="addPhone"
                                        class="w-full px-3 py-2 border rounded" placeholder="Enter Phone" required>
                                </div>

                                <!-- Address -->
                                <div>
                                    <label for="address" class="block font-semibold">Address:</label>
                                    <input type="text" name="address" id="addaddress"
                                        class="w-full px-3 py-2 border rounded" placeholder="Enter Address" required>
                                </div>

                                <!-- Date of Birth -->
                                <div>
                                    <label for="date_of_birth" class="block font-semibold">Date of Birth:</label>
                                    <input type="date" name="date_of_birth" id="adddate_of_birth"
                                        class="w-full px-3 py-2 border rounded" required>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block font-semibold">Email:</label>
                                    <input type="email" name="email" id="addemail"
                                        class="w-full px-3 py-2 border rounded" placeholder="Enter Email" required>
                                </div>

                                <!-- Gender -->
                                <div>
                                    <label for="gender" class="block font-semibold">Gender:</label>
                                    <select name="gender" id="addgender" class="w-full px-3 py-2 border rounded" required>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>

                                <!-- Nationality -->
                                <div>
                                    <label for="nationality" class="block font-semibold">Nationality:</label>
                                    <input type="text" name="nationality" id="addnationality"
                                        class="w-full px-3 py-2 border rounded" placeholder="Enter Nationality" required>
                                </div>

                                <!-- Reservation -->
                                <div>
                                    <label for="reservation" class="block font-semibold">Reservation:</label>
                                    <input type="text" name="reservation" id="addreservation"
                                        class="w-full px-3 py-2 border rounded" placeholder="Reservation Details" required>
                                </div>

                                <!-- Check-In -->
                                <div>
                                    <label for="check_in" class="block font-semibold">Check-In:</label>
                                    <input type="datetime-local" name="check_in" id="addcheck_in"
                                        class="w-full px-3 py-2 border rounded" required>
                                </div>

                                <!-- Check-Out -->
                                <div>
                                    <label for="check_out" class="block font-semibold">Check-Out:</label>
                                    <input type="datetime-local" name="check_out" id="addcheck_out"
                                        class="w-full px-3 py-2 border rounded" required>
                                </div>

                                <!-- Status -->
                                <div>
                                    <label for="status" class="block font-semibold">Status:</label>
                                    <select name="status" id="addStatus" class="w-full px-3 py-2 border rounded" required>
                                        <option value="Pending">Pending</option>
                                        <option value="Confirmed">Confirmed</option>
                                        <option value="Cancelled">Cancelled</option>
                                        <option value="Completed">Completed</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="room" class="block font-semibold">Room:</label>
                                    <select name="room_id" id="addroom_id" class="w-full px-3 py-2 border rounded" required>
                                        <option value="" disabled selected>Select Room</option>
                                        <?php foreach ($rooms as $room): ?>
                                            <option value="<?= $room['room_id'] ?>">
                                                <?= $room['room_name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="flex justify-end space-x-2 mt-4">
                                <button type="button" onclick="closeModal()"
                                    class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                                <button type="submit"
                                    class="bg-green-500 text-white px-4 py-2 rounded">Add Guest</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            <div class="mt-5 flex justify-center">
                <button class="text-center p-1 text-lg font-bold rounded-lg mb-6 bg-[#F7E6CA] shadow-2xl px-4 py-2" onclick="openModal('addModal')">Add Reservation</button>
            </div>
        </main>
    <?php break;
    case 'staff': ?>
        <!-------------------------------------------------------------------------- STAFF ---------------------------------------------------------------------->
    <?php break;
    case 'guest': ?>
        <!---------------------------------------------------------------------------GUEST------------------------------------------------------------------->

<?php endswitch; ?>

</div>
</div>
<?php
require '../partials/admin/footer.php';
?>

<script>
    function openModal(viewModal) {
        const modal = document.getElementById(viewModal);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }


    function closeSidebar() {
        sidebar.classList.remove('mobile-active');
        overlay.classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    function openSidebar() {
        sidebar.classList.add('mobile-active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function toggleSidebar() {
        if (window.innerWidth <= 968) {
            sidebar.classList.add('sidebar-expanded');
            sidebar.classList.remove('sidebar-collapsed');
            sidebar.classList.contains('mobile-active') ? closeSidebar() : openSidebar();
        } else {
            sidebar.classList.toggle('sidebar-collapsed');
            sidebar.classList.toggle('sidebar-expanded');
            main.classList.toggle('md:ml-[85px]');
            main.classList.toggle('md:ml-[360px]');
        }
    }

    menu.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', closeSidebar);
    close.addEventListener('click', closeSidebar);

    window.addEventListener('resize', () => {
        if (window.innerWidth > 968) {
            closeSidebar();
            sidebar.classList.remove('mobile-active');
            overlay.classList.remove('active');
            sidebar.classList.remove('sidebar-collapsed');
            sidebar.classList.add('sidebar-expanded');
        } else {
            sidebar.classList.add('sidebar-expanded');
            sidebar.classList.remove('sidebar-collapsed');
        }
    });

    function toggleDropdown(dropdownId, element) {
        const dropdown = document.getElementById(dropdownId);
        const icon = element.querySelector('.arrow-icon');
        const allDropdowns = document.querySelectorAll('.menu-drop');
        const allIcons = document.querySelectorAll('.arrow-icon');

        allDropdowns.forEach(d => {
            if (d !== dropdown) d.classList.add('hidden');
        });

        allIcons.forEach(i => {
            if (i !== icon) {
                i.classList.remove('bx-chevron-down');
                i.classList.add('bx-chevron-right');
            }
        });

        dropdown.classList.toggle('hidden');
        icon.classList.toggle('bx-chevron-right');
        icon.classList.toggle('bx-chevron-down');
    }
</script>

</body>

</html>