<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Clear the cookie
if (isset($_COOKIE['cmsUser'])) {
    setcookie('cmsUser', '', time() - 3600, '/');
}

// Redirect to login page
header('Location: login.php?logged_out=1');
exit;
?>
