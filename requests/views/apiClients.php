<?php
// API Clients Endpoint (for both leads and customers)
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

// Only admin users can manage clients
if ($auth["userType"] !== 0) {
    echo outputError("Access denied. Only administrators can manage clients.");
    exit;
}

// Get the request method
$method = $_SERVER["REQUEST_METHOD"];

// Process based on method
switch ($method) {
    case "GET":
        // Get a list of clients (leads and customers) or a specific client
        if (isset($_GET["id"]) && !empty($_GET["id"])) {
            // Get specific client
            $clientId = sanitizeInputData($_GET["id"]);
            $client = selectDB("client", "`id` = '{$clientId}'");
            
            if (!$client) {
                echo outputError("Client not found");
            } else {
                echo outputData($client[0]);
            }
        } else {
            // Get all clients with optional filtering
            $where = "";
            
            // Filter by client type (lead=0, customer=1)
            if (isset($_GET["type"]) && ($_GET["type"] === "0" || $_GET["type"] === "1")) {
                $type = sanitizeInputData($_GET["type"]);
                $where = "`type` = '{$type}'";
            }
            
            // Filter by status (0=active, 1=inactive, 2=deleted)
            if (isset($_GET["status"])) {
                $status = sanitizeInputData($_GET["status"]);
                if (!empty($where)) {
                    $where .= " AND ";
                }
                $where .= "`status` = '{$status}'";
            } else {
                // By default, only show non-deleted clients
                if (!empty($where)) {
                    $where .= " AND ";
                }
                $where .= "`status` != '2'";
            }
            
            $clients = selectDB("client", $where);
            
            if (!$clients) {
                echo outputData([]);
            } else {
                echo outputData($clients);
            }
        }
        break;
        
    case "POST":
        // Create new client (lead or customer)
        $data = getRequestData();
        
        // Validate required fields
        $missingFields = validateRequiredFields($data, ["name", "email", "phone"]);
        if (!empty($missingFields)) {
            echo outputError("Missing required fields: " . implode(", ", $missingFields));
            exit;
        }
        
        // Check if email or phone already exists
        if (selectDB("client", "`email` = '{$data["email"]}'")) {
            echo outputError("Email already exists");
            exit;
        }
        
        if (selectDB("client", "`phone` = '{$data["phone"]}'")) {
            echo outputError("Phone number already exists");
            exit;
        }
        
        // Prepare data for insertion
        $insertData = [
            "name" => $data["name"],
            "email" => $data["email"],
            "phone" => $data["phone"],
            "address" => isset($data["address"]) ? $data["address"] : "",
            "type" => isset($data["type"]) ? $data["type"] : "0", // Default to lead (0)
            "notes" => isset($data["notes"]) ? $data["notes"] : "",
            "userId" => $auth["userId"],
            "date" => date("Y-m-d H:i:s"),
            "status" => isset($data["status"]) ? $data["status"] : "0" // Default to active (0)
        ];
        
        // Insert client
        if (insertDB("client", $insertData)) {
            $newClientId = $GLOBALS["dbconnect"]->insert_id;
            $newClient = selectDB("client", "`id` = '{$newClientId}'");
            
            echo outputData($newClient[0]);
        } else {
            echo outputError("Failed to create client");
        }
        break;
        
    case "PUT":
        // Update existing client
        $data = getRequestData();
        
        // Check if ID is provided
        if (!isset($data["id"]) || empty($data["id"])) {
            echo outputError("Client ID is required");
            exit;
        }
        
        // Check if client exists
        $clientId = $data["id"];
        $existingClient = selectDB("client", "`id` = '{$clientId}'");
        
        if (!$existingClient) {
            echo outputError("Client not found");
            exit;
        }
        
        // Prepare data for update
        $updateData = [];
        
        // Only include fields that are provided and allowed to be updated
        if (isset($data["name"])) $updateData["name"] = $data["name"];
        if (isset($data["address"])) $updateData["address"] = $data["address"];
        if (isset($data["notes"])) $updateData["notes"] = $data["notes"];
        if (isset($data["type"])) $updateData["type"] = $data["type"]; // Convert lead to customer
        if (isset($data["status"])) $updateData["status"] = $data["status"];
        
        // Check if email is being updated and not already in use by another client
        if (isset($data["email"]) && $data["email"] !== $existingClient[0]["email"]) {
            if (selectDB("client", "`email` = '{$data["email"]}' AND `id` != '{$clientId}'")) {
                echo outputError("Email already exists");
                exit;
            }
            $updateData["email"] = $data["email"];
        }
        
        // Check if phone is being updated and not already in use by another client
        if (isset($data["phone"]) && $data["phone"] !== $existingClient[0]["phone"]) {
            if (selectDB("client", "`phone` = '{$data["phone"]}' AND `id` != '{$clientId}'")) {
                echo outputError("Phone number already exists");
                exit;
            }
            $updateData["phone"] = $data["phone"];
        }
        
        // If there's nothing to update
        if (empty($updateData)) {
            echo outputError("No fields to update");
            exit;
        }
        
        // Update client
        if (updateDB("client", $updateData, "`id` = '{$clientId}'")) {
            $updatedClient = selectDB("client", "`id` = '{$clientId}'");
            echo outputData($updatedClient[0]);
        } else {
            echo outputError("Failed to update client");
        }
        break;
        
    case "DELETE":
        // Delete or mark client as deleted (status 2)
        if (!isset($_GET["id"]) || empty($_GET["id"])) {
            echo outputError("Client ID is required");
            exit;
        }
        
        $clientId = sanitizeInputData($_GET["id"]);
        
        // Check if client exists
        $existingClient = selectDB("client", "`id` = '{$clientId}'");
        
        if (!$existingClient) {
            echo outputError("Client not found");
            exit;
        }
        
        // Check if client has any projects
        $projects = selectDB("project", "`clientId` = '{$clientId}'");
        if ($projects) {
            // Mark as deleted instead of real delete (status 2)
            if (updateDB("client", ["status" => "2"], "`id` = '{$clientId}'")) {
                echo outputData(["message" => "Client marked as deleted"]);
            } else {
                echo outputError("Failed to delete client");
            }
        } else {
            // No projects, can perform real delete
            if (deleteDB("client", "`id` = '{$clientId}'")) {
                echo outputData(["message" => "Client deleted successfully"]);
            } else {
                echo outputError("Failed to delete client");
            }
        }
        break;
        
    default:
        echo outputError("Method not allowed");
        break;
}
?>
