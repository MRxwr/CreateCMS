<?php
if ( !isset($_GET["page"]) ){
	require_once('pages/home.php');
}elseif( $_GET["page"] == "home" ){
	require_once('pages/home.php');
}elseif( $_GET["page"] == "leads" ){
	require_once('pages/leads.php');
}elseif( $_GET["page"] == "customers" ){
	require_once('pages/customers.php');
}elseif( $_GET["page"] == "users" ){
	require_once('pages/users.php');
}elseif( $_GET["page"] == "settings" ){
	require_once('pages/settings.php');
}elseif( $_GET["page"] == "details" ){
	require_once('pages/details.php');
}elseif( $_GET["page"] == "employees" ){
	require_once('pages/employees.php');
}elseif( $_GET["page"] == "comments" ){
	require_once('pages/comments.php');
}else{
	require_once('pages/home.php');
}
?>