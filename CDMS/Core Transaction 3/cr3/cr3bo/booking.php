<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../testing/login.php");
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'];
$user_id = $_SESSION['user_id'] = $user['user_id'];

include $_SERVER['DOCUMENT_ROOT'] . "/cr3/cr3bo/conn.php";

$notif = $connections["cr3_re"];
$usmDb = $connections["cr3_re"];
$db_booking = "cr3_re"; 
$db_guest = "cr3_re";
$connection = $connections[$db_booking];  

$itemsPerPage = isset($_GET['items']) ? (int)$_GET['items'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

$query = "
    SELECT 
        gp.ReservationID,
        g.guest_name AS GuestName,
        gp.CheckinDate,
        gp.CheckOutDate,
        gp.RoomNumber,
        gp.NumberOfGuests,
        gp.ReservationStatus,
        b.ChannelName,
        gp.SpecialRequests,
        'booking' AS SourceTable
    FROM `$db_booking`.booking AS gp
    LEFT JOIN `$db_guest`.guests AS g ON gp.GuestID = g.GuestID
    LEFT JOIN `$db_booking`.booking_channels AS b ON gp.BookingChannelID = b.ChannelID
    UNION
    SELECT 
        r.reservation_id AS ReservationID,
        CONCAT(r.first_name, ' ', r.last_name) AS GuestName,
        r.checkin_date AS CheckinDate,
        r.checkout_date AS CheckOutDate,
        r.room_id AS RoomNumber,
        r.guests AS NumberOfGuests,
        'Pending' AS ReservationStatus,
        NULL AS ChannelName,
        NULL AS SpecialRequests,
        'reservations' AS SourceTable
    FROM `$db_booking`.reservations AS r
    ORDER BY ReservationID ASC
    LIMIT $offset, $itemsPerPage
";

$result = $connection->query($query);

$totalResults = $connection->query("SELECT (SELECT COUNT(*) FROM booking) + (SELECT COUNT(*) FROM reservations) AS total");
$totalRow = $totalResults->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $itemsPerPage);

$query = "
    SELECT 
        u.user_id, 
        u.first_name, 
        u.last_name, 
        CONCAT(u.first_name, ' ', u.last_name) AS Name,
        u.email, 
        u.role, 
        u.department_id, 
        d.department_id, 
        d.dept_name 
    FROM user_account u 
    INNER JOIN departments d ON u.department_id = d.department_id  
    WHERE u.user_id = ?
";

$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result_user = mysqli_stmt_get_result($stmt);

