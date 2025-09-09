<?php
require_once("../../includes/functions.php");
require_once("../../includes/checkLogin.php");

// Make sure only admin can access this page
if ( !isset($_SESSION['adminId']) || $_SESSION['userType'] != 0 ) {
    header('Location: ../../login.php');
    exit();
}

// Process user approval or rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['userId'])) {
        $userId = intval($_POST['userId']);
        
        if ($_POST['action'] === 'approve') {
            // Update user status to active (0)
            updateDB('users', ['status' => '0'], "`id` = '{$userId}'");
            
            // Get user details for notification
            $user = selectDB('users', "`id` = '{$userId}'");
            if (count($user) > 0 && !empty($user[0]['phone'])) {
                // Notify user of approval via WhatsApp
                require_once("../../includes/functions/notification.php");
                $message = "Your account registration has been approved. You can now log in to CreateCMS.";
                whatsappUltraMsg($user[0]['phone'], $message);
            }
            
            // Redirect with success message
            header('Location: ../../pages/bladeUserRegistrations.php?success=User approved successfully');
            exit();
        } 
        else if ($_POST['action'] === 'reject') {
            // Get user details for notification
            $user = selectDB('users', "`id` = '{$userId}'");
            
            // Delete the user
            deleteDB('users', "`id` = '{$userId}'");
            
            if (count($user) > 0 && !empty($user[0]['phone'])) {
                // Notify user of rejection via WhatsApp
                require_once("../../includes/functions/notification.php");
                $message = "Your account registration has been rejected. Please contact admin for more information.";
                whatsappUltraMsg($user[0]['phone'], $message);
            }
            
            // Redirect with success message
            header('Location: ../../pages/bladeUserRegistrations.php?success=User rejected successfully');
            exit();
        }
    }
}

// Get pending registrations (status = 0)
$registrations = selectDB('users', "`status` = '0' AND `type` = '1'");
?>

<div class="card mb-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fs-17 fw-semi-bold mb-0">Pending User Registrations</h6>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if(count($registrations) > 0): ?>
            <div class="table-responsive">
                <table class="table display table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Registration Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($registrations as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                <td><?php echo date('Y-m-d H:i:s', strtotime($user['date'])); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <form method="POST" class="me-1">
                                            <input type="hidden" name="userId" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to approve this user?')">
                                                <i class="fa fa-check"></i> Approve
                                            </button>
                                        </form>
                                        <form method="POST">
                                            <input type="hidden" name="userId" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to reject this user?')">
                                                <i class="fa fa-times"></i> Reject
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                No pending user registrations found.
            </div>
        <?php endif; ?>
    </div>
</div>
