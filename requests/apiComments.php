<?php
header('Content-Type: application/json');
require_once('../admin/includes/config.php');
require_once('../admin/includes/functions.php');
require_once('../admin/includes/checkLogin.php');

$method = $_SERVER['REQUEST_METHOD'];
$response = ['ok' => false, 'data' => null];

try {
    switch($method) {
        case 'GET':
            if(isset($_GET['taskId'])) {
                // Get comments for a specific task
                $taskId = (int)$_GET['taskId'];
                
                // Verify task exists
                $task = selectDB("task", "id = {$taskId}");
                if(!$task || !is_array($task) || count($task) == 0) {
                    throw new Exception('Task not found');
                }
                
                // Get comments with user information using direct query
                $comments = [];
                $query = "SELECT c.*, COALESCE(u.name, e.name) as user_name, COALESCE(u.username, e.username) as username 
                          FROM comments c 
                          LEFT JOIN user u ON c.userId = u.id 
                          LEFT JOIN employee e ON c.empId = e.id 
                          WHERE c.taskId = {$taskId} AND c.status = 1 
                          ORDER BY c.id ASC";
                $result = $dbconnect->query($query);
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $comments[] = $row;
                    }
                }
                
                $response['ok'] = true;
                $response['data'] = $comments ?: [];
            } else {
                throw new Exception('Task ID is required');
            }
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if(!$input) {
                throw new Exception('Invalid JSON data');
            }
            
            // Validate required fields
            if(empty($input['taskId'])) {
                throw new Exception('Task ID is required');
            }
            
            if(empty($input['comment'])) {
                throw new Exception('Comment message is required');
            }
            
            $taskId = (int)$input['taskId'];
            
            // Verify task exists
            $task = selectDB("task", "id = {$taskId}");
            if(!$task || !is_array($task) || count($task) == 0) {
                throw new Exception('Task not found');
            }
            
            // Check if user is involved in the task (assignee, creator, or admin)
            $taskData = $task[0];
            $hasAccess = false;
            
            if($userType == 0) { // Admin has access to all tasks
                $hasAccess = true;
            } elseif($taskData['to'] == $userId || $taskData['by'] == $userId) { // Assignee or creator
                $hasAccess = true;
            }
            
            if(!$hasAccess) {
                throw new Exception('You do not have permission to comment on this task');
            }
            
            // Determine if user is employee or regular user
            $empId = 0;
            $isEmployee = selectDB("employee", "id = {$userId} AND status = 0");
            if($isEmployee && is_array($isEmployee) && count($isEmployee) > 0) {
                $empId = $userId;
            }
            
            $commentData = [
                'userId' => $userId,
                'empId' => $empId,
                'taskId' => $taskId,
                'send-msg' => trim($input['comment']),
                'type' => 1,
                'status' => 1
            ];
            
            $result = insertDB("comments", $commentData);
            
            if($result) {
                $response['ok'] = true;
                $response['data'] = ['message' => 'Comment added successfully'];
            } else {
                throw new Exception('Failed to add comment');
            }
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if(!$input || empty($input['id'])) {
                throw new Exception('Comment ID is required');
            }
            
            $commentId = (int)$input['id'];
            
            // Get existing comment
            $comment = selectDB("comments", "id = {$commentId}");
            if(!$comment || !is_array($comment) || count($comment) == 0) {
                throw new Exception('Comment not found');
            }
            
            $commentData = $comment[0];
            
            // Check if user owns the comment or is admin
            if($commentData['userId'] != $userId && $userType != 0) {
                throw new Exception('You can only edit your own comments');
            }
            
            if(empty($input['comment'])) {
                throw new Exception('Comment message is required');
            }
            
            $updateData = [
                'send-msg' => trim($input['comment'])
            ];
            
            $result = updateDB("comments", $updateData, "id = {$commentId}");
            
            if($result) {
                $response['ok'] = true;
                $response['data'] = ['message' => 'Comment updated successfully'];
            } else {
                throw new Exception('Failed to update comment');
            }
            break;
            
        case 'DELETE':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if(!$input || empty($input['id'])) {
                throw new Exception('Comment ID is required');
            }
            
            $commentId = (int)$input['id'];
            
            // Get existing comment
            $comment = selectDB("comments", "id = {$commentId}");
            if(!$comment || !is_array($comment) || count($comment) == 0) {
                throw new Exception('Comment not found');
            }
            
            $commentData = $comment[0];
            
            // Check if user owns the comment or is admin
            if($commentData['userId'] != $userId && $userType != 0) {
                throw new Exception('You can only delete your own comments');
            }
            
            // Soft delete by updating status
            $result = updateDB("comments", ['status' => 0], "id = {$commentId}");
            
            if($result) {
                $response['ok'] = true;
                $response['data'] = ['message' => 'Comment deleted successfully'];
            } else {
                throw new Exception('Failed to delete comment');
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
