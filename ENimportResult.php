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
    <div class="wrapper">
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <div class="custom-file">
                    <label class="btn btn-default btn-file">
                        <input type="file" name="file" id="file" class="custom-file-input" accept=".csv"/>
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
                    <option value="2019/2020">2019/2020</option>
                    <option value="2020/2021">2020/2021</option>
                    <option value="2021/2022">2021/2022</option>
                    <option value="2022/2023">2022/2023</option>
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
                    <input type="submit" name="submit" class="btn btn-primary" value="Submit"/>
            </div>

        </form>
</body>
</html>
