<?php
// Authentication check for frontend users
if (!session_id()) {
    session_start();
}

function checkAuthentication() {
    global $dbconnect;
    
    // Check if user is logged in via session
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_type'])) {
        return true;
    }
    
    // Check if user has remember me cookie
    if (isset($_COOKIE['cmsUser'])) {
        $hash = $_COOKIE['cmsUser'];
        
        // Check in user table
        $user = selectDB("user", "hash = '$hash' AND status = 0");
        if ($user && is_array($user) && count($user) > 0) {
            $_SESSION['user_id'] = $user[0]['id'];
            $_SESSION['user_type'] = 0;
            $_SESSION['username'] = $user[0]['username'];
            $_SESSION['user_name'] = $user[0]['name'];
            return true;
        }
        
        // Check in employee table
        $employee = selectDB("employee", "hash = '$hash' AND status = 0");
        if ($employee && is_array($employee) && count($employee) > 0) {
            $_SESSION['user_id'] = $employee[0]['id'];
            $_SESSION['user_type'] = 1;
            $_SESSION['username'] = $employee[0]['username'];
            $_SESSION['user_name'] = $employee[0]['name'];
            return true;
        }
        
        // Invalid cookie, clear it
        setcookie('cmsUser', '', time() - 3600, '/');
        
        // Clear session if cookie was invalid
        session_unset();
        session_destroy();
    }
    
    return false;
}

function requireAuth() {
    if (!checkAuthentication()) {
        header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

function getCurrentUser() {
    if (checkAuthentication()) {
        return [
            'id' => $_SESSION['user_id'],
            'type' => $_SESSION['user_type'], // 0 = user, 1 = employee
            'username' => $_SESSION['username'],
            'name' => $_SESSION['user_name']
        ];
    }
    return null;
}

// Set global variables for compatibility with existing code
if (checkAuthentication()) {
    $userId = $_SESSION['user_id'];
    $userType = $_SESSION['user_type'];
    $username = $_SESSION['username'];
    $currentUser = getCurrentUser();
} else {
    $userId = null;
    $userType = null;
    $username = null;
    $currentUser = null;
}
?>
