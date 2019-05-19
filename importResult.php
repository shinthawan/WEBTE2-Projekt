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
    header("location: ../index.php");
    exit;
}

if ( isset($_POST["submit"]) ) {

    $year = htmlspecialchars($_POST["years"]);
    $separator = htmlspecialchars($_POST["separator"]);
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

    if (isset($_FILES["file"])) {

        //if there was an error uploading the file
        if ($_FILES["file"]["error"] > 0) {
            echo "Return Code: " . $_FILES["file"]["error"] . "<br />";

        } else {

            //if file already exists
            if (file_exists("upload/" . $_FILES["file"]["name"])) {
                echo $_FILES["file"]["name"] . " already exists. ";
            } else {
                //Store file in directory "upload" with the name of "uploaded_file.txt"
                $storagename = "file.txt";
                move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $storagename);
                echo "Stored in: " . "upload/" . $_FILES["file"]["name"] . "<br />";
            }
        }
    } else {
        echo "No file selected <br />";
    }

// The nested array to hold all the arrays
    $result = [];

    if (isset($storagename) && $file = fopen("upload/" . $storagename, 'r')) {

        echo "File opened.<br />";

        while (($data = fgetcsv($file, 1000, "$separator")) !== FALSE) {
            // Each individual array is being pushed into the nested array
            $result[] = $data;

        }
        fclose($file);
    }
    $i = 0;
    $info = [];
    foreach ($result as $index => $item) {
        for ($j = 0; $j < count($item); $j++) {
            if ($i == 0) {
                $info = $item;
                $i = 1;
            } else {
                if ($j == 0) {
                    $sql4 = "SELECT * FROM uzivatel WHERE id_uzivatel ='$item[0]'";
                    $result = $conn->query($sql4);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                        }
                    } else {
                        echo "som tu";
                        $sql5 = "INSERT INTO  uzivatel(id_uzivatel,meno)
                    VALUES ('$item[0]','$item[1]')";
                        if (mysqli_query($conn, $sql5)) {
                            echo "Pridal uzivatela";
                        } else {
                            echo "Error updating record: " . mysqli_error($conn);
                        }
                    }
                }
                if ($index > 0 && $j > 1 && strcmp($info[$j], 'Spolu') != 0 && strcmp($info[$j], 'Znamka') != 0) {

                    $sql1 = "INSERT INTO  zaznam(id_uzivatel,id_predmet,nazov,hodnota,obdobie)
                    VALUES ('$item[0]',$id,' $info[$j] ',' $item[$j]','$year')";
                    if (mysqli_query($conn, $sql1)) {
                        echo "Success";
                    } else {
                        echo "Error updating record: " . mysqli_error($conn);
                    }

                }
                if ($index > 0 && $j > 1) {
                    if (strcmp($info[$j], 'Spolu') == 0) {
                        $cislo = $j + 1;
                        $sql6 = "INSERT INTO  vysledok(id_uzivatel,id_predmet,znamka,spolu,obdobie)
                    VALUES ('$item[0]',$id,' $item[$cislo] ',' $item[$j]','$year')";
                        if (mysqli_query($conn, $sql6)) {
                            echo "ahoj";
                        } else {
                            echo "Error updating record: " . mysqli_error($conn);
                        }
                    }
                }
            }
        }
    }
}

/*
if (isset($_POST["import"])) {

    if (isset($_POST["predmetName"])){
        $predmet = htmlspecialchars($_POST["predmetName"]);
        echo "viem meno";
        var_dump($schoolYear);
        var_dump($predmet);
        var_dump($columSeparator);
    }
    $fileName = $_FILES["file"]["tmp_name"];

    if ($_FILES["file"]["size"] > 0) {

        $file = fopen($fileName, "r");

        while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
            $sqlInsert = "INSERT into predmety (ID,name,cv1,cv2,cv3,cv4,cv5,cv6,cv7,cv8,cv9,cv10,cv11,Z1,Z2,VT,SK-T,SK-P,Spolu,Znamka,predmet)
                   values ('" . $column[0] . "','" . $column[1] . "','" . $column[2] . "','" . $column[3] . "','" . $column[4] . "','" . $column[5] . "',
                            '" . $column[6] . "','" . $column[7] . "','" . $column[8] . "','" . $column[9] . "','" . $column[10] . "','" . $column[11] . "',
                            '" . $column[12] . "','" . $column[13] . "','" . $column[14] . "','" . $column[15] . "','" . $column[16] . "','" . $column[17] . "',
                            '" . $column[18] . "','" . $column[19] . "','" . $column[20] . "','" . $predmet . "','" . $schoolYear . "')"; //colum 21 zmenit na predmet z formulara
            $result = mysqli_query($conn, $sqlInsert);

            if (! empty($result)) {
                $type = "success";
                $message = "CSV Data Imported into the Database";
            } else {
                $type = "error";
                $message = "Problem in Importing CSV Data";
            }
        }
    }
}*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <div class="custom-file">
                    <label class="btn btn-default btn-file">
                        <input type="file" name="file" id="file" class="custom-file-input" accept=".csv" />
                    </label>
                </div>
            </div>

            <div class="form-group">
                <select name="separator" class="form-control">
                    <option value=";">;</option>
                    <option value=",">,</option>
                </select>
            </div>

            <div class="form-group">
                <select name="years" class="form-control">
                    <option value="2015/2016">2015/2016</option>
                    <option value="2016/2017">2016/2017</option>
                    <option value="2017/2018">2017/2018</option>
                    <option value="2018/2019">2018/2019</option>
                </select>
            </div>

            <div class="form-group">
                <?php

                $sql2 = "SELECT nazov FROM predmet";

                $result2 = $conn->query($sql2);

                echo "<select name='predmety' class=\"form-control\">";
                if ($result2->num_rows > 0) {
                    while($row = $result2->fetch_assoc()) {
                        echo "<option>".$row["nazov"]."</option>";
                    }
                } else {
                    echo "0 results";
                }
                echo "</select>";
                ?>
            </div>
            <div class="form-group">
                    <input type="submit" name="submit" class="btn btn-primary" />
            </div>

        </form>
    </div>
</body>
</html>
