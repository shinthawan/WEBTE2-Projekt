<?php
require_once("u2_config.php");
if(!empty($_POST['rok'])) {
    $sql = "SELECT nazov FROM predmet WHERE rok = '" . $_POST["rok"] . "' GROUP BY nazov";
    $result = $conn->query($sql);
    ?>
    <option value="">Vyber predmet</option>

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {   //Creates a loop to loop through results
            ?>

            <option value="<?php echo $row["nazov"]; ?>"><?php echo $row["nazov"]; ?></option>
            <?php
        }
    }
}

if(!empty($_POST['rokStudent'])) {
    $sql = "SELECT nazov FROM predmet 
    INNER JOIN zaznam ON zaznam.id_student = '".$_POST['idStudent']."'
    WHERE rok = '" . $_POST["rokStudent"] . "' AND predmet.id=zaznam.id_predmet";
    $result = $conn->query($sql);
    ?>
    <option value="">Vyber predmet</option>

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {   //Creates a loop to loop through results
            ?>

            <option value="<?php echo $row["nazov"]; ?>"><?php echo $row["nazov"]; ?></option>
            <?php
        }
    }
}
?>
