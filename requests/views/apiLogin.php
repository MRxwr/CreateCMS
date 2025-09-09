<?php
// API Login Endpoint
header("Content-Type: application/json");

// Include helpers
require_once("helpers/apiAuth.php");

// Process only POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo outputError("Method not allowed. Use POST.");
    exit;
}

// Get the request data
$data = getRequestData();

// Validate required fields
$missingFields = validateRequiredFields($data, ["username", "password"]);
if (!empty($missingFields)) {
    echo outputError("Missing required fields: " . implode(", ", $missingFields));
    exit;
}

// Sanitize input
$username = sanitizeInputData($data["username"]);
$password = sanitizeInputData($data["password"]);

// Try to authenticate as user
if ($user = selectDBNew("user", [$username, sha1($password)], "`username` LIKE ? AND `password` LIKE ? AND `status` = '0'", "")) {
    // Generate new token
    $token = md5(time() . $username . rand(1000, 9999));
    
    // Update user with new token
    updateDB("user", ["hash" => $token], "`id` = {$user[0]["id"]}");
    
    // Return success response with token
    echo outputData([
        "token" => $token,
        "userId" => $user[0]["id"],
        "username" => $user[0]["username"],
        "userType" => 0, // Regular user
        "phone" => $user[0]["phone"]
    ]);
} 
// Try to authenticate as employee
elseif ($user = selectDBNew("employee", [$username, sha1($password)], "`username` LIKE ? AND `password` LIKE ? AND `status` = '0'", "")) {
    // Generate new token
    $token = md5(time() . $username . rand(1000, 9999));
    
    // Update employee with new token
    updateDB("employee", ["hash" => $token], "`id` = {$user[0]["id"]}");
    
    // Return success response with token
    echo outputData([
        "token" => $token,
        "userId" => $user[0]["id"],
        "username" => $user[0]["username"],
        "userType" => 1, // Employee
        "phone" => $user[0]["phone"]
    ]);
} 
// Authentication failed
else {
    echo outputError("Invalid username or password");
}
?>
