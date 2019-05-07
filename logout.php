<?php
// Initialize the session
session_start();

if($_SESSION['loggedin'] === true) {
// Unset all of the session variables
$_SESSION = array();

// Destroy the session.
session_destroy();

// Redirect to login page //////// tempMenu.php prepísať na index.php
header("location: tempMenu.php");
exit;
}
?>