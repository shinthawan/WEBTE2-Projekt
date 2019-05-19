<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Initialize the session
session_start();
// Include config file
require_once "u2_config.php";
include "u2_exportData.php";

if(isset($_GET['language']) && $_GET['language'] == "EN"){
    header("location: u2en_showResult.php");

}elseif(isset($_GET['language']) && $_GET['language'] == "SK"){
    header("location: u2_showResult.php");
}

// Check if the user is already logged in, if yes then redirect him to logged page
if((!(isset($_SESSION["type"])) || ($_SESSION['type'] != admin))){
    header("location: u2en_index.php");
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
                  <button type='submit' name='export' class='btn btn-primary'>Export data</button>		
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
                           <div style='color: #4d8056;display: inline-block'>I agree with this valuation.</div>";
                            break;
                        case "2":
                            $toPrintBasic ="
                           <i style='color: #803024 ;' class='fa fa-thumbs-down'></i>
                           <div style='color: #803024;display: inline-block'>I disagree with this valuation.</div>";
                            break;
                    }
                }
            }

            if($previousTeam!=$actualTeam){
                $toPrint=$toPrint."</table><br><br><label>Team #".$actualTeam." <br>
                Team points: $body_tim</label>
                  <form action='".$_SERVER['REQUEST_URI']."' method='post' enctype='multipart/form-data'>
                      <input type='number' id ='teampointsfill' name='body' placeholder='Team points' min='0'>
                      <input type='text' name='tim' value='$actualTeam' hidden>
                      <input type='text' name='rok' value='$rok' hidden>
                      <input type='text' name='predmet' value='$predmet' hidden>
                      <button type='submit' name='sumbit_body' class='btn btn-primary' $classUdelitBody >Submit points to team</button>
                  </form>
                   
                  $toPrintBasic
         
                  <table class='table table-bordered'>
                      <thead class='thead-dark'>
                          <tr>
                              <th scope='col' class=\"col-md-3\">Email</th>
                              <th scope='col' class=\"col-md-4\">Name</th>
                              <th scope='col' class=\"col-md-2\">Points</th>
                              <th scope='col' class=\"col-md-2\">Agreement</th>
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
                            <div style='color: #7b8080;'>Student has not decided yet.</div>
                            </td></tr>";
                    break;
                case "1":
                    $toPrint = $toPrint . "<td>
                            <i style='color: #4d8056 ;' class='fa fa-thumbs-up'></i>
                            <div style='color: #4d8056;display: inline-block'>Student <b>agrees</b> with this valuation.</div>
                            </td></tr>";
                    break;
                case "2":
                    $toPrint = $toPrint . "<td>
                            <i style='color: #803024 ;' class='fa fa-thumbs-down'></i>
                            <div style='color: #803024;display: inline-block'>Student <b>disagrees</b> with this valuation.</div>
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
    <title>Task 2</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" media="print" href="print.css" type="text/css">
    <style type="text/css">
    body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
</head>
<body>
<div class="fixed-top">
    <ul><a href="u2en_showResult.php?language=SK">Prepni do <img name="sk" src="u2_sk.png" alt="sk"/></a></ul>
</div>
<!--    zobrazenie timov-->
<div class="wrapper" id="selector">
    <h2>Show results</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">
        <select name='schoolYear' id="schoolYear" class="form-control" onChange="getPredmet(this.value);">
            <option value="">Select year</option>
            <option value="ZS 2019/2020">ZS 2019/2020</option>
            <option value="LS 2019/2020">LS 2019/2020</option>
            <option value="ZS 2020/2021">ZS 2020/2021</option>
            <option value="LS 2020/2021">LS 2020/2021</option>
            <option value="ZS 2021/2022">ZS 2021/2022</option>
            <option value="LS 2021/2022">LS 2021/2022</option>
        </select>

        <select name='predmetName' id="predmetName" class="form-control">
            <option value="">Select subject</option>
        </select>
        <br>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" name="show" value="Show">
        </div>
        <a href="u2en_stats.php" class="btn btn-info">Statistics</a>
        <a href="u2en_importResult.php" class="btn btn-primary">Import data</a><br><br>
        <a href="u2_logout.php" class="btn btn-danger">Sign out</a><br>
    </form>
</div>

<!--    jQuery -> $_POST na "select option"-->
<script>
    function postSuhlas(suhlas, timID) {
        $.ajax({
            type: "POST",
            url: "u2en_showResult.php",
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
