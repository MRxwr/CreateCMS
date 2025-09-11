<?php
// Prevent any output before JSON response
ob_start();

// Error handling - convert all errors to exceptions
set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

header('Content-Type: application/json');
require_once('../admin/includes/config.php');
require_once('../admin/includes/functions.php');
require_once('../includes/auth.php');

// Check if user is authenticated
if (!checkAuthentication()) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'data' => 'Authentication required']);
    exit;
}

// Get current user info
$currentUser = getCurrentUser();
if (!$currentUser) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'data' => 'User session invalid']);
    exit;
}

$userId = $currentUser['id'];

$method = $_SERVER['REQUEST_METHOD'];
$response = ['ok' => false, 'data' => null];

try {
    switch($method) {
        case 'GET':
            if(isset($_GET['id'])) {
                // Get single client
                $clientId = (int)$_GET['id'];
                $client = selectDB("client", "id = {$clientId}");
                
                if($client && is_array($client) && count($client) > 0) {
                    $response['ok'] = true;
                    $response['data'] = $client[0];
                } else {
                    throw new Exception('Client not found');
                }
            } elseif(isset($_GET['export']) && $_GET['export'] == 'csv') {
                // Export clients to CSV
                $clients = selectDB("client", "1=1 ORDER BY id DESC");
                
                if($clients && is_array($clients)) {
                    $filename = 'clients_export_' . date('Y-m-d') . '.csv';
                    
                    header('Content-Type: text/csv');
                    header('Content-Disposition: attachment; filename="' . $filename . '"');
                    
                    $output = fopen('php://output', 'w');
                    
                    // CSV Headers
                    fputcsv($output, ['ID', 'Name', 'Company', 'Email', 'Phone', 'Notes', 'Created Date']);
                    
                    // CSV Data
                    foreach($clients as $client) {
                        fputcsv($output, [
                            $client['id'],
                            $client['name'],
                            $client['company'],
                            $client['email'],
                            $client['phone'] ?? '',
                            $client['notes'] ?? '',
                            $client['date'] ?? ''
                        ]);
                    }
                    
                    fclose($output);
                    exit;
                }
            } else {
                // Get all clients
                $clients = selectDB("client", "1=1 ORDER BY id DESC");
                $response['ok'] = true;
                $response['data'] = $clients ?: [];
            }
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if(!$input) {
                throw new Exception('Invalid JSON data');
            }
            
            // Validate required fields
            if(empty($input['name']) || empty($input['email'])) {
                throw new Exception('Name and email are required');
            }
            
            // Validate email format
            if(!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format');
            }
            
            // Check if email already exists
            $existingClient = selectDB("client", "email = '{$input['email']}'");
            if($existingClient && is_array($existingClient) && count($existingClient) > 0) {
                throw new Exception('A client with this email already exists');
            }
            
            $clientData = [
                'userId' => $userId,
                'type' => 1,
                'name' => $input['name'],
                'company' => $input['company'] ?? $input['name'],
                'email' => $input['email'],
                'phone' => $input['phone'] ?? '',
                'notes' => $input['notes'] ?? '',
                'image' => ''
            ];
            
            if(isset($input['id']) && !empty($input['id'])) {
                // Update existing client
                $clientId = (int)$input['id'];
                unset($clientData['userId'], $clientData['type']);
                
                $result = updateDB("client", $clientData, "id = {$clientId}");
                
                if($result) {
                    $response['ok'] = true;
                    $response['data'] = ['message' => 'Client updated successfully'];
                } else {
                    throw new Exception('Failed to update client');
                }
            } else {
                // Create new client
                $result = insertDB("client", $clientData);
                
                if($result) {
                    $response['ok'] = true;
                    $response['data'] = ['message' => 'Client created successfully'];
                } else {
                    throw new Exception('Failed to create client');
                }
            }
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if(!$input || empty($input['id'])) {
                throw new Exception('Client ID is required');
            }
            
            $clientId = (int)$input['id'];
            
            // Validate required fields
            if(empty($input['name']) || empty($input['email'])) {
                throw new Exception('Name and email are required');
            }
            
            // Validate email format
            if(!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format');
            }
            
            // Check if email already exists (excluding current client)
            $existingClient = selectDB("client", "email = '{$input['email']}' AND id != {$clientId}");
            if($existingClient && is_array($existingClient) && count($existingClient) > 0) {
                throw new Exception('A client with this email already exists');
            }
            
            $clientData = [
                'name' => $input['name'],
                'company' => $input['company'] ?? $input['name'],
                'email' => $input['email'],
                'phone' => $input['phone'] ?? '',
                'notes' => $input['notes'] ?? ''
            ];
            
            $result = updateDB("client", $clientData, "id = {$clientId}");
            
            if($result) {
                $response['ok'] = true;
                $response['data'] = ['message' => 'Client updated successfully'];
            } else {
                throw new Exception('Failed to update client');
            }
            break;
            
        case 'DELETE':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if(!$input || empty($input['id'])) {
                throw new Exception('Client ID is required');
            }
            
            $clientId = (int)$input['id'];
            
            // Check if client has active projects
            $activeProjects = getTotals("project", "clientId = {$clientId}");
            if($activeProjects > 0) {
                throw new Exception('Cannot delete client with active projects. Please delete all projects first.');
            }
            
            // Hard delete from your live structure
            $result = deleteDB("client", "id = {$clientId}");
            
            if($result) {
                $response['ok'] = true;
                $response['data'] = ['message' => 'Client deleted successfully'];
            } else {
                throw new Exception('Failed to delete client');
            }
            break;
            
        default:
            throw new Exception('Method not allowed');
    }
    
} catch(Exception $e) {
    $response['ok'] = false;
    $response['data'] = $e->getMessage();
    http_response_code(400);
} catch(Error $e) {
    $response['ok'] = false;
    $response['data'] = 'Server error: ' . $e->getMessage();
    http_response_code(500);
}

// Clear any unwanted output and send JSON
ob_clean();
echo json_encode($response);
?>
