<?php
include 'connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Get user by email
    $sql = "SELECT * FROM User_account WHERE Email = '$email' AND Status = 'Active'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['Password'])) {
            $_SESSION['User_ID'] = $user['User_ID'];
            $_SESSION['Role'] = $user['Role'];
            $_SESSION['Name'] = $user['Name'];

            // Redirect based on role
            if ($user['Role'] === 'super admin') {
                header("Location: dashboard.php");
                exit();
            } elseif ($user['Role'] === 'manager') {
                header("Location: manager/manager.php");
                exit();
            } elseif ($user['Role'] === 'staff') {
                header("Location: staff/staff.php");
                exit();
            } else {
                echo "Unknown role.";
            }
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "Invalid email or inactive account.";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <form method="POST">
        <h2>Login</h2>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </form>
</body>
</html>
