<?php
// Get projects using correct table name and status system
$projects = selectDB("project", "status != 2 ORDER BY id DESC"); // status 2 = deleted
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 text-primary">Projects Management</h1>
                    <p class="text-muted">Organize and track your project portfolio</p>
                </div>
                <button class="btn btn-success btn-lg" onclick="showAddModal('project')">
                    <i class="bi bi-plus-circle"></i> New Project
                </button>
            </div>
        </div>
    </div>

    <!-- Projects Grid -->
    <div class="row">
        <?php if($projects && is_array($projects)): ?>
            <?php foreach($projects as $project): ?>
            <?php
            // Get project statistics using correct table names and status
            // Get ALL tasks for this project (including completed ones)
            $totalTasks = getTotals("task", "projectId = {$project['id']}"); // all tasks
            $completedTasks = getTotals("task", "projectId = {$project['id']} AND status = 2"); // completed
            $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
            
            // Get team members using direct query (exclude completed/deleted tasks for active team)
            $teamMembers = [];
            $query = "SELECT DISTINCT e.name FROM task t 
                      JOIN employee e ON t.to = e.id 
                      WHERE t.projectId = {$project['id']} AND t.status IN (0,1)"; // pending or in progress
            $result = $dbconnect->query($query);
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $teamMembers[] = $row;
                }
            }
            $teamCount = count($teamMembers);
            ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 priority-<?php echo $project['priority'] ?? 'medium'; ?>">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><?php echo htmlspecialchars($project['title']); ?></h5>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="viewProject(<?php echo $project['id']; ?>)">
                                    <i class="bi bi-eye"></i> View Details
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="editProject(<?php echo $project['id']; ?>)">
                                    <i class="bi bi-pencil"></i> Edit
                                </a></li>
                                <li><a class="dropdown-item" href="?v=Tasks&project=<?php echo $project['id']; ?>">
                                    <i class="bi bi-list-task"></i> View Tasks
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteItem('project', <?php echo $project['id']; ?>, '<?php echo addslashes($project['title']); ?>')">
                                    <i class="bi bi-trash"></i> Delete
                                </a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            <?php echo $project['description'] ? htmlspecialchars(substr($project['description'], 0, 100)) . '...' : 'No description available'; ?>
                        </p>
                        
                        <!-- Project Progress -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted">Progress</small>
                                <small class="text-muted"><?php echo $progress; ?>%</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progress; ?>%"></div>
                            </div>
                        </div>

                        <!-- Project Stats -->
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="border-end">
                                    <div class="h6 mb-0"><?php echo $totalTasks; ?></div>
                                    <small class="text-muted">Tasks</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border-end">
                                    <div class="h6 mb-0"><?php echo $completedTasks; ?></div>
                                    <small class="text-muted">Done</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="h6 mb-0"><?php echo $teamCount; ?></div>
                                <small class="text-muted">Team</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <?php if($project['client']): ?>
                                <small class="text-muted">
                                    <i class="bi bi-building"></i> <?php echo htmlspecialchars($project['client']); ?>
                                </small>
                                <?php endif; ?>
                            </div>
                            <div>
                                <?php if($project['endDate']): ?>
                                <small class="text-muted">
                                    <i class="bi bi-calendar-event"></i> 
                                    <?php echo date('M d, Y', strtotime($project['endDate'])); ?>
                                </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-folder-plus display-1 text-muted"></i>
                    <h3 class="text-muted">No Projects Found</h3>
                    <p class="text-muted">Start organizing your work by creating your first project.</p>
                    <button class="btn btn-success btn-lg" onclick="showAddModal('project')">
                        <i class="bi bi-plus-circle"></i> Create Your First Project
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Project Statistics -->
    <?php if($projects && is_array($projects)): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up"></i> Project Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="text-center">
                                <div class="h2 text-primary"><?php echo count($projects); ?></div>
                                <p class="text-muted mb-0">Total Projects</p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="text-center">
                                <?php
                                $activeProjects = 0;
                                foreach($projects as $project) {
                                    $totalTasks = getTotals("tasks", "projectId = {$project['id']} AND status != 'DELETED'");
                                    $completedTasks = getTotals("tasks", "projectId = {$project['id']} AND status = 'FINISHED'");
                                    if($totalTasks > 0 && $completedTasks < $totalTasks) $activeProjects++;
                                }
                                ?>
                                <div class="h2 text-warning"><?php echo $activeProjects; ?></div>
                                <p class="text-muted mb-0">In Progress</p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="text-center">
                                <?php
                                $completedProjects = 0;
                                foreach($projects as $project) {
                                    $totalTasks = getTotals("tasks", "projectId = {$project['id']} AND status != 'DELETED'");
                                    $completedTasks = getTotals("tasks", "projectId = {$project['id']} AND status = 'FINISHED'");
                                    if($totalTasks > 0 && $completedTasks >= $totalTasks) $completedProjects++;
                                }
                                ?>
                                <div class="h2 text-success"><?php echo $completedProjects; ?></div>
                                <p class="text-muted mb-0">Completed</p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="text-center">
                                <?php
                                $totalProjectTasks = getTotals("tasks", "status != 'DELETED'");
                                ?>
                                <div class="h2 text-info"><?php echo $totalProjectTasks; ?></div>
                                <p class="text-muted mb-0">Total Tasks</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// View project details
function viewProject(id) {
    fetch(`requests/apiProjects.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                const project = data.data;
                const content = `
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Title:</strong> ${project.title}</p>
                            <p><strong>Client:</strong> ${project.client || 'Not specified'}</p>
                            <p><strong>Start Date:</strong> ${project.startDate ? new Date(project.startDate).toLocaleDateString() : 'Not set'}</p>
                            <p><strong>End Date:</strong> ${project.endDate ? new Date(project.endDate).toLocaleDateString() : 'Not set'}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> <span class="badge bg-success">${project.status == '1' ? 'Active' : 'Inactive'}</span></p>
                            <p><strong>Created:</strong> ${new Date(project.created_at || project.date).toLocaleDateString()}</p>
                        </div>
                        <div class="col-12">
                            <p><strong>Description:</strong></p>
                            <p>${project.description || 'No description available'}</p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="?v=Tasks&project=${id}" class="btn btn-primary">
                            <i class="bi bi-list-task"></i> View Tasks
                        </a>
                        <button class="btn btn-secondary" onclick="editProject(${id})">
                            <i class="bi bi-pencil"></i> Edit Project
                        </button>
                    </div>
                `;
                
                const modal = createModal('viewProjectModal', `Project - ${project.title}`, content);
                modal.show();
            }
        })
        .catch(error => showToast('Error loading project details', 'danger'));
}

// Edit project
function editProject(id) {
    fetch(`requests/apiProjects.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                const project = data.data;
                const content = getProjectForm(project) + `<input type="hidden" name="id" value="${id}">`;
                const modal = createModal('editProjectModal', `Edit Project - ${project.title}`, content);
                modal.show();
            }
        })
        .catch(error => showToast('Error loading project details', 'danger'));
}
</script>
