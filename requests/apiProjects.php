<?php
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

$method = $_SERVER['REQUEST_METHOD'];
$response = ['ok' => false, 'data' => null];

try {
    switch($method) {
        case 'GET':
            if(isset($_GET['id'])) {
                // Get single project with client info using direct query
                $projectId = (int)$_GET['id'];
                $project = [];
                $query = "SELECT p.*, c.name as clientName, c.company as clientCompany 
                          FROM project p 
                          LEFT JOIN client c ON p.clientId = c.id 
                          WHERE p.id = {$projectId}";
                $result = $dbconnect->query($query);
                if ($result && $result->num_rows > 0) {
                    $project = $result->fetch_assoc();
                }
                
                if($project) {
                    $response['ok'] = true;
                    $response['data'] = $project;
                } else {
                    throw new Exception('Project not found');
                }
            } else {
                // Get all projects with client info using direct query
                $projects = [];
                $query = "SELECT p.*, c.name as clientName, c.company as clientCompany 
                          FROM project p 
                          LEFT JOIN client c ON p.clientId = c.id 
                          WHERE p.status != 2 
                          ORDER BY p.id DESC";
                $result = $dbconnect->query($query);
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $projects[] = $row;
                    }
                }
                $response['ok'] = true;
                $response['data'] = $projects;
            }
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if(!$input) {
                throw new Exception('Invalid JSON data');
            }
            
            // Validate required fields
            if(empty($input['title'])) {
                throw new Exception('Project title is required');
            }
            
            if(empty($input['clientId'])) {
                throw new Exception('Client selection is required');
            }
            
            // Check if project title already exists
            $existingProject = selectDB("project", "title = '{$input['title']}'");
            if($existingProject && is_array($existingProject) && count($existingProject) > 0) {
                throw new Exception('A project with this title already exists');
            }
            
            $projectData = [
                'clientId' => (int)$input['clientId'],
                'userId' => $userId,
                'title' => $input['title'],
                'details' => $input['description'] ?? '',
                'price' => (float)($input['price'] ?? 0),
                'expected' => $input['endDate'] ?? date('Y-m-d H:i:s'),
                'file' => '',
                'url' => $input['url'] ?? '',
                'actualDays' => '',
                'status' => 1
            ];
            
            if(isset($input['id']) && !empty($input['id'])) {
                // Update existing project
                $projectId = (int)$input['id'];
                unset($projectData['userId']);
                
                $result = updateDB("project", $projectData, "id = {$projectId}");
                
                if($result) {
                    $response['ok'] = true;
                    $response['data'] = ['message' => 'Project updated successfully'];
                } else {
                    throw new Exception('Failed to update project');
                }
            } else {
                // Create new project
                $result = insertDB("project", $projectData);
                
                if($result) {
                    $response['ok'] = true;
                    $response['data'] = ['message' => 'Project created successfully'];
                } else {
                    throw new Exception('Failed to create project');
                }
            }
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if(!$input || empty($input['id'])) {
                throw new Exception('Project ID is required');
            }
            
            $projectId = (int)$input['id'];
            
            // Validate required fields
            if(empty($input['title'])) {
                throw new Exception('Project title is required');
            }
            
            if(empty($input['clientId'])) {
                throw new Exception('Client selection is required');
            }
            
            // Check if project title already exists (excluding current project)
            $existingProject = selectDB("project", "title = '{$input['title']}' AND id != {$projectId}");
            if($existingProject && is_array($existingProject) && count($existingProject) > 0) {
                throw new Exception('A project with this title already exists');
            }
            
            $projectData = [
                'clientId' => (int)$input['clientId'],
                'title' => $input['title'],
                'details' => $input['description'] ?? '',
                'price' => (float)($input['price'] ?? 0),
                'expected' => $input['endDate'] ?? date('Y-m-d H:i:s'),
                'url' => $input['url'] ?? ''
            ];
            
            $result = updateDB("project", $projectData, "id = {$projectId}");
            
            if($result) {
                $response['ok'] = true;
                $response['data'] = ['message' => 'Project updated successfully'];
            } else {
                throw new Exception('Failed to update project');
            }
            break;
            
        case 'DELETE':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if(!$input || empty($input['id'])) {
                throw new Exception('Project ID is required');
            }
            
            $projectId = (int)$input['id'];
            
            // Check if project has active tasks
            $activeTasks = getTotals("task", "projectId = {$projectId}");
            if($activeTasks > 0) {
                throw new Exception('Cannot delete project with active tasks. Please delete or move all tasks first.');
            }
            
            // Hard delete from your live structure
            $result = deleteDB("project", "id = {$projectId}");
            
            if($result) {
                $response['ok'] = true;
                $response['data'] = ['message' => 'Project deleted successfully'];
            } else {
                throw new Exception('Failed to delete project');
            }
            break;
            
        default:
            throw new Exception('Method not allowed');
    }
    
} catch(Exception $e) {
    $response['ok'] = false;
    $response['data'] = $e->getMessage();
    http_response_code(400);
}

echo json_encode($response);
?>
