<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Initialize the session
session_start();

// Include config file
require_once "u2_config.php";

if(isset($_GET['language']) && $_GET['language'] == "EN"){
    header("location: u2en_index.php");

}elseif(isset($_GET['language']) && $_GET['language'] == "SK"){
    header("location: u2_index.php");
}

// Check if the user is already logged in, and redirect him to correct page

if((isset($_SESSION["type"]) && (($_SESSION["type"])== admin))){
    header("location: u2en_importResult.php");
    exit;
}
if ((isset($_SESSION["type"]) && (($_SESSION["type"]) == student))){
    header("location: u2en_studentView.php");
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
    <title>Login Task 2</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" media="print" href="print.css" type="text/css">
</head>
<body>
<div class="fixed-top">
    <ul><a href="u2_index.php?language=SK">Prepni do <img name="sk" src="u2_sk.png" alt="sk"/></a></ul>
</div>

<div class="container">
    <h1>Choose your login method</h1>
    <a href="u2en_loginAdmin.php?" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Login as admin</a>
    <a href="u2en_loginLdap.php?" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Login as student</a><br><br>
    <a href="../index.php?" class="btn btn-danger btn-lg" role="button" aria-pressed="true">Back</a>
</div>
<script>
    function getPredmet(val) {
        $.ajax({
            type: "POST",
            url: "u2_selectOptions.php",
            data: 'rok=' + val,
            success: function (data) {
                $("#predmetName").html(data);
            }
        });
    }
</script>

</body>
</html>
