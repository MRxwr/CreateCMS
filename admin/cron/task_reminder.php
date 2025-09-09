<?php
require_once('../includes/config.php');
require_once('../includes/functions.php');

// Send daily reminders for all pending tasks
sendDailyTaskReminders();

function sendDailyTaskReminders() {    
    // Get all pending tasks
    $sql = "SELECT t.*, u.username, u.phone as userPhone, e.name, e.phone as employeePhone, p.title
            FROM `task` as t
            JOIN `user` as u ON u.id = t.by
            JOIN `employee` as e ON e.id = t.to
            JOIN `project` as p ON p.id = t.projectId
            WHERE t.status = '0' OR t.status = '1'
            ORDER BY t.expected ASC";
    
    $result = $GLOBALS['dbconnect']->query($sql);
    if ($result) {
        $count = 0;
        $employeeTasks = []; // Group tasks by employee
        $userTasks = [];     // Group tasks by user
        
        // Group all pending tasks by employee and user
        while ($row = $result->fetch_assoc()) {
            $daysToDeadline = "Unknown";
            if (!empty($row["expected"])) {
                $expectedDate = new DateTime(substr($row["expected"], 0, 10));
                $currentDate = new DateTime(date('Y-m-d'));
                $interval = $currentDate->diff($expectedDate);
                
                if ($expectedDate < $currentDate) {
                    // Task is overdue
                    $daysToDeadline = "OVERDUE by " . $interval->days . " days";
                } else {
                    // Task is upcoming
                    $daysToDeadline = $interval->days . " days remaining";
                }
            }
            
            // Add task to employee's list
            $employeePhone = $row['employeePhone'];
            if (!isset($employeeTasks[$employeePhone])) {
                $employeeTasks[$employeePhone] = [
                    'name' => $row['name'],
                    'tasks' => []
                ];
            }
            $employeeTasks[$employeePhone]['tasks'][] = [
                'id' => $row['id'],
                'project' => $row['title'],
                'task' => $row['task'],
                'assignedBy' => $row['username'],
                'expectedDate' => substr($row["expected"], 0, 10),
                'deadline' => $daysToDeadline
            ];
            
            // Add task to user's list
            $userPhone = $row['userPhone'];
            if (!isset($userTasks[$userPhone])) {
                $userTasks[$userPhone] = [
                    'username' => $row['username'],
                    'tasks' => []
                ];
            }
            $userTasks[$userPhone]['tasks'][] = [
                'id' => $row['id'],
                'project' => $row['title'],
                'task' => $row['task'],
                'assignedTo' => $row['name'],
                'expectedDate' => substr($row["expected"], 0, 10),
                'deadline' => $daysToDeadline
            ];
            
            $count++;
        }
        
        // Send a single consolidated message to each employee
        foreach ($employeeTasks as $phone => $data) {
            $message = "Daily Task Reminder:\n\nHello {$data['name']},\n\nYou have " . count($data['tasks']) . " pending tasks:\n\n";
            
            foreach ($data['tasks'] as $index => $task) {
                $taskNum = $index + 1;
                $message .= "{$taskNum}. Project: {$task['project']}\n";
                $message .= "   Task: {$task['task']}\n";
                $message .= "   Assigned By: {$task['assignedBy']}\n";
                $message .= "   Expected Date: {$task['expectedDate']}\n";
                $message .= "   Status: {$task['deadline']}\n\n";
            }
            $message .= "Please update the status of your tasks when completed.";
            // Send WhatsApp message to employee
            $response = whatsappUltraMsg($phone, $message);
        }
        // Send a single consolidated message to each user (task creator)
        foreach ($userTasks as $phone => $data) {
            $message = "Daily Task Status Report:\n\nHello {$data['username']},\n\nYou have assigned " . count($data['tasks']) . " pending tasks:\n\n";
            
            foreach ($data['tasks'] as $index => $task) {
                $taskNum = $index + 1;
                $message .= "{$taskNum}. Project: {$task['project']}\n";
                $message .= "   Task: {$task['task']}\n";
                $message .= "   Assigned To: {$task['assignedTo']}\n";
                $message .= "   Expected Date: {$task['expectedDate']}\n";
                $message .= "   Status: {$task['deadline']}\n\n";
            }
            // Send WhatsApp message to user
            $response = whatsappUltraMsg($phone, $message);
        }
    }else{
    }
}
?>
