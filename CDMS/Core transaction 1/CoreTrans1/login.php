<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: pages/Department/Department_Acc.php');
    exit();
}
$error_message = '';
if (isset($_GET['error'])) {
    $error_message = htmlspecialchars($_GET['error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - USM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #FFF6E8;
        }
        .login-container {
            background-color: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 2px solid #594423;
            color: #4E3B2A;
        }
        .login-title {
            font-family: 'Cinzel', serif;
            color: #594423;
        }
        .form-input {
            border: 1px solid #594423;
            color: #4E3B2A;
            border-radius: 0.375rem; /* rounded-md */
            padding: 0.5rem 0.75rem; /* px-3 py-2 */
        }
        .form-input:focus {
            border-color: #4E3B2A;
            box-shadow: 0 0 0 0.2rem rgba(89, 68, 35, 0.25);
            outline: none;
        }
        .submit-button {
            background-color: #4E3B2A;
            color: white;
            font-weight: 600; /* semibold */
            padding: 0.5rem 1rem; /* py-2 px-4 */
            border-radius: 0.375rem; /* rounded-md */
            transition: background-color 0.3s;
        }
        .submit-button:hover {
            background-color: #594423;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 0.75rem 1.25rem;
            border-radius: 0.25rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="login-container w-full max-w-md">
        <h2 class="login-title text-2xl sm:text-3xl font-bold text-center mb-6">USM LOGIN</h2>

        <?php if ($error_message): ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="process_login.php" method="POST">
            <div class="mb-4">
                <label for="identifier" class="block text-sm font-medium mb-1">User ID or Email</label>
                <input type="text" id="identifier" name="identifier" required
                       class="form-input w-full">
            </div>
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium mb-1">Password</label>
                <input type="password" id="password" name="password" required
                       class="form-input w-full">
            </div>
            <div>
                <button type="submit"
                        class="submit-button w-full">
                    Login
                </button>
            </div>
        </form>
    </div>
</body>
</html>