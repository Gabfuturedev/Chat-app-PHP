<?php
session_start(); // Start the session

// Destroy all session variables
$_SESSION = []; // Clear the session array
session_destroy(); // Destroy the session

// Redirect to the login page
header("Location: login.php");
exit();
?>
