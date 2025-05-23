<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'staff'; // default role
    $status = 'Active';

    // Get next User_ID manually
    $getID = "SELECT MAX(User_ID) AS max_id FROM User_account";
    $result = $conn->query($getID);
    $row = $result->fetch_assoc();
    $new_user_id = $row['max_id'] + 1;

    $sql = "INSERT INTO User_account (User_ID, Name, Email, Password, Role, Status)
            VALUES ('$new_user_id', '$name', '$email', '$password', '$role', '$status')";

    if ($conn->query($sql) === TRUE) {
        echo "Registration successful. <a href='login.php'>Login Here</a>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <form method="POST">
        <h2>Register</h2>
        <input type="text" name="name" placeholder="Full Name" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Register</button>
        <p>Already registered? <a href="login.php">Login here</a></p>
    </form>
</body>
</html>
