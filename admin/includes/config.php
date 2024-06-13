<?php
// db data connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cms";

// Create connection
$dbconnect = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$dbconnect) {
  die("Connection failed: " . mysqli_connect_error());
}
?>