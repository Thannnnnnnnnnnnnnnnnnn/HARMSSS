<?php
session_start();
require '../Database.php';
require '../functions.php';
$config = require '../config.php';

$db = new Database($config['database']);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if (!$username || !$password || !$role) {
        $errors[] = 'All fields are required.';
    } else {
        $exists = $db->query("SELECT user_id FROM user_account WHERE username = :username", ['username' => $username])->fetch();
        if ($exists) {
            $errors[] = 'Username already exists.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $db->query("INSERT INTO user_account (username, password, role) VALUES (:username, :password, :role)", [
                'username' => $username,
                'password' => $hashed,
                'role' => $role
            ]);
            $_SESSION['message'] = "Registration successful. You can now login.";
            header("Location: login.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-6 rounded shadow-md w-full max-w-sm">
        <h2 class="text-xl font-bold mb-4">Register</h2>

        <?php foreach ($errors as $error): ?>
            <p class="text-red-500 text-sm"><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>

        <form method="POST" class="space-y-4">
            <input name="username" type="text" placeholder="Username" required class="w-full p-2 border rounded">
            <input name="password" type="password" placeholder="Password" required class="w-full p-2 border rounded">
            <select name="role" required class="w-full p-2 border rounded">
                <option value="">Select Role</option>
                <option value="admin">Admin</option>
                <option value="manager">Manager</option>
                <option value="staff">Staff</option>
                <option value="guest">Guest</option>
            </select>
            <button type="submit" class="w-full bg-green-500 text-white py-2 rounded">Register</button>
        </form>
        <p class="mt-4 text-sm">Already have an account? <a href="login.php" class="text-blue-600 underline">Login here</a></p>
    </div>
</body>

</html>