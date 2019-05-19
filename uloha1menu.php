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

<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">WEBTE 2</a>
        </div>
        <ul class="nav navbar-nav">
            <li class="active"><a href="#">Home</a></li>
            <li><?php if(isset($_SESSION["admin"]) && $_SESSION["admin"] === true) {
                    echo "<a href=\"importResult.php\">Import výsledkov</a>";           //ak je prihlaseny ako admin tak "Uloha1" sluzi na importResult
                } ?></li>
            <li><?php if(isset($_SESSION["admin"]) && $_SESSION["admin"] === true){                                                     //ak je student tak na studentView
                echo"<a href=\"showResult.php\">Zobrazenie výsledkov</a>";          //pri prihlaseni ako student ale aj pri prihlaseni cez ldap sa naplni session student
                }?></li>
        </ul>
    </div>
</nav>

<div class="container">
    <h3>Basic Navbar Example</h3>
    <p>A navigation bar is a navigation header that is placed at the top of the page.</p>
</div>

</body>
</html>