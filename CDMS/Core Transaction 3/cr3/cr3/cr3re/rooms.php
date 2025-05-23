<?php

session_start();
require '../Database.php';
require '../functions.php';
$config = require '../config.php';

$conn = new Database($config['database']);
$user = $_SESSION['user'];
$rooms = $conn->query('SELECT * FROM rooms')->fetchAll();




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    // dd($_POST);
    $id = $_POST['id'];
    $status = $_POST['status'];
    $conn->query('UPDATE rooms SET status = :status WHERE room_id = :room_id', [':status' => $status, ':room_id' => $id]);
    header('Location: rooms.php');
    exit();
}

require '../partials/admin/head.php';
require '../partials/admin/sidebar.php';
require '../partials/admin/navbar.php';
?>
<?php switch ($user['role']):
    case 'admin': ?>

        <main class="px-8 py-8">


            <div class="text-center p-1 text-lg font-bold rounded-lg mb-6 bg-[#F7E6CA] shadow-2xl">
                <h1>ROOM STATUS</h1>
            </div>
            <div class="overflow-x-auto shadow-md sm:rounded-lg">
                <table class="text-center w-full text-sm text-left rtl:text-right text-black dark:text-gray-400 shadow-2xl">
                    <thead class="p-1 text-lg font-bold rounded-lg mb-6 bg-[#F7E6CA]">
                        <tr>
                            <th scope="col" class="px-6 py-3">ID</th>
                            <th scope="col" class="px-6 py-3">Room Name</th>
                            <th scope="col" class="px-6 py-3">Description</th>
                            <th scope="col" class="px-6 py-3">Price</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rooms as $room): ?>
                            <tr class="odd:bg-white even:bg-gray-50 border-b">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900"><?= $room['room_id'] ?></th>
                                <td class="px-6 py-4"><?= $room['room_name'] ?></td>
                                <td class="px-6 py-4"><?= $room['description'] ?></td>
                                <td class="px-6 py-4">$<?= number_format($room['price'], 2) ?></td>
                                <td class="px-6 py-4"><?= $room['status'] ?></td>
                                <td class="px-6 py-4">

                                    <form method="POST" action="rooms.php">
                                        <div class="flex items-center justify-center bg-[#F7E6CA] border">
                                            <input type="hidden" name="id" value="<?= $room['room_id'] ?>">
                                            <label>Update Status:</label>
                                            <select name="status">

                                                <option value="<?= $room['status'] ?>" selected disabled><?= $room['status'] ?></option>
                                                <option value="Available">Available</option>
                                                <option value="Occupied">Occupied</option>
                                                <option value="Under Maintenance">Under Maintenance</option>
                                                <option value="Out of Service">Out of Service</option>
                                            </select><br><br>
                                        </div>
                                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Update Status</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>

    <?php break;
    case 'manager': ?>
        <main class="px-8 py-8">


            <div class="text-center p-1 text-lg font-bold rounded-lg mb-6 bg-[#F7E6CA] shadow-2xl">
                <h1>ROOM STATUS</h1>
            </div>
            <div class="overflow-x-auto shadow-md sm:rounded-lg">
                <table class="text-center w-full text-sm text-left rtl:text-right text-black dark:text-gray-400 shadow-2xl">
                    <thead class="p-1 text-lg font-bold rounded-lg mb-6 bg-[#F7E6CA]">
                        <tr>
                            <th scope="col" class="px-6 py-3">ID</th>
                            <th scope="col" class="px-6 py-3">Room Name</th>
                            <th scope="col" class="px-6 py-3">Description</th>
                            <th scope="col" class="px-6 py-3">Price</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rooms as $room): ?>
                            <tr class="odd:bg-white even:bg-gray-50 border-b">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900"><?= $room['room_id'] ?></th>
                                <td class="px-6 py-4"><?= $room['room_name'] ?></td>
                                <td class="px-6 py-4"><?= $room['description'] ?></td>
                                <td class="px-6 py-4">$<?= number_format($room['price'], 2) ?></td>
                                <td class="px-6 py-4"><?= $room['status'] ?></td>
                                <td class="px-6 py-4">

                                    <form method="POST" action="rooms.php">
                                        <div class="flex items-center justify-center bg-[#F7E6CA] border">
                                            <input type="hidden" name="id" value="<?= $room['room_id'] ?>">
                                            <label>Update Status:</label>
                                            <select name="status">

                                                <option value="<?= $room['status'] ?>" selected disabled><?= $room['status'] ?></option>
                                                <option value="Available">Available</option>
                                                <option value="Occupied">Occupied</option>
                                                <option value="Under Maintenance">Under Maintenance</option>
                                                <option value="Out of Service">Out of Service</option>
                                            </select><br><br>
                                        </div>
                                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Update Status</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    <?php break;
    case 'staff': ?>

    <?php break;
    case 'guest': ?>

<?php endswitch; ?>
<?php require '../partials/admin/footer.php'; ?>