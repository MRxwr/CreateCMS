<?php 
require_once('templates/header.php');

if( isset($_GET["p"]) && searchFile("pages","blade{$_GET["p"]}.php") ){
	require_once("pages/".searchFile("pages","blade{$_GET["p"]}.php"));
}else{
	require_once("pages/bladeHome.php");
}

require_once('templates/footer.php');
?>