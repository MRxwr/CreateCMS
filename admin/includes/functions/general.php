<?php
// search for file name inside a folder \\
function searchFile($path, $fileName) {
	if ($handle = opendir($path)) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry == $fileName) {
				closedir($handle);
				return $entry;
			}
		}
		closedir($handle);
	}
	return false;
}
// general \\
function direction($valEn,$valAr){
	GLOBAL $_GET;
	$event = selectDB("events","`code` = '{$_GET["systemCode"]}'");
	$directionHTML = $event[0]["language"];
	if ( $directionHTML == "rtl" ){
		$response = $valAr;
	}else{
		$response = $valEn;
	}
	return $response;
}

// select a randon letter \\
function randLetter() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyz';
	$letter = $alphabet[rand(0, 25)];
	return $letter;
}

// get sgin for url \\
function getSign(){
	GLOBAL $_SERVER;
	if (strpos("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", '?') !== false) {
		return '&';
	} else {
		return '?';
	}
}

// get file extension \\
function getFileExtension($filePath) { 
    $dotPosition = strrpos($filePath, '.');
    $extension = ( $dotPosition === false ? '' : substr($filePath, $dotPosition + 1) );
    return $extension;
}

// change time zone \\
function timeZoneConverter($date){
	return date('Y-m-d H:i:s', strtotime($date) + 10800);
}

//add trailing zeros
function formatNumber($num) {
  return str_pad($num, 6, '0', STR_PAD_LEFT);
}

// convert numbers to 3 digits \\
function numTo3Float($data){
	$data = number_format((float)$data, 3);
	return $data;
}

// generating a random alphanumeric code of 8 characters \\
function generateRandomString() {
    $bytes = random_bytes(8);
    $hex   = bin2hex($bytes);
    return substr($hex, 0, 8);
}

// make sure that phone numbers are in english \\
function convertMobileNumber($phone){
	$arabic = ['١','٢','٣','٤','٥','٦','٧','٨','٩','٠'];
	$english = [ 1 ,  2 ,  3 ,  4 ,  5 ,  6 ,  7 ,  8 ,  9 , 0];
	$phone = str_replace($arabic, $english, $phone);
	return $phone;
}

// validating emal address \\
function validateEmail($email){
	GLOBAL $settingsEmail;
	if ( filter_var($email, FILTER_VALIDATE_EMAIL) === false ){
	  return $settingsEmail;
	}else{
	  return $email;
	}
}

// generating automatic orderId \\
function generateOrderId(){
	if($orders = selectDB("orders2", "`id` != '' ORDER BY `id` DESC")){
		$newOrderNumber = (int)$orders[0]["orderId"] + 1;
	}else{
		$newOrderNumber = 1;
	}
	return $newOrderNumber;
}

// showing the response in a json form \\
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

// resoring arrays of multiple dimensions \\
function array_sort($array, $on, $order){
    $new_array = array();
    $sortable_array = array();
    if (count($array) > 0){
        foreach ($array as $k => $v){
            if (is_array($v)){
                foreach ($v as $k2 => $v2){
                    if ($k2 == $on){
                        $sortable_array[$k] = $v2;
                    }
                }
            }else{
                $sortable_array[$k] = $v;
            }
        }
        switch($order){
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }
        foreach ($sortable_array as $k => $v){
            $new_array[$k] = $array[$k];
        }
    }
    return $new_array;
}


function str_lreplace($search, $replace, $subject){
    $pos = strrpos($subject, $search);
    if($pos !== false){
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject;
}

// Encryption function
function encryptData($plaintext, $key, $iv) {
    return base64_encode(openssl_encrypt($plaintext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv));
}

// Decryption function
function decryptData($ciphertext, $key, $iv) {
    return openssl_decrypt(base64_decode($ciphertext), 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
}

// Generate QR code for encrypted invitee data
function generateInviteeQR($inviteeCode) {
    // Use a fixed key and IV for consistency (in production, use more secure methods)
    $key = 'ceremony2024secretkeyforqrencryption12'; // 32 characters for AES-256
    $iv = 'ceremony2024iv16'; // 16 characters for CBC mode
    
    // Encrypt the invitee code
    $encryptedData = encryptData($inviteeCode, $key, $iv);
    
    // Generate QR code URL using Google Charts API (or you can use a local library)
    $qrData = urlencode($encryptedData);
    $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . $qrData;
    
    return array(
        'encrypted' => $encryptedData,
        'qr_url' => $qrCodeUrl
    );
}

function getTotals($table, $where){
	GLOBAL $dbconnect, $userId, $date;
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
	GLOBAL $dbconnect, $userId, $date;
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
	GLOBAL $dbconnect, $userId, $date;
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