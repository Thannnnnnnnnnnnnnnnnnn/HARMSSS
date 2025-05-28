<?php
session_start();
include("Database/connection.php");

// Define the database name
$db_name = "fin_usm";

// Instantiate the Database class and connect
$db = new Database();
$connection = $db->connect($db_name);

if (!$connection) {
    die("Database connection not found for $db_name");
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $password = $_POST['password'];

    // Validate input
    if (empty($user_id) || empty($password)) {
        $error = "Please enter both User ID and Password.";
    } else {
        // Check credentials in department_accounts
        $query = "SELECT User_ID, Department_ID, Role, Name, Password FROM department_accounts WHERE User_ID = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "s", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            // Verify password (assuming plain text; use password_verify() for hashed passwords)
            if ($row['Password'] === $password) {
                // Store session data
                $_SESSION['user_id'] = $row['User_ID'];
                $_SESSION['department_id'] = $row['Department_ID'];
                $_SESSION['role'] = $row['Role'];
                $_SESSION['name'] = $row['Name'];

                // Log successful login in logs_table
                $log_query = "INSERT INTO logs_table (User_ID, Department_ID, Username, Logstatus, Role) VALUES (?, ?, ?, 'Success', ?)";
                $log_stmt = mysqli_prepare($connection, $log_query);
                mysqli_stmt_bind_param($log_stmt, "ssss", $row['User_ID'], $row['Department_ID'], $row['User_ID'], $row['Role']);
                mysqli_stmt_execute($log_stmt);

                // Get the inserted logID
                $log_id = mysqli_insert_id($connection);

                // Log in department_log_history
                $dept_log_query = "INSERT INTO department_log_history (Department_ID, User_LogID, User_ID, Name, Role, Log_Status, Log_Date_Time, Attempt_type) VALUES (?, ?, ?, ?, ?, 'Success', ?, 'Login')";
                $dept_log_stmt = mysqli_prepare($connection, $dept_log_query);
                $log_date_time = date('Y-m-d H:i:s');
                mysqli_stmt_bind_param($dept_log_stmt, "sissss", $row['Department_ID'], $log_id, $row['User_ID'], $row['Name'], $row['Role'], $log_date_time);
                mysqli_stmt_execute($dept_log_stmt);

                // Redirect to department_accounts.php
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid User ID or Password.";
                // Log failed login in logs_table
                $log_query = "INSERT INTO logs_table (User_ID, Department_ID, Username, Logstatus, Role) VALUES (?, ?, ?, 'Failed', ?)";
                $log_stmt = mysqli_prepare($connection, $log_query);
                $role = $row['Role'] ?? 'unknown';
                mysqli_stmt_bind_param($log_stmt, "ssss", $user_id, $row['Department_ID'], $user_id, $role);
                mysqli_stmt_execute($log_stmt);

                // Get the inserted logID
                $log_id = mysqli_insert_id($connection);

                // Log in department_log_history
                $dept_log_query = "INSERT INTO department_log_history (Department_ID, User_LogID, User_ID, Name, Role, Log_Status, Log_Date_Time, Attempt_type, Failure_reason) VALUES (?, ?, ?, ?, ?, 'Failed', ?, 'Login', 'Invalid password')";
                $dept_log_stmt = mysqli_prepare($connection, $dept_log_query);
                $log_date_time = date('Y-m-d H:i:s');
                mysqli_stmt_bind_param($dept_log_stmt, "sissss", $row['Department_ID'], $log_id, $user_id, $row['Name'], $role, $log_date_time);
                mysqli_stmt_execute($dept_log_stmt);
            }
        } else {
            $error = "Invalid User ID or Password.";
            // Log failed login in logs_table (no user found)
            $log_query = "INSERT INTO logs_table (User_ID, Username, Logstatus) VALUES (?, ?, 'Failed')";
            $log_stmt = mysqli_prepare($connection, $log_query);
            mysqli_stmt_bind_param($log_stmt, "ss", $user_id, $user_id);
            mysqli_stmt_execute($log_stmt);

            // Get the inserted logID
            $log_id = mysqli_insert_id($connection);

            // Log in department_log_history
            $dept_log_query = "INSERT INTO department_log_history (User_ID, User_LogID, Log_Status, Log_Date_Time, Attempt_type, Failure_reason) VALUES (?, ?, 'Failed', ?, 'Login', 'User not found')";
            $dept_log_stmt = mysqli_prepare($connection, $dept_log_query);
            $log_date_time = date('Y-m-d H:i:s');
            mysqli_stmt_bind_param($dept_log_stmt, "sis", $user_id, $log_id, $log_date_time);
            mysqli_stmt_execute($dept_log_stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('includes/head.php'); ?>
    <!-- Include Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Login - Financial USM</title>
</head>
<body class="min-h-screen flex items-center justify-center bg-[#FFF6E8]">
    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-md">
        <h2 class="text-3xl font-bold text-center text-[#4E3B2A] mb-6">Login</h2>
        <?php if ($error): ?>
            <p class="text-red-500 text-center mb-4"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="space-y-6">
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700">User ID</label>
                <input 
                    type="text" 
                    name="user_id" 
                    id="user_id" 
                    value="<?php echo isset($_POST['user_id']) ? htmlspecialchars($_POST['user_id']) : ''; ?>"
                    class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#4E3B2A] focus:border-[#4E3B2A]" 
                    required
                >
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#4E3B2A] focus:border-[#4E3B2A]" 
                    required
                >
            </div>
            <div>
                <button 
                    type="submit" 
                    class="w-full py-2 px-4 bg-[#4E3B2A] text-white rounded-md hover:bg-[#3A2C20] transition-colors"
                >
                    Sign In
                </button>
            </div>
        </form>
    </div>
</body>
</html>