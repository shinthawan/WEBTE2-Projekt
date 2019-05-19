<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'config.php';
//$conn = new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
//$conn->set_charset("utf8");

if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}

// Initialize the session
session_start();
// Include config file
require_once "config.php";

// Check if the user is already logged in, if yes then redirect him to welcome page
if(!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] !== true){
header("location: uloha1index.php");
exit;
}

if (isset($_POST["delete"])) {
    $predmet = htmlspecialchars($_POST["predmety"]);
    $id = 0;

    $sql8 = "SELECT id_predmet FROM predmet WHERE nazov = '$predmet'";
    $result = $conn->query($sql8);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $id = $row["id_predmet"];
        }
    } else {
        echo "0 results";
    }
    echo "$id";
    $sql9 = "DELETE FROM zaznam WHERE id_predmet ='$id'";
    $sql10 = "DELETE FROM predmet WHERE id_predmet='$id'";
    $sql11 = "DELETE FROM vysledok WHERE id_predmet='$id'";

    if (mysqli_query($conn, $sql9)) {
        if (mysqli_query($conn, $sql10)) {
            if (mysqli_query($conn, $sql11)) {
                echo "delete success";
            } else {
                echo "Error updating record: " . mysqli_error($conn);
            }
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
    echo "Delete complete" . $predmet;
    header("Refresh:0");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TASK 1</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
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
            <li><?php if(isset($_SESSION["admin"]) && $_SESSION["admin"] === true){                                                     //ak je student tak na studentView
                    echo"<a href=\"ENdeleteSubject.php\">Delete subject</a>";          //pri prihlaseni ako student ale aj pri prihlaseni cez ldap sa naplni session student
                }?></li>
            <li><a href="uloha1menu.php?language=SK">Language</a></li>
        </ul>
    </div>
</nav>
<div class="container">
    <div class="wrapper">
        <form action="ENdeleteSubject.php" method="post" enctype="multipart/form-data">
            <?php

            $sql2 = "SELECT nazov FROM predmet";

            $result2 = $conn->query($sql2);
            echo "<div class=\"form-group\">";
            echo "<select name='predmety' class=\"form-control\">";
            if ($result2->num_rows > 0) {
                while($row = $result2->fetch_assoc()) {
                    echo "<option>".$row["nazov"]."</option>";
                }
            } else {
                echo "0 results";
            }
            echo "</select>";
            echo "</div>";
            ?>
            <input type="submit" name="delete" value="Delete"/>
        </form>
    </div>
</div>

</body>
</html>

