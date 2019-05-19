<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Initialize the session
session_start();
// Include config file
require_once "config.php";

// Check if the user is already logged in, if yes then redirect him to welcome page
if(!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] !== true){
    header("location: uloha1index.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="sk">
<head>
   <title>ULOHA 1</title>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="print.css">
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
   <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
   <script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
   <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
   <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
   <link rel="stylesheet" href=https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css"">
   <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <script>
        $(document).ready(function () {
            $('#tabulka').DataTable({
                dom: 'Blfrtip',
                buttons: [ 'pdf'
                ]
            });
        });
    </script>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">ULOHA 1</a>
            </div>
            <ul class="nav navbar-nav">
                <li class="active"><a href="uloha1menu.php">Domov</a></li>
                <li><?php if(isset($_SESSION["admin"]) && $_SESSION["admin"] === true) {
                        echo "<a href=\"importResult.php\">Import výsledkov</a>";           //ak je prihlaseny ako admin tak "Uloha1" sluzi na importResult
                    } ?></li>
                <li><?php if(isset($_SESSION["admin"]) && $_SESSION["admin"] === true){                                                     //ak je student tak na studentView
                        echo"<a href=\"showResult.php\">Zobrazenie výsledkov</a>";          //pri prihlaseni ako student ale aj pri prihlaseni cez ldap sa naplni session student
                    }?></li>
                <li><?php if(isset($_SESSION["admin"]) && $_SESSION["admin"] === true){                                                     //ak je student tak na studentView
                        echo"<a href=\"deleteSubject.php\">Vymazanie predmetu</a>";          //pri prihlaseni ako student ale aj pri prihlaseni cez ldap sa naplni session student
                    }?></li>
                <li><a href="uloha1menu.php?language=EN">Jazyk</a></li>
            </ul>
        </div>
    </nav>
    <div class="container">
    <div class="wrapper">
            <form action="showResult.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                <select name="years" class="form-control">
                    <option value="2015/2016">2018/2019</option>
                    <option value="2019/2020">2019/2020</option>
                    <option value="2020/2021">2020/2021</option>
                    <option value="2021/2022">2021/2022</option>
                    <option value="2022/2023">2022/2023</option>
                </select>
                </div>
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
                    <input type="submit" name="zobraz" class="btn btn-primary" />


            </form>
    </div>
        <?php

        if (isset($_POST["zobraz"])) {

            $year = htmlspecialchars($_POST["years"]);
            $predmet = htmlspecialchars($_POST["predmety"]);

            $id = 0;

            $sql3 = "SELECT id_predmet FROM predmet WHERE nazov ='$predmet'";
            $result = $conn->query($sql3);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $id = $row["id_predmet"];
                }
            } else {
                echo "0 results";
            }


            $sql3 = "SELECT nazov,hodnota,id_uzivatel,obdobie FROM zaznam WHERE obdobie = '$year' AND id_predmet = '$id'";
            $result = $conn->query($sql3);
            $temp = 0;

            echo "<table class=\"table table-striped table-bordered\" id='tabulka'>";
            echo "<thead><tr>";
            echo "<th>ID</th>";
            echo "<th>Meno</th>";
            echo "<th>Spolu</th>";
            echo "<th>Známka</th>";
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    if($temp == 0){
                        $uzivatel = $row["id_uzivatel"];
                        $temp = 1;
                    }
                    if($uzivatel == $row["id_uzivatel"]) {
                        echo "<th>" . $row['nazov'] . "</th>";
                    }
                }
            }else{
               // echo"niesu vysledky" ;
            }

            echo "</tr></thead>";

            $temp = 0;
            $temp2 = 0;
            $sql7 = "SELECT z.nazov,z.hodnota,z.id_uzivatel,u.meno,v.znamka,v.spolu,z.id_zaznam FROM zaznam z JOIN uzivatel u ON u.id_uzivatel = z.id_uzivatel JOIN vysledok v ON z.id_uzivatel = v.id_uzivatel WHERE z.obdobie = '$year' AND z.id_predmet = '$id' AND v.id_predmet = '$id' AND v.obdobie = '$year' GROUP BY z.nazov,z.hodnota,z.id_uzivatel,u.meno,v.znamka,v.spolu,z.id_zaznam ORDER BY z.id_zaznam";
            $result = $conn->query($sql7);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    if($uzivatel != $row["id_uzivatel"] && $temp2 ==1) {
                        $temp =0;
                    }
                    if($temp == 0){
                        if($temp2 ==1){
                            echo"</tr>";
                        }
                        $uzivatel = $row["id_uzivatel"];
                        $temp = 1;
                        echo"<tr><td>".$row['id_uzivatel']."</td><td>".$row['meno']."</td><td>".$row['spolu']."</td><td>".$row['znamka']."</td>";

                    }
                    if($uzivatel == $row["id_uzivatel"]) {
                        echo "<td>" . $row['hodnota'] . "</td>";
                        $temp2 =1;
                    }

                }
            }else{
                echo"ziadne vysledky" ;
            }
            echo "</tr></table>";
        }
        ?>
    </div>
    </div>
</body>
</html>
