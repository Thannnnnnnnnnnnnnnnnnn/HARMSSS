<?php
include 'db_connection.php';

// Handle Add User
if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (Username, Email, Password, Role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $role);
    $stmt->execute();
    header('Location: users.php');
    exit;
}

// Handle Delete User
if (isset($_GET['delete_user'])) {
    $userID = $_GET['delete_user'];
    $stmt = $conn->prepare("DELETE FROM users WHERE UserID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    header('Location: users.php');
    exit;
}

// Fetch Users
$users = $conn->query("SELECT * FROM users");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="bg-[#FFF6E8] text-[#4E3B2A] min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">User Management</h1>
        <!-- Add User Form -->
        <form method="POST" class="mb-8 bg-white p-6 rounded-lg shadow flex flex-col md:flex-row gap-4 items-end">
            <div>
                <label class="block text-sm font-medium mb-1">Username</label>
                <input type="text" name="username" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" required class="border rounded px-3 py-2 w-full" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Role</label>
                <select name="role" class="border rounded px-3 py-2 w-full">
                    <option value="admin">Admin</option>
                    <option value="employee">Employee</option>
                    <option value="trainer">Trainer</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" required class="border rounded px-3 py-2 w-full" />
            </div>
            <button type="submit" name="add_user" class="bg-[#594423] text-white px-4 py-2 rounded hover:bg-[#4E3B2A]">Add User</button>
        </form>
        <!-- Users Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">ID</th>
                        <th class="py-2 px-4 border-b">Username</th>
                        <th class="py-2 px-4 border-b">Email</th>
                        <th class="py-2 px-4 border-b">Role</th>
                        <th class="py-2 px-4 border-b">Created At</th>
                        <th class="py-2 px-4 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $users->fetch_assoc()): ?>
                    <tr>
                        <td class="py-2 px-4 border-b"><?php echo $row['UserID']; ?></td>
                        <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($row['Username']); ?></td>
                        <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($row['Email']); ?></td>
                        <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($row['Role']); ?></td>
                        <td class="py-2 px-4 border-b"><?php echo $row['CreatedAt']; ?></td>
                        <td class="py-2 px-4 border-b">
                            <a href="?delete_user=<?php echo $row['UserID']; ?>" class="text-red-600 hover:underline" onclick="return confirm('Delete this user?')">
                                <i class="fa-solid fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
