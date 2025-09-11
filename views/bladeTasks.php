<?php
// Get tasks with project and employee information using your live database structure
$whereClause = "t.status != 2"; // status 2 = deleted in your numeric system
if(isset($_GET['project']) && !empty($_GET['project'])) {
    $projectId = (int)$_GET['project'];
    $whereClause .= " AND t.projectId = {$projectId}";
}

// Use direct query since selectJoinDBNew might have issues
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

// Get projects and employees for filters using correct table names
$projects = selectDB("project", "status != 2"); // not deleted
$employees = selectDB("employee", "status = 0"); // active employees

// Status helper functions
function getTaskStatusLabel($status) {
    switch((int)$status) {
        case 0: return 'Pending';
        case 1: return 'In Progress'; 
        case 2: return 'Completed';
        case 3: return 'On Hold';
        default: return 'Unknown';
    }
}

function getTaskStatusClass($status) {
    switch((int)$status) {
        case 0: return 'warning';
        case 1: return 'info';
        case 2: return 'success';
        case 3: return 'secondary';
        default: return 'secondary';
    }
}
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 text-primary">Tasks Management</h1>
                    <p class="text-muted">Track and manage project tasks efficiently</p>
                </div>
                <button class="btn btn-info btn-lg" onclick="showAddModal('task')">
                    <i class="bi bi-plus-circle"></i> Add Task
                </button>
            </div>
        </div>
    </div>

    <!-- Filters and Stats -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <select class="form-control" id="filterProject" onchange="filterTasks()">
                                <option value="">All Projects</option>
                                <?php if($projects): foreach($projects as $project): ?>
                                <option value="<?php echo $project['id']; ?>" <?php echo (isset($_GET['project']) && $_GET['project'] == $project['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($project['title']); ?>
                                </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="filterEmployee" onchange="filterTasks()">
                                <option value="">All Employees</option>
                                <?php if($employees): foreach($employees as $employee): ?>
                                <option value="<?php echo $employee['id']; ?>">
                                    <?php echo htmlspecialchars($employee['name']); ?>
                                </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="filterStatus" onchange="filterTasks()">
                                <option value="">All Status</option>
                                <option value="PENDING">Pending</option>
                                <option value="DOING">In Progress</option>
                                <option value="FINISHED">Completed</option>
                                <option value="RETURNED">Returned</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="filterPriority" onchange="filterTasks()">
                                <option value="">All Priorities</option>
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="row">
                <div class="col-6">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h3><?php echo count($tasks ?: []); ?></h3>
                            <small>Total Tasks</small>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <?php
                            $completedTasks = 0;
                            if($tasks) {
                                foreach($tasks as $task) {
                                    if($task['status'] == 2) $completedTasks++; // status 2 = completed
                                }
                            }
                            ?>
                            <h3><?php echo $completedTasks; ?></h3>
                            <small>Completed</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks Board View -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Task Board</h5>
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="viewMode" id="boardView" checked>
                    <label class="btn btn-outline-primary" for="boardView">
                        <i class="bi bi-kanban"></i> Board
                    </label>
                    <input type="radio" class="btn-check" name="viewMode" id="listView">
                    <label class="btn btn-outline-primary" for="listView">
                        <i class="bi bi-list"></i> List
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Kanban Board -->
    <div id="kanbanBoard" class="row">
        <!-- Pending Column -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">
                        <i class="bi bi-clock"></i> Pending
                        <span class="badge bg-dark ms-2"><?php echo count(array_filter($tasks ?: [], function($t) { return $t['status'] == 0; })); ?></span>
                    </h6>
                </div>
                <div class="card-body p-2" style="max-height: 600px; overflow-y: auto;">
                    <?php if($tasks): foreach($tasks as $task): if($task['status'] == 0): ?>
                        <div class="card mb-2 task-card" data-task-id="<?php echo $task['id']; ?>">
                            <div class="card-body p-2">
                                <h6 class="card-title"><?php echo htmlspecialchars($task['task']); ?></h6>
                                <p class="card-text small text-muted">
                                    Project: <?php echo htmlspecialchars($task['project_title'] ?? 'No Project'); ?><br>
                                    Assigned: <?php echo htmlspecialchars($task['employee_name'] ?? 'Unassigned'); ?><br>
                                    Due: <?php echo date('M d, Y', strtotime($task['expected'] ?? '')); ?>
                                </p>
                                <span class="badge bg-<?php echo getTaskStatusClass($task['status']); ?>">
                                    <?php echo getTaskStatusLabel($task['status']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endif; endforeach; endif; ?>
                </div>
            </div>
        </div>

        <!-- In Progress Column -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-arrow-clockwise"></i> In Progress
                        <span class="badge bg-dark ms-2"><?php echo count(array_filter($tasks ?: [], function($t) { return $t['status'] == 1; })); ?></span>
                    </h6>
                </div>
                <div class="card-body p-2" style="max-height: 600px; overflow-y: auto;">
                    <?php if($tasks): foreach($tasks as $task): if($task['status'] == 1): ?>
                        <div class="card mb-2 task-card" data-task-id="<?php echo $task['id']; ?>">
                            <div class="card-body p-2">
                                <h6 class="card-title"><?php echo htmlspecialchars($task['task']); ?></h6>
                                <p class="card-text small text-muted">
                                    Project: <?php echo htmlspecialchars($task['project_title'] ?? 'No Project'); ?><br>
                                    Assigned: <?php echo htmlspecialchars($task['employee_name'] ?? 'Unassigned'); ?><br>
                                    Due: <?php echo date('M d, Y', strtotime($task['expected'] ?? '')); ?>
                                </p>
                                <span class="badge bg-<?php echo getTaskStatusClass($task['status']); ?>">
                                    <?php echo getTaskStatusLabel($task['status']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endif; endforeach; endif; ?>
                </div>
            </div>
        </div>

        <!-- Completed Column -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-check-circle"></i> Completed
                        <span class="badge bg-dark ms-2"><?php echo count(array_filter($tasks ?: [], function($t) { return $t['status'] == 2; })); ?></span>
                    </h6>
                </div>
                <div class="card-body p-2" style="max-height: 600px; overflow-y: auto;">
                    <?php if($tasks): foreach($tasks as $task): if($task['status'] == 2): ?>
                        <div class="card mb-2 task-card" data-task-id="<?php echo $task['id']; ?>">
                            <div class="card-body p-2">
                                <h6 class="card-title"><?php echo htmlspecialchars($task['task']); ?></h6>
                                <p class="card-text small text-muted">
                                    Project: <?php echo htmlspecialchars($task['project_title'] ?? 'No Project'); ?><br>
                                    Assigned: <?php echo htmlspecialchars($task['employee_name'] ?? 'Unassigned'); ?><br>
                                    Due: <?php echo date('M d, Y', strtotime($task['expected'] ?? '')); ?>
                                </p>
                                <span class="badge bg-<?php echo getTaskStatusClass($task['status']); ?>">
                                    <?php echo getTaskStatusLabel($task['status']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endif; endforeach; endif; ?>
                </div>
            </div>
        </div>
            </div>
        </div>

        <!-- Returned/On Hold Column -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-arrow-return-left"></i> On Hold
                        <span class="badge bg-dark ms-2"><?php echo count(array_filter($tasks ?: [], function($t) { return $t['status'] == 3; })); ?></span>
                    </h6>
                </div>
                <div class="card-body p-2" style="max-height: 600px; overflow-y: auto;">
                    <?php if($tasks): foreach($tasks as $task): if($task['status'] == 3): ?>
                        <div class="card mb-2 task-card" data-task-id="<?php echo $task['id']; ?>">
                            <div class="card-body p-2">
                                <h6 class="card-title"><?php echo htmlspecialchars($task['task']); ?></h6>
                                <p class="card-text small text-muted">
                                    Project: <?php echo htmlspecialchars($task['project_title'] ?? 'No Project'); ?><br>
                                    Assigned: <?php echo htmlspecialchars($task['employee_name'] ?? 'Unassigned'); ?><br>
                                    Due: <?php echo date('M d, Y', strtotime($task['expected'] ?? '')); ?>
                                </p>
                                <span class="badge bg-<?php echo getTaskStatusClass($task['status']); ?>">
                                    <?php echo getTaskStatusLabel($task['status']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endif; endforeach; endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- List View -->
    <div id="listView" class="row d-none">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-task"></i> All Tasks
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($tasks && is_array($tasks)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="tasksTable">
                                <thead>
                                    <tr>
                                        <th>Task</th>
                                        <th>Project</th>
                                        <th>Assigned To</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($tasks as $task): ?>
                                    <tr>
                                        <td>
                                            <div class="priority-<?php echo $task['priority'] ?? 'medium'; ?>">
                                                <strong><?php echo htmlspecialchars($task['task']); ?></strong>
                                                <?php if($task['description']): ?>
                                                <br><small class="text-muted"><?php echo htmlspecialchars(substr($task['description'], 0, 50)); ?>...</small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($task['project_title']); ?></td>
                                        <td><?php echo htmlspecialchars($task['employee_name']); ?></td>
                                        <td>
                                            <span class="badge status-<?php echo strtolower($task['status']); ?>">
                                                <?php echo $task['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $task['priority'] == 'high' ? 'danger' : 
                                                    ($task['priority'] == 'medium' ? 'warning' : 'info'); 
                                            ?>">
                                                <?php echo ucfirst($task['priority'] ?? 'medium'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                            if($task['expected']) {
                                                $dueDate = strtotime($task['expected']);
                                                $isOverdue = $dueDate < time() && $task['status'] != 'FINISHED';
                                                echo '<span class="' . ($isOverdue ? 'text-danger' : '') . '">';
                                                echo date('M d, Y', $dueDate);
                                                echo '</span>';
                                            } else {
                                                echo '<span class="text-muted">No due date</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-primary" onclick="openTaskChat(<?php echo $task['id']; ?>)">
                                                    <i class="bi bi-chat"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-secondary" onclick="editTask(<?php echo $task['id']; ?>)">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('task', <?php echo $task['id']; ?>, '<?php echo addslashes($task['task']); ?>')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-check-square display-1 text-muted"></i>
                            <h3 class="text-muted">No Tasks Found</h3>
                            <p class="text-muted">Start organizing work by creating your first task.</p>
                            <button class="btn btn-info btn-lg" onclick="showAddModal('task')">
                                <i class="bi bi-plus-circle"></i> Create Your First Task
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Task Card Template (used in Kanban board) -->
<?php
function renderTaskCard($task) {
    $priorityColor = $task['priority'] == 'high' ? 'danger' : ($task['priority'] == 'medium' ? 'warning' : 'info');
    $isOverdue = $task['expected'] && strtotime($task['expected']) < time() && $task['status'] != 'FINISHED';
    
    echo '<div class="card mb-2 task-card" data-task-id="' . $task['id'] . '">';
    echo '<div class="card-body p-3">';
    
    // Priority indicator
    echo '<div class="d-flex justify-content-between align-items-start mb-2">';
    echo '<span class="badge bg-' . $priorityColor . '">' . ucfirst($task['priority'] ?? 'medium') . '</span>';
    if($isOverdue) {
        echo '<span class="badge bg-danger">Overdue</span>';
    }
    echo '</div>';
    
    // Task title
    echo '<h6 class="card-title">' . htmlspecialchars($task['task']) . '</h6>';
    
    // Project and assignee
    echo '<small class="text-muted d-block mb-2">';
    echo '<i class="bi bi-folder"></i> ' . htmlspecialchars($task['project_title']);
    echo '</small>';
    
    echo '<small class="text-muted d-block mb-2">';
    echo '<i class="bi bi-person"></i> ' . htmlspecialchars($task['employee_name']);
    echo '</small>';
    
    // Due date
    if($task['expected']) {
        echo '<small class="text-muted d-block mb-2">';
        echo '<i class="bi bi-calendar"></i> ' . date('M d, Y', strtotime($task['expected']));
        echo '</small>';
    }
    
    // Actions
    echo '<div class="d-flex justify-content-between align-items-center mt-2">';
    echo '<button class="btn btn-sm btn-outline-primary" onclick="openTaskChat(' . $task['id'] . ')">';
    echo '<i class="bi bi-chat"></i>';
    echo '</button>';
    echo '<div>';
    echo '<button class="btn btn-sm btn-outline-secondary me-1" onclick="editTask(' . $task['id'] . ')">';
    echo '<i class="bi bi-pencil"></i>';
    echo '</button>';
    echo '<button class="btn btn-sm btn-outline-danger" onclick="deleteItem(\'task\', ' . $task['id'] . ', \'' . addslashes($task['task']) . '\')">';
    echo '<i class="bi bi-trash"></i>';
    echo '</button>';
    echo '</div>';
    echo '</div>';
    
    echo '</div>';
    echo '</div>';
}

// Render task cards for each status (this would be used in the kanban columns)
?>

<script>
// View mode toggle
document.getElementById('boardView').addEventListener('change', function() {
    if(this.checked) {
        document.getElementById('kanbanBoard').classList.remove('d-none');
        document.getElementById('listView').classList.add('d-none');
    }
});

document.getElementById('listView').addEventListener('change', function() {
    if(this.checked) {
        document.getElementById('kanbanBoard').classList.add('d-none');
        document.getElementById('listView').classList.remove('d-none');
    }
});

// Filter tasks
function filterTasks() {
    const project = document.getElementById('filterProject').value;
    const employee = document.getElementById('filterEmployee').value;
    const status = document.getElementById('filterStatus').value;
    const priority = document.getElementById('filterPriority').value;
    
    // Build URL with filters
    let url = '?v=Tasks';
    if(project) url += '&project=' + project;
    if(employee) url += '&employee=' + employee;
    if(status) url += '&status=' + status;
    if(priority) url += '&priority=' + priority;
    
    window.location.href = url;
}

// Edit task
function editTask(id) {
    fetch(`requests/apiTasks.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                const task = data.data;
                const content = getTaskForm(task) + `<input type="hidden" name="id" value="${id}">`;
                const modal = createModal('editTaskModal', `Edit Task - ${task.task}`, content);
                modal.show();
            }
        })
        .catch(error => showToast('Error loading task details', 'danger'));
}

// Update task status (for drag & drop functionality - future enhancement)
function updateTaskStatus(taskId, newStatus) {
    fetch('requests/apiTasks.php', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id: taskId,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.ok) {
            showToast('Task status updated', 'success');
            location.reload();
        } else {
            showToast('Error updating task status', 'danger');
        }
    })
    .catch(error => showToast('Error updating task status', 'danger'));
}
</script>
