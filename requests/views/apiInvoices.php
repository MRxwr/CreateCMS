<?php
// API Invoices Endpoint
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

// Only admin users can manage invoices
if ($auth["userType"] !== 0) {
    echo outputError("Access denied. Only administrators can manage invoices.");
    exit;
}

// Get the request method
$method = $_SERVER["REQUEST_METHOD"];

// Process based on method
switch ($method) {
    case "GET":
        // Get a list of invoices or a specific invoice
        if (isset($_GET["id"]) && !empty($_GET["id"])) {
            // Get specific invoice
            $invoiceId = sanitizeInputData($_GET["id"]);
            
            $sql = "SELECT i.*, c.name as clientName, p.title as projectTitle
                    FROM `invoice` as i
                    LEFT JOIN `client` as c ON i.clientId = c.id
                    LEFT JOIN `project` as p ON i.projectId = p.id
                    WHERE i.id = '{$invoiceId}'";
            
            $result = $GLOBALS["dbconnect"]->query($sql);
            
            if ($result && $result->num_rows > 0) {
                $invoice = $result->fetch_assoc();
                
                // Get invoice items
                $items = selectDB("invoiceItems", "`invoiceId` = '{$invoiceId}'");
                $invoice["items"] = $items ? $items : [];
                
                echo outputData($invoice);
            } else {
                echo outputError("Invoice not found");
            }
        } else {
            // Get all invoices with optional filtering
            $sql = "SELECT i.*, c.name as clientName, p.title as projectTitle
                    FROM `invoice` as i
                    LEFT JOIN `client` as c ON i.clientId = c.id
                    LEFT JOIN `project` as p ON i.projectId = p.id";
            
            $whereAdded = false;
            
            // Filter by status
            if (isset($_GET["status"]) && $_GET["status"] !== "") {
                $status = sanitizeInputData($_GET["status"]);
                $sql .= " WHERE i.status = '{$status}'";
                $whereAdded = true;
            }
            
            // Filter by client
            if (isset($_GET["clientId"]) && !empty($_GET["clientId"])) {
                $clientId = sanitizeInputData($_GET["clientId"]);
                if ($whereAdded) {
                    $sql .= " AND i.clientId = '{$clientId}'";
                } else {
                    $sql .= " WHERE i.clientId = '{$clientId}'";
                    $whereAdded = true;
                }
            }
            
            // Filter by project
            if (isset($_GET["projectId"]) && !empty($_GET["projectId"])) {
                $projectId = sanitizeInputData($_GET["projectId"]);
                if ($whereAdded) {
                    $sql .= " AND i.projectId = '{$projectId}'";
                } else {
                    $sql .= " WHERE i.projectId = '{$projectId}'";
                    $whereAdded = true;
                }
            }
            
            // Add sorting
            $sql .= " ORDER BY i.date DESC";
            
            $result = $GLOBALS["dbconnect"]->query($sql);
            
            if ($result && $result->num_rows > 0) {
                $invoices = [];
                while ($row = $result->fetch_assoc()) {
                    $invoices[] = $row;
                }
                echo outputData($invoices);
            } else {
                echo outputData([]);
            }
        }
        break;
        
    case "POST":
        // Create new invoice
        $data = getRequestData();
        
        // Validate required fields
        $missingFields = validateRequiredFields($data, ["clientId", "total", "items"]);
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
        
        // Check project if provided
        if (isset($data["projectId"]) && !empty($data["projectId"])) {
            $projectCheck = selectDB("project", "`id` = '{$data["projectId"]}'");
            if (!$projectCheck) {
                echo outputError("Project not found");
                exit;
            }
        }
        
        // Validate items format
        if (!is_array($data["items"]) || empty($data["items"])) {
            echo outputError("Items must be a non-empty array");
            exit;
        }
        
        // Start transaction
        $GLOBALS["dbconnect"]->begin_transaction();
        
        try {
            // Prepare invoice data
            $invoiceData = [
                "clientId" => $data["clientId"],
                "projectId" => isset($data["projectId"]) ? $data["projectId"] : "0",
                "total" => $data["total"],
                "subtotal" => isset($data["subtotal"]) ? $data["subtotal"] : $data["total"],
                "tax" => isset($data["tax"]) ? $data["tax"] : "0",
                "discount" => isset($data["discount"]) ? $data["discount"] : "0",
                "notes" => isset($data["notes"]) ? $data["notes"] : "",
                "date" => date("Y-m-d H:i:s"),
                "dueDate" => isset($data["dueDate"]) ? $data["dueDate"] : date("Y-m-d", strtotime("+30 days")),
                "status" => isset($data["status"]) ? $data["status"] : "0", // 0=unpaid, 1=paid
                "userId" => $auth["userId"]
            ];
            
            // Insert invoice
            insertDB("invoice", $invoiceData);
            $invoiceId = $GLOBALS["dbconnect"]->insert_id;
            
            // Insert invoice items
            foreach ($data["items"] as $item) {
                if (!isset($item["description"]) || !isset($item["amount"])) {
                    throw new Exception("Each item must have a description and amount");
                }
                
                $itemData = [
                    "invoiceId" => $invoiceId,
                    "description" => $item["description"],
                    "quantity" => isset($item["quantity"]) ? $item["quantity"] : "1",
                    "price" => isset($item["price"]) ? $item["price"] : $item["amount"],
                    "amount" => $item["amount"]
                ];
                
                insertDB("invoiceItems", $itemData);
            }
            
            // Commit the transaction
            $GLOBALS["dbconnect"]->commit();
            
            // Get the newly created invoice with items
            $sql = "SELECT i.*, c.name as clientName, p.title as projectTitle
                    FROM `invoice` as i
                    LEFT JOIN `client` as c ON i.clientId = c.id
                    LEFT JOIN `project` as p ON i.projectId = p.id
                    WHERE i.id = '{$invoiceId}'";
            
            $result = $GLOBALS["dbconnect"]->query($sql);
            
            if ($result && $result->num_rows > 0) {
                $invoice = $result->fetch_assoc();
                
                // Get invoice items
                $items = selectDB("invoiceItems", "`invoiceId` = '{$invoiceId}'");
                $invoice["items"] = $items ? $items : [];
                
                echo outputData($invoice);
            } else {
                echo outputData(["message" => "Invoice created successfully", "invoiceId" => $invoiceId]);
            }
        } catch (Exception $e) {
            // Rollback the transaction on error
            $GLOBALS["dbconnect"]->rollback();
            echo outputError("Failed to create invoice: " . $e->getMessage());
        }
        break;
        
    case "PUT":
        // Update existing invoice
        $data = getRequestData();
        
        // Check if ID is provided
        if (!isset($data["id"]) || empty($data["id"])) {
            echo outputError("Invoice ID is required");
            exit;
        }
        
        // Check if invoice exists
        $invoiceId = $data["id"];
        $existingInvoice = selectDB("invoice", "`id` = '{$invoiceId}'");
        
        if (!$existingInvoice) {
            echo outputError("Invoice not found");
            exit;
        }
        
        // Start transaction
        $GLOBALS["dbconnect"]->begin_transaction();
        
        try {
            // Prepare invoice update data
            $updateData = [];
            
            // Only include fields that are provided and allowed to be updated
            if (isset($data["clientId"])) {
                // Check if client exists
                $clientCheck = selectDB("client", "`id` = '{$data["clientId"]}'");
                if (!$clientCheck) {
                    throw new Exception("Client not found");
                }
                $updateData["clientId"] = $data["clientId"];
            }
            
            if (isset($data["projectId"])) {
                // Check if project exists
                if (!empty($data["projectId"])) {
                    $projectCheck = selectDB("project", "`id` = '{$data["projectId"]}'");
                    if (!$projectCheck) {
                        throw new Exception("Project not found");
                    }
                }
                $updateData["projectId"] = $data["projectId"];
            }
            
            if (isset($data["total"])) $updateData["total"] = $data["total"];
            if (isset($data["subtotal"])) $updateData["subtotal"] = $data["subtotal"];
            if (isset($data["tax"])) $updateData["tax"] = $data["tax"];
            if (isset($data["discount"])) $updateData["discount"] = $data["discount"];
            if (isset($data["notes"])) $updateData["notes"] = $data["notes"];
            if (isset($data["dueDate"])) $updateData["dueDate"] = $data["dueDate"];
            if (isset($data["status"])) $updateData["status"] = $data["status"];
            
            // Update invoice if there are fields to update
            if (!empty($updateData)) {
                updateDB("invoice", $updateData, "`id` = '{$invoiceId}'");
            }
            
            // Update items if provided
            if (isset($data["items"]) && is_array($data["items"])) {
                // Delete existing items
                deleteDB("invoiceItems", "`invoiceId` = '{$invoiceId}'");
                
                // Insert new items
                foreach ($data["items"] as $item) {
                    if (!isset($item["description"]) || !isset($item["amount"])) {
                        throw new Exception("Each item must have a description and amount");
                    }
                    
                    $itemData = [
                        "invoiceId" => $invoiceId,
                        "description" => $item["description"],
                        "quantity" => isset($item["quantity"]) ? $item["quantity"] : "1",
                        "price" => isset($item["price"]) ? $item["price"] : $item["amount"],
                        "amount" => $item["amount"]
                    ];
                    
                    insertDB("invoiceItems", $itemData);
                }
            }
            
            // Commit the transaction
            $GLOBALS["dbconnect"]->commit();
            
            // Get the updated invoice with items
            $sql = "SELECT i.*, c.name as clientName, p.title as projectTitle
                    FROM `invoice` as i
                    LEFT JOIN `client` as c ON i.clientId = c.id
                    LEFT JOIN `project` as p ON i.projectId = p.id
                    WHERE i.id = '{$invoiceId}'";
            
            $result = $GLOBALS["dbconnect"]->query($sql);
            
            if ($result && $result->num_rows > 0) {
                $invoice = $result->fetch_assoc();
                
                // Get invoice items
                $items = selectDB("invoiceItems", "`invoiceId` = '{$invoiceId}'");
                $invoice["items"] = $items ? $items : [];
                
                echo outputData($invoice);
            } else {
                echo outputData(["message" => "Invoice updated successfully"]);
            }
        } catch (Exception $e) {
            // Rollback the transaction on error
            $GLOBALS["dbconnect"]->rollback();
            echo outputError("Failed to update invoice: " . $e->getMessage());
        }
        break;
        
    case "DELETE":
        // Delete invoice
        if (!isset($_GET["id"]) || empty($_GET["id"])) {
            echo outputError("Invoice ID is required");
            exit;
        }
        
        $invoiceId = sanitizeInputData($_GET["id"]);
        
        // Check if invoice exists
        $existingInvoice = selectDB("invoice", "`id` = '{$invoiceId}'");
        
        if (!$existingInvoice) {
            echo outputError("Invoice not found");
            exit;
        }
        
        // Start transaction
        $GLOBALS["dbconnect"]->begin_transaction();
        
        try {
            // Delete invoice items first
            deleteDB("invoiceItems", "`invoiceId` = '{$invoiceId}'");
            
            // Then delete the invoice
            deleteDB("invoice", "`id` = '{$invoiceId}'");
            
            // Commit the transaction
            $GLOBALS["dbconnect"]->commit();
            
            echo outputData(["message" => "Invoice deleted successfully"]);
        } catch (Exception $e) {
            // Rollback the transaction on error
            $GLOBALS["dbconnect"]->rollback();
            echo outputError("Failed to delete invoice: " . $e->getMessage());
        }
        break;
        
    default:
        echo outputError("Method not allowed");
        break;
}
?>
