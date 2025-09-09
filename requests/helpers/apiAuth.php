<?php
// API Authentication Helper
// This file handles API authentication using bearer tokens

function apiAuthenticate(){
    // Get authorization header
    $headers = getallheaders();
    $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';
    
    // Check if bearer token is provided in headers
    if(empty($authHeader) || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        return ["authenticated" => false, "userId" => null, "userType" => null, "message" => "No bearer token provided"];
    }
    
    // Extract the token
    $token = $matches[1];
    
    // Check if the token belongs to a user
    if($user = selectDBNew("user", [$token], "`hash` LIKE ? AND `status` = '0'", "")){
        return [
            "authenticated" => true,
            "userId" => $user[0]["id"],
            "username" => $user[0]["username"],
            "userType" => 0, // Regular user
            "userPhone" => $user[0]["phone"]
        ];
    }
    // Check if the token belongs to an employee
    elseif($user = selectDBNew("employee", [$token], "`hash` LIKE ? AND `status` = '0'", "")){
        return [
            "authenticated" => true,
            "userId" => $user[0]["id"],
            "username" => $user[0]["username"],
            "userType" => 1, // Employee
            "userPhone" => $user[0]["phone"]
        ];
    }
    else {
        return ["authenticated" => false, "userId" => null, "userType" => null, "message" => "Invalid bearer token"];
    }
}

// Function to check if the user has the right permissions
function checkPermission($auth, $requiredType = null){
    if(!$auth["authenticated"]){
        return false;
    }
    
    // If no specific type required, just check if authenticated
    if($requiredType === null){
        return true;
    }
    
    // Check if user type matches required type
    return $auth["userType"] === $requiredType;
}

// Function to get request data (works for GET, POST, PUT, DELETE methods)
function getRequestData(){
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch($method){
        case 'GET':
            return $_GET;
        case 'POST':
            return $_POST;
        case 'PUT':
        case 'DELETE':
            // For PUT and DELETE methods, we need to get data from php://input
            $data = [];
            parse_str(file_get_contents('php://input'), $data);
            return $data;
        default:
            return [];
    }
}

// Function to validate required fields
function validateRequiredFields($data, $requiredFields){
    $missingFields = [];
    
    foreach($requiredFields as $field){
        if(!isset($data[$field]) || empty($data[$field])){
            $missingFields[] = $field;
        }
    }
    
    return $missingFields;
}

// Function to sanitize input data
function sanitizeInputData($data){
    if(is_array($data)){
        foreach($data as $key => $value){
            $data[$key] = sanitizeInputData($value);
        }
    } else {
        $data = htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    return $data;
}
