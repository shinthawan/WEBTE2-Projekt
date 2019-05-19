<?php
// Initialize the session
session_start();

if(isset($_SESSION['type'])) {
// Unset all of the session variables
$_SESSION = array();

// Destroy the session.
session_destroy();

// Redirect to login page
header("location: u2_index.php");
exit;
}
?>
