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
                // Get single task with project and employee information using direct query
                $taskId = (int)$_GET['id'];
                $task = [];
                $query = "SELECT t.*, p.title as project_title, e.name as employee_name 
                          FROM task t 
                          LEFT JOIN project p ON t.projectId = p.id 
                          LEFT JOIN employee e ON t.to = e.id 
                          WHERE t.id = {$taskId}";
                $result = $dbconnect->query($query);
                if ($result && $result->num_rows > 0) {
                    $task = $result->fetch_assoc();
                }
                
                if($task) {
                    $response['ok'] = true;
                    $response['data'] = $task;
                } else {
                    throw new Exception('Task not found');
                }
            } else {
                // Get all tasks with project and employee information using direct query
                $whereClause = "t.status != 2"; // status 2 = deleted
                
                // Apply filters if provided
                if(isset($_GET['project']) && !empty($_GET['project'])) {
                    $projectId = (int)$_GET['project'];
                    $whereClause .= " AND t.projectId = {$projectId}";
                }
                
                if(isset($_GET['employee']) && !empty($_GET['employee'])) {
                    $employeeId = (int)$_GET['employee'];
                    $whereClause .= " AND t.to = {$employeeId}";
                }
                
                if(isset($_GET['status']) && !empty($_GET['status'])) {
                    $status = (int)$_GET['status'];
                    $whereClause .= " AND t.status = {$status}";
                }
                
                $tasks = [];
                $query = "SELECT t.*, p.title as project_title, e.name as employee_name 
                          FROM task t 
                          LEFT JOIN project p ON t.projectId = p.id 
                          LEFT JOIN employee e ON t.to = e.id 
                          WHERE {$whereClause} 
                          ORDER BY t.id DESC";
                $result = $dbconnect->query($query);
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $tasks[] = $row;
                    }
                }
                $response['ok'] = true;
                $response['data'] = $tasks;
            }
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if(!$input) {
                throw new Exception('Invalid JSON data');
            }
            
            // Validate required fields
            if(empty($input['task'])) {
                throw new Exception('Task title is required');
            }
            
            if(empty($input['projectId'])) {
                throw new Exception('Project is required');
            }
            
            if(empty($input['to'])) {
                throw new Exception('Please assign the task to an employee');
            }
            
            // Verify project exists
            $project = selectDB("project", "id = {$input['projectId']}");
            if(!$project || !is_array($project) || count($project) == 0) {
                throw new Exception('Selected project not found');
            }
            
            // Verify employee exists
            $employee = selectDB("employee", "id = {$input['to']} AND status = 0");
            if(!$employee || !is_array($employee) || count($employee) == 0) {
                throw new Exception('Selected employee not found');
            }
            
            $taskData = [
                'projectId' => (int)$input['projectId'],
                'by' => $userId,
                'to' => (int)$input['to'],
                'toUser' => 0, // Set based on your logic
                'expected' => $input['expected'] ?? date('Y-m-d H:i:s'),
                'task' => $input['task'],
                'file' => '',
                'status' => isset($input['status']) ? (int)$input['status'] : 0 // 0 = pending
            ];
            
            if(isset($input['id']) && !empty($input['id'])) {
                // Update existing task
                $taskId = (int)$input['id'];
                
                $result = updateDB("task", $taskData, "id = {$taskId}");
                
                if($result) {
                    $response['ok'] = true;
                    $response['data'] = ['message' => 'Task updated successfully'];
                } else {
                    throw new Exception('Failed to update task');
                }
            } else {
                // Create new task
                $result = insertDB("task", $taskData);
                
                if($result) {
                    $response['ok'] = true;
                    $response['data'] = ['message' => 'Task created successfully'];
                } else {
                    throw new Exception('Failed to create task');
                }
            }
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if(!$input || empty($input['id'])) {
                throw new Exception('Task ID is required');
            }
            
            $taskId = (int)$input['id'];
            
            // Get current task
            $currentTask = selectDB("task", "id = {$taskId}");
            if(!$currentTask || !is_array($currentTask) || count($currentTask) == 0) {
                throw new Exception('Task not found');
            }
            
            $updateData = [];
            
            // Handle status updates with timestamps
            if(isset($input['status'])) {
                $updateData['status'] = (int)$input['status'];
                
                // Add timestamps for status changes (assuming your DB has these fields)
                switch((int)$input['status']) {
                    case 1: // doing
                        $updateData['doing'] = date('Y-m-d H:i:s');
                        break;
                    case 2: // finished
                        $updateData['finished'] = date('Y-m-d H:i:s');
                        break;
                }
            }
            
            // Handle full task update
            if(isset($input['task'])) {
                if(empty($input['task'])) {
                    throw new Exception('Task title is required');
                }
                
                if(empty($input['projectId'])) {
                    throw new Exception('Project is required');
                }
                
                if(empty($input['to'])) {
                    throw new Exception('Please assign the task to an employee');
                }
                
                // Verify project exists
                $project = selectDB("project", "id = {$input['projectId']}");
                if(!$project || !is_array($project) || count($project) == 0) {
                    throw new Exception('Selected project not found');
                }
                
                // Verify employee exists
                $employee = selectDB("employee", "id = {$input['to']} AND status = 0");
                if(!$employee || !is_array($employee) || count($employee) == 0) {
                    throw new Exception('Selected employee not found');
                }
                
                $updateData = array_merge($updateData, [
                    'task' => $input['task'],
                    'projectId' => (int)$input['projectId'],
                    'to' => (int)$input['to'],
                    'expected' => $input['expected'] ?? date('Y-m-d H:i:s')
                ]);
            }
            
            $result = updateDB("task", $updateData, "id = {$taskId}");
            
            if($result) {
                $response['ok'] = true;
                $response['data'] = ['message' => 'Task updated successfully'];
            } else {
                throw new Exception('Failed to update task');
            }
            break;
            
        case 'DELETE':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if(!$input || empty($input['id'])) {
                throw new Exception('Task ID is required');
            }
            
            $taskId = (int)$input['id'];
            
            // Soft delete by updating status to deleted (status = 2)
            $result = updateDB("task", ['status' => 2], "id = {$taskId}");
            
            if($result) {
                $response['ok'] = true;
                $response['data'] = ['message' => 'Task deleted successfully'];
            } else {
                throw new Exception('Failed to delete task');
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
