<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Initialize the session
session_start();
// Include config file
require_once "u2_config.php";

if(isset($_GET['language']) && $_GET['language'] == "EN"){
    header("location: u2en_importResult.php");

}elseif(isset($_GET['language']) && $_GET['language'] == "SK"){
    header("location: u2_importResult.php");
}

// Check if the user is already logged in, if yes then redirect him to logged page
if((!(isset($_SESSION["type"])) || ($_SESSION['type'] != admin))){
    header("location: u2en_index.php");
    exit;
}

//importovani
$predmetNameErr="";
if (isset($_POST["import"])) {
    $fileName=$_FILES['file']['tmp_name'];

    if($_POST['predmetName']==""){
        $predmetNameErr="Pole názov predmetu musí byť vyplnené";
    }

    $predmet=$_POST['predmetName'];
    $rok=$_POST['schoolYear'];

    if ($_FILES["file"]["size"] > 0 && ($_POST['predmetName']!=null)) {

        $file = fopen($fileName, "r");
        fgetcsv($file, 10000, $_POST['separator']); //skip one line

        //ulozenie predmetu
        $sql="SELECT id FROM predmet WHERE rok='$rok' AND nazov='$predmet'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id_predmetu_ins=$row['id'];
        }else{
            $sql = "INSERT INTO predmet (nazov,rok) values ('" . $predmet . "','" . $rok . "')";

            if (mysqli_query($conn, $sql) === TRUE) {
                $id_predmetu_ins = $conn->insert_id;
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }


        while (($column = fgetcsv($file, 10000, $_POST['separator'])) !== FALSE) {
            $heslo=password_hash($column[3], PASSWORD_DEFAULT);

            //ulozenie studentov
            $sql="SELECT * FROM student
                 WHERE id=$column[0]";
            $result = $conn->query($sql);

            if ($result->num_rows == 0) {
                $sql = "INSERT INTO student (id,meno,email,heslo)
                  values ('" . $column[0] . "','". $column[1] ."','". $column[2] ."','". $heslo ."')";

                if (mysqli_query($conn, $sql) === TRUE) {
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }

            //ulozenie timov
            $sql="SELECT id FROM tim
                WHERE cislo=$column[4] AND id_predmet=$id_predmetu_ins";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $id_timu_ins=$row['id'];
            }else{
                $sql = "INSERT into tim (cislo, id_predmet)
                      values ('". $column[4] ."','". $id_predmetu_ins ."')";

                $result = mysqli_query($conn, $sql);
                $id_timu_ins = $conn->insert_id;
            }

            //ulozene zaznamu
            $sql="SELECT id FROM zaznam
                WHERE id_student='$column[0]'";

            $result = $conn->query($sql);

            if ($result->num_rows >= 0) {
                $sql = "INSERT INTO zaznam (id_predmet,id_tim,id_student)
                  values ('" . $id_predmetu_ins . "','". $id_timu_ins ."','". $column[0] ."')";
                if (mysqli_query($conn, $sql) === TRUE) {
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
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
    <title>Task 2 - Import results</title>
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
    <ul><a href="u2_importResult.php?language=SK">Prepni do <img name="sk" src="u2_sk.png" alt="sk"/></a></ul>
</div>

<!--    upload udajov-->
<div class="wrapper" id="selector">
    <h2>Import results</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>School year</label>
            <select name='schoolYear' class="form-control">
                <option value="ZS 2019/2020">ZS 2019/2020</option>
                <option value="LS 2019/2020">LS 2019/2020</option>
                <option value="ZS 2020/2021">ZS 2020/2021</option>
                <option value="LS 2020/2021">LS 2020/2021</option>
                <option value="ZS 2021/2022">ZS 2021/2022</option>
                <option value="LS 2021/2022">LS 2021/2022</option>
            </select>
        </div>
        <div class="form-group" <?php echo (!empty($predmetNameErr)) ? 'has-error' : ''; ?>>
            <label>Subject name</label>
            <input type="text" name="predmetName" class="form-control">
            <span class="help-block"><?php echo $predmetNameErr; ?></span>
        </div>
        <div class="form-group">
            <div class="custom-file">
                <label class="btn btn-default btn-file">
                    <input type="file" name="file" class="custom-file-input" accept=".csv">
                </label>
            </div>
        </div>
        <div class="form-group">
            <label>Collumn separator</label>
            <select class="form-control" name="separator">
                <option value=";"> ; </option>
                <option value=","> , </option>
            </select>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Import" name="import">
        </div>
        <a href="u2en_showResult.php" class="btn btn-danger">Back</a>
    </form>
</div>
</body>
</html>
