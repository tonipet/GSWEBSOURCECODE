<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: /index.php");
    exit();
}

// Destroy the session to log out the user
session_unset();
session_destroy();

// Redirect to login page
header("Location: /index.php");
exit();
?>
