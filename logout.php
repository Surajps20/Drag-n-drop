<?php
// 1. Start the session so we can access it
session_start();

// 2. Unset all session variables
session_unset();

// 3. Destroy the session completely
session_destroy();

// 4. Redirect to the login page
header('Location: login.php');
exit();
?>