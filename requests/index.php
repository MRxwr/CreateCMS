<?php 
require_once('../admin/includes/config.php');
require_once('../admin/includes/functions.php');

if( isset($_GET["endpoint"]) && searchFile("views","api{$_GET["endpoint"]}.php") ){
	require_once("views/".searchFile("views","api{$_GET["endpoint"]}.php"));
}else{
	echo outputError("msg","Invalid API Endpoint");
}
?>