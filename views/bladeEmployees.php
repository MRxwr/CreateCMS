<?php
// Only show employees page to administrators
if($userType != 0) {
    echo '<div class="alert alert-warning">Access denied. Only administrators can view this page.</div>';
    return;
}

$employees = selectDB("employees", "status = '0' ORDER BY id DESC");
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 text-primary">Team Management</h1>
                    <p class="text-muted">Manage your team members and their roles</p>
                </div>
                <button class="btn btn-warning btn-lg" onclick="showAddModal('employee')">
                    <i class="bi bi-person-plus"></i> Add Employee
                </button>
            </div>
        </div>
    </div>

    <!-- Team Statistics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body text-center">
                    <i class="bi bi-people display-4 mb-3"></i>
                    <h2><?php echo count($employees ?: []); ?></h2>
                    <p class="mb-0">Total Employees</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle display-4 mb-3"></i>
                    <?php
                    $activeTasks = 0;
                    if($employees) {
                        foreach($employees as $employee) {
                            $activeTasks += getTotals("tasks", "to = {$employee['id']} AND status = 'DOING'");
                        }
                    }
                    ?>
                    <h2><?php echo $activeTasks; ?></h2>
                    <p class="mb-0">Active Tasks</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-trophy display-4 mb-3"></i>
                    <?php
                    $completedTasks = 0;
                    if($employees) {
                        foreach($employees as $employee) {
                            $completedTasks += getTotals("tasks", "to = {$employee['id']} AND status = 'FINISHED'");
                        }
                    }
                    ?>
                    <h2><?php echo $completedTasks; ?></h2>
                    <p class="mb-0">Completed Tasks</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-graph-up display-4 mb-3"></i>
                    <?php
                    $avgProductivity = 0;
                    if($employees && count($employees) > 0) {
                        $totalTasks = 0;
                        $totalCompleted = 0;
                        foreach($employees as $employee) {
                            $empTotal = getTotals("tasks", "to = {$employee['id']} AND status != 'DELETED'");
                            $empCompleted = getTotals("tasks", "to = {$employee['id']} AND status = 'FINISHED'");
                            $totalTasks += $empTotal;
                            $totalCompleted += $empCompleted;
                        }
                        if($totalTasks > 0) {
                            $avgProductivity = round(($totalCompleted / $totalTasks) * 100);
                        }
                    }
                    ?>
                    <h2><?php echo $avgProductivity; ?>%</h2>
                    <p class="mb-0">Avg. Productivity</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Employees Grid -->
    <div class="row">
        <?php if($employees && is_array($employees)): ?>
            <?php foreach($employees as $employee): ?>
            <?php
            // Get employee statistics
            $totalTasks = getTotals("tasks", "to = {$employee['id']} AND status != 'DELETED'");
            $completedTasks = getTotals("tasks", "to = {$employee['id']} AND status = 'FINISHED'");
            $activeTasks = getTotals("tasks", "to = {$employee['id']} AND status = 'DOING'");
            $productivity = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
            ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <!-- Employee Avatar -->
                        <div class="avatar-circle bg-primary text-white mx-auto mb-3" style="width: 80px; height: 80px; font-size: 24px;">
                            <?php echo strtoupper(substr($employee['name'], 0, 2)); ?>
                        </div>
                        
                        <!-- Employee Info -->
                        <h5 class="card-title"><?php echo htmlspecialchars($employee['name']); ?></h5>
                        <p class="text-muted mb-2">@<?php echo htmlspecialchars($employee['username']); ?></p>
                        
                        <?php if($employee['department']): ?>
                        <span class="badge bg-secondary mb-3"><?php echo htmlspecialchars($employee['department']); ?></span>
                        <?php endif; ?>
                        
                        <!-- Contact Info -->
                        <div class="text-start mb-3">
                            <small class="text-muted d-block">
                                <i class="bi bi-envelope"></i> <?php echo htmlspecialchars($employee['email']); ?>
                            </small>
                            <?php if($employee['phone']): ?>
                            <small class="text-muted d-block">
                                <i class="bi bi-telephone"></i> <?php echo htmlspecialchars($employee['phone']); ?>
                            </small>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Task Statistics -->
                        <div class="row text-center mb-3">
                            <div class="col-4">
                                <div class="border-end">
                                    <div class="h6 mb-0 text-primary"><?php echo $totalTasks; ?></div>
                                    <small class="text-muted">Total</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border-end">
                                    <div class="h6 mb-0 text-info"><?php echo $activeTasks; ?></div>
                                    <small class="text-muted">Active</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="h6 mb-0 text-success"><?php echo $completedTasks; ?></div>
                                <small class="text-muted">Done</small>
                            </div>
                        </div>
                        
                        <!-- Productivity Bar -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted">Productivity</small>
                                <small class="text-muted"><?php echo $productivity; ?>%</small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $productivity; ?>%"></div>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="viewEmployee(<?php echo $employee['id']; ?>)">
                                <i class="bi bi-eye"></i> View
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="editEmployee(<?php echo $employee['id']; ?>)">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-info" onclick="viewEmployeeTasks(<?php echo $employee['id']; ?>)">
                                <i class="bi bi-list-task"></i> Tasks
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-people display-1 text-muted"></i>
                    <h3 class="text-muted">No Employees Found</h3>
                    <p class="text-muted">Start building your team by adding your first employee.</p>
                    <button class="btn btn-warning btn-lg" onclick="showAddModal('employee')">
                        <i class="bi bi-person-plus"></i> Add Your First Employee
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Team Performance Chart -->
    <?php if($employees && count($employees) > 0): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bar-chart"></i> Team Performance Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Total Tasks</th>
                                    <th>Active Tasks</th>
                                    <th>Completed Tasks</th>
                                    <th>Productivity</th>
                                    <th>Last Activity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($employees as $employee): ?>
                                <?php
                                $totalTasks = getTotals("tasks", "to = {$employee['id']} AND status != 'DELETED'");
                                $completedTasks = getTotals("tasks", "to = {$employee['id']} AND status = 'FINISHED'");
                                $activeTasks = getTotals("tasks", "to = {$employee['id']} AND status = 'DOING'");
                                $productivity = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                                
                                // Get last activity (last task update)
                                $lastActivity = selectDB2("MAX(date) as last_date", "tasks", "to = {$employee['id']}");
                                $lastDate = $lastActivity && $lastActivity[0]['last_date'] ? $lastActivity[0]['last_date'] : null;
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                                <?php echo strtoupper(substr($employee['name'], 0, 2)); ?>
                                            </div>
                                            <div>
                                                <strong><?php echo htmlspecialchars($employee['name']); ?></strong>
                                                <br><small class="text-muted">@<?php echo htmlspecialchars($employee['username']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo $employee['department'] ? htmlspecialchars($employee['department']) : '<span class="text-muted">Not set</span>'; ?></td>
                                    <td><span class="badge bg-primary"><?php echo $totalTasks; ?></span></td>
                                    <td><span class="badge bg-info"><?php echo $activeTasks; ?></span></td>
                                    <td><span class="badge bg-success"><?php echo $completedTasks; ?></span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress me-2" style="width: 60px; height: 8px;">
                                                <div class="progress-bar bg-success" style="width: <?php echo $productivity; ?>%"></div>
                                            </div>
                                            <small><?php echo $productivity; ?>%</small>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if($lastDate): ?>
                                            <small class="text-muted"><?php echo date('M d, Y', strtotime($lastDate)); ?></small>
                                        <?php else: ?>
                                            <small class="text-muted">No activity</small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// View employee details
function viewEmployee(id) {
    fetch(`requests/apiEmployees.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                const employee = data.data;
                const content = `
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> ${employee.name}</p>
                            <p><strong>Username:</strong> ${employee.username}</p>
                            <p><strong>Email:</strong> ${employee.email}</p>
                            <p><strong>Phone:</strong> ${employee.phone || 'Not provided'}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Department:</strong> ${employee.department || 'Not assigned'}</p>
                            <p><strong>Status:</strong> <span class="badge bg-success">Active</span></p>
                            <p><strong>Joined:</strong> ${new Date(employee.created_at || employee.date).toLocaleDateString()}</p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button class="btn btn-primary" onclick="viewEmployeeTasks(${id})">
                            <i class="bi bi-list-task"></i> View Tasks
                        </button>
                        <button class="btn btn-secondary" onclick="editEmployee(${id})">
                            <i class="bi bi-pencil"></i> Edit Employee
                        </button>
                    </div>
                `;
                
                const modal = createModal('viewEmployeeModal', `Employee - ${employee.name}`, content);
                modal.show();
            }
        })
        .catch(error => showToast('Error loading employee details', 'danger'));
}

// Edit employee
function editEmployee(id) {
    fetch(`requests/apiEmployees.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                const employee = data.data;
                const content = getEmployeeForm(employee) + `<input type="hidden" name="id" value="${id}">`;
                const modal = createModal('editEmployeeModal', `Edit Employee - ${employee.name}`, content);
                modal.show();
            }
        })
        .catch(error => showToast('Error loading employee details', 'danger'));
}

// View employee tasks
function viewEmployeeTasks(id) {
    window.location.href = `?v=Tasks&employee=${id}`;
}
</script>
