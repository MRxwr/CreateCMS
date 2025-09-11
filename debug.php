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
?>
