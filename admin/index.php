<?php 
// calling database
require_once('includes/config.php');

// checking login
require_once('includes/checkLogin.php');

// loading functions
require_once('includes/functions.php');

// header
require_once('templates/header.php');
die();
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