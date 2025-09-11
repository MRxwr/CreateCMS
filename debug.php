<?php
require_once('admin/includes/config.php');
require_once('admin/includes/functions.php');

echo "<h2>Database Connection Test</h2>";

// Test basic connection
if ($dbconnect->connect_error) {
    echo "<p style='color: red;'>Database connection failed: " . $dbconnect->connect_error . "</p>";
} else {
    echo "<p style='color: green;'>Database connected successfully!</p>";
    echo "<p>Database name: " . $dbname . "</p>";
}

// Test if tables exist
$tables = ['client', 'project', 'task', 'employee'];
foreach ($tables as $table) {
    $result = $dbconnect->query("SHOW TABLES LIKE '$table'");
    if ($result && $result->num_rows > 0) {
        echo "<p style='color: green;'>Table '$table' exists</p>";
        
        // Count records
        $count_result = $dbconnect->query("SELECT COUNT(*) as count FROM $table");
        if ($count_result) {
            $count = $count_result->fetch_assoc()['count'];
            echo "<p>Records in '$table': $count</p>";
        }
    } else {
        echo "<p style='color: red;'>Table '$table' does not exist</p>";
    }
}

// Test getTotals function
echo "<h3>Testing getTotals function:</h3>";
if (function_exists('getTotals')) {
    $totalClients = getTotals("client", "1=1");
    $totalProjects = getTotals("project", "1=1"); 
    $totalTasks = getTotals("task", "1=1");
    $totalEmployees = getTotals("employee", "1=1");
    
    echo "<p>Total Clients: $totalClients</p>";
    echo "<p>Total Projects: $totalProjects</p>";
    echo "<p>Total Tasks: $totalTasks</p>";
    echo "<p>Total Employees: $totalEmployees</p>";
} else {
    echo "<p style='color: red;'>getTotals function not found</p>";
}

// Test selectDB function  
echo "<h3>Testing selectDB function:</h3>";
if (function_exists('selectDB')) {
    // Test simple selectDB call (2 parameters only)
    $projects = selectDB("project", "1=1 LIMIT 3");
    
    if ($projects && is_array($projects)) {
        echo "<p style='color: green;'>selectDB function working. Found " . count($projects) . " projects</p>";
        foreach ($projects as $project) {
            echo "<p>Project: " . $project['title'] . " | Status: " . $project['status'] . "</p>";
        }
    } else {
        echo "<p style='color: red;'>selectDB function returned: " . var_export($projects, true) . "</p>";
    }
    
    // Test the JOIN query used in dashboard
    echo "<h4>Testing JOIN query:</h4>";
    $query = "SELECT t.*, p.title as project_title, e.name as employee_name 
              FROM task t 
              LEFT JOIN project p ON t.projectId = p.id 
              LEFT JOIN employee e ON t.to = e.id 
              WHERE t.status != 2 
              ORDER BY t.id DESC 
              LIMIT 3";
    $result = $dbconnect->query($query);
    if ($result && $result->num_rows > 0) {
        echo "<p style='color: green;'>JOIN query working. Found " . $result->num_rows . " tasks</p>";
        while ($row = $result->fetch_assoc()) {
            echo "<p>Task: " . $row['task'] . " | Project: " . ($row['project_title'] ?? 'No project') . " | Employee: " . ($row['employee_name'] ?? 'No employee') . "</p>";
        }
    } else {
        echo "<p style='color: red;'>JOIN query failed or returned no data</p>";
        if ($dbconnect->error) {
            echo "<p style='color: red;'>MySQL Error: " . $dbconnect->error . "</p>";
        }
    }
} else {
    echo "<p style='color: red;'>selectDB function not found</p>";
}

echo "<br><br><h3>Task Status Analysis:</h3>";
$statusQuery = "SELECT status, COUNT(*) as count FROM task GROUP BY status ORDER BY status";
$result = $dbconnect->query($statusQuery);
if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Status</th><th>Count</th><th>Meaning</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $meaning = '';
        switch($row['status']) {
            case 0: $meaning = 'Pending'; break;
            case 1: $meaning = 'In Progress'; break;
            case 2: $meaning = 'Completed/Finished'; break;
            case 3: $meaning = 'On Hold/Returned'; break;
            default: $meaning = 'Unknown'; break;
        }
        echo "<tr><td>{$row['status']}</td><td>{$row['count']}</td><td>{$meaning}</td></tr>";
    }
    echo "</table>";
} else {
    echo "No task status data found";
}

// Test specific project progress calculation
echo "<br><br><h3>Project Progress Test:</h3>";
$projects = selectDB("project", "1=1 LIMIT 3");
if ($projects && is_array($projects)) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Project</th><th>Total Tasks</th><th>Completed Tasks</th><th>Progress</th></tr>";
    foreach ($projects as $project) {
        $totalTasks = getTotals("task", "projectId = {$project['id']}");
        $completedTasks = getTotals("task", "projectId = {$project['id']} AND status = 2");
        $activeTasks = getTotals("task", "projectId = {$project['id']} AND status IN (0,1)");
        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        echo "<tr>";
        echo "<td>{$project['title']}</td>";
        echo "<td>{$totalTasks}</td>";
        echo "<td>{$completedTasks}</td>";
        echo "<td>{$progress}%</td>";
        echo "</tr>";
        echo "<tr><td colspan='4'>Active tasks: {$activeTasks}</td></tr>";
    }
    echo "</table>";
}

echo "<br><br><h3>Testing API Dashboard:</h3>";
echo "<a href='requests/apiDashboard.php' target='_blank'>Test API Dashboard (opens in new tab)</a><br><br>";

echo "<h3>Testing Other APIs:</h3>";
echo "<a href='requests/apiTasks.php' target='_blank'>Test Tasks API</a><br>";
echo "<a href='requests/apiProjects.php' target='_blank'>Test Projects API</a><br>";
echo "<a href='requests/apiEmployees.php' target='_blank'>Test Employees API</a><br>";
echo "<a href='requests/apiLeads.php' target='_blank'>Test Leads/Clients API</a><br>";
echo "<a href='requests/apiComments.php?taskId=1' target='_blank'>Test Comments API (Task ID 1)</a><br><br>";

echo "<h3>Testing Other Pages:</h3>";
echo "<a href='index.php?v=Tasks' target='_blank'>Test Tasks Page</a> - <em>Click on status badges to update, chat icons to access task chat</em><br>";
echo "<a href='index.php?v=Projects' target='_blank'>Test Projects Page</a> - <em>Progress bars should now work correctly</em><br>";
echo "<a href='index.php?v=Employees' target='_blank'>Test Employees Page</a><br>";
echo "<a href='index.php?v=Leads' target='_blank'>Test Leads/Clients Page</a><br>";

echo "<br><h3>New Features Added:</h3>";
echo "<ul>";
echo "<li><strong>Task Status Updates:</strong> Click on any status badge on task cards to change status</li>";
echo "<li><strong>Task Chat:</strong> Click the chat icon on task cards or use the 'Open Chat' button in status modal</li>";
echo "<li><strong>Fixed Progress Bars:</strong> Project progress calculations now include completed tasks in total</li>";
echo "<li><strong>Improved UI:</strong> Task cards have hover effects and visual improvements</li>";
echo "</ul>";
?>
