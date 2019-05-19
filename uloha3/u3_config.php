<?php




function getDBConnection(){
    $servername="localhost";
    $username="xblaziceka";
    $password="KonPrazenicaNoha";
    $port=4429;
    $db="FinalProject";
    $conn = new mysqli($servername,$username,$password,$db,$port);
    $conn->set_charset("utf8");
    if($conn->connect_error){
        die("connection failed : " .$conn->connect_errno);
    }
    return $conn;

}