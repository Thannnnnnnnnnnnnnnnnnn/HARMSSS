<?php

session_start();
require '../Database.php';
require '../functions.php';
$config = require '../config.php';

$db = new Database($config['database']);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $user = $db->query("SELECT * FROM user_account WHERE username = :username", ['username' => $username])->fetch();

    if (password_verify($password, $user['password'])) {
       $_SESSION['user'] = $user;
        $_SESSION['user_id'] = $user_id;
        header("Location: dashboard.php");
        exit();
    } else {
        $errors[] = 'Invalid username or password.';
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-6 rounded shadow-md w-full max-w-sm">
        <h2 class="text-xl font-bold mb-4">Login</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <p class="text-green-600 text-sm"><?= $_SESSION['message'] ?></p>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?php foreach ($errors as $error): ?>
            <p class="text-red-500 text-sm"><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>

        <form method="POST" class="space-y-4">
            <input name="username" type="text" placeholder="Username" required class="w-full p-2 border rounded">
            <input name="password" type="password" placeholder="Password" required class="w-full p-2 border rounded">
            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded">Login</button>
        </form>
        <p class="mt-4 text-sm">Don't have an account? <a href="register.php" class="text-blue-600 underline">Register here</a></p>
    </div>
</body>

</html>