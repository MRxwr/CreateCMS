<?php
// API Projects Endpoint
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

// Only admin users can create/edit/delete projects
if ($auth["userType"] !== 0 && ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "PUT" || $_SERVER["REQUEST_METHOD"] == "DELETE")) {
    echo outputError("Access denied. Only administrators can manage projects.");
    exit;
}

// Get the request method
$method = $_SERVER["REQUEST_METHOD"];

// Process based on method
switch ($method) {
    case "GET":
        // Get a list of projects or a specific project
        if (isset($_GET["id"]) && !empty($_GET["id"])) {
            // Get specific project
            $projectId = sanitizeInputData($_GET["id"]);
            
            $sql = "SELECT p.*, c.name as clientName, u.username
                    FROM `project` as p
                    JOIN `client` as c ON p.clientId = c.id
                    JOIN `user` as u ON p.userId = u.id
                    WHERE p.id = '{$projectId}'";
            
            $result = $GLOBALS["dbconnect"]->query($sql);
            
            if ($result && $result->num_rows > 0) {
                $project = $result->fetch_assoc();
                echo outputData($project);
            } else {
                echo outputError("Project not found");
            }
        } else {
            // Get all projects or filter by client
            $sql = "SELECT p.*, c.name as clientName, u.username
                    FROM `project` as p
                    JOIN `client` as c ON p.clientId = c.id
                    JOIN `user` as u ON p.userId = u.id";
            
            // Filter by status if provided
            if (isset($_GET["status"]) && $_GET["status"] !== "") {
                $status = sanitizeInputData($_GET["status"]);
                $sql .= " WHERE p.status = '{$status}'";
            } else {
                $sql .= " WHERE p.status != '2'"; // By default don't show deleted projects
            }
            
            // Filter by client if provided
            if (isset($_GET["clientId"]) && !empty($_GET["clientId"])) {
                $clientId = sanitizeInputData($_GET["clientId"]);
                if (strpos($sql, "WHERE") !== false) {
                    $sql .= " AND p.clientId = '{$clientId}'";
                } else {
                    $sql .= " WHERE p.clientId = '{$clientId}'";
                }
            }
            
            // Filter by user if provided
            if (isset($_GET["userId"]) && !empty($_GET["userId"])) {
                $userId = sanitizeInputData($_GET["userId"]);
                if (strpos($sql, "WHERE") !== false) {
                    $sql .= " AND p.userId = '{$userId}'";
                } else {
                    $sql .= " WHERE p.userId = '{$userId}'";
                }
            }
            
            // Add sorting
            $sql .= " ORDER BY p.date DESC";
            
            $result = $GLOBALS["dbconnect"]->query($sql);
            
            if ($result && $result->num_rows > 0) {
                $projects = [];
                while ($row = $result->fetch_assoc()) {
                    $projects[] = $row;
                }
                echo outputData($projects);
            } else {
                echo outputData([]);
            }
        }
        break;
        
    case "POST":
        // Create new project
        $data = getRequestData();
        
        // Validate required fields
        $missingFields = validateRequiredFields($data, ["title", "details", "clientId"]);
        if (!empty($missingFields)) {
            echo outputError("Missing required fields: " . implode(", ", $missingFields));
            exit;
        }
        
        // Check if client exists
        $clientCheck = selectDB("client", "`id` = '{$data["clientId"]}'");
        if (!$clientCheck) {
            echo outputError("Client not found");
            exit;
        }
        
        // Prepare data for insertion
        $insertData = [
            "title" => $data["title"],
            "details" => $data["details"],
            "clientId" => $data["clientId"],
            "userId" => $auth["userId"],
            "date" => date("Y-m-d H:i:s"),
            "status" => isset($data["status"]) ? $data["status"] : "0"
        ];
        
        // Insert project
        if (insertDB("project", $insertData)) {
            $newProjectId = $GLOBALS["dbconnect"]->insert_id;
            
            // Get the newly created project with related information
            $sql = "SELECT p.*, c.name as clientName, u.username
                    FROM `project` as p
                    JOIN `client` as c ON p.clientId = c.id
                    JOIN `user` as u ON p.userId = u.id
                    WHERE p.id = '{$newProjectId}'";
            
            $result = $GLOBALS["dbconnect"]->query($sql);
            
            if ($result && $result->num_rows > 0) {
                $newProject = $result->fetch_assoc();
                echo outputData($newProject);
            } else {
                echo outputData(["message" => "Project created successfully", "projectId" => $newProjectId]);
            }
        } else {
            echo outputError("Failed to create project");
        }
        break;
        
    case "PUT":
        // Update existing project
        $data = getRequestData();
        
        // Check if ID is provided
        if (!isset($data["id"]) || empty($data["id"])) {
            echo outputError("Project ID is required");
            exit;
        }
        
        // Check if project exists
        $projectId = $data["id"];
        $existingProject = selectDB("project", "`id` = '{$projectId}'");
        
        if (!$existingProject) {
            echo outputError("Project not found");
            exit;
        }
        
        // Prepare data for update
        $updateData = [];
        
        // Only include fields that are provided and allowed to be updated
        if (isset($data["title"])) $updateData["title"] = $data["title"];
        if (isset($data["details"])) $updateData["details"] = $data["details"];
        if (isset($data["status"])) $updateData["status"] = $data["status"];
        
        // Check if client exists if clientId is provided
        if (isset($data["clientId"]) && !empty($data["clientId"])) {
            $clientCheck = selectDB("client", "`id` = '{$data["clientId"]}'");
            if (!$clientCheck) {
                echo outputError("Client not found");
                exit;
            }
            $updateData["clientId"] = $data["clientId"];
        }
        
        // If there's nothing to update
        if (empty($updateData)) {
            echo outputError("No fields to update");
            exit;
        }
        
        // Update project
        if (updateDB("project", $updateData, "`id` = '{$projectId}'")) {
            // Get the updated project with related information
            $sql = "SELECT p.*, c.name as clientName, u.username
                    FROM `project` as p
                    JOIN `client` as c ON p.clientId = c.id
                    JOIN `user` as u ON p.userId = u.id
                    WHERE p.id = '{$projectId}'";
            
            $result = $GLOBALS["dbconnect"]->query($sql);
            
            if ($result && $result->num_rows > 0) {
                $updatedProject = $result->fetch_assoc();
                echo outputData($updatedProject);
            } else {
                echo outputData(["message" => "Project updated successfully"]);
            }
        } else {
            echo outputError("Failed to update project");
        }
        break;
        
    case "DELETE":
        // Delete or mark project as deleted (status 2)
        if (!isset($_GET["id"]) || empty($_GET["id"])) {
            echo outputError("Project ID is required");
            exit;
        }
        
        $projectId = sanitizeInputData($_GET["id"]);
        
        // Check if project exists
        $existingProject = selectDB("project", "`id` = '{$projectId}'");
        
        if (!$existingProject) {
            echo outputError("Project not found");
            exit;
        }
        
        // Mark as deleted (status 2)
        if (updateDB("project", ["status" => "2"], "`id` = '{$projectId}'")) {
            echo outputData(["message" => "Project deleted successfully"]);
        } else {
            echo outputError("Failed to delete project");
        }
        break;
        
    default:
        echo outputError("Method not allowed");
        break;
}
?>
