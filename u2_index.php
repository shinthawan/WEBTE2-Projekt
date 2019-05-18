<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Initialize the session
session_start();

// Include config file
require_once "u2_config.php";

// Check if the user is already logged in, and redirect him to correct page

if((isset($_SESSION["type"]) && (($_SESSION["type"])== admin))){
    header("location: u2_importResult.php");
    exit;
}
if ((isset($_SESSION["type"]) && (($_SESSION["type"]) == student))){
    header("location: u2_studentView.php");
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>

    <title>Login app</title>
    <meta name="google-site-verification" content="Nm8_jfnV7uC9sELhiGjZhFj4XAWI1ZDAm5G49D_vV70" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
</head>
<div class="container">
    <h1>Vyber si spôsob prihlásenia</h1>
    <a href="u2_loginAdmin.php?" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Login admin</a>
    <a href="u2_loginLdap.php?" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Login</a>
</div>
</html>