<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Initialize the session
session_start();
// Include config file
require_once "u2_config.php";
include "u2_exportData.php";

// Check if the user is already logged in, if yes then redirect him to logged page
if((!(isset($_SESSION["type"])) || ($_SESSION['type'] != admin))){
    header("location: u2_index.php");
    exit;
}

//odsuhlasenie admina
if(!empty($_POST['suhlas'])) {
    $idTim = $_POST['timID'];
    $suhlas = $_POST['suhlas'];
    $sql = "UPDATE tim SET suhlas = '$suhlas'
           WHERE tim.id = '$idTim'";
    $result = mysqli_query($conn, $sql);
}

if(isset($_POST['sumbit_body'])){
    $cislo_tim=$_POST['tim'];
    $predmet=$_POST['predmet'];
    $rok=$_POST['rok'];
    $body=$_POST['body'];


    $sql = "UPDATE tim
        INNER JOIN predmet ON predmet.id = tim.id_predmet
       SET tim.body='$body'
       WHERE predmet.nazov='$predmet' AND tim.cislo='$cislo_tim' AND predmet.rok='$rok'";

    $result = mysqli_query($conn, $sql);
}

if(isset($_POST['sumbit_body_admin'])){
    $tim=$_POST['tim'];
    $predmet=$_POST['predmet'];
    $rok=$_POST['rok'];

    $sql="UPDATE tim
        INNER JOIN predmet ON predmet.id = tim.id_predmet
       SET tim.suhlas=1
       WHERE predmet.nazov='$predmet' AND tim.cislo='$cislo_tim' AND predmet.rok='$rok'";

    $result = mysqli_query($conn, $sql);
}

