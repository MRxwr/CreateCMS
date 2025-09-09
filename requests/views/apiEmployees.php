<?php
// API Employees Endpoint
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

// Only admin users (type 0) can manage employees
if ($auth["userType"] !== 0 && $_SERVER["REQUEST_METHOD"] != "GET") {
    echo outputError("Access denied. Only administrators can manage employees.");
    exit;
}

// Get the request method
$method = $_SERVER["REQUEST_METHOD"];

// Process based on method
switch ($method) {
    case "GET":
        // Get a list of employees or a specific employee
        if (isset($_GET["id"]) && !empty($_GET["id"])) {
            // Get specific employee
            $employeeId = sanitizeInputData($_GET["id"]);
            $employees = selectDB("employee", "`id` = '{$employeeId}'");
            
            if (!$employees) {
                echo outputError("Employee not found");
            } else {
                // Remove sensitive data
                unset($employees[0]["password"]);
                unset($employees[0]["hash"]);
                echo outputData($employees[0]);
            }
        } else {
            // Get all employees
            $employees = selectDB("employee", "`status` = '0'");
            
            if (!$employees) {
                echo outputData([]);
            } else {
                // Remove sensitive data from all employees
                foreach ($employees as &$employee) {
                    unset($employee["password"]);
                    unset($employee["hash"]);
                }
                echo outputData($employees);
            }
        }
        break;
        
    case "POST":
        // Create new employee
        $data = getRequestData();
        
        // Validate required fields
        $missingFields = validateRequiredFields($data, ["username", "password", "email", "phone", "name"]);
        if (!empty($missingFields)) {
            echo outputError("Missing required fields: " . implode(", ", $missingFields));
            exit;
        }
        
        // Check if username or email already exists
        if (selectDB("employee", "`username` = '{$data["username"]}'")) {
            echo outputError("Username already exists");
            exit;
        }
        
        if (selectDB("employee", "`email` = '{$data["email"]}'")) {
            echo outputError("Email already exists");
            exit;
        }
        
        // Prepare data for insertion
        $insertData = [
            "username" => $data["username"],
            "password" => sha1($data["password"]),
            "email" => $data["email"],
            "phone" => $data["phone"],
            "name" => $data["name"],
            "date" => date("Y-m-d H:i:s"),
            "status" => isset($data["status"]) ? $data["status"] : "0",
            "hash" => md5(time() . $data["username"])
        ];
        
        // Insert employee
        if (insertDB("employee", $insertData)) {
            $newEmployeeId = $GLOBALS["dbconnect"]->insert_id;
            $newEmployee = selectDB("employee", "`id` = '{$newEmployeeId}'");
            
            // Remove sensitive data
            unset($newEmployee[0]["password"]);
            unset($newEmployee[0]["hash"]);
            
            echo outputData($newEmployee[0]);
        } else {
            echo outputError("Failed to create employee");
        }
        break;
        
    case "PUT":
        // Update existing employee
        $data = getRequestData();
        
        // Check if ID is provided
        if (!isset($data["id"]) || empty($data["id"])) {
            echo outputError("Employee ID is required");
            exit;
        }
        
        // Check if employee exists
        $employeeId = $data["id"];
        $existingEmployee = selectDB("employee", "`id` = '{$employeeId}'");
        
        if (!$existingEmployee) {
            echo outputError("Employee not found");
            exit;
        }
        
        // Prepare data for update
        $updateData = [];
        
        // Only include fields that are provided and allowed to be updated
        if (isset($data["email"])) $updateData["email"] = $data["email"];
        if (isset($data["phone"])) $updateData["phone"] = $data["phone"];
        if (isset($data["name"])) $updateData["name"] = $data["name"];
        if (isset($data["status"])) $updateData["status"] = $data["status"];
        
        // Handle password update separately
        if (isset($data["password"]) && !empty($data["password"])) {
            $updateData["password"] = sha1($data["password"]);
        }
        
        // Update employee
        if (updateDB("employee", $updateData, "`id` = '{$employeeId}'")) {
            $updatedEmployee = selectDB("employee", "`id` = '{$employeeId}'");
            
            // Remove sensitive data
            unset($updatedEmployee[0]["password"]);
            unset($updatedEmployee[0]["hash"]);
            
            echo outputData($updatedEmployee[0]);
        } else {
            echo outputError("Failed to update employee");
        }
        break;
        
    case "DELETE":
        // Delete or deactivate employee
        $data = getRequestData();
        
        // Check if ID is provided
        if (!isset($_GET["id"]) || empty($_GET["id"])) {
            echo outputError("Employee ID is required");
            exit;
        }
        
        $employeeId = sanitizeInputData($_GET["id"]);
        
        // Check if employee exists
        $existingEmployee = selectDB("employee", "`id` = '{$employeeId}'");
        
        if (!$existingEmployee) {
            echo outputError("Employee not found");
            exit;
        }
        
        // We'll soft delete by setting status to 2
        if (updateDB("employee", ["status" => "2"], "`id` = '{$employeeId}'")) {
            echo outputData(["message" => "Employee deleted successfully"]);
        } else {
            echo outputError("Failed to delete employee");
        }
        break;
        
    default:
        echo outputError("Method not allowed");
        break;
}
?>
