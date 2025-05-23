<?php
session_start();
require 'Database.php';
require 'functions.php';
$config = require 'config.php';
require 'partials/head.php';
// $conn = new mysqli('localhost', 'root', '', 'hotel_reservation'); // Update database credentials
// session_start();

// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

$db = new Database($config['database']);
$users = $db->query('SELECT * FROM users')->fetchAll();
$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = trim($_POST['fname']);
    $email = trim($_POST['email']);
    $password = ($_POST['password']);
    // validation($fname, $errors);
    // validation($email, $errors);
    // validation($password, $errors);
    // dd($errors);
    try {
        $stmt = $db->query("INSERT INTO users (role, fname, email, password) VALUES (:role, :fname, :email, :password)", [
            ':role' => 1,
            ':fname' => $fname,
            ':email' => $email,
            ':password' => $password

        ]);
        $success = true;
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            $errors['email'] = "Email already exists";
        }
    }


    // Initialize variables

    if (empty($errors)) {
        $success = "Registration successful!";
    }
}


// Check if form is submitted
// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     $name = trim($_POST["fname"]);
//     $email = trim($_POST["email"]);
//     $password = trim($_POST["password"]);
//     $confirm_password = trim($_POST["confirm_password"]);
// }



// if (empty($email)) {
//     $errors[] = "Email is required.";
// }


// if (empty($password)) {
//     $errors[] = "Password is required.";
// } elseif (strlen($password) < 6) {
//     $errors[] = "Password must be at least 6 characters.";
// }


// if ($password !== $confirm_password) {
//     $errors[] = "Passwords do not match.";
// }




?>
<?php require 'partials/head.php'; ?>
<?php //require 'partials/admin/navbar.php'; 
?>
<header class="h-17 font-semibold bg-gradient-to-t from-[#4E3B2A]-700 to-[#F5E1C8] text-2xl text-center text-black py-5 sticky top-0 flex items-center justify-between px-10 shadow-md ">
    <h1 class="flex-grow text-center">Registration</h1>

</header>


<body>
    <div class="flex justify-center items-center h-screen ">
        <!-- <?php if (isset($errors['email'])): ?>
        <div role="alert" class="alert alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
    <?php endif; ?> -->

        <div class="w-full max-w-md p-6 backdrop-blur border-transparent border text-white rounded-md shadow-sm">
            <div class="justify-center">
                <!-- Display errors -->
                <?php if (!empty($errors)): ?>
                    <div class="bg-red-100 text-red-600 p-3 rounded-md mb-4">
                        <?php foreach ($errors as $error) echo "<p>$error</p>"; ?>
                    </div>
                <?php endif; ?>

                <!-- Display success message -->
                <?php if ($success == true): ?>
                    <p class="bg-green-100 text-green-600 p-3 rounded-md mb-4"><?php echo $success; ?></p>
                <?php endif; ?>

                <div class="flex justify-center items-center text-black">
                    <form name="registerForm" method="POST" action="" onsubmit=" return validateForm()">
                        <div class="text-[#4E3B2A]-700 mb-4">
                            <label class="block text-[#F5E1C8]">Full Name</label>
                            <input type="text" name="fname" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-[#F5E1C8]">Email</label>
                            <input type="email" name="email" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-[#F5E1C8]">Password</label>
                            <input type="password" name="password" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-[#F5E1C8]">Confirm Password</label>
                            <input type="password" name="confirm_password" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none" required>
                        </div>

                        <button type="submit" class="w-full bg-[#F5E1C8] text-black py-2 rounded-md hover:bg-white transition cursor-pointer">Register</button>
                </div>
                <div class="text-center mt-4 text-xs">
                    Already have an account? <a href="login.php" class="text-bold text-black text-lg">Sign In</a>
                </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function validateForm() {
            let name = document.forms["registerForm"]["name"].value;
            let email = document.forms["registerForm"]["email"].value;
            let password = document.forms["registerForm"]["password"].value;
            let confirm_password = document.forms["registerForm"]["confirm_password"].value;
            let errors = [];

            if (name == "") errors.push("Name is required.");
            if (email == "" || !email.includes("@")) errors.push("Valid email is required.");
            if (password == "" || password.length < 6) errors.push("Password must be at least 6 characters.");
            if (password !== confirm_password) errors.push("Passwords do not match.");

            if (errors.length > 0) {
                alert(errors.join("\n"));
                return false;
            }
            return true;
        }
    </script>
    <?php require 'partials/footer.php'; ?>

</body>

</html>