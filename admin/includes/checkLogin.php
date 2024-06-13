<?php
if ( isset($_COOKIE["cmsCreate"]) && !empty($_COOKIE["cmsCreate"])){
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
	if ( isset($_POST["username"]) && !empty($_POST["username"]) && isset($_POST["password"]) && !empty($_POST["password"]) ){
		if( $user = selectDBNew("user",[$_POST['username'],sha1($_POST['password'])],"`username` LIKE ? AND `password` LIKE ? AND `status` = '0'","") ){
			setcookie('cmsCreate', md5(time().$_POST['username']), time() + (3600*24*30) , '/');
			updateDB("user",["hash"=>md5(time().$_POST['username'])],"`id` = {$user[0]["id"]}");
			$error = 0;
			header('LOCATION: index.php');die();
		}elseif( $user = selectDBNew("employee",[$_POST['username'],sha1($_POST['password'])],"`username` LIKE ? AND `password` LIKE ? AND `status` = '0'","") ){
			setcookie('cmsCreate', md5(time().$_POST['username']), time() + (3600*24*30) , '/');
			updateDB("employee",["hash"=>md5(time().$_POST['username'])],"`id` = {$user[0]["id"]}");
			$error = 0;
			header("LOCATION: index.php?page=details&action=employees&id={$user[0]["id"]}");die();
		}else{
			header('LOCATION: login.php?error=1');die();
		}
	}else{
		setcookie("cmsCreate", "", time() - 3600, '/');
		header('LOCATION: login.php?error=4');die();
	}
}
?>