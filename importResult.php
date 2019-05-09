<?php
// Initialize the session
session_start();
// Include config file
require_once "config.php";

if (isset($_POST["import"])) {

    $fileName = $_FILES["file"]["tmp_name"];

    if ($_FILES["file"]["size"] > 0) {

        $file = fopen($fileName, "r");

        while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
            $sqlInsert = "INSERT into users (userId,userName,password,firstName,lastName)
                   values ('" . $column[0] . "','" . $column[1] . "','" . $column[2] . "','" . $column[3] . "','" . $column[4] . "')";
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
}
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
        <h2>Import výsledkov</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Školský rok</label>
                <select name='schoolYear' class="form-control">
                    <!--
                        tu este nie je jasne ci to chcu takto napevno roky alebo ci to bude nejako v DB a
                        odtial sa to bude tahat
                     -->
                    <option value="<?php $schoolYear="ZS 2019/2020" ?>">ZS 2019/2020</option>
                    <option value="<?php $schoolYear="LS 2019/2020" ?>">LS 2019/2020</option>
                    <option value="<?php $schoolYear="ZS 2020/2021" ?>">ZS 2020/2021</option>
                    <option value="<?php $schoolYear="LS 2020/2021" ?>">LS 2020/2021</option>
                    <option value="<?php $schoolYear="ZS 2022/2023" ?>">ZS 2022/2023</option>
                    <option value="<?php $schoolYear="LS 2022/2023" ?>">LS 2022/2023</option>
                </select>
            </div>
            <div class="form-group">
                <label>Názov predmetu</label>
                <input type="text" name="predmetName" class="form-control">
            </div>
            <div class="form-group">
                <div class="custom-file">
                    <label class="btn btn-default btn-file">
                        <input type="file" class="custom-file-input" accept=".csv">
                    </label>

                </div>
            </div>
            <div class="form-group">
                <label>Oddeľovač stĺpcov</label>
                <select name='schoolYear' class="form-control">
                    <option value="<?php $columSeparator=";" ?>"> ; </option>
                    <option value="<?php $columSeparator="," ?>"> , </option>
                </select>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Import" name="import">
            </div>
            <div id="labelError"></div>
            <!--<p>Don't have an account? <a href="register.php">Sign up now</a>.</p> Ak by bolo potrebne aj registráciu tak pridám-->
            <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
        </form>
    </div>
</body>
</html>
