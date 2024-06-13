<?php
//database connections
function deleteDB($table, $where){
    GLOBAL $dbconnect, $userId, $username, $_GET;
    $check = [';', '"'];
    $where = str_replace($check, "", $where);
    $sql = "DELETE FROM `" . $table . "` WHERE " . $where;
    if( isset($_GET["page"]) && !empty($_GET["page"]) ){
        $array = array(
            "userId" => $userId,
            "username" => $username,
            "module" => $_GET["page"],
            "action" => "Delete",
            "sqlQuery" => json_encode(array("table"=>$table,"where"=>$where)),
        );
        LogsHistory($array);
    }
    if ($stmt = $dbconnect->prepare($sql)) {
        if ($stmt->execute()) {
            return 1;
        } else {
            $error = array("msg" => "delete table error");
            return outputError($error);
        }
        $stmt->close();
    } else {
        $error = array("msg" => "prepare statement error");
        return outputError($error);
    }
}

function deleteDBNew($table, $params, $where){
    GLOBAL $dbconnect, $userId, $username, $_GET;
    $sql = "DELETE FROM `" . $table . "` WHERE " . $where;
    if (isset($_GET["page"]) && !empty($_GET["page"])) {
        $array = array(
            "userId" => $userId,
            "username" => $username,
            "module" => $_GET["page"],
            "action" => "Delete",
            "sqlQuery" => json_encode(array("table" => $table, "where" => $where)),
        );
        LogsHistory($array);
    }
    if ($stmt = $dbconnect->prepare($sql)) {
        $types = str_repeat('s', count($params)); // Assuming all parameters are strings. Adjust if needed.
        $stmt->bind_param($types, ...$params);
        if ($stmt->execute()) {
            $stmt->close();
            return 1;
        } else {
            $stmt->close();
            $error = array("msg" => "delete table error");
            return outputError($error);
        }
    } else {
        $error = array("msg" => "prepare statement error");
        return outputError($error);
    }
}


function selectDBNew($table, $placeHolders, $where, $order){
    GLOBAL $dbconnect;
    $check = [';', '"'];
    $where = str_replace($check, "", $where);
    $sql = "SELECT * FROM `{$table}`";
    if(!empty($where)) {
        $sql .= " WHERE {$where}";
    }
    if(!empty($order)) {
        $sql .= " ORDER BY {$order}";
    }
    if( $table == "employee" && strstr($where,"email") ){
        $array = array(
            "userId" => 0,
            "username" => 0,
            "module" => "Login",
            "action" => "Select",
            "sqlQuery" => json_encode(array("table"=>$table,"data"=>$placeHolders,"where"=>$where)),
        );
        LogsHistory($array);
    }
    if($stmt = $dbconnect->prepare($sql)) {
        $types = str_repeat('s', count($placeHolders));
        $stmt->bind_param($types, ...$placeHolders);
        $stmt->execute();
        $result = $stmt->get_result();
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $array[] = $row;
        }
        if(isset($array) && is_array($array)) {
            return $array;
        }else{
            return 0;
        }
    }else{
        return 0;
    }
}

function selectDB($table, $where){
    GLOBAL $dbconnect;
    $check = [';', '"'];
    $where = str_replace($check, "", $where);
    $sql = "SELECT * FROM `{$table}`";
    if (!empty($where)) {
        $sql .= " WHERE {$where}";
    }
    if ($stmt = $dbconnect->prepare($sql)) {
        $stmt->execute();
        $result = $stmt->get_result();
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $array[] = $row;
        }
        if (isset($array) && is_array($array)) {
            return $array;
        } else {
            return 0;
        }
    } else {
        $error = array("msg" => "select table error");
        return outputError($error);
    }
}

function selectDB2($select, $table, $where){
    GLOBAL $dbconnect;
    $check = [';', '"'];
    $where = str_replace($check, "", $where);
    $sql = "SELECT {$select} FROM `{$table}`";
    if (!empty($where)) {
        $sql .= " WHERE {$where}";
    }
    if ($stmt = $dbconnect->prepare($sql)) {
        $stmt->execute();
        $result = $stmt->get_result();
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $array[] = $row;
        }
        if (isset($array) && is_array($array)) {
            return $array;
        } else {
            return 0;
        }
    } else {
        $error = array("msg" => "select table error");
        return outputError($error);
    }
}

function selectJoinDB($table, $joinData, $where){
    global $dbconnect;
    global $date;
    $check = [';', '"'];
    $where = str_replace($check,"",$where);
    $sql = "SELECT ";
    for($i = 0 ; $i < sizeof($joinData["select"]) ; $i++ ){
        $sql .= $joinData["select"][$i];
        if ( $i+1 != sizeof($joinData["select"]) ){
            $sql .= ", ";
        }
    }
    $sql .=" FROM `$table` as t ";
    for($i = 0 ; $i < sizeof($joinData["join"]) ; $i++ ){
        $counter = $i+1;
        $sql .= " JOIN `".$joinData["join"][$i]."` as t{$counter} ";
        if( isset($joinData["on"][$i]) && !empty($joinData["on"][$i]) ){
            $sql .= " ON ".$joinData["on"][$i]." ";
        }
    }
    if ( !empty($where) ){
        $sql .= " WHERE " . $where;
    }
    if($stmt = $dbconnect->prepare($sql)){
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc() ){
            $array[] = $row;
        }
        if ( isset($array) AND is_array($array) ){
            return $array;
        }else{
            return 0;
        }
    }else{
        $error = array("msg"=>"select table error");
        return outputError($error);
    }
}

