<?php 
require_once('admin/includes/config.php');
require_once('admin/includes/functions.php');
require_once('includes/auth.php');

// Require authentication for all pages
requireAuth();

// Get current user info
$currentUser = getCurrentUser();

// Define allowed pages for employees
$employeeAllowedPages = ['Home', 'Tasks', 'ChatTask'];

// Check if employee is trying to access restricted pages
if ($currentUser['type'] == 1) { // Employee
    if (isset($_GET['v']) && !in_array($_GET['v'], $employeeAllowedPages)) {
        // Redirect employees to Tasks page if they try to access restricted areas
        header("Location: ?v=Tasks");
        exit;
    }
}

// Handle chat form submission
if (isset($_POST["taskId"]) && isset($_POST["send-msg"])) {
    $table = "comments";
    
    // Clean the data
    $commentData = [
        'date' => date('Y-m-d H:i:s'),
        'userId' => (int)$_POST["userId"],
        'empId' => (int)$_POST["empId"],
        'taskId' => (int)$_POST["taskId"],
        'send-msg' => trim($_POST["send-msg"]),
        'type' => (int)$_POST["type"],
        'status' => 1
    ];
    
    $result = insertDB($table, $commentData);
    
    if ($result) {
        // Redirect to avoid resubmission on refresh
        header("Location: ?v=ChatTask&task=".$_POST["taskId"]);
        exit;
    } else {
        $error_message = "Failed to send message. Please try again.";
    }
}

require_once('templates/header.php');

// Show error message if any
if (isset($error_message)) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            '.$error_message.'
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
}

// Show welcome message for new users
if (isset($_GET['welcome']) && $_GET['welcome'] == '1') {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i>
            <strong>Welcome!</strong> Your account has been created successfully. Enjoy using our platform!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
}

if( isset($_GET["v"]) && searchFile("views","blade{$_GET["v"]}.php") ){
	require_once("views/".searchFile("views","blade{$_GET["v"]}.php"));
}else{
	require_once("views/bladeHome.php");
}

require_once('templates/footer.php');
?>