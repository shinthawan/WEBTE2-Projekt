<?php
// Initialize the session
session_start();
// Include config file
require_once "config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bootstrap Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Vyber si spôsob prihlásenia / Choose login method</h1>

        <a href="loginStudent.php?" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Login študent</a>
        <a href="loginAdmin.php?" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Login admin</a>
        <a href="loginLdap.php?" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Login LDAP</a>
    </div>
</body>
</html>