<?php
// Initialize the session
session_start();
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <title>Koncoročné zadanie</title>
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
            <li><a href="uloha1/uloha1index.php">Úloha 1</a></li>
            <li><a href="uloha2/u2_index.php">Úloha 2</a></li>
            <li><a href="uloha3/u3_index.php">Úloha 3</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <h3>Rozdelenie úloh v záverečnom zadaní</h3>
    <table class="table">
        <thead><tr><th>Meno študenta</th><th>úloha</th></tr></thead>
        <tbody>
            <tr><td>Sebastian Schurdak</td><td>Úloha 1 - prihlasovanie, pohľad administrátora </td></tr>
            <tr><td>Matúš Holotňák</td><td>Úloha 1 - pohľad študenta</td></tr>
            <tr><td>Jozef Šimek</td><td>Úloha 2 - prihlasovanie, pohľad administrátora, import\export csv súborov</td></tr>
            <tr><td>Milan Javor</td><td>Úloha 2 - návrh db, pohľad študenta, štatistika, preklad stránky</td></tr>
            <tr><td>Adrian Blažíček</td><td>Úloha 3</td></tr>
        </tbody>
    </table>
    <a href="dokumentacia.html">Technická dokumentácia projektu</a>
</div>

</body>
</html>
