<?php
// This is the dedicated chat page for a specific task
if(!isset($_GET['task']) || empty($_GET['task'])) {
    echo '<div class="alert alert-warning">Please select a task to view chat.</div>';
    return;
}

$taskId = (int)$_GET['task'];

// Get task details with project and employee information using direct query
$taskDetails = [];
$query = "SELECT t.*, p.title as project_title, e.name as employee_name, u.username as creator_name 
          FROM task t 
          LEFT JOIN project p ON t.projectId = p.id 
          LEFT JOIN employee e ON t.to = e.id 
          LEFT JOIN user u ON t.by = u.id 
          WHERE t.id = {$taskId}";
$result = $dbconnect->query($query);
if ($result && $result->num_rows > 0) {
    $taskDetails[] = $result->fetch_assoc();
}

if(!$taskDetails || !is_array($taskDetails) || count($taskDetails) == 0) {
    echo '<div class="alert alert-danger">Task not found.</div>';
    return;
}

$task = $taskDetails[0];

// Get chat messages (comments) - using correct table name
$comments = selectDB("comments", "taskId = {$taskId} ORDER BY id ASC");
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 text-primary">Task Chat</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="?v=Tasks">Tasks</a></li>
                            <li class="breadcrumb-item active"><?php echo htmlspecialchars($task['task']); ?></li>
                        </ol>
                    </nav>
                </div>
                <a href="?v=Tasks" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Tasks
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Task Details Sidebar -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle"></i> Task Details
                    </h5>
                </div>
                <div class="card-body">
                    <h6 class="card-title"><?php echo htmlspecialchars($task['task']); ?></h6>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Project</small>
                        <strong><?php echo htmlspecialchars($task['project_title']); ?></strong>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Assigned To</small>
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle bg-primary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                <?php echo strtoupper(substr($task['employee_name'], 0, 2)); ?>
                            </div>
                            <strong><?php echo htmlspecialchars($task['employee_name']); ?></strong>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Created By</small>
                        <strong><?php echo htmlspecialchars($task['creator_name']); ?></strong>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Status</small>
                        <span class="badge status-<?php echo strtolower($task['status']); ?>">
                            <?php echo $task['status']; ?>
                        </span>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Priority</small>
                        <span class="badge bg-<?php 
                            echo $task['priority'] == 'high' ? 'danger' : 
                                ($task['priority'] == 'medium' ? 'warning' : 'info'); 
                        ?>">
                            <?php echo ucfirst($task['priority'] ?? 'medium'); ?>
                        </span>
                    </div>
                    
                    <?php if($task['expected']): ?>
                    <div class="mb-3">
                        <small class="text-muted d-block">Due Date</small>
                        <?php 
                        $dueDate = strtotime($task['expected']);
                        $isOverdue = $dueDate < time() && $task['status'] != 'FINISHED';
                        ?>
                        <strong class="<?php echo $isOverdue ? 'text-danger' : ''; ?>">
                            <?php echo date('M d, Y', $dueDate); ?>
                            <?php if($isOverdue): ?>
                                <small class="text-danger">(Overdue)</small>
                            <?php endif; ?>
                        </strong>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($task['description']): ?>
                    <div class="mb-3">
                        <small class="text-muted d-block">Description</small>
                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($task['description'])); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Quick Actions -->
                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-outline-primary btn-sm" onclick="editTask(<?php echo $task['id']; ?>)">
                            <i class="bi bi-pencil"></i> Edit Task
                        </button>
                        
                        <?php if($task['status'] == 'PENDING'): ?>
                        <button class="btn btn-outline-info btn-sm" onclick="updateTaskStatus(<?php echo $task['id']; ?>, 'DOING')">
                            <i class="bi bi-play"></i> Start Task
                        </button>
                        <?php elseif($task['status'] == 'DOING'): ?>
                        <button class="btn btn-outline-success btn-sm" onclick="updateTaskStatus(<?php echo $task['id']; ?>, 'FINISHED')">
                            <i class="bi bi-check"></i> Mark Complete
                        </button>
                        <button class="btn btn-outline-warning btn-sm" onclick="updateTaskStatus(<?php echo $task['id']; ?>, 'RETURNED')">
                            <i class="bi bi-arrow-return-left"></i> Return Task
                        </button>
                        <?php elseif($task['status'] == 'FINISHED'): ?>
                        <button class="btn btn-outline-info btn-sm" onclick="updateTaskStatus(<?php echo $task['id']; ?>, 'DOING')">
                            <i class="bi bi-arrow-clockwise"></i> Reopen Task
                        </button>
                        <?php elseif($task['status'] == 'RETURNED'): ?>
                        <button class="btn btn-outline-info btn-sm" onclick="updateTaskStatus(<?php echo $task['id']; ?>, 'DOING')">
                            <i class="bi bi-play"></i> Resume Task
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-chat-dots"></i> Task Discussion
                    </h5>
                    <small class="text-muted">
                        <?php echo count($comments ?: []); ?> message(s)
                    </small>
                </div>
                
                <!-- Chat Messages Container -->
                <div class="card-body p-0">
                    <div id="chatContainer" class="p-3" style="height: 500px; overflow-y: auto;">
                        <?php if($comments && is_array($comments)): ?>
                            <?php foreach($comments as $comment): ?>
                            <?php
                            // Get user info for the comment
                            $commentUser = selectDB("users", "id = {$comment['userId']}");
                            if(!$commentUser) {
                                $commentUser = selectDB("employees", "id = {$comment['userId']}");
                            }
                            $senderName = $commentUser ? $commentUser[0]['username'] : 'Unknown';
                            $isOwn = $comment['userId'] == $userId;
                            ?>
                            <div class="chat-message <?php echo $isOwn ? 'own' : 'other'; ?> mb-3">
                                <div class="d-flex <?php echo $isOwn ? 'justify-content-end' : 'justify-content-start'; ?>">
                                    <div class="message-bubble p-3 rounded" style="max-width: 70%; background-color: <?php echo $isOwn ? 'var(--primary-color)' : '#e9ecef'; ?>; color: <?php echo $isOwn ? 'white' : 'var(--dark-color)'; ?>;">
                                        <?php if(!$isOwn): ?>
                                        <div class="sender mb-1">
                                            <small style="font-weight: 600; opacity: 0.8;">
                                                <?php echo htmlspecialchars($senderName); ?>
                                            </small>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="content">
                                            <?php echo nl2br(htmlspecialchars($comment['comment'])); ?>
                                        </div>
                                        
                                        <div class="time mt-2">
                                            <small style="opacity: 0.7; font-size: 0.75rem;">
                                                <?php echo date('M d, Y - H:i', strtotime($comment['date'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-chat display-4"></i>
                                <p class="mt-3">No messages yet. Start the conversation!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Message Input -->
                <div class="card-footer">
                    <form id="chatForm" onsubmit="sendMessage(event, <?php echo $taskId; ?>)">
                        <div class="input-group">
                            <input type="text" class="form-control" name="message" placeholder="Type your message..." required>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i> Send
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}

.message-bubble {
    border-radius: 18px !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.chat-message.own .message-bubble {
    background: linear-gradient(135deg, var(--primary-color), #0056b3) !important;
}

#chatContainer {
    background: linear-gradient(to bottom, #f8f9fa, #ffffff);
}

/* Auto-scroll to bottom */
#chatContainer::-webkit-scrollbar {
    width: 6px;
}

#chatContainer::-webkit-scrollbar-track {
    background: #f1f1f1;
}

#chatContainer::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

#chatContainer::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>

<script>
// Auto-scroll to bottom of chat
function scrollToBottom() {
    const chatContainer = document.getElementById('chatContainer');
    chatContainer.scrollTop = chatContainer.scrollHeight;
}

// Scroll to bottom on page load
document.addEventListener('DOMContentLoaded', function() {
    scrollToBottom();
});

// Send message function
async function sendMessage(event, taskId) {
    event.preventDefault();
    
    const form = event.target;
    const input = form.querySelector('input[name="message"]');
    const message = input.value.trim();
    
    if (!message) return;
    
    try {
        const result = await makeRequest('requests/apiComments.php', {
            method: 'POST',
            body: JSON.stringify({
                taskId: taskId,
                comment: message,
                userId: currentUser.id
            })
        });
        
        if(result.ok) {
            input.value = '';
            // Reload the page to show new message
            location.reload();
        }
        
    } catch (error) {
        // Error already handled in makeRequest
    }
}

// Update task status
async function updateTaskStatus(taskId, newStatus) {
    if(!confirm(`Are you sure you want to change the task status to ${newStatus}?`)) {
        return;
    }
    
    try {
        const result = await makeRequest('requests/apiTasks.php', {
            method: 'PUT',
            body: JSON.stringify({
                id: taskId,
                status: newStatus
            })
        });
        
        if(result.ok) {
            showToast('Task status updated successfully!', 'success');
            location.reload();
        }
        
    } catch (error) {
        // Error already handled in makeRequest
    }
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

// Auto-refresh messages every 10 seconds
setInterval(() => {
    // Only reload if the page is visible and user hasn't typed anything
    if (document.visibilityState === 'visible' && !document.querySelector('input[name="message"]').value) {
        const currentScrollPos = document.getElementById('chatContainer').scrollTop;
        const maxScrollPos = document.getElementById('chatContainer').scrollHeight - document.getElementById('chatContainer').clientHeight;
        
        // Only reload if user is at the bottom of the chat
        if (Math.abs(currentScrollPos - maxScrollPos) < 50) {
            location.reload();
        }
    }
}, 10000);

// Handle Enter key for sending messages
document.querySelector('input[name="message"]').addEventListener('keypress', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        document.getElementById('chatForm').dispatchEvent(new Event('submit'));
    }
});
</script>
