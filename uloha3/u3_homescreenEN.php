<?php
session_start();
if(isset($_GET['language']) && $_GET['language'] == "EN"){
    header("location: u3_homescreenEN.php");
}elseif(isset($_GET['language']) && $_GET['language'] == "SK"){
    header("location: u3_homescreen.php");
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="u3_ajaxCalls.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <title>Task 3</title>
</head>
<body>


<div style="width: 90%; margin: auto;">
    <h2>Password generator</h2>
    <form action="u3_fileHandler.php" method="post" enctype="multipart/form-data">

        <div class="custom-file">
            <input type="file" class="custom-file-input" id="customFile" name="initialCSV">
            <label class="custom-file-label" for="customFile">choose csv file</label>
        </div>
        <input class="form-control" type="text" name="delimeter" id="Delimeter" placeholder="delimeter used in csv"><br>
        <input type="submit" value="generate passwords" name="submit" class="btn btn-primary">
    </form>
    <br>


    <hr>
    <h2>Mass correspondence</h2>
    <form action="u3_mailSender.php" method="post" enctype="multipart/form-data">
        choose template : <select id="templateName" class="form-control" onchange="getTemplate()" onload="getTemplate() class=dropdown-divider">
            <?php
            require_once "u3_config.php";
            $conn = getDBConnection();
            $sql = "select nazov from uloha3_sablony";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                for ($i = 0; $i < $result->num_rows; $i++) {
                    $row = $result->fetch_assoc();
                    echo "<option value='" . $row["nazov"] . "'>" . $row["nazov"] . "</option>";
                }
            }
            ?>
        </select><br>
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="customFile" name="finalCSV">
            <label class="custom-file-label" for="customFile">Choose csv file</label>
        </div>
        <input type="text" class="form-control" name="delimeter" placeholder="delimeter used in csv" required><br>
        <input type="text" class="form-control" name="sender" placeholder="sender" required><br>
        <input type="email" class="form-control" name="senderMail" placeholder="sender´s mail" required><br>
        <input type="text" class="form-control" name="object" placeholder="subject" required><br>

        <input class="form-check-input" type="radio" id="exampleRadios1" name="isHTML" value="true" checked>send as HTML<br>
        <input class="form-check-input" type="radio" id="exampleRadios2" name="isHTML" value="false">send asPlaint-Text<br>
        <textarea class="form-control" name="template" rows="15" cols="100" id="templateShowPlace"></textarea><br>
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="customFile" name="attachment">
            <label class="custom-file-label" for="customFile">choose attachment</label>
        </div>
        <input type="submit" value="send" name="submit" class="btn btn-primary">
    </form>
    <button onclick="clearInput()" class="btn btn-danger">clear attachments</button>

</div>
<hr>
sent mails :
<div   id="divTable">
    <table id="sortableTable" class="display">
        <thead><tr><th>recipient</th><th>message subject</th><th>time</th><th>id of template used</th></tr></thead>
        <tbody>
        <?php
        $con=getDBConnection();
        $sql="select * from uloha3_maily";
        $result=$conn->query($sql);
        while($row=$result->fetch_assoc()){
            echo "<tr><td>".$row["meno"]."</td><td>".$row["predmet"]."</td><td>".$row["odoslane"]."</td><td>".$row["sablona"]."</td></tr>";
        }
        ?>
        </tbody>
    </table>
    <script>
        $(document).ready( function () {
            $('#sortableTable').DataTable({
                paging: true,
                searching: false

            });
        } );
    </script>
</div>
<div class="container" style="position: fixed; bottom: 0; right: 0;">
    <button class="btn btn-secondary" style="float: right" onclick="window.location.href='u3_homescreen.php'">Language</button>
    <button class="btn btn-danger"  style="float: right" onclick="window.location.href='../uloha1/logout.php'">Log Out</button>

</div>
</body>
</html>
