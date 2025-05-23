<?php
include 'conn.php';

// bagohin nalang to kung ano dapat
$user_id = $_SESSION['user_id'] ?? 1;

if (!$user_id) {
    header('Location: login.php');
    exit();
}

$depDB = $connections["cr3_re"];

$query = "
    SELECT 
        u.user_id, 
        u.first_name, 
        u.last_name, 
        CONCAT(u.first_name, ' ', u.last_name) AS Name,
        u.email, 
        u.role, 
        u.department_id , 
        d.department_id , 
        d.dept_name 
    FROM user_account u 
    INNER JOIN departments d ON u.department_id = d.department_id  
    WHERE u.user_id = ?
";

$stmt = mysqli_prepare($depDB, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $user_name = $row['Name'];
        $role = $row['role'];
        $department = $row['dept_name'];
        $email = $row['email'];
        $department_id = $row['department_id'];
    }
} else {
    die("Error fetching user data: " . mysqli_error($depDB));
}

mysqli_stmt_close($stmt);
?>
