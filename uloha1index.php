<?php
// Initialize the session
session_start();
// Include config file
require_once "config.php";
?>
<!doctype html>
<html lang="en">
</head>
<div class="container">
    <h1>Vyber si spôsob prihlásenia</h1>

    <a href="loginStudent.php?" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Login študent</a>
    <a href="loginAdmin.php?" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Login admin</a>
    <a href="loginLdap.php?" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Login LDAP</a>
</div>
</html>
</html>