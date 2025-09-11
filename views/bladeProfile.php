<?php
// User profile view
$user = getCurrentUser();
if (!$user) {
    echo '<div class="alert alert-danger">Unable to load user profile.</div>';
    return;
}

// Get full user details from database
if ($user['type'] == 0) {
    $userDetails = selectDB("user", "id = " . $user['id']);
} else {
    $userDetails = selectDB("employee", "id = " . $user['id']);
}

$userDetails = $userDetails[0] ?? [];
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 text-primary">My Profile</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="?v=Home">Home</a></li>
                            <li class="breadcrumb-item active">Profile</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Profile Information -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header text-center">
                    <div class="avatar-circle bg-primary text-white mx-auto mb-3" style="width: 80px; height: 80px; font-size: 32px;">
                        <?php echo strtoupper(substr($user['name'], 0, 2)); ?>
                    </div>
                    <h5 class="card-title mb-1"><?php echo htmlspecialchars($user['name']); ?></h5>
                    <p class="card-text text-muted">@<?php echo htmlspecialchars($user['username']); ?></p>
                    <span class="badge bg-<?php echo $user['type'] == 0 ? 'primary' : 'info'; ?>">
                        <?php echo $user['type'] == 0 ? 'User' : 'Employee'; ?>
                    </span>
                </div>
                
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="d-flex flex-column">
                                <strong class="text-primary"><?php echo getTotals("task", "by = {$user['id']} OR toUser = {$user['id']}"); ?></strong>
                                <small class="text-muted">Tasks</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="d-flex flex-column">
                                <strong class="text-success"><?php echo getTotals("project", $user['type'] == 0 ? "userId = {$user['id']}" : "1=1"); ?></strong>
                                <small class="text-muted">Projects</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="d-flex flex-column">
                                <strong class="text-info"><?php echo getTotals("comments", $user['type'] == 0 ? "userId = {$user['id']}" : "empId = {$user['id']}"); ?></strong>
                                <small class="text-muted">Comments</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Details -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-lines-fill"></i> Profile Information
                    </h5>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Full Name</label>
                            <div class="form-control-plaintext fw-bold">
                                <?php echo htmlspecialchars($userDetails['name'] ?? 'Not provided'); ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Username</label>
                            <div class="form-control-plaintext fw-bold">
                                @<?php echo htmlspecialchars($userDetails['username'] ?? 'Not provided'); ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Email</label>
                            <div class="form-control-plaintext fw-bold">
                                <?php echo htmlspecialchars($userDetails['email'] ?? 'Not provided'); ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Phone</label>
                            <div class="form-control-plaintext fw-bold">
                                <?php echo htmlspecialchars($userDetails['phone'] ?? 'Not provided'); ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Account Type</label>
                            <div class="form-control-plaintext fw-bold">
                                <span class="badge bg-<?php echo $user['type'] == 0 ? 'primary' : 'info'; ?>">
                                    <?php echo $user['type'] == 0 ? 'Administrator/User' : 'Employee'; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Member Since</label>
                            <div class="form-control-plaintext fw-bold">
                                <?php 
                                $joinDate = $userDetails['date'] ?? date('Y-m-d');
                                echo date('F j, Y', strtotime($joinDate)); 
                                ?>
                            </div>
                        </div>
                        
                        <?php if ($user['type'] == 1 && isset($userDetails['department'])): ?>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Department</label>
                            <div class="form-control-plaintext fw-bold">
                                <?php echo htmlspecialchars($userDetails['department'] ?? 'Not assigned'); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" onclick="editProfile()">
                            <i class="bi bi-pencil"></i> Edit Profile
                        </button>
                        <button class="btn btn-outline-secondary" onclick="changePassword()">
                            <i class="bi bi-shield-lock"></i> Change Password
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function editProfile() {
    showToast('Profile editing feature coming soon!', 'info');
}

function changePassword() {
    showToast('Password change feature coming soon!', 'info');
}
</script>
