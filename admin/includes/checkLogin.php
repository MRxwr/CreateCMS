<?php
if ( isset($_COOKIE["cmsCreate"]) && !empty($_COOKIE["cmsCreate"]) && !isset($_GET["page"]) || ( isset($_GET["page"]) && $_GET["page"] !== "logout") ){
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
		header('LOCATION: login.php?error=3');die();
	}
}elseif( isset($_GET["page"]) && $_GET["page"] == "logout" ){
	setcookie("cmsCreate", "", time() - 3600, '/');
	header('LOCATION: login.php?error=2');die();
}else{
	setcookie("cmsCreate", "", time() - 3600, '/');
	header('LOCATION: login.php?error=3');die();
}
?>