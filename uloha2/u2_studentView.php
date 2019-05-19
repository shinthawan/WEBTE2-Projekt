<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize the session
session_start();

require_once "u2_config.php";

if(isset($_GET['language']) && $_GET['language'] == "EN"){
    header("location: u2en_studentView.php");

}elseif(isset($_GET['language']) && $_GET['language'] == "SK"){
    header("location: u2_studentView.php");
}

// Check if the user is already logged in, if yes then redirect him to welcome page
if((!(isset($_SESSION["type"])) || ($_SESSION['type'] != student))){
    header("location: u2_index.php");
    exit;
}


if(isset($_POST['sumbit_body_kapitan'])){
    $timID = $_POST['timID'];
    $predmetID = $_POST['predmetID'];
    $pridelene = $_POST['pridelene'];

    $studentBody=$_POST['body'];
    $studentID = $_POST['studentID'];
    $pocetB = 0;
    $pocetS = count($studentID);

    for($i = 0;$i<$pocetS;$i++){
        $pocetB = $pocetB + $studentBody[$i];
    }

    if($pocetB != $pridelene) {
        echo "<div class=\"alert alert-danger\" role=\"alert\">
                Pridelili ste málo alebo veľa bodov (" . $pocetB . "). Váš tím získal " . $pridelene . " bodov.</div>";
    }else{
        for($i = 0;$i<$pocetS;$i++){
            $sql="UPDATE zaznam 
                  SET body_stud = '$studentBody[$i]'
                  WHERE zaznam.id_student='$studentID[$i]' AND zaznam.id_predmet='$predmetID' AND zaznam.id_tim = '$timID'";
            $result = mysqli_query($conn, $sql);
        }
    }
}

if(!empty($_POST['suhlas'])) {
    $id = $_SESSION['id'];
    $suhlas = $_POST['suhlas'];
    $timID =  $_POST['timID'];

    $sql = "UPDATE zaznam SET suhlas_stud = '$suhlas' 
            WHERE zaznam.id_student = '$id' AND zaznam.id_tim = '$timID'";
    $result = mysqli_query($conn, $sql);
}

