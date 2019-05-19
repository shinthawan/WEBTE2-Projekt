<?php
// Initialize the session
session_start();

if($_SESSION['loggedin'] === true) {
// Unset all of the session variables
$_SESSION = array();

// Destroy the session.
session_destroy();

// Redirect to login page
header("location: ../index.php");
exit;
}

if($_SESSION['ldapName']){
    // destroy session
    session_unset();
    $_SESSION = array();
    unset($_SESSION['ldapName']);
    session_destroy();

    // Redirect to login page
    header("location: uloha1menu.php");
    exit;
}

?>