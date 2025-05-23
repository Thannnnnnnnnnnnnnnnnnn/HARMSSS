
<?php
session_start();
require 'Database.php';
require 'functions.php';
$config = require 'config.php';


$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $rooms = $db->query("SELECT * FROM rooms WHERE id = ?" [])->fetch();



}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Hotel Reservation</title>
</head>
<body>

<div class="container">
    <h2>Login</h2>
    <?php if ($errors):?>
    <div>
        <?= $errors['invalid']?>
        
    </div>
    <?php endif;?>

    <!--<form method="POST"> 
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>-->
    <div>
        <div>
            <p></p>
        </div>
    <form>
        <h2> </h2>
        <button type="submit">go</button>
        </form>

        
    </div>
</div>

</body>
</html>
