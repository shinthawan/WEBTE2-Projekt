<?php
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'xjavor');
define('DB_PASSWORD', 'WEBTEphp');
define('DB_NAME', 'FinalProject2');

//define type of login
define("admin","uloha2_admin");
define("student","uloha2_student");

/* Attempt to connect to MySQL database */
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>