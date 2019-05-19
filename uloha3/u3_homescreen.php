<?php
session_start();
if (isset($_GET['language']) && $_GET['language'] == "EN") {
    header("location: u3_homescreenEN.php");
} elseif (isset($_GET['language']) && $_GET['language'] == "SK") {
    header("location: u3_homescreen.php");
}


?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="u3_ajaxCalls.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">


    <title>Uloha3</title>
</head>
<body style="padding-bottom: 70px;">


<div style="width: 90%; margin: auto;">
    <h2>Generovanie hesiel</h2>
    <form action="u3_fileHandler.php" method="post" enctype="multipart/form-data">

        <div class="custom-file">
            <input type="file" class="custom-file-input" id="customFile" name="initialCSV">
            <label class="custom-file-label" for="customFile">Vyberte csv subor</label>
        </div>
        <input class="form-control" type="text" name="delimeter" id="Delimeter"
               placeholder="oddelovač v csv súbore"><br>
        <input type="submit" value="Vygenerovať heslá" name="submit" class="btn btn-primary">
    </form>
    <br>


    <hr>
    <h2>Odoslať hromadný mail:</h2>
    <form action="u3_mailSender.php" method="post" enctype="multipart/form-data">
        vyberte šablonu : <select id="templateName" class="form-control" onchange="getTemplate()"
                                  onload="getTemplate() class=dropdown-divider">
            <?php
            require "u3_config.php";
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
            <label class="custom-file-label" for="customFile">Vyberte csv subor</label>
        </div>
        <input type="text"  class="form-control" name="delimeter" placeholder="oddelovač v csv súbore" required><br>
        <input type="text" class="form-control" name="sender" placeholder="odosielatel" required><br>
        <input type="email" class="form-control" name="senderMail" placeholder="váš e-mail" required><br>
        <input type="text" class="form-control" name="object" placeholder="predmet správy" required><br>

        <input class="form-check-input" type="radio" id="exampleRadios1" name="isHTML" value="true" checked>Poslať ako
        HTML<br>
        <input class="form-check-input" type="radio" id="exampleRadios2" name="isHTML" value="false">Poslať ako
        Plaint-Text<br>
        <textarea class="form-control" name="template" rows="15" cols="100" id="templateShowPlace" placeholder="Tu bude text vami zvolenej šablóny. Šablónu môžete lubovolňe upravovať. Ak chcete aby boli v maily údaje z csv súboru, treba ich v texte označiť ako {{nazov stlpca}}. Ak chcete pridať meno odosielatela, pridajte {{sender}}, ak chcete pridať mail odosielatela pridajte{{senderMail}}"></textarea><br>
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="customFile" name="attachment">
            <label class="custom-file-label" for="customFile">Vyberte prilohu</label>
        </div>
        <input type="submit" value="odoslať" name="submit" class="btn btn-primary">
    </form>
    <button onclick="clearInput()" class="btn btn-danger">vymazať prílohu</button>
</div>
<hr>
Odoslané maily :
<div id="divTable">
    <table id="sortableTable" class="display">
        <thead>
        <tr>
            <th>príjmateľ</th>
            <th>predmet správy</th>
            <th>čas odoslania</th>
            <th>id použitej šablóny</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $con = getDBConnection();
        $sql = "select * from uloha3_maily";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["meno"] . "</td><td>" . $row["predmet"] . "</td><td>" . $row["odoslane"] . "</td><td>" . $row["sablona"] . "</td></tr>";
        }
        ?>
        </tbody>
    </table>
    <script>
        $(document).ready(function () {
            $('#sortableTable').DataTable({
                paging: true,
                searching: false

            });
        });
    </script>
</div>

<div class="container" style="position: fixed; bottom: 0; right: 0;">
    <button class="btn btn-secondary" style="float: right" onclick="window.location.href='u3_homescreenEN.php'">Jazyk</button>
    <button class="btn btn-danger"  style="float: right" onclick="window.location.href='../uloha1/logout.php'">Odhlásiť</button>
</div>

</body>
</html>