//zobrazovani timov vybraneho roku a predmetu
if(isset($_GET['show'])){
    $rok=$_GET['schoolYear'];
    $predmet = $_GET['predmetName'];

    echo "<h2>".$predmet." ".$rok."</h2>";

    echo "<form action='".$_SERVER['REQUEST_URI']."' method='post' enctype='multipart/form-data'>		
                  <button type='submit' name='export' class='btn btn-primary'>Exportovať údaje</button>		
          </form>";

    $sql="SELECT * FROM zaznam
           INNER JOIN predmet ON predmet.id = zaznam.id_predmet
           INNER JOIN student ON student.id = zaznam.id_student
           INNER JOIN tim ON tim.id = zaznam.id_tim
         WHERE predmet.nazov='$predmet' AND predmet.rok = '$rok'
         ORDER BY tim.cislo ASC";

    //premenne na zistenie ci je uz iny tim
    $actualTeam=null;
    $previousTeam=null;

    //premenna do ktorej sa uklada vypis
    $toPrint="";

    //vypisanie timov daneho predmetu, strasne nepekna vec, nic lepsi mna nenapadlo
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {   //Creates a loop to loop through results
            $classUdelitBody="";
            $classSuhlasBody="";
            $actualTeam= $row['cislo'];
            $meno=$row['meno'];
            $email=$row['email'];
            $body=$row['body_stud'];
            $suhlas = $row['suhlas_stud'];
            $body_tim = $row['body'];
            $suhlas_admin=$row['suhlas'];
            $hidden = '';

            if($body_tim!=0 && $body_tim!=null){
                $classUdelitBody="disabled";
            }

            //suhlas clenov timov --> suhlas admina povoleny
            $sql2 = "SELECT * FROM zaznam
                           INNER JOIN predmet ON predmet.id = zaznam.id_predmet
                           INNER JOIN student ON student.id = zaznam.id_student
                           INNER JOIN tim ON tim.id = zaznam.id_tim
                       WHERE predmet.nazov='$predmet' AND predmet.rok = '$rok' AND tim.cislo='$actualTeam'
                       ORDER BY tim.cislo ASC";

            $result2 = $conn->query($sql2);
            if ($result2->num_rows > 0) {
                while ($row = $result2->fetch_assoc()) {
                    $suhlasyClenov = $row['suhlas_stud'];
                    $suhlasAdmina = $row['suhlas'];
                    $idTim=$row['id_tim'];

                    if($suhlasyClenov==0 || $suhlasyClenov==2){
                        $hidden = 'hidden';
                    }

                    switch($suhlasAdmina){
                        case "0":
                            $toPrintBasic ="<div $hidden>
                           <i style='cursor: pointer; color: green;display: inline-block' id='suhlas_btn' onclick='postSuhlas(1, $idTim);' class='fa fa-thumbs-up'></i>
                           <i style='cursor: pointer; color: red;display: inline-block' id='nesuhlas_btn' onclick='postSuhlas(2, $idTim);' class='fa fa-thumbs-down'></i></div>";
                            break;
                        case "1":
                            $toPrintBasic = "
                           <i style='color: #4d8056 ;' class='fa fa-thumbs-up'></i>
                           <div style='color: #4d8056;display: inline-block'>Už odsúhlasené.</div>";
                            break;
                        case "2":
                            $toPrintBasic ="
                           <i style='color: #803024 ;' class='fa fa-thumbs-down'></i>
                           <div style='color: #803024;display: inline-block'>Nesúhlasím s rozdelením bodov.</div>";
                            break;
                    }
                }
            }

            if($previousTeam!=$actualTeam){
                $toPrint=$toPrint."</table><br><br><label>Tím ".$actualTeam." <br>
               Body tímu: $body_tim</label>
                  <form action='".$_SERVER['REQUEST_URI']."' method='post' enctype='multipart/form-data'>
                      <input type='number' name='body' placeholder='Body pre tím' min='0'>
                      <input type='text' name='tim' value='$actualTeam' hidden>
                      <input type='text' name='rok' value='$rok' hidden>
                      <input type='text' name='predmet' value='$predmet' hidden>
                      <button type='submit' name='sumbit_body' class='btn btn-primary' $classUdelitBody >Prideliť body tímu</button>
                  </form>
                   
                  $toPrintBasic
         
                  <table class='table table-bordered'>
                      <thead class='thead-dark'>
                          <tr>
                              <th scope='col' class=\"col-md-3\">Email</th>
                              <th scope='col' class=\"col-md-4\">Meno</th>
                              <th scope='col' class=\"col-md-2\">Body</th>
                              <th scope='col' class=\"col-md-2\">Súhlas</th>
                          </tr>
                      </thead>";
                $previousTeam=$actualTeam;
            }
            $toPrint=$toPrint."
           <tr>
               <td>".$email."</td>
               <td>".$meno."</td>
               <td>".$body."</td>";
            switch($suhlas){
                case "0":
                    $toPrint = $toPrint . "<td>
                            <div style='color: #7b8080;'>Študent sa ešte nerozhodol.</div>
                            </td></tr>";
                    break;
                case "1":
                    $toPrint = $toPrint . "<td>
                            <i style='color: #4d8056 ;' class='fa fa-thumbs-up'></i>
                            <div style='color: #4d8056;display: inline-block'>Študent súhlasí s týmto hodnotením.</div>
                            </td></tr>";
                    break;
                case "2":
                    $toPrint = $toPrint . "<td>
                            <i style='color: #803024 ;' class='fa fa-thumbs-down'></i>
                            <div style='color: #803024;display: inline-block'>Študent nesúhlasí s týmto hodnotením.</div>
                            </td></tr>";
                    break;
            };
        }
        if(isset($toPrint)){
            $toPrint=$toPrint."</table>";
            echo $toPrint;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Úloha 2</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
    body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
</head>
<body>
<!--    zobrazenie timov-->
<div class="wrapper">
    <h2>Zobrazenie výsledkov</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">
        <select name='schoolYear' id="schoolYear" class="form-control" onChange="getPredmet(this.value);">
            <option value="">Vyber rok</option>
            <option value="ZS 2019/2020">ZS 2019/2020</option>
            <option value="LS 2019/2020">LS 2019/2020</option>
            <option value="ZS 2020/2021">ZS 2020/2021</option>
            <option value="LS 2020/2021">LS 2020/2021</option>
            <option value="ZS 2021/2022">ZS 2021/2022</option>
            <option value="LS 2021/2022">LS 2021/2022</option>
        </select>

        <select name='predmetName' id="predmetName" class="form-control">
            <option value="">Vyber predmet</option>
        </select>
        <br>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" name="show" value="Zobraziť">
        </div>
        <a href="u2_stats.php" class="btn btn-info">Štatistiky</a>
        <a href="u2_importResult.php" class="btn btn-primary">Importovať údaje</a><br><br>
        <a href="u2_logout.php" class="btn btn-danger">Odhlásiť sa</a><br>
    </form>
</div>

<!--    jQuery -> $_POST na "select option"-->
<script>
    function postSuhlas(suhlas, timID) {
        $.ajax({
            type: "POST",
            url: "u2_showResult.php",
            data:{ suhlas: suhlas, timID:timID},
            success: function () {
                setTimeout(function(){
                    location.reload();
                }, 500);
            }
        });
    }

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
