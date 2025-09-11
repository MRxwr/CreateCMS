<?php
// Get dashboard statistics
$totalLeads = getTotals("leads", "status = '1'");
$totalProjects = getTotals("projects", "status = '1'");
$totalTasks = getTotals("tasks", "status != 'DELETED'");
$totalEmployees = getTotals("employees", "status = '0'");

// Get recent tasks
$recentTasks = selectDB2("tasks.id, tasks.task, tasks.status, tasks.expected, projects.title as project_title, employees.name as employee_name", 
                        "tasks", 
                        "tasks.projectId = projects.id AND tasks.to = employees.id ORDER BY tasks.id DESC LIMIT 5");

// Get project progress
$projectProgress = selectDB("projects", "status = '1' ORDER BY id DESC LIMIT 5");
?>

<div class="container-fluid">
    <!-- Page Title -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-6 text-primary">Dashboard</h1>
            <p class="text-muted">Welcome back, <?php echo $username; ?>! Here's what's happening with your projects.</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stats-card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-person-plus display-4 mb-3"></i>
                    <div class="stats-number"><?php echo $totalLeads ?: 0; ?></div>
                    <div class="stats-label">Total Leads</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stats-card h-100" style="background: linear-gradient(135deg, #198754, #0f5132);">
                <div class="card-body text-center">
                    <i class="bi bi-folder display-4 mb-3"></i>
                    <div class="stats-number"><?php echo $totalProjects ?: 0; ?></div>
                    <div class="stats-label">Active Projects</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stats-card h-100" style="background: linear-gradient(135deg, #0dcaf0, #087990);">
                <div class="card-body text-center">
                    <i class="bi bi-check-square display-4 mb-3"></i>
                    <div class="stats-number"><?php echo $totalTasks ?: 0; ?></div>
                    <div class="stats-label">Total Tasks</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stats-card h-100" style="background: linear-gradient(135deg, #ffc107, #b08800);">
                <div class="card-body text-center">
                    <i class="bi bi-people display-4 mb-3"></i>
                    <div class="stats-number"><?php echo $totalEmployees ?: 0; ?></div>
                    <div class="stats-label">Team Members</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <!-- Recent Tasks -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history"></i> Recent Tasks
                    </h5>
                    <a href="?v=Tasks" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if($recentTasks && is_array($recentTasks)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Task</th>
                                        <th>Project</th>
                                        <th>Assigned To</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recentTasks as $task): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($task['task']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($task['project_title']); ?></td>
                                        <td><?php echo htmlspecialchars($task['employee_name']); ?></td>
                                        <td>
                                            <span class="badge status-<?php echo strtolower($task['status']); ?>">
                                                <?php echo $task['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                            if($task['expected']) {
                                                echo date('M d, Y', strtotime($task['expected']));
                                            } else {
                                                echo '<span class="text-muted">No due date</span>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <p class="text-muted">No recent tasks found</p>
                            <button class="btn btn-primary" onclick="showAddModal('task')">
                                <i class="bi bi-plus-circle"></i> Create First Task
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning"></i> Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <button class="btn btn-outline-primary btn-lg" onclick="showAddModal('lead')">
                            <i class="bi bi-person-plus"></i>
                            Add New Lead
                        </button>
                        <button class="btn btn-outline-success btn-lg" onclick="showAddModal('project')">
                            <i class="bi bi-folder-plus"></i>
                            Create Project
                        </button>
                        <button class="btn btn-outline-info btn-lg" onclick="showAddModal('task')">
                            <i class="bi bi-plus-square"></i>
                            Add Task
                        </button>
                        <?php if($userType == 0): ?>
                        <button class="btn btn-outline-warning btn-lg" onclick="showAddModal('employee')">
                            <i class="bi bi-person-plus-fill"></i>
                            Add Employee
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Projects Progress -->
    <?php if($projectProgress && is_array($projectProgress)): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up"></i> Project Progress
                    </h5>
                    <a href="?v=Projects" class="btn btn-sm btn-outline-primary">View All Projects</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach($projectProgress as $project): ?>
                        <?php
                        // Calculate project progress based on tasks
                        $totalProjectTasks = getTotals("tasks", "projectId = {$project['id']} AND status != 'DELETED'");
                        $completedProjectTasks = getTotals("tasks", "projectId = {$project['id']} AND status = 'FINISHED'");
                        $progress = $totalProjectTasks > 0 ? round(($completedProjectTasks / $totalProjectTasks) * 100) : 0;
                        ?>
                        <div class="col-lg-6 mb-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title"><?php echo htmlspecialchars($project['title']); ?></h6>
                                        <span class="badge bg-primary"><?php echo $progress; ?>%</span>
                                    </div>
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar" role="progressbar" style="width: <?php echo $progress; ?>%"></div>
                                    </div>
                                    <small class="text-muted">
                                        <?php echo $completedProjectTasks; ?> of <?php echo $totalProjectTasks; ?> tasks completed
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Auto-refresh dashboard every 30 seconds
setInterval(() => {
    // Refresh statistics without full page reload
    fetch('requests/apiDashboard.php')
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                // Update statistics
                document.querySelectorAll('.stats-number').forEach((element, index) => {
                    if (data.data.stats && data.data.stats[index]) {
                        element.textContent = data.data.stats[index];
                    }
                });
            }
        })
        .catch(error => console.error('Error refreshing dashboard:', error));
}, 30000);
</script>
