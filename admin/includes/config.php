<?php
// Database Configuration
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'createcms';

// Create database connection
$dbconnect = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

// Check connection
if ($dbconnect->connect_error) {
    die("Connection failed: " . $dbconnect->connect_error);
}

// Set charset
$dbconnect->set_charset("utf8");

// Global variables
date_default_timezone_set('Asia/Kuwait');
$date = date('Y-m-d H:i:s');

// Session configuration
if (!session_id()) {
    session_start();
}

// Define base paths
define('BASE_PATH', realpath(dirname(__FILE__) . '/../../') . '/');
define('ADMIN_PATH', realpath(dirname(__FILE__) . '/../') . '/');
?> 
