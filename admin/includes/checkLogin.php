<?php
if ( !$_COOKIE["cmsCreate"] || ( isset( $_GET["page"] ) && $_GET["page"] == "logout" ) ){
	setcookie("cmsCreate", "", time() - 3600, '/');
	header('LOCATION: login.php');
}else{
	$sql = "SELECT *
			FROM `user`
			WHERE 
			`hash` LIKE '".$_COOKIE["cmsCreate"]."'
			";
	$result = $dbconnect->query($sql);
	$userType = 0;
	if ( $result->num_rows < 1 ){
		$sql = "SELECT *
				FROM `employee`
				WHERE 
				`hash` LIKE '".$_COOKIE["cmsCreate"]."'
				";
		$result = $dbconnect->query($sql);
		$userType = 1;
	}
	$row = $result->fetch_assoc();
	$userId = $row["id"];
	$username = $row["username"];
	date_default_timezone_set('Asia/Kuwait');
	$date = date('Y-m-d H:i:s');
}
?>