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

// Only administrators can manage employees (assuming userType = 0 for admin)
if($userType != 0) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'data' => 'Access denied. Only administrators can manage employees.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$response = ['ok' => false, 'data' => null];

try {
    switch($method) {
        case 'GET':
            if(isset($_GET['id'])) {
                // Get single employee
                $employeeId = (int)$_GET['id'];
                $employee = selectDB("employee", "id = {$employeeId} AND status = 0");
                
                if($employee && is_array($employee) && count($employee) > 0) {
                    // Remove sensitive information
                    unset($employee[0]['password'], $employee[0]['hash']);
                    $response['ok'] = true;
                    $response['data'] = $employee[0];
                } else {
                    throw new Exception('Employee not found');
                }
            } else {
                // Get all employees
                $employees = selectDB("employee", "status = 0 ORDER BY id DESC");
                
                if($employees && is_array($employees)) {
                    // Remove sensitive information
                    foreach($employees as &$employee) {
                        unset($employee['password'], $employee['hash']);
                    }
                }
                
                $response['ok'] = true;
                $response['data'] = $employees ?: [];
            }
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if(!$input) {
                throw new Exception('Invalid JSON data');
            }
            
            // Validate required fields
            if(empty($input['name'])) {
                throw new Exception('Employee name is required');
            }
            
            if(empty($input['username'])) {
                throw new Exception('Username is required');
            }
            
            if(empty($input['email'])) {
                throw new Exception('Email is required');
            }
            
            // Validate email format
            if(!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format');
            }
            
            // Check if username already exists
            $existingUser = selectDB("employee", "username = '{$input['username']}' AND status = 0");
            if($existingUser && is_array($existingUser) && count($existingUser) > 0) {
                throw new Exception('Username already exists');
            }
            
            // Check if email already exists
            $existingEmail = selectDB("employee", "email = '{$input['email']}' AND status = 0");
            if($existingEmail && is_array($existingEmail) && count($existingEmail) > 0) {
                throw new Exception('Email already exists');
            }
            
            $employeeData = [
                'userId' => $userId,
                'name' => $input['name'],
                'username' => $input['username'],
                'email' => $input['email'],
                'phone' => $input['phone'] ?? '',
                'department' => $input['department'] ?? '',
                'hash' => '',
                'status' => 0
            ];
            
            if(isset($input['id']) && !empty($input['id'])) {
                // Update existing employee
                $employeeId = (int)$input['id'];
                
                // Check if username already exists (excluding current employee)
                $existingUser = selectDB("employee", "username = '{$input['username']}' AND status = 0 AND id != {$employeeId}");
                if($existingUser && is_array($existingUser) && count($existingUser) > 0) {
                    throw new Exception('Username already exists');
                }
                
                // Check if email already exists (excluding current employee)
                $existingEmail = selectDB("employee", "email = '{$input['email']}' AND status = 0 AND id != {$employeeId}");
                if($existingEmail && is_array($existingEmail) && count($existingEmail) > 0) {
                    throw new Exception('Email already exists');
                }
                
                unset($employeeData['userId']);
                
                // Handle password update
                if(!empty($input['password'])) {
                    $employeeData['password'] = sha1($input['password']);
                }
                
                $result = updateDB("employee", $employeeData, "id = {$employeeId}");
                
                if($result) {
                    $response['ok'] = true;
                    $response['data'] = ['message' => 'Employee updated successfully'];
                } else {
                    throw new Exception('Failed to update employee');
                }
            } else {
                // Create new employee
                if(empty($input['password'])) {
                    throw new Exception('Password is required for new employees');
                }
                
                if(strlen($input['password']) < 6) {
                    throw new Exception('Password must be at least 6 characters long');
                }
                
                $employeeData['password'] = sha1($input['password']);
                
                $result = insertDB("employee", $employeeData);
                
                if($result) {
                    $response['ok'] = true;
                    $response['data'] = ['message' => 'Employee created successfully'];
                } else {
                    throw new Exception('Failed to create employee');
                }
            }
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if(!$input || empty($input['id'])) {
                throw new Exception('Employee ID is required');
            }
            
            $employeeId = (int)$input['id'];
            
            // Validate required fields
            if(empty($input['name'])) {
                throw new Exception('Employee name is required');
            }
            
            if(empty($input['username'])) {
                throw new Exception('Username is required');
            }
            
            if(empty($input['email'])) {
                throw new Exception('Email is required');
            }
            
            // Validate email format
            if(!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format');
            }
            
            // Check if username already exists (excluding current employee)
            $existingUser = selectDB("employee", "username = '{$input['username']}' AND status = 0 AND id != {$employeeId}");
            if($existingUser && is_array($existingUser) && count($existingUser) > 0) {
                throw new Exception('Username already exists');
            }
            
            // Check if email already exists (excluding current employee)
            $existingEmail = selectDB("employee", "email = '{$input['email']}' AND status = 0 AND id != {$employeeId}");
            if($existingEmail && is_array($existingEmail) && count($existingEmail) > 0) {
                throw new Exception('Email already exists');
            }
            
            $employeeData = [
                'name' => $input['name'],
                'username' => $input['username'],
                'email' => $input['email'],
                'phone' => $input['phone'] ?? '',
                'department' => $input['department'] ?? ''
            ];
            
            // Handle password update
            if(!empty($input['password'])) {
                if(strlen($input['password']) < 6) {
                    throw new Exception('Password must be at least 6 characters long');
                }
                $employeeData['password'] = sha1($input['password']);
            }
            
            $result = updateDB("employee", $employeeData, "id = {$employeeId}");
            
            if($result) {
                $response['ok'] = true;
                $response['data'] = ['message' => 'Employee updated successfully'];
            } else {
                throw new Exception('Failed to update employee');
            }
            break;
            
        case 'DELETE':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if(!$input || empty($input['id'])) {
                throw new Exception('Employee ID is required');
            }
            
            $employeeId = (int)$input['id'];
            
            // Check if employee has active tasks
            $activeTasks = getTotals("task", "to = {$employeeId} AND status != 2");
            if($activeTasks > 0) {
                throw new Exception('Cannot delete employee with active tasks. Please reassign or complete all tasks first.');
            }
            
            // Soft delete by updating status
            $result = updateDB("employee", ['status' => 1], "id = {$employeeId}");
            
            if($result) {
                $response['ok'] = true;
                $response['data'] = ['message' => 'Employee deleted successfully'];
            } else {
                throw new Exception('Failed to delete employee');
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