if ($result_user) {
    while ($row = mysqli_fetch_assoc($result_user)) {
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

function logTransaction($usmDb, $department_id, $user_id, $type, $description) {
    $stmt = $usmDb->prepare("INSERT INTO department_transaction (department_id, user_id, transaction_type, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $department_id, $user_id, $type, $description);
    return $stmt->execute();
}

function logAudit($usmDb, $department_id, $user_id, $action, $department, $module, $description) {
    $stmt = $usmDb->prepare("INSERT INTO department_audit_trail (department_id, user_id, action, department_affected, module_affected, description) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissss", $department_id, $user_id, $action, $department, $module, $description);
    return $stmt->execute();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_reservation'])) {
    $reservationId = $_POST['update_reservation_id'];
    $checkIn = $_POST['update_check_in'];
    $checkOut = $_POST['update_check_out'];
    $roomNum = $_POST['update_room_number'];
    $noOfGuests = $_POST['update_no_of_guests'];
    $status = $_POST['update_status'];
    $specialRequests = $_POST['update_special_requests'];
    $sourceTable = $_POST['source_table'];

    if ($sourceTable === 'booking') {
        $origboss = $connection->prepare("SELECT * FROM booking WHERE ReservationID = ?");
        $origboss->bind_param("i", $reservationId); 
        $origboss->execute();
        $resultboss = $origboss->get_result();
        $bossdata = $resultboss->fetch_assoc();
        $origboss->close();

        $binago = [];
        if ($bossdata['CheckinDate'] !== $checkIn) {
            $binago[] = "Check-in date: '{$bossdata['CheckinDate']}' : '$checkIn'";
        }
        if ($bossdata['CheckOutDate'] !== $checkOut) {
            $binago[] = "Check-out date: '{$bossdata['CheckOutDate']}' : '$checkOut'";
        }
        if ($bossdata['RoomNumber'] !== $roomNum) {
            $binago[] = "Room number: '{$bossdata['RoomNumber']}' : '$roomNum'";
        }
        if ($bossdata['NumberOfGuests'] != $noOfGuests) {
            $binago[] = "No. of guests: '{$bossdata['NumberOfGuests']}' : '$noOfGuests'";
        }
        if ($bossdata['ReservationStatus'] !== $status) {
            $binago[] = "Status: '{$bossdata['ReservationStatus']}' : '$status'";
        }
        if ($bossdata['SpecialRequests'] !== $specialRequests) {
            $binago[] = "Special requests updated";
        }

        if (!empty($binago)) {
            $stmt = $connection->prepare("UPDATE booking SET CheckinDate=?, CheckOutDate=?, RoomNumber=?, NumberOfGuests=?, ReservationStatus=?, SpecialRequests=? WHERE ReservationID=?");
            $stmt->bind_param("sssissi", $checkIn, $checkOut, $roomNum, $noOfGuests, $status, $specialRequests, $reservationId);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Booking updated successfully!";
                $changeSummary = implode("; ", $binago);
                
                $notification = $notif->prepare("INSERT INTO notifications (User_ID, notifType, message, status, Department, date_sent) 
                    VALUES (?, 'update', ?, 'Unread', 'Core 3', NOW())");
                $message = "- $role updated Booking ID #$reservationId. Changes: $changeSummary";
                $notification->bind_param("ss", $user_id, $message);
                $notification->execute();

                logTransaction($usmDb, $department_id, $user_id, 'Update', "$user_name Update Booking Data");
                logAudit($usmDb, $department_id, $user_id, 'Update', 'Core 3', 'Booking', "$user_name Update Booking Data");

                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            }
            $stmt->close();
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_reservation'])) {
    $reserve_id = $_POST['delete_reservation_id'];
    $sourceTable = $_POST['source_table'];

    if ($sourceTable === 'booking') {
        $stmt = $connection->prepare("DELETE FROM booking WHERE ReservationID = ?");
        $stmt->bind_param("i", $reserve_id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Booking Data deleted successfully!";
            
            $notification = $notif->prepare("INSERT INTO notifications (User_ID, notifType, message, status, Department, date_sent) 
                VALUES (?, 'delete', ?, 'Unread', 'Core 3', NOW())");
            $message = "- $role Delete Booking ID #$reserve_id";
            $notification->bind_param("ss", $user_id, $message);
            $notification->execute();

            logTransaction($usmDb, $department_id, $user_id, 'Delete', "Delete Booking Data");
            logAudit($usmDb, $department_id, $user_id, 'Delete', 'Core 3', 'Booking', "Delete Booking Data");

            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        } else {
            $_SESSION['error_message'] = "Error deleting record: " . $stmt->error;
        }
        $stmt->close();
    }
}

include "../partials/admin/sidebar.php";
include "../partials/admin/head.php";
include "../partials/admin/navbar.php";
include "../partials/admin/footer.php";
?>

<main class="px-4 py-4">
    <h2 class="text-center text-[#594423] text-2xl font-bold mb-4">Booking Records</h2>
    <div class="bg-gradient-to-b from-white to-orange-100 min-h-screen p-8">
        <div class="overflow-x-auto bg-white shadow-lg rounded-md">
            <table class="min-w-full text-[12px] text-center text-[#594423]">
                <thead class="bg-[#F7E6CA] text-[#594423] uppercase">
                    <tr>
                        <th class="p-2">Booking ID</th>
                        <th class="p-2">Guest Name</th>
                        <th class="p-2">Check In Date</th>
                        <th class="p-2">Check Out Date</th>
                        <th class="p-2">Room Number</th>
                        <th class="p-2">No. of Guests</th>
                        <th class="p-2">Status</th>
                        <th class="p-2">Booking Channel ID</th>
                        <th class="p-2">Special Requests</th>
                        <th class="p-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $status = $row['ReservationStatus'];
                        $statusColors = [
                            'Cancelled' => 'bg-red-100 text-red-800',
                            'Confirmed' => 'bg-blue-100 text-blue-800',
                            'Paid' => 'bg-green-100 text-green-800',
                            'Pending' => 'bg-yellow-100 text-yellow-800',
                        ];
                        $bgClass = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';

                        // Prepare data for the button
                        $buttonData = [
                            'id' => $row['ReservationID'],
                            'gusId' => $row['GuestName'],
                            'bookId' => $row['ChannelName'] ?? 'Front Desk',
                            'checkin' => $row['CheckinDate'],
                            'checkout' => $row['CheckOutDate'],
                            'num' => $row['RoomNumber'],
                            'gue' => $row['NumberOfGuests'],
                            'stat' => $row['ReservationStatus'],
                            'req' => $row['SpecialRequests'] ?? 'No Request',
                            'source' => $row['SourceTable']
                        ];
                        $encodedData = json_encode($buttonData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

                        echo "<tr class='hover:bg-[#FFF6E8] text-[11px]'>
                            <td class='border'>{$row['ReservationID']}</td>
                            <td class='border'>{$row['GuestName']}</td>
                            <td class='border'>{$row['CheckinDate']}</td>
                            <td class='border'>{$row['CheckOutDate']}</td>
                            <td class='border'>{$row['RoomNumber']}</td>
                            <td class='border'>{$row['NumberOfGuests']}</td>
                            <td class='border $bgClass'>{$row['ReservationStatus']}</td>
                            <td class='border'>" . ($row['ChannelName'] ?? 'Front Desk') . "</td>
                            <td class='border'>" . ($row['SpecialRequests'] ?? 'No Request') . "</td>
                            <td class='border p-2 flex justify-center space-x-2'>";

                
                        echo "
                            <button 
                                onclick='openViewModal(this)' 
                                data-details='{$encodedData}' 
                                class='bg-[#f1ddbe] rounded text-black p-2'>
                                <i class='bx bx-show'></i>
                            </button>";

                    
                        if ($row['SourceTable'] === 'booking') {
                            if ($role === 'admin' || $role === 'manager') {
                                echo "
                                <button 
                                    onclick='openUpdateModal(this)' 
                                    data-details='{$encodedData}' 
                                    class='bg-[#f1ddbe] rounded-sm text-black p-2'>
                                    <i class='bx bx-edit'></i>
                                </button>";
                            }

                            if ($role === 'admin') {
                                echo "
                                <button 
                                    onclick='openDeleteModal(this)' 
                                    data-details='{$encodedData}' 
                                    class='bg-[#f1ddbe] rounded-sm text-black p-2'>
                                    <i class='bx bx-trash'></i>
                                </button>";
                            }
                        }

                        echo "</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='10' class='text-center py-4'>No Records Found.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-between mt-4">
            <div class="text-sm text-gray-700">
                Showing <?= min($itemsPerPage, $totalPages) ?> to <?= $itemsPerPage ?> Booking
            </div>
            
            <div class="flex items-center space-x-1">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page-1 ?>&items=<?= $itemsPerPage ?>" 
                       class="px-3 py-1 bg-[#f1ddbe] hover:bg-gray-300 rounded-md"><</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>&items=<?= $itemsPerPage ?>" 
                       class="px-3 py-1 <?= $i == $page ? 'bg-[#f1ddbe]' : 'bg-[#f1ddbe]' ?> hover:bg-gray-300 rounded-md"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page+1 ?>&items=<?= $itemsPerPage ?>" 
                       class="px-3 py-1 bg-[#f1ddbe] hover:bg-gray-300 rounded-md">></a>
                <?php endif; ?>
            </div>

            <div class="text-sm text-gray-700 flex items-center">
                Items per page:
                <select onchange="changeItemsPerPage(this)" class="bg-[#f1ddbe] border border-gray-300 rounded-md py-1 px-2 ml-2">
                    <option value="10" <?= $itemsPerPage == 10 ? 'selected' : '' ?>>10</option>
                    <option value="20" <?= $itemsPerPage == 20 ? 'selected' : '' ?>>20</option>
                    <option value="50" <?= $itemsPerPage == 50 ? 'selected' : '' ?>>50</option>
                </select>
            </div>
        </div>
    </div>

    <div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50" onclick="closeViewModal()">
        <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg relative animate__animated animate__fadeInDown" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-[#594423] flex items-center gap-2">
                    <i class="fas fa-info-circle"></i> Booking Details
                </h2>
                <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600 text-2xl">×</button>
            </div>

            <div class="space-y-4 text-gray-700 text-sm">
                <div class="flex items-center">
                    <span class="font-semibold w-48">Booking ID:</span>
                    <span id="modalreserve" class="text-gray-900"></span>
                </div>
                <div class="flex items-center">
                    <span class="font-semibold w-48">Guest Name:</span>
                    <span id="modalguestID" class="text-gray-900"></span>
                </div>
                <div class="flex items-center">
                    <span class="font-semibold w-48">Check In Date:</span>
                    <span id="modalcheckin" class="text-gray-900"></span>
                </div>
                <div class="flex items-center">
                    <span class="font-semibold w-48">Check Out Date:</span>
                    <span id="modalcheckout" class="text-gray-900"></span>
                </div>
                <div class="flex items-center">
                    <span class="font-semibold w-48">Room Number:</span>
                    <span id="modalroNum" class="text-gray-900"></span>
                </div>
                <div class="flex items-center">
                    <span class="font-semibold w-48">No. of Guests:</span>
                    <span id="modalguest" class="text-gray-900"></span>
                </div>
                <div class="flex items-center">
                    <span class="font-semibold w-48">Status:</span>
                    <span id="modalstat" class="text-gray-900"></span>
                </div>
                <div class="flex items-center">
                    <span class="font-semibold w-48">Channel Name:</span>
                    <span id="modalChannelID" class="text-gray-900"></span>
                </div>
                <div class="flex items-start">
                    <span class="font-semibold w-48">Special Requests:</span>
                    <span id="modalrequ" class="text-gray-900"></span>
                </div>
            </div>

            <div class="flex justify-end mt-8">
                <button onclick="closeViewModal()" class="px-6 py-2 bg-[#594423] hover:bg-[#4a3820] text-white rounded-lg font-medium transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    <?php if ($role === 'admin' || $role === 'manager'): ?>
    <div id="updateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50" onclick="closeUpdateModal()">
        <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-3xl relative animate__animated animate__fadeInDown" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-[#594423] flex items-center gap-2">
                    <i class="fas fa-edit"></i> Update Booking Details
                </h2>
                <button onclick="closeUpdateModal()" class="text-gray-400 hover:text-gray-600 text-2xl">×</button>
            </div>

            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-700">
                <input type="hidden" name="update_reservation_id" id="updateReservationId">
                <input type="hidden" name="update_guest_id" id="updateGuestId">
                <input type="hidden" name="source_table" id="updateSourceTable">

                <div>
                    <label class="block font-semibold mb-1">Check-In Date</label>
                    <input type="date" name="update_check_in" id="updateCheckIn" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-[#f1ddbe] focus:border-[#f1ddbe]" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Check-Out Date</label>
                    <input type="date" name="update_check_out" id="updateCheckOut" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-[#f1ddbe] focus:border-[#f1ddbe]" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Room Number</label>
                    <input type="text" name="update_room_number" id="updateRoomNum" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-[#f1ddbe] focus:border-[#f1ddbe]" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1">No. of Guests</label>
                    <input type="number" name="update_no_of_guests" id="updateNoOfGuests" min="1" max="6" oninput="if (this.value > 6) this.value = 6; if (this.value < 1) this.value = 1;" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-[#f1ddbe] focus:border-[#f1ddbe]" required>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Status</label>
                    <select name="update_status" id="updateStatus" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-[#f1ddbe] focus:border-[#f1ddbe]" required>
                        <option value="Pending">Pending</option>
                        <option value="Confirmed">Confirmed</option>
                        <option value="Cancelled">Cancelled</option>
                        <option value="Paid">Paid</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block font-semibold mb-1">Special Requests</label>
                    <textarea name="update_special_requests" id="updateSpecialRequests" rows="3" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-[#f1ddbe] focus:border-[#f1ddbe]"></textarea>
                </div>
                <div class="md:col-span-2 flex justify-end gap-3 mt-4">
                    <button type="button" onclick="closeUpdateModal()" class="px-6 py-2 bg-red-400 hover:bg-red-500 text-white rounded-lg font-semibold transition">
                        Cancel
                    </button>
                    <button type="submit" name="update_reservation" class="px-6 py-2 bg-[#594423] hover:bg-[#4a3820] text-white rounded-lg font-semibold transition">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($role === 'admin'): ?>
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50" onclick="closeDeleteModal()">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md relative" onclick="event.stopPropagation()">
            <h2 class="text-xl font-bold mb-4 text-red-600">Confirm Deletion</h2>
            <p class="mb-4">Are you sure you want to delete this record?</p>
            <form method="POST" class="flex justify-end space-x-2">
                <input type="hidden" name="delete_reservation_id" id="deleteReservationId">
                <input type="hidden" name="source_table" id="deleteSourceTable">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded">
                    Cancel
                </button>
                <button type="submit" name="delete_reservation" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded">
                    Delete
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</main>

<script>
function changeItemsPerPage(select) {
    const items = select.value;
    window.location.href = "?page=1&items=" + items;
}

function openViewModal(button) {
    const details = JSON.parse(button.getAttribute('data-details'));
    const reserveId = details.id;
    const guestId = details.gusId;
    const checkIn = details.checkin;
    const checkOut = details.checkout;
    const roomNum = details.num;
    const noGuests = details.gue;
    const status = details.stat;
    const channelId = details.bookId;
    const specialRequests = details.req;

    const formatDate = (dateString) => {
        const date = new Date(dateString);
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    };

    document.getElementById('modalreserve').innerText = reserveId;
    document.getElementById('modalguestID').innerText = guestId;
    document.getElementById('modalcheckin').innerText = formatDate(checkIn);
    document.getElementById('modalcheckout').innerText = formatDate(checkOut);
    document.getElementById('modalroNum').innerText = roomNum;
    document.getElementById('modalguest').innerText = noGuests;
    document.getElementById('modalstat').innerText = status;
    document.getElementById('modalChannelID').innerText = channelId;
    document.getElementById('modalrequ').innerText = specialRequests;

    document.getElementById('viewModal').classList.remove('hidden');
}

function closeViewModal() {
    document.getElementById('viewModal').classList.add('hidden');
}

function openUpdateModal(button) {
    const details = JSON.parse(button.getAttribute('data-details'));
    document.getElementById('updateReservationId').value = details.id;
    document.getElementById('updateGuestId').value = details.gusId;
    document.getElementById('updateCheckIn').value = details.checkin;
    document.getElementById('updateCheckOut').value = details.checkout;
    document.getElementById('updateRoomNum').value = details.num;
    document.getElementById('updateNoOfGuests').value = details.gue;
    document.getElementById('updateStatus').value = details.stat;
    document.getElementById('updateSpecialRequests').value = details.req;
    document.getElementById('updateSourceTable').value = details.source;

    document.getElementById('updateModal').classList.remove('hidden');
}

function closeUpdateModal() {
    document.getElementById('updateModal').classList.add('hidden');
}

function openDeleteModal(button) {
    const details = JSON.parse(button.getAttribute('data-details'));
    const id = details.id;
    document.getElementById('deleteReservationId').value = id;
    document.getElementById('deleteSourceTable').value = details.source;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}
</script>
</body>
</html>