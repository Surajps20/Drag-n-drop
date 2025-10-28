<?php
// Start the session so we can access it
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect the user back to the login page
header("Location: login.php");
exit;
?>