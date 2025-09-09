<?php
// API Users Endpoint
header("Content-Type: application/json");

// Include helpers
require_once("helpers/apiAuth.php");

// Authenticate the request
$auth = apiAuthenticate();

// Check if authenticated
if (!$auth["authenticated"]) {
    echo outputError($auth["message"]);
    exit;
}

// Only admin users (type 0) can manage users
if ($auth["userType"] !== 0) {
    echo outputError("Access denied. Only administrators can manage users.");
    exit;
}

// Get the request method
$method = $_SERVER["REQUEST_METHOD"];

// Process based on method
switch ($method) {
    case "GET":
        // Get a list of users or a specific user
        if (isset($_GET["id"]) && !empty($_GET["id"])) {
            // Get specific user
            $userId = sanitizeInputData($_GET["id"]);
            $users = selectDB("user", "`id` = '{$userId}'");
            
            if (!$users) {
                echo outputError("User not found");
            } else {
                // Remove sensitive data
                unset($users[0]["password"]);
                unset($users[0]["hash"]);
                echo outputData($users[0]);
            }
        } else {
            // Get all users
            $users = selectDB("user", "");
            
            if (!$users) {
                echo outputData([]);
            } else {
                // Remove sensitive data from all users
                foreach ($users as &$user) {
                    unset($user["password"]);
                    unset($user["hash"]);
                }
                echo outputData($users);
            }
        }
        break;
        
    case "POST":
        // Create new user
        $data = getRequestData();
        
        // Validate required fields
        $missingFields = validateRequiredFields($data, ["username", "password", "email", "phone"]);
        if (!empty($missingFields)) {
            echo outputError("Missing required fields: " . implode(", ", $missingFields));
            exit;
        }
        
        // Check if username or email already exists
        if (selectDB("user", "`username` = '{$data["username"]}'")) {
            echo outputError("Username already exists");
            exit;
        }
        
        if (selectDB("user", "`email` = '{$data["email"]}'")) {
            echo outputError("Email already exists");
            exit;
        }
        
        // Prepare data for insertion
        $insertData = [
            "username" => $data["username"],
            "password" => sha1($data["password"]),
            "email" => $data["email"],
            "phone" => $data["phone"],
            "date" => date("Y-m-d H:i:s"),
            "status" => isset($data["status"]) ? $data["status"] : "0",
            "hash" => md5(time() . $data["username"])
        ];
        
        // Insert user
        if (insertDB("user", $insertData)) {
            $newUserId = $GLOBALS["dbconnect"]->insert_id;
            $newUser = selectDB("user", "`id` = '{$newUserId}'");
            
            // Remove sensitive data
            unset($newUser[0]["password"]);
            unset($newUser[0]["hash"]);
            
            echo outputData($newUser[0]);
        } else {
            echo outputError("Failed to create user");
        }
        break;
        
    case "PUT":
        // Update existing user
        $data = getRequestData();
        
        // Check if ID is provided
        if (!isset($data["id"]) || empty($data["id"])) {
            echo outputError("User ID is required");
            exit;
        }
        
        // Check if user exists
        $userId = $data["id"];
        $existingUser = selectDB("user", "`id` = '{$userId}'");
        
        if (!$existingUser) {
            echo outputError("User not found");
            exit;
        }
        
        // Prepare data for update
        $updateData = [];
        
        // Only include fields that are provided and allowed to be updated
        if (isset($data["email"])) $updateData["email"] = $data["email"];
        if (isset($data["phone"])) $updateData["phone"] = $data["phone"];
        if (isset($data["status"])) $updateData["status"] = $data["status"];
        
        // Handle password update separately
        if (isset($data["password"]) && !empty($data["password"])) {
            $updateData["password"] = sha1($data["password"]);
        }
        
        // Update user
        if (updateDB("user", $updateData, "`id` = '{$userId}'")) {
            $updatedUser = selectDB("user", "`id` = '{$userId}'");
            
            // Remove sensitive data
            unset($updatedUser[0]["password"]);
            unset($updatedUser[0]["hash"]);
            
            echo outputData($updatedUser[0]);
        } else {
            echo outputError("Failed to update user");
        }
        break;
        
    case "DELETE":
        // Delete or deactivate user
        $data = getRequestData();
        
        // Check if ID is provided
        if (!isset($_GET["id"]) || empty($_GET["id"])) {
            echo outputError("User ID is required");
            exit;
        }
        
        $userId = sanitizeInputData($_GET["id"]);
        
        // Check if user exists
        $existingUser = selectDB("user", "`id` = '{$userId}'");
        
        if (!$existingUser) {
            echo outputError("User not found");
            exit;
        }
        
        // We'll soft delete by setting status to 2
        if (updateDB("user", ["status" => "2"], "`id` = '{$userId}'")) {
            echo outputData(["message" => "User deleted successfully"]);
        } else {
            echo outputError("Failed to delete user");
        }
        break;
        
    default:
        echo outputError("Method not allowed");
        break;
}
?>
