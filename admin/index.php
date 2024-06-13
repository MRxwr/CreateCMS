<?php 
// calling database
require_once('includes/config.php');

// loading functions
require_once('includes/functions.php');

// checking login
require_once('includes/checkLogin.php');
die("dead check login");

// header
require_once('templates/header.php');

// navbar
require_once('templates/topMenu.php');

// left menu
require_once('templates/leftMenu.php');

// right menu
require_once('templates/rightMenu.php');

// getting data
require_once('templates/mainContent.php');

// footer
require_once('templates/footer.php');
?>