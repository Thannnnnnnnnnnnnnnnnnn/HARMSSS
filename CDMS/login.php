<?php
session_start();
include("connection.php"); // DB connection natin

// Check for errors
if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}

$employee_id = $password = "";
$employee_idErr = $passwordErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["employee_id"])) {
        $employee_idErr = "Employee ID is required";
    } else {
        $employee_id = trim($_POST["employee_id"]);
    }
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = trim($_POST["password"]);
    }

    if ($employee_id && $password) {
        // Query multiple tables: employees and admins
        $query = "SELECT employee_id, password, account_type, 'employee' AS user_type FROM employees WHERE employee_id = ?
                  UNION
                  SELECT Admin_ID, password, account_type, 'admin' AS user_type FROM admin_accounts WHERE admin_id = ?";

                  // we can add multiple tables here

        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "ss", $employee_id, $employee_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $db_password = $row["password"];
            $db_account_type = $row["account_type"];
            $user_type = $row["user_type"];

            // Verify password using password hashing (recommended)
            if (password_verify($password, $db_password)) {
                // Set session variables for user identification
                $_SESSION["user_id"] = $row["employee_id"];
                $_SESSION["account_type"] = $db_account_type;
                $_SESSION["user_type"] = $user_type;

                // Redirect based on account type
                if ($db_account_type == "1") {
                    header("Location: #.php");
                } elseif ($db_account_type == "11") {
                    header("Location: #.php");
                } elseif ($db_account_type == "2") {
                    header("Location: #.php");
                } else {
                    header("Location: #");
                }
                exit();
            } else {
                $passwordErr = "Invalid password.";
            }
        } else {
            $employee_idErr = "Invalid Employee ID or password.";
        }

        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="pendings.css">
    <link rel="shortcut icon" href="emsaa.png" type="image/x-icon">
    </head>
<body>
    <section class="vh-100" style="background-color: skyblue;">
        <div class="container-fluid h-100 d-flex justify-content-center align-items-center">
            <div class="card" style="border-radius: 1rem; max-width: 800px; width: 100%;">
                <div class="row g-0">
                    <div class="col-md-6 d-none d-md-block">
                    </div>
                    <div class="col-md-6 d-flex align-items-center">
                        <div class="card-body p-4 p-lg-5 text-black">
                            <form method="POST" action="">
                                <div class="d-flex align-items-center mb-3 pb-1">
                                    <i class="fas fa-cubes fa-2x me-3" style="color: #ff6219;"></i>
                                    <span class="h1 fw-bold mb-0"> Centralized Database Management</span>
                                </div>
                                <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Login to your Centralized Database</h5>
                                <div class="form-outline mb-4">
                                    <input type="text" id="form2Example17" name="student_id" class="form-control form-control-lg" value="<?php echo htmlspecialchars($employee_id); ?>" />
                                    <label class="form-label" for="form2Example17">Employee ID</label>
                                    <span class="text-danger"><?php echo $employee_idErr; ?></span>
                                </div>
                                <div class="form-outline mb-4">
                                    <input type="password" id="form2Example27" name="password" class="form-control form-control-lg" />
                                    <label class="form-label" for="form2Example27">Password</label>
                                    <span class="text-danger"><?php echo $passwordErr; ?></span>
                                </div>
                                <div class="pt-1 mb-4">
                                    <button type="submit" class="btn btn-dark btn-lg btn-block">Login</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <footer class="text-center mt-4">
        <p>Avalon 2025</p>
        <p>©BSIT - 3206</p>
    </footer>
</body>
</html>
