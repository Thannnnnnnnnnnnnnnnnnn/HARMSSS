<?php
// Start the session
session_start();

// Destroy all session data
session_destroy();

// Redirect to the login page or home page
header("Location: login.php"); // Or use "Location: index.php" for the home page
exit();
?>
