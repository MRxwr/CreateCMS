<?php
if ( !$_COOKIE["cmsCreate"] || ( isset( $_GET["page"] ) && $_GET["page"] == "logout" ) ){
	setcookie("cmsCreate", "", time() - 3600, '/');
	header('LOCATION: login.php');
}else{
	if( $user = selectDBNew("user",[$_COOKIE["cmsCreate"]],"`hash` LIKE ? AND `status` = '0'","") ){
		$userId = $user[0]["id"];
		$username = $user[0]["username"];
		date_default_timezone_set('Asia/Kuwait');
		$date = date('Y-m-d H:i:s');
		$userType = 0;
	}elseif( $user = selectDBNew("employee",[$_COOKIE["cmsCreate"]],"`hash` LIKE ? AND `status` = '0'","") ){
		$userId = $user[0]["id"];
		$username = $user[0]["username"];
		date_default_timezone_set('Asia/Kuwait');
		$date = date('Y-m-d H:i:s');
		$userType = 1;
	}else{
		setcookie("cmsCreate", "", time() - 3600, '/');
		header('LOCATION: login.php');
	}
}
?>