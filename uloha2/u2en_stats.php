<?php
// Initialize the session
session_start();
// Include config file
require_once "u2_config.php";

if(isset($_GET['language']) && $_GET['language'] == "EN"){
    header("location: u2en_stats.php");

}elseif(isset($_GET['language']) && $_GET['language'] == "SK"){
    header("location: u2_stats.php");
}


// Check if the user is already logged in, if yes then redirect him to logged page
if((!(isset($_SESSION["type"])) || ($_SESSION['type'] != admin))){
    header("location: u2en_index.php");
    exit;
}
if((isset($_POST['show'])) && ($_POST['predmetName'] != null)) {
    $rok = $_POST['schoolYear'];
    $predmet = $_POST['predmetName'];

    echo "<h2>" . $predmet . " " . $rok . "</h2>";
    $sql = "SELECT COUNT(*) AS pocet
    FROM zaznam 
    INNER JOIN predmet ON predmet.id = zaznam.id_predmet 
    INNER JOIN student ON student.id = zaznam.id_student
    INNER JOIN tim ON tim.id = zaznam.id_tim 
    WHERE predmet.nazov='$predmet' AND predmet.rok = '$rok'";

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pocetAllStudentov = $row['pocet'];
        }
    }

    $sql = "SELECT COUNT(*) AS pocet
    FROM zaznam 
    INNER JOIN predmet ON predmet.id = zaznam.id_predmet 
    INNER JOIN student ON student.id = zaznam.id_student
    INNER JOIN tim ON tim.id = zaznam.id_tim 
    WHERE predmet.nazov='$predmet' AND predmet.rok = '$rok' AND zaznam.suhlas_stud = '1'" ;

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pocet1Studentov = $row['pocet'];
        }
    }
    $sql = "SELECT COUNT(*) AS pocet
    FROM zaznam 
    INNER JOIN predmet ON predmet.id = zaznam.id_predmet 
    INNER JOIN student ON student.id = zaznam.id_student
    INNER JOIN tim ON tim.id = zaznam.id_tim 
    WHERE predmet.nazov='$predmet' AND predmet.rok = '$rok' AND zaznam.suhlas_stud = '2'" ;

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pocet2Studentov = $row['pocet'];
        }
    }
    $sql = "SELECT COUNT(*) AS pocet
    FROM zaznam 
    INNER JOIN predmet ON predmet.id = zaznam.id_predmet 
    INNER JOIN student ON student.id = zaznam.id_student
    INNER JOIN tim ON tim.id = zaznam.id_tim 
    WHERE predmet.nazov='$predmet' AND predmet.rok = '$rok' AND zaznam.suhlas_stud = '0'" ;

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pocet0Studentov = $row['pocet'];
        }
    }

    echo   "The number of students in subject ".$predmet." is ".$pocetAllStudentov.".<br><br>
            The number of students who agree with their valuation is ".$pocet1Studentov.".<br><br>
            The number of students who disagree with their valuation is ".$pocet2Studentov.".<br><br>
            The number of undecided students is ".$pocet0Studentov.".<br><br>
            <div id='piechartStudenti'></div>";



    $sql="SELECT COUNT(*) AS pocet FROM tim, predmet 
          WHERE predmet.nazov='$predmet' AND predmet.rok = '$rok' AND predmet.id = tim.id_predmet";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pocetAllTimov = $row['pocet'];
        }
    }

    $sql="SELECT COUNT(*) AS pocet FROM tim, predmet 
          WHERE predmet.nazov='$predmet' AND predmet.rok = '$rok' AND predmet.id = tim.id_predmet AND tim.suhlas='0'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pocet0Timov = $row['pocet'];
        }
    }

    $sql="SELECT COUNT(*) AS pocet FROM tim, predmet 
          WHERE predmet.nazov='$predmet' AND predmet.rok = '$rok' AND predmet.id = tim.id_predmet AND tim.suhlas='1'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pocet1Timov = $row['pocet'];
        }
    }

    $sql="SELECT COUNT(*) AS pocet FROM tim, predmet 
          WHERE predmet.nazov='$predmet' AND predmet.rok = '$rok' AND predmet.id = tim.id_predmet AND tim.suhlas='2'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pocet2Timov = $row['pocet'];
        }
    }

    echo   "The number of teams in subject ".$predmet." is ".$pocetAllTimov.".<br><br>
            The number of closed teams is ".$pocet1Timov.".<br><br>
            The number of teams, which need further discussion, is ".$pocet2Timov.".<br><br>
            The number of teams with undecided students is ".$pocet0Timov.".<br><br>
            <div id='piechartTimy'></div>";
}
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Task 2 - Statistics</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <link rel="stylesheet" media="print" href="print.css" type="text/css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
</head>
<body style="padding-left: 7vw">

<div class="fixed-top">
    <ul><a href="u2_stats.php?language=SK">Prepni do <img name="sk" src="u2_sk.png" alt="sk"/></a></ul>
</div>

<div class="wrapper" id="teampointsfill">
    <h2>Show results</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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

        <div class="form-group">
            <input style="margin-top:5px;" type="submit" class="btn btn-primary" name="show" value="Show">
        </div>
        <a href="u2en_showResult.php" class="btn btn-primary">Back</a>
        <a href="u2_logout.php" class="btn btn-danger">Sign out</a>
    </form>
</div>

<script type="text/javascript">
    // Load google charts
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawStudentiChart);
    google.charts.setOnLoadCallback(drawTimyChart);

    function drawStudentiChart() {
        var data = google.visualization.arrayToDataTable([
            ['Students', 'Count'],
            ['Agree', <?php echo $pocet1Studentov ?>],
            ['Disagree', <?php echo $pocet2Studentov ?>],
            ['Not decided yet', <?php echo $pocet0Studentov ?>],
        ]);

        // Optional; add a title and set the width and height of the chart
        var options = {'title':'Students', 'width':550, 'height':400};

        var chart = new google.visualization.PieChart(document.getElementById('piechartStudenti'));
        chart.draw(data, options);
    }

    function drawTimyChart() {
        var data = google.visualization.arrayToDataTable([
            ['Teams', 'Count'],
            ['Closed team', <?php echo $pocet1Timov ?>],
            ['Admin disagrees', <?php echo $pocet2Timov ?>],
            ['Undecided students in team', <?php echo $pocet0Timov ?>],
        ]);

        // Optional; add a title and set the width and height of the chart
        var options = {'title':'Teams', 'width':550, 'height':400};

        var chart = new google.visualization.PieChart(document.getElementById('piechartTimy'));
        chart.draw(data, options);
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
