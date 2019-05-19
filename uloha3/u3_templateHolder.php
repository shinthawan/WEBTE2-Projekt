<?php
require_once "u3_config.php";

$con=getDBConnection();
$sql = "select sablona from uloha3_sablony where nazov='".$_POST["templateName"]."'";
$result=$con->query($sql);
$result=$result->fetch_assoc();
echo $result["sablona"];
