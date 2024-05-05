<?php
// Start the session (if it's not already started)
session_start();

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to the login page or any other page
header("Location: login2.php"); // Change "login.php" to your desired page

// Make sure to exit the script to prevent further execution
exit();
?>