function insertDB($table, $data){
    GLOBAL $dbconnect, $userId, $username, $_GET;
    $check = [';', '"'];
    //$data = escapeString($data);
    $keys = array_keys($data);
    $sql = "INSERT INTO `{$table}`(";
    $placeholders = "";
    foreach ($keys as $key) {
        $sql .= "`{$key}`,";
        $placeholders .= "?,";
    }
    $sql = rtrim($sql, ",");
    $placeholders = rtrim($placeholders, ",");
    $sql .= ") VALUES ({$placeholders})";
    $stmt = $dbconnect->prepare($sql);
    $types = str_repeat('s', count($data));
    $stmt->bind_param($types, ...array_values($data));
    if( isset($_GET["page"]) && !empty($_GET["page"]) ){
        $array = array(
            "userId" => $userId,
            "username" => $username,
            "module" => $_GET["page"],
            "action" => "Insert",
            "sqlQuery" => json_encode(array("table"=>$table,"data"=>$data)),
        );
        LogsHistory($array);
    }
   
    if($stmt->execute()){
        return 1;
    }else{
        $error = array("msg"=>"insert table error");
        return outputError($error);
    }
}

function updateDB($table, $data, $where) {
    GLOBAL $dbconnect, $userId, $username, $_GET;
    $check = [';', '"'];
    //$data = escapeString($data);
    $where = str_replace($check, "", $where);
    $keys = array_keys($data);
    $sql = "UPDATE `" . $table . "` SET ";
    $params = "";
    for ($i = 0; $i < sizeof($data); $i++) {
        $sql .= "`" . $keys[$i] . "` = ?";
        if (isset($keys[$i + 1])) {
            $sql .= ", ";
        }
        $params .= "s";
    }
    $sql .= " WHERE " . $where;
    $stmt = $dbconnect->prepare($sql); 
    $values = array_values($data);
    $stmt->bind_param($params, ...$values);
    
    if( isset($_GET["page"]) && !empty($_GET["page"]) ){
        $array = array(
            "userId" => $userId,
            "username" => $username,
            "module" => $_GET["page"],
            "action" => "update",
            "sqlQuery" => json_encode(array("table"=>$table,"data"=>$data,"where"=>$where)),
        );
        LogsHistory($array);
    }
    

    if ($stmt->execute()) {
        return 1;
    } else {
        $error = array("msg" => "update table error");
        return outputError($error);
    }
}

function outputData($data){
	$response["ok"] = true;
	$response["error"] = "0";
	$response["status"] = "successful";
	$response["data"] = $data;
	return json_encode($response);
}

// showing erros in json form \\
function outputError($data){
	$response["ok"] = false;
	$response["error"] = "1";
	$response["status"] = "Error";
	$response["data"] = $data;
	return json_encode($response);
}

function escapeString($data){
	GLOBAL $dbconnect;
	$keys = array_keys($data);
	for($i = 0 ; $i < sizeof($keys) ; $i++ ){
		$output[$keys[$i]] = mysqli_real_escape_string($dbconnect,$data[$keys[$i]]);
	}
	return $output;
}

function escapeStringDirect($data){
	GLOBAL $dbconnect;
	$output = mysqli_real_escape_string($dbconnect,$data);
	return $output;
}

function insertLogDB($table,$data){
    GLOBAL $dbconnect;
    $check = [';', '"'];
    //$data = escapeString($data);
    $keys = array_keys($data);
    $sql = "INSERT INTO `{$table}`(";
    $placeholders = "";
    foreach ($keys as $key) {
        $sql .= "`{$key}`,";
        $placeholders .= "?,";
    }
    $sql = rtrim($sql, ",");
    $placeholders = rtrim($placeholders, ",");
    $sql .= ") VALUES ({$placeholders})";
    $stmt = $dbconnect->prepare($sql);
    $types = str_repeat('s', count($data));
    $stmt->bind_param($types, ...array_values($data));
    if($stmt->execute()){
        return 1;
    }else{
        $error = array("msg"=>"insert table error");
        return outputError($error);
    }
}

function LogsHistory($array){
    insertLogDB("logs",$array);
}

function queryDB($sql){
    GLOBAL $dbconnect;
    if ($stmt = $dbconnect->prepare($sql)) {
        $stmt->execute();
        $result = $stmt->get_result();
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $array[] = $row;
        }
        if (isset($array) && is_array($array)) {
            return $array;
        } else {
            return 0;
        }
    } else {
        $error = array("msg" => "select table error");
        return outputError($error);
    }
}

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