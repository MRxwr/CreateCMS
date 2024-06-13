<?php
// db data connection
$servername = "localhost";
$username = "u409066344_createcmsUSER";
$password = "N@b$90949089";
$dbname = "u409066344_createcmsDB";

// Create connection
$dbconnect = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$dbconnect) {
  die("Connection failed: " . mysqli_connect_error());
}
?>