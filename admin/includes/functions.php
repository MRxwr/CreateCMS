<?php
function getTotals($table, $where){
	GLOBAL $dbconnect;
	GLOBAL $userId;
	GLOBAL $date;
	$check = [';','"',"'"];
	$where = str_replace($check,"",$where);
	$sql = "SELECT * FROM `".$table."` WHERE " . $where;
	if($result = $dbconnect->query($sql)){
		return $result->num_rows;
	}else{
		return 0;
	}
}

function insertDB($table, $data){
	GLOBAL $dbconnect;
	GLOBAL $userId;
	GLOBAL $date;
	$check = [';','"',"'"];
	$data = str_replace($check,"",$data);
	$keys = array_keys($data);
	$sql = "INSERT INTO `".$table."`(";
	for($i = 0 ; $i < sizeof($keys) ; $i++ ){
		$sql .= "`".$keys[$i]."`";
		if ( isset($keys[$i+1]) ){
			$sql .= ", ";
		}
	}
	$sql .= ")VALUES(";
	for($i = 0 ; $i < sizeof($data) ; $i++ ){
		$sql .= "'".$data[$keys[$i]]."'";
		if ( isset($keys[$i+1]) ){
			$sql .= ", ";
		}
	}		
	$sql .= ")";
	if($dbconnect->query($sql)){
		$array = array('log'=> str_replace($check,"",$sql));
		$sql1 = "INSERT INTO `logs`
				(`date`,`userId`,`log`)
				VALUES
				('".$date."','".$userId."','".json_encode($array['log'])."')
				";
		if($dbconnect->query($sql1)){
			return 1;
		}else{
			return 0;
		}
	}else{
		return 0;
	}
}

function updateDB($table ,$data, $where){
	GLOBAL $dbconnect;
	GLOBAL $userId;
	GLOBAL $date;
	$check = [';','"',"'"];
	$data = str_replace($check,"",$data);
	$where = str_replace($check,"",$where);
	$keys = array_keys($data);
	$sql = "UPDATE `".$table."` SET ";
	for($i = 0 ; $i < sizeof($data) ; $i++ ){
		$sql .= "`".$keys[$i]."` = '".$data[$keys[$i]]."'";
		if ( isset($keys[$i+1]) ){
			$sql .= ", ";
		}
	}		
	$sql .= " WHERE " . $where;
	if($dbconnect->query($sql)){
		$array = array('log'=> str_replace($check,"",$sql));
		$sql1 = "INSERT INTO `logs`
				(`date`,`userId`,`log`)
				VALUES
				('".$date."','".$userId."','".json_encode($array['log'])."')
				";
		if($dbconnect->query($sql1)){
			return 1;
		}else{
			return 0;
		}
	}else{
		return 0;
	}
}

function notifyMe($id,$msg){
	GLOBAL $dbconnect;
	GLOBAL $userId;
	GLOBAL $date;
	$check = [';','"',"'"];
	$msg = str_replace($check,"",$msg);
	$id = str_replace($check,"",$id);
	
	$sql = "SELECT * FROM `task` WHERE `id` LIKE '".$id."'";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$to = $row["by"];
	$sql1 = "INSERT INTO `notifications`
			(`date`,`from`,`to`,`msg`)
			VALUES
			('".$date."','".$userId."','".$to."','".$msg."')
			";
	$dbconnect->query($sql1);
}

function selectTask($id,$status){
	GLOBAL $dbconnect;
	GLOBAL $userId;
	GLOBAL $date;
	$check = [';','"',"'"];
	$id = str_replace($check,"",$id);
	$sql1 = "SELECT t.*, p.title, e.name
			FROM `task` as t
			JOIN `project` as p
			ON p.id = t.projectId
			JOIN `employee` as e
			ON e.id = t.to
			WHERE t.id LIKE '".$id."'
			";
	if($result = $dbconnect->query($sql1)){
		$row = $result->fetch_assoc();
		$msg = "Task status changed to " . $status . " By " . $row["name"] . " of " . $row["title"] . " project.";
		return $msg;
	}else{
		return 0;
	}
	
}


?>