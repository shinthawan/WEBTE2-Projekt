<?php
// Initialize the session
session_start();
// Include config file
require_once "config.php";

// Check if the user is already logged in, if yes then redirect him to welcome page
if(!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] !== true){
    header("location: uloha1index.php");
    exit;
}

if(isset($_GET['language']) && $_GET['language'] == "EN"){
    header("location: ENuloha1menu.php");
}elseif(isset($_GET['language']) && $_GET['language'] == "SK"){
    header("location: uloha1menu.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>TASK 1</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="print.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
</head>
<body>

<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">TASK 1</a>
        </div>
        <ul class="nav navbar-nav">
            <li class="active"><a href="ENuloha1menu.php">Home</a></li>
            <li><?php if(isset($_SESSION["admin"]) && $_SESSION["admin"] === true) {
                    echo "<a href=\"ENimportResult.php\">Import results</a>";           //ak je prihlaseny ako admin tak "Uloha1" sluzi na importResult
                } ?></li>
            <li><?php if(isset($_SESSION["admin"]) && $_SESSION["admin"] === true){                                                     //ak je student tak na studentView
                echo"<a href=\"ENshowResult.php\">Show results</a>";          //pri prihlaseni ako student ale aj pri prihlaseni cez ldap sa naplni session student
                }?></li>
            <li><?php if(isset($_SESSION["student"]) && $_SESSION["student"] === true){                                                     //ak je student tak na studentView
                    echo"<a href=\"ENstudentView.php\">Show results</a>";          //pri prihlaseni ako student ale aj pri prihlaseni cez ldap sa naplni session student
                }?></li>
            <li><?php if(isset($_SESSION["admin"]) && $_SESSION["admin"] === true){                                                     //ak je student tak na studentView
                    echo"<a href=\"ENdeleteSubject.php\">Delete subject</a>";          //pri prihlaseni ako student ale aj pri prihlaseni cez ldap sa naplni session student
                }?></li>
            <li><a href="uloha1menu.php?language=SK">Language</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <h3>Log out now!</h3>
    <a href="logout.php?" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">LOG OUT</a>
</div>

</body>
</html>