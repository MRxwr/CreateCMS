<?php
session_start();
require_once('admin/includes/config.php');
require_once('admin/includes/functions.php');
require_once('includes/auth.php');

header('Content-Type: application/json');

$debug_info = [
    'session_id' => session_id(),
    'session_data' => $_SESSION,
    'cookies' => $_COOKIE,
    'checkAuthentication_result' => checkAuthentication(),
    'getCurrentUser_result' => getCurrentUser(),
    'timestamp' => date('Y-m-d H:i:s')
];

echo json_encode($debug_info, JSON_PRETTY_PRINT);
?>
