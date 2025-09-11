<?php
header('Content-Type: application/json');
require_once('../admin/includes/config.php');
require_once('../admin/includes/functions.php');
require_once('../admin/includes/checkLogin.php');

$method = $_SERVER['REQUEST_METHOD'];
$response = ['ok' => false, 'data' => null];

try {
    if($method !== 'GET') {
        throw new Exception('Method not allowed');
    }
    
    // Get dashboard statistics based on your live DB structure
    $stats = [];
    
    // Total clients
    $stats['clients'] = getTotals("client", "1=1");
    
    // Total projects
    $stats['projects'] = getTotals("project", "1=1");
    
    // Total tasks (excluding deleted status = 2)
    $stats['tasks'] = getTotals("task", "status != 2");
    
    // Total employees (active status = 0)
    $stats['employees'] = getTotals("employee", "status = 0");
    
    // Recent tasks (last 5)
    $recentTasks = selectDB("task t LEFT JOIN project p ON t.projectId = p.id LEFT JOIN employee e ON t.to = e.id", 
        "t.status != 2 ORDER BY t.id DESC LIMIT 5", 
        "t.id, t.task, t.status, t.expected, t.date, p.title as project_title, e.name as employee_name");
    
    // Task status breakdown (based on your numeric status system)
    $taskStatusBreakdown = [
        'pending' => getTotals("task", "status = 0"),
        'doing' => getTotals("task", "status = 1"), 
        'finished' => getTotals("task", "status = 2"),
        'returned' => getTotals("task", "status = 3") // if you use this status
    ];
    
    // Project progress overview
    $projects = selectDB("project", "1=1 ORDER BY id DESC LIMIT 5");
    $projectProgress = [];
    
    if($projects && is_array($projects)) {
        foreach($projects as $project) {
            $totalTasks = getTotals("task", "projectId = {$project['id']} AND status != 2");
            $completedTasks = getTotals("task", "projectId = {$project['id']} AND status = 2");
            $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
            
            $projectProgress[] = [
                'id' => $project['id'],
                'title' => $project['title'],
                'totalTasks' => $totalTasks,
                'completedTasks' => $completedTasks,
                'progress' => $progress,
                'price' => $project['price'] ?? 0
            ];
        }
    }
    
    // Employee productivity (if user is admin - assuming type 0 = admin)
    $employeeProductivity = [];
    if($userType == 0) {
        $employees = selectDB("employee", "status = 0 ORDER BY id DESC LIMIT 5");
        
        if($employees && is_array($employees)) {
            foreach($employees as $employee) {
                $totalTasks = getTotals("task", "to = {$employee['id']} AND status != 2");
                $completedTasks = getTotals("task", "to = {$employee['id']} AND status = 2");
                $activeTasks = getTotals("task", "to = {$employee['id']} AND status = 1");
                $productivity = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                
                $employeeProductivity[] = [
                    'id' => $employee['id'],
                    'name' => $employee['name'],
                    'department' => $employee['department'] ?? '',
                    'totalTasks' => $totalTasks,
                    'completedTasks' => $completedTasks,
                    'activeTasks' => $activeTasks,
                    'productivity' => $productivity
                ];
            }
        }
    }
    
    // Overdue tasks
    $currentDate = date('Y-m-d H:i:s');
    $overdueTasks = selectDB("task t LEFT JOIN project p ON t.projectId = p.id LEFT JOIN employee e ON t.to = e.id", 
        "t.status != 2 AND t.status != 2 AND t.expected < '{$currentDate}' ORDER BY t.expected ASC LIMIT 10", 
        "t.id, t.task, t.status, t.expected, p.title as project_title, e.name as employee_name");
    
    // Recent activity (comments from last 24 hours)
    $yesterday = date('Y-m-d H:i:s', strtotime('-24 hours'));
    $recentComments = selectDB("comments c LEFT JOIN task t ON c.taskId = t.id LEFT JOIN user u ON c.userId = u.id LEFT JOIN employee e ON c.empId = e.id", 
        "c.date >= '{$yesterday}' AND c.status = 1 ORDER BY c.id DESC LIMIT 10", 
        "c.*, t.task as task_title, COALESCE(u.name, e.name) as user_name");
    
    // Recent invoices (if you want to include them)
    $recentInvoices = selectDB("invoice i LEFT JOIN project p ON i.projectId = p.id", 
        "1=1 ORDER BY i.id DESC LIMIT 5", 
        "i.*, p.title as project_title");
    
    $dashboardData = [
        'stats' => [
            $stats['clients'],
            $stats['projects'], 
            $stats['tasks'],
            $stats['employees']
        ],
        'detailed_stats' => $stats,
        'recent_tasks' => $recentTasks ?: [],
        'task_status_breakdown' => $taskStatusBreakdown,
        'project_progress' => $projectProgress,
        'employee_productivity' => $employeeProductivity,
        'overdue_tasks' => $overdueTasks ?: [],
        'recent_activity' => $recentComments ?: [],
        'recent_invoices' => $recentInvoices ?: [],
        'user_info' => [
            'id' => $userId,
            'username' => $username,
            'type' => $userType
        ]
    ];
    
    $response['ok'] = true;
    $response['data'] = $dashboardData;
    
} catch(Exception $e) {
    $response['ok'] = false;
    $response['data'] = $e->getMessage();
    http_response_code(400);
}

echo json_encode($response);
?>
