<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once "../helpers/apiAuth.php";
require_once "../../admin/includes/functions.php";
require_once "../../admin/includes/functions/notification.php";

// Only handle POST method for registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Parse the input data
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Validate required fields
    if (!isset($data['username']) || !isset($data['password']) || !isset($data['email']) || !isset($data['phone'])) {
        outputError("Required fields missing. Please provide username, password, email, and phone.");
        exit;
    }
    
    // Validate username format (alphanumeric, 3-20 chars)
    if (!preg_match('/^[a-zA-Z0-9]{3,20}$/', $data['username'])) {
        outputError("Username must be 3-20 characters and contain only letters and numbers.");
        exit;
    }
    
    // Validate email format
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        outputError("Invalid email format.");
        exit;
    }
    
    // Validate phone format (simple validation, can be enhanced)
    if (!preg_match('/^\+?[0-9]{8,15}$/', $data['phone'])) {
        outputError("Invalid phone number format. Please use 8-15 digits with optional + prefix.");
        exit;
    }
    
    // Check if username already exists
    $checkUser = selectDB("users", "`username` = '{$data['username']}'");
    if (count($checkUser) > 0) {
        outputError("Username already exists. Please choose a different username.");
        exit;
    }
    
    // Check if email already exists
    $checkEmail = selectDB("users", "`email` = '{$data['email']}'");
    if (count($checkEmail) > 0) {
        outputError("Email already registered. Please use a different email address.");
        exit;
    }
    
    // Generate a unique token for the user
    $token = md5(uniqid() . time() . $data['username']);
    
    // Hash the password
    $hashedPassword = md5($data['password']);
    
    // Prepare user data for insertion
    $userData = array(
        "username" => $data['username'],
        "password" => $hashedPassword,
        "email" => $data['email'],
        "phone" => $data['phone'],
        "token" => $token,
        "status" => "0", // Default to pending approval
        "date" => date("Y-m-d H:i:s"),
        "type" => "1"    // Register as regular user/employee (type 1)
    );
    
    // Insert the new user into the database
    $insert = insertDB("users", $userData);
    
    // If insertion successful
    if ($insert) {
        // Notify admin about new user registration (optional)
        // Get admin users
        $admins = selectDB("users", "`type` = '0'");
        
        if (count($admins) > 0) {
            foreach ($admins as $admin) {
                if (!empty($admin['phone'])) {
                    $message = "New user registration: {$data['username']} ({$data['email']})";
                    whatsappUltraMsg($admin['phone'], $message);
                }
            }
        }
        
        // Return success response with user data (excluding password)
        unset($userData['password']);
        outputData($userData);
    } else {
        outputError("Registration failed. Please try again later.");
    }
} else {
    outputError("Only POST method is allowed for registration.");
}
?>
