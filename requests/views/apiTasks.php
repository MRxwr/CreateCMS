<?php
// API Tasks Endpoint
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

// Get the request method
$method = $_SERVER["REQUEST_METHOD"];

// Process based on method
switch ($method) {
    case "GET":
        // Get a list of tasks or a specific task
        if (isset($_GET["id"]) && !empty($_GET["id"])) {
            // Get specific task
            $taskId = sanitizeInputData($_GET["id"]);
            
            // Build the query to get task with related information
            $sql = "SELECT t.*, u.username, e.name as employeeName, p.title as projectTitle
                    FROM `task` as t
                    JOIN `user` as u ON u.id = t.by
                    JOIN `employee` as e ON e.id = t.to
                    JOIN `project` as p ON p.id = t.projectId
                    WHERE t.id = '{$taskId}'";
            
            $result = $GLOBALS["dbconnect"]->query($sql);
            
            if ($result && $result->num_rows > 0) {
                $task = $result->fetch_assoc();
                echo outputData($task);
            } else {
                echo outputError("Task not found");
            }
        } else {
            // Filter tasks based on user type
            if ($auth["userType"] == 1) {
                // Employee: Only see tasks assigned to them
                $sql = "SELECT t.*, u.username, e.name as employeeName, p.title as projectTitle
                        FROM `task` as t
                        JOIN `user` as u ON u.id = t.by
                        JOIN `employee` as e ON e.id = t.to
                        JOIN `project` as p ON p.id = t.projectId
                        WHERE t.to = '{$auth["userId"]}'";
            } else {
                // Admin: See all tasks or filter by project
                $sql = "SELECT t.*, u.username, e.name as employeeName, p.title as projectTitle
                        FROM `task` as t
                        JOIN `user` as u ON u.id = t.by
                        JOIN `employee` as e ON e.id = t.to
                        JOIN `project` as p ON p.id = t.projectId";
                
                // Optional filter by project
                if (isset($_GET["projectId"]) && !empty($_GET["projectId"])) {
                    $projectId = sanitizeInputData($_GET["projectId"]);
                    $sql .= " WHERE t.projectId = '{$projectId}'";
                }
            }
            
            // Add status filter if provided
            if (isset($_GET["status"]) && $_GET["status"] !== "") {
                $status = sanitizeInputData($_GET["status"]);
                if (strpos($sql, "WHERE") !== false) {
                    $sql .= " AND t.status = '{$status}'";
                } else {
                    $sql .= " WHERE t.status = '{$status}'";
                }
            }
            
            // Add sorting
            $sql .= " ORDER BY t.date DESC";
            
            $result = $GLOBALS["dbconnect"]->query($sql);
            
            if ($result && $result->num_rows > 0) {
                $tasks = [];
                while ($row = $result->fetch_assoc()) {
                    $tasks[] = $row;
                }
                echo outputData($tasks);
            } else {
                echo outputData([]);
            }
        }
        break;
        
    case "POST":
        // Create new task
        // Regular users (admins) can create tasks
        if ($auth["userType"] !== 0) {
            echo outputError("Access denied. Only administrators can create tasks.");
            exit;
        }
        
        $data = getRequestData();
        
        // Validate required fields
        $missingFields = validateRequiredFields($data, ["task", "expected", "to", "projectId"]);
        if (!empty($missingFields)) {
            echo outputError("Missing required fields: " . implode(", ", $missingFields));
            exit;
        }
        
        // Check if project exists
        $projectCheck = selectDB("project", "`id` = '{$data["projectId"]}'");
        if (!$projectCheck) {
            echo outputError("Project not found");
            exit;
        }
        
        // Check if employee exists
        $employeeCheck = selectDB("employee", "`id` = '{$data["to"]}' AND `status` = '0'");
        if (!$employeeCheck) {
            echo outputError("Employee not found or inactive");
            exit;
        }
        
        // Handle file upload if included
        $filePath = "";
        if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $directory = "../admin/files/";
            $originalfile = $directory . md5(date("d-m-y").time().rand(111111,999999))."." . $ext;
            
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $originalfile)) {
                $filePath = basename($originalfile);
            }
        }
        
        // Prepare data for insertion
        $insertData = [
            "task" => $data["task"],
            "expected" => $data["expected"],
            "to" => $data["to"],
            "projectId" => $data["projectId"],
            "by" => $auth["userId"],
            "date" => date("Y-m-d H:i:s"),
            "status" => "0",
            "file" => $filePath
        ];
        
        // Insert task
        if (insertDB("task", $insertData)) {
            $newTaskId = $GLOBALS["dbconnect"]->insert_id;
            
            // Get the newly created task with related information
            $sql = "SELECT t.*, u.username, e.name as employeeName, p.title as projectTitle
                    FROM `task` as t
                    JOIN `user` as u ON u.id = t.by
                    JOIN `employee` as e ON e.id = t.to
                    JOIN `project` as p ON p.id = t.projectId
                    WHERE t.id = '{$newTaskId}'";
            
            $result = $GLOBALS["dbconnect"]->query($sql);
            
            if ($result && $result->num_rows > 0) {
                $newTask = $result->fetch_assoc();
                
                // Send notifications
                $employee = selectDB("employee", "`id` = '{$data["to"]}'");
                $project = selectDB("project", "`id` = '{$data["projectId"]}'");
                
                $Msg = "New Task Assigned to you:\n\nProject: ".$project[0]["title"]."\nTask Details: ".$data["task"]."\nAssigned By: ".$auth["username"]."\nAssigned To: ".$employee[0]["name"]."\nExpected Date: ".substr($data["expected"], 0, 10);
                whatsappUltraMsg($employee[0]["phone"], $Msg);
                
                echo outputData($newTask);
            } else {
                echo outputData(["message" => "Task created successfully", "taskId" => $newTaskId]);
            }
        } else {
            echo outputError("Failed to create task");
        }
        break;
        
    case "PUT":
        // Update existing task status
        $data = getRequestData();
        
        // Check if ID is provided
        if (!isset($data["id"]) || empty($data["id"])) {
            echo outputError("Task ID is required");
            exit;
        }
        
        // Check if task exists
        $taskId = $data["id"];
        $existingTask = selectDB("task", "`id` = '{$taskId}'");
        
        if (!$existingTask) {
            echo outputError("Task not found");
            exit;
        }
        
        // Prepare update data
        $updateData = [];
        $statusChanged = false;
        
        // If employee updating status (employee can only update status)
        if ($auth["userType"] == 1) {
            // Employee can only update tasks assigned to them
            if ($existingTask[0]["to"] != $auth["userId"]) {
                echo outputError("Access denied. You can only update tasks assigned to you.");
                exit;
            }
            
            // Employee can only update status
            if (isset($data["status"])) {
                $newStatus = $data["status"];
                
                // Status transitions
                switch ($newStatus) {
                    case "1": // To doing
                        $updateData["status"] = "1";
                        $updateData["doing"] = date("Y-m-d H:i:s");
                        $updateData["finished"] = "";
                        $statusChanged = true;
                        break;
                    case "2": // To finished
                        $updateData["status"] = "2";
                        $updateData["finished"] = date("Y-m-d H:i:s");
                        $statusChanged = true;
                        break;
                    case "0": // To pending
                        $updateData["status"] = "0";
                        $updateData["doing"] = "";
                        $updateData["finished"] = "";
                        $statusChanged = true;
                        break;
                    default:
                        echo outputError("Invalid status value");
                        exit;
                }
            } else {
                echo outputError("Status field is required for employee updates");
                exit;
            }
        } else {
            // Admin can update more fields
            if (isset($data["task"])) $updateData["task"] = $data["task"];
            if (isset($data["expected"])) $updateData["expected"] = $data["expected"];
            if (isset($data["to"])) $updateData["to"] = $data["to"];
            
            // Admin can also update status
            if (isset($data["status"])) {
                $newStatus = $data["status"];
                
                // Status transitions
                switch ($newStatus) {
                    case "1": // To doing
                        $updateData["status"] = "1";
                        $updateData["doing"] = date("Y-m-d H:i:s");
                        $updateData["finished"] = "";
                        $statusChanged = true;
                        break;
                    case "2": // To finished
                        $updateData["status"] = "2";
                        $updateData["finished"] = date("Y-m-d H:i:s");
                        $statusChanged = true;
                        break;
                    case "0": // To pending
                        $updateData["status"] = "0";
                        $updateData["doing"] = "";
                        $updateData["finished"] = "";
                        $statusChanged = true;
                        break;
                    default:
                        echo outputError("Invalid status value");
                        exit;
                }
            }
        }
        
        // If there's nothing to update
        if (empty($updateData)) {
            echo outputError("No fields to update");
            exit;
        }
        
        // Update task
        if (updateDB("task", $updateData, "`id` = '{$taskId}'")) {
            // Send notification if status changed
            if ($statusChanged) {
                $updatedTask = selectDB("task", "`id` = '{$taskId}'");
                $employee = selectDB("employee", "`id` = '{$updatedTask[0]["to"]}'");
                $user = selectDB("user", "`id` = '{$updatedTask[0]["by"]}'");
                $project = selectDB("project", "`id` = '{$updatedTask[0]["projectId"]}'");
                
                $statusText = "";
                switch ($newStatus) {
                    case "0": $statusText = "RETURNED"; break;
                    case "1": $statusText = "STARTED"; break;
                    case "2": $statusText = "FINISHED"; break;
                }
                
                $Msg = "Task {$statusText}:\n\nProject: {$project[0]["title"]}\nTask Details: {$updatedTask[0]["task"]}\nAssigned By: {$user[0]["username"]}\nAssigned To: {$employee[0]["name"]}\n";
                
                if ($newStatus == "1") {
                    $Msg .= "Start Date: " . substr($updatedTask[0]["doing"], 0, 10) . "\n";
                } elseif ($newStatus == "2") {
                    $Msg .= "Finish Date: " . substr($updatedTask[0]["finished"], 0, 10) . "\n";
                }
                
                $Msg .= "Expected Date: " . substr($updatedTask[0]["expected"], 0, 10);
                
                // Send to both employee and user
                whatsappUltraMsg($employee[0]["phone"], $Msg);
                whatsappUltraMsg($user[0]["phone"], $Msg);
            }
            
            // Get the updated task with related information
            $sql = "SELECT t.*, u.username, e.name as employeeName, p.title as projectTitle
                    FROM `task` as t
                    JOIN `user` as u ON u.id = t.by
                    JOIN `employee` as e ON e.id = t.to
                    JOIN `project` as p ON p.id = t.projectId
                    WHERE t.id = '{$taskId}'";
            
            $result = $GLOBALS["dbconnect"]->query($sql);
            
            if ($result && $result->num_rows > 0) {
                $updatedTask = $result->fetch_assoc();
                echo outputData($updatedTask);
            } else {
                echo outputData(["message" => "Task updated successfully"]);
            }
        } else {
            echo outputError("Failed to update task");
        }
        break;
        
    case "DELETE":
        // Delete or mark task as deleted (status 2)
        // Only admin can delete tasks
        if ($auth["userType"] !== 0) {
            echo outputError("Access denied. Only administrators can delete tasks.");
            exit;
        }
        
        // Check if ID is provided
        if (!isset($_GET["id"]) || empty($_GET["id"])) {
            echo outputError("Task ID is required");
            exit;
        }
        
        $taskId = sanitizeInputData($_GET["id"]);
        
        // Check if task exists
        $existingTask = selectDB("task", "`id` = '{$taskId}'");
        
        if (!$existingTask) {
            echo outputError("Task not found");
            exit;
        }
        
        // Mark as deleted (status 2)
        if (updateDB("task", ["status" => "2"], "`id` = '{$taskId}'")) {
            // Send notifications
            $employee = selectDB("employee", "`id` = '{$existingTask[0]["to"]}'");
            $user = selectDB("user", "`id` = '{$existingTask[0]["by"]}'");
            $project = selectDB("project", "`id` = '{$existingTask[0]["projectId"]}'");
            
            $Msg = "Task Deleted:\n\nProject: {$project[0]["title"]}\nTask Details: {$existingTask[0]["task"]}\nAssigned By: {$user[0]["username"]}\nAssigned To: {$employee[0]["name"]}\nExpected Date: " . substr($existingTask[0]["expected"], 0, 10);
            
            whatsappUltraMsg($employee[0]["phone"], $Msg);
            whatsappUltraMsg($user[0]["phone"], $Msg);
            
            echo outputData(["message" => "Task deleted successfully"]);
        } else {
            echo outputError("Failed to delete task");
        }
        break;
        
    default:
        echo outputError("Method not allowed");
        break;
}
?>
