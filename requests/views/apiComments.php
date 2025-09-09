<?php
// API Comments Endpoint
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
        // Get comments for a specific task
        if (!isset($_GET["taskId"]) || empty($_GET["taskId"])) {
            echo outputError("Task ID is required");
            exit;
        }
        
        $taskId = sanitizeInputData($_GET["taskId"]);
        
        // Check task exists and user has access
        $taskCheck = null;
        
        if ($auth["userType"] == 1) { // Employee
            // Employee can only view comments for tasks assigned to them
            $taskCheck = selectDB("task", "`id` = '{$taskId}' AND `to` = '{$auth["userId"]}'");
        } else { // Admin
            $taskCheck = selectDB("task", "`id` = '{$taskId}'");
        }
        
        if (!$taskCheck) {
            echo outputError("Task not found or access denied");
            exit;
        }
        
        // Get comments with user information
        $sql = "SELECT c.*, 
                CASE 
                    WHEN c.userType = 0 THEN u.username
                    WHEN c.userType = 1 THEN e.name
                    ELSE 'Unknown'
                END as authorName
                FROM `comments` as c
                LEFT JOIN `user` as u ON c.userId = u.id AND c.userType = 0
                LEFT JOIN `employee` as e ON c.userId = e.id AND c.userType = 1
                WHERE c.taskId = '{$taskId}'
                ORDER BY c.date ASC";
        
        $result = $GLOBALS["dbconnect"]->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $comments = [];
            while ($row = $result->fetch_assoc()) {
                $comments[] = $row;
            }
            echo outputData($comments);
        } else {
            echo outputData([]);
        }
        break;
        
    case "POST":
        // Add new comment
        $data = getRequestData();
        
        // Validate required fields
        $missingFields = validateRequiredFields($data, ["taskId", "comment"]);
        if (!empty($missingFields)) {
            echo outputError("Missing required fields: " . implode(", ", $missingFields));
            exit;
        }
        
        $taskId = $data["taskId"];
        
        // Check task exists and user has access
        $taskCheck = null;
        
        if ($auth["userType"] == 1) { // Employee
            // Employee can only comment on tasks assigned to them
            $taskCheck = selectDB("task", "`id` = '{$taskId}' AND `to` = '{$auth["userId"]}'");
        } else { // Admin
            $taskCheck = selectDB("task", "`id` = '{$taskId}'");
        }
        
        if (!$taskCheck) {
            echo outputError("Task not found or access denied");
            exit;
        }
        
        // Prepare data for insertion
        $insertData = [
            "taskId" => $taskId,
            "userId" => $auth["userId"],
            "userType" => $auth["userType"], // 0 for admin, 1 for employee
            "comment" => $data["comment"],
            "date" => date("Y-m-d H:i:s")
        ];
        
        // Insert comment
        if (insertDB("comments", $insertData)) {
            $newCommentId = $GLOBALS["dbconnect"]->insert_id;
            
            // Get the newly created comment
            $sql = "SELECT c.*, 
                    CASE 
                        WHEN c.userType = 0 THEN u.username
                        WHEN c.userType = 1 THEN e.name
                        ELSE 'Unknown'
                    END as authorName
                    FROM `comments` as c
                    LEFT JOIN `user` as u ON c.userId = u.id AND c.userType = 0
                    LEFT JOIN `employee` as e ON c.userId = e.id AND c.userType = 1
                    WHERE c.id = '{$newCommentId}'";
            
            $result = $GLOBALS["dbconnect"]->query($sql);
            
            if ($result && $result->num_rows > 0) {
                $newComment = $result->fetch_assoc();
                
                // Get task details for notification
                $task = selectDB("task", "`id` = '{$taskId}'");
                
                // Determine recipient (if employee commented, notify admin and vice versa)
                $recipientId = null;
                $recipientPhone = null;
                $recipientType = null;
                $authorName = null;
                
                if ($auth["userType"] == 0) {
                    // Admin commented, notify employee
                    $recipientId = $task[0]["to"];
                    $recipientType = "employee";
                    $employee = selectDB("employee", "`id` = '{$recipientId}'");
                    $recipientPhone = $employee[0]["phone"];
                    $authorName = $auth["username"];
                } else {
                    // Employee commented, notify admin
                    $recipientId = $task[0]["by"];
                    $recipientType = "user";
                    $user = selectDB("user", "`id` = '{$recipientId}'");
                    $recipientPhone = $user[0]["phone"];
                    $authorName = selectDB("employee", "`id` = '{$auth["userId"]}'")[0]["name"];
                }
                
                // Get project details for the notification
                $project = selectDB("project", "`id` = '{$task[0]["projectId"]}'");
                
                // Send notification
                if ($recipientPhone) {
                    $Msg = "New Comment on Task:\n\nProject: {$project[0]["title"]}\nTask: {$task[0]["task"]}\nFrom: {$authorName}\nComment: {$data["comment"]}";
                    whatsappUltraMsg($recipientPhone, $Msg);
                }
                
                echo outputData($newComment);
            } else {
                echo outputData(["message" => "Comment added successfully", "commentId" => $newCommentId]);
            }
        } else {
            echo outputError("Failed to add comment");
        }
        break;
        
    case "DELETE":
        // Delete a comment (only owner or admin can delete)
        if (!isset($_GET["id"]) || empty($_GET["id"])) {
            echo outputError("Comment ID is required");
            exit;
        }
        
        $commentId = sanitizeInputData($_GET["id"]);
        
        // Get the comment
        $comment = selectDB("comments", "`id` = '{$commentId}'");
        
        if (!$comment) {
            echo outputError("Comment not found");
            exit;
        }
        
        // Check if user has permission to delete
        if ($auth["userType"] == 1 && ($comment[0]["userId"] != $auth["userId"] || $comment[0]["userType"] != $auth["userType"])) {
            echo outputError("Access denied. You can only delete your own comments.");
            exit;
        }
        
        // Delete the comment
        if (deleteDB("comments", "`id` = '{$commentId}'")) {
            echo outputData(["message" => "Comment deleted successfully"]);
        } else {
            echo outputError("Failed to delete comment");
        }
        break;
        
    default:
        echo outputError("Method not allowed");
        break;
}
?>
