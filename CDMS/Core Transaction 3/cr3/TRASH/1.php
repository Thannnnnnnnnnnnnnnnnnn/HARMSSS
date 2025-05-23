<?php
require 'functions.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $age = $_POST['age'];
    //dd($age);

    //dd($day);
    if ($age < 18) {
        echo 'Not quali';
    } elseif ($age >= 18) {
        echo 'qualified to vote';
         header('Location: index.php');
    } elseif ($age >= 18) {
        echo 'qualified to vote';
       
    } else {
        echo 'its not a day';
    }
}

?>
<?php require 'head.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head class="text-center">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guess the day</title>
</head>

<body>


    <form action="" method="POST">
        <label for=:age>Enter a date </label>
        <br>
        <input type="number" name="age">
        <button type="submit">Submit</button>
    </form>



</body>

</html>


<!-- check the weeather a person is qualified vote or not 
mon 1
tues 2
wed 3
$day = 1