if(isset($_GET['show']) && !empty($_GET['predmetName']) && !empty($_GET['schoolYear'])){
    $id = $_SESSION['id'];
    $email=$_SESSION['email'];
    $rok =$_GET['schoolYear'];
    $predmet = $_GET['predmetName'];
    $toPrintBasic='';
    $toPrintKapitan='';
    $classUdelitBody = '';

    echo "<h2>".$predmet." ".$rok."</h2>";

    $sql="SELECT tim.id FROM zaznam 
    INNER JOIN predmet ON predmet.id = zaznam.id_predmet 
    INNER JOIN student ON student.id = zaznam.id_student 
    INNER JOIN tim ON tim.id = zaznam.id_tim 
    WHERE predmet.nazov='$predmet' AND predmet.rok = '$rok' AND student.id='$id'";


    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {   //Creates a loop to loop through results
            $timID = $row['id'];
        }
    }

    //kontrola zadelenia bodov v time
    $sql="SELECT zaznam.body_stud FROM zaznam 
                            INNER JOIN predmet ON predmet.id = zaznam.id_predmet 
                            INNER JOIN student ON student.id = zaznam.id_student
                            INNER JOIN tim ON tim.id = zaznam.id_tim
                        WHERE predmet.nazov='$predmet' AND predmet.rok = '$rok' AND tim.id='$timID'";
    $result = $conn->query($sql);
    $zadelene = false;

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {   //Creates a loop to loop through results
            $bodyStudent = $row['body_stud'];
            if ($bodyStudent != 0){
                $zadelene = true;
                break;
            }
        }
    }

    $sql="SELECT predmet.id as predmetID, tim.cislo,student.id, tim.suhlas AS suhlas_adm, student.meno, zaznam.body_stud, zaznam.suhlas_stud, tim.body, student.email FROM zaznam 
                            INNER JOIN predmet ON predmet.id = zaznam.id_predmet 
                            INNER JOIN student ON student.id = zaznam.id_student
                            INNER JOIN tim ON tim.id = zaznam.id_tim
                        WHERE predmet.nazov='$predmet' AND predmet.rok = '$rok' AND tim.id='$timID'
                        ORDER BY student.meno";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {   //Creates a loop to loop through results
            $cislo = $row['cislo'];
            $body_tim = $row['body'];
            $studentID = $row['id'];
            $predmetID = $row['predmetID'];
            $suhlasStudent = $row['suhlas_stud'];
            $suhlasAdmin= $row['suhlas_adm'];

            if($body_tim==0 || $body_tim == null){
                $classUdelitBody="disabled";
            }

            if(!$zadelene) {
                $toPrintKapitan = $toPrintKapitan . "
                <tr>
                    <td>" . $row['meno'] . "</td>
                    <td> <input type='number' name='body[]' max='$body_tim'>
                        <input type='text' name='studentID[]' value='$studentID' hidden></td>
                </tr>";
            }else{
                $toPrintBasic = $toPrintBasic . "
                <tr>
                    <td>" . $row['email'] . "</td>
                    <td>" . $row['meno'] . "</td>
                    <td>" . $row['body_stud'] . "</td>";
                if ($id == $studentID){
                    switch($suhlasStudent){
                        case "0":
                            $toPrintBasic = $toPrintBasic . "<td>
                            <i style='cursor: pointer; color: green;display: inline-block' onclick='postSuhlas(1, $timID);' class='fa fa-thumbs-up'></i>
                            <i style='cursor: pointer; color: red;display: inline-block' onclick='postSuhlas(2, $timID);' class='fa fa-thumbs-down'></i>
                            </td>";
                            break;
                        case "1":
                            $toPrintBasic = $toPrintBasic . "<td>
                            <i style='color: #4d8056 ;' class='fa fa-thumbs-up'></i>
                            <div style='color: #4d8056;display: inline-block'>Študent <b>súhlasí</b> s týmto hodnotením.</div>
                            </td> ";
                            break;
                        case "2":
                            $toPrintBasic = $toPrintBasic . "<td>
                            <i style='color: #803024 ;' class='fa fa-thumbs-down'></i>
                            <div style='color: #803024;display: inline-block'>Študent <b>nesúhlasí</b> s týmto hodnotením.</div>
                            </td>";
                            break;
                    }
                }else{
                    switch($suhlasStudent){
                        case "0":
                            $toPrintBasic = $toPrintBasic . "<td>
                            <div style='color: #7b8080;'>Študent sa ešte nerozhodol.</div>
                            </td>";
                            break;
                        case "1":
                            $toPrintBasic = $toPrintBasic . "<td>
                            <i style='color: #4d8056 ;' class='fa fa-thumbs-up'></i>
                            <div style='color: #4d8056;display: inline-block'>Študent <b>súhlasí</b> s týmto hodnotením.</div>
                            </td>";
                            break;
                        case "2":
                            $toPrintBasic = $toPrintBasic . "<td>
                            <i style='color: #803024 ;' class='fa fa-thumbs-down'></i>
                            <div style='color: #803024;display: inline-block'>Študent <b>nesúhlasí</b> s týmto hodnotením.</div>
                            </td>";
                            break;
                    }
                }
            }
        }

        if(isset($toPrintKapitan)){
            if(!$zadelene){
                echo"<div id='vysledok'><br><br><label>Tím č." . $cislo . "<br>
                Celkové body tímu: " . $body_tim . "</label><br>
                Váš tím ešte nemá rozdelené body. Môžete tak urobiť teraz.<br><br>
                <table class='table table-bordered'>
                <thead class='thead-dark'>
                    <tr>
                        <th scope='col' class=\"col-md-3\">Meno</th>
                        <th scope='col' class=\"col-md-4\">Body</th>
                    </tr>
                </thead>
                <form action='" . $_SERVER['REQUEST_URI'] . "' method='post' enctype='multipart/form-data'>
                    <input type='text' name='timID' value='$timID' hidden>
                    <input type='text' name='predmetID' value='$predmetID' hidden>
                    <input type='text' name='pridelene' value='$body_tim' hidden>";

                echo $toPrintKapitan;
                echo"
                </table><button type='submit' name='sumbit_body_kapitan' class='btn btn-warning' $classUdelitBody >Prideliť body </button>
                </form></div>";
            }
        }
        if(isset($toPrintBasic)){
            if ($zadelene){
                echo"<div id='vysledok'><br><br><label>Tím č." . $cislo . "<br>
                Celkové body tímu: " . $body_tim . "</label><br> 
                Váš tím má rozdelené body.";

                switch($suhlasAdmin){
                    case 0:
                        echo " Čaká sa na schválenie adminom.<br><br>";
                        break;
                    case 1:
                        echo " Admin <b>súhlasí</b> s týmto rozdelením.<br><br>";
                        break;
                    case 2:
                        echo " Admin <b>nesúhlasí</b> s týmto rozdelením.<br><br>";
                        break;
                };
                echo "
                <table class='table table-bordered'>
                <thead class='thead-dark'>
                    <tr>
                        <th scope='col' class=\"col-md-3\">Email</th>
                        <th scope='col' class=\"col-md-3\">Meno</th>
                        <th scope='col' class=\"col-md-4\">Body</th>
                        <th scope='col' class=\"col-md-4\">Súhlas</th>
                    </tr>
                </thead>";
                echo $toPrintBasic."</table></div>";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Úloha 2 - Pohľad Študenta</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <link rel="stylesheet" media="print" href="print.css" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body style="padding: 7vw">
<div class="fixed-top">
    <ul><a href="u2_studentView.php?language=EN">Switch to <img name="en" src="u2_gb.png" alt="en"/></a></ul>
</div>

<div class="wrapper">
    <h3>Prihlásený študent: <?php
        $id = $_SESSION['id'];
        $sql = "SELECT meno FROM student WHERE id='$id'";
        $result = mysqli_query($conn, $sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {   //Creates a loop to loop through results
                echo $row['meno'];
            }
        }
        ?></h3>
    <div id="selector">
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

            <div class="form-group">
                <input style="margin-top:5px;" type="submit" class="btn btn-primary" name="show" value="Zobraziť">
            </div>

            <br>
            <a href="u2_logout.php" class="btn btn-danger">Odhlásiť sa</a>
        </form>
    </div>
</div>
<script>
    function postSuhlas(suhlas, timID) {
        $.ajax({
            type: "POST",
            url: "u2_studentView.php",
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
            data:{ rokStudent: val, idStudent:<?php echo $_SESSION['id'] ?>},
            success: function (data) {
                $("#predmetName").html(data);
            }
        });
    }
</script>
</body>
</html>
