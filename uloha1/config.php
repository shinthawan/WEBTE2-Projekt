<?php
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'xblaziceka');
define('DB_PASSWORD', 'KonPrazenicaNoha');
define('DB_NAME', 'FinalProject');


/* Attempt to connect to MySQL database */

$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
$conn->set_charset("utf8");
// Check connection

if($mysqli === false){
    die("ERROR: Could not connect. " . $mysqli->connect_error);
}

?>