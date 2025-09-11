<?php
// Note: Authentication is handled in index.php before including this file
// Global variables available: $userId, $userType, $username, $currentUser
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CreateCMS - Dashboard</title>
    <meta name="description" content="CreateCMS Dashboard Application">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Styles -->
    <?php require_once('templates/styles.php'); ?>
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#0d6efd">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="apple-touch-icon" href="img/logo.png">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="img/logo.png">
</head>
<body>
    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="d-none">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="?v=Home">
                <img src="img/logo.png" alt="CreateCMS" width="32" height="32" class="me-2">
                CreateCMS
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo (!isset($_GET['v']) || $_GET['v'] == 'Home') ? 'active' : ''; ?>" href="?v=Home">
                            <i class="bi bi-house-door"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($_GET['v']) && $_GET['v'] == 'Leads') ? 'active' : ''; ?>" href="?v=Leads">
                            <i class="bi bi-person-plus"></i> Clients
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($_GET['v']) && $_GET['v'] == 'Projects') ? 'active' : ''; ?>" href="?v=Projects">
                            <i class="bi bi-folder"></i> Projects
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($_GET['v']) && $_GET['v'] == 'Tasks') ? 'active' : ''; ?>" href="?v=Tasks">
                            <i class="bi bi-check-square"></i> Tasks
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($_GET['v']) && $_GET['v'] == 'Employees') ? 'active' : ''; ?>" href="?v=Employees">
                            <i class="bi bi-people"></i> Employees
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> 
                            <?php echo htmlspecialchars($currentUser['name'] ?? $currentUser['username']); ?>
                            <small class="text-light opacity-75">
                                (<?php echo $currentUser['type'] == 0 ? 'User' : 'Employee'; ?>)
                            </small>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="dropdown-header">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        <?php echo strtoupper(substr($currentUser['name'] ?? $currentUser['username'], 0, 2)); ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold"><?php echo htmlspecialchars($currentUser['name'] ?? 'User'); ?></div>
                                        <small class="text-muted">@<?php echo htmlspecialchars($currentUser['username']); ?></small>
                                    </div>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" onclick="showProfile()"><i class="bi bi-person"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="#" onclick="showSettings()"><i class="bi bi-gear"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid mt-5 pt-3">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-2 d-none d-lg-block">
                <div class="sidebar bg-light p-3 rounded shadow-sm">
                    <h6 class="sidebar-heading">Quick Actions</h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="showAddModal('lead')">
                            <i class="bi bi-plus-circle"></i> Add Client
                        </button>
                        <button class="btn btn-outline-success btn-sm" onclick="showAddModal('project')">
                            <i class="bi bi-plus-circle"></i> Add Project
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="showAddModal('task')">
                            <i class="bi bi-plus-circle"></i> Add Task
                        </button>
                        <?php if($userType == 0): // Only admins can add employees ?>
                        <button class="btn btn-outline-warning btn-sm" onclick="showAddModal('employee')">
                            <i class="bi bi-plus-circle"></i> Add Employee
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Main Content Area -->
            <div class="col-lg-10">
                <div id="main-content">