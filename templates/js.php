<script>
// Global variables
let currentUser = <?php echo json_encode([
    'id' => $userId, 
    'username' => $username, 
    'type' => $userType,
    'name' => $currentUser['name'] ?? $username
]); ?>;
let csrfToken = '<?php echo md5(session_id() . time()); ?>';

// Utility functions
function showLoading() {
    document.getElementById('loadingSpinner').classList.remove('d-none');
    document.getElementById('loadingSpinner').classList.add('show');
}

function hideLoading() {
    document.getElementById('loadingSpinner').classList.add('d-none');
    document.getElementById('loadingSpinner').classList.remove('show');
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastBody = toast.querySelector('.toast-body');
    const toastHeader = toast.querySelector('.toast-header');
    
    // Set message and styling
    toastBody.textContent = message;
    toastHeader.className = `toast-header bg-${type} text-white`;
    
    // Show toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
}

// Profile and Settings functions
function showProfile() {
    window.location.href = '?p=Profile';
}

function showSettings() {
    showToast('Settings page coming soon!', 'info');
}

// AJAX Helper Functions
async function makeRequest(url, options = {}) {
    showLoading();
    
    const defaultOptions = {
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
        }
    };
    
    const config = { ...defaultOptions, ...options };
    
    try {
        const response = await fetch(url, config);
        const data = await response.json();
        
        hideLoading();
        
        if (!data.ok) {
            throw new Error(data.data || 'An error occurred');
        }
        
        return data;
    } catch (error) {
        hideLoading();
        showToast(error.message, 'danger');
        throw error;
    }
}

// Navigation Functions
function navigateTo(view) {
    window.location.href = `?p=${view}`;
}

// Modal Functions
function createModal(id, title, content, size = 'lg') {
    const modalHTML = `
        <div class="modal fade" id="${id}" tabindex="-1">
            <div class="modal-dialog modal-${size}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ${content}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    const container = document.getElementById('modals-container');
    container.insertAdjacentHTML('beforeend', modalHTML);
    
    return new bootstrap.Modal(document.getElementById(id));
}

// Add/Edit Modal Functions
function showAddModal(type) {
    let title, content;
    
    switch(type) {
        case 'lead':
            title = 'Add New Lead';
            content = getLeadForm();
            break;
        case 'project':
            title = 'Add New Project';
            content = getProjectForm();
            break;
        case 'task':
            title = 'Add New Task';
            content = getTaskForm();
            break;
        case 'employee':
            if (currentUser.type !== 0) {
                showToast('Only administrators can add employees', 'warning');
                return;
            }
            title = 'Add New Employee';
            content = getEmployeeForm();
            break;
    }
    
    const modal = createModal('addModal', title, content);
    modal.show();
}

function showEditModal(type, id) {
    let title = `Edit ${type.charAt(0).toUpperCase() + type.slice(1)}`;
    let content = getEditForm(type, id);
    
    const modal = createModal('editModal', title, content);
    modal.show();
}

// Form Templates
function getLeadForm(data = {}) {
    return `
        <form id="leadForm" onsubmit="submitForm(event, 'leads')">
            ${data.id ? `<input type="hidden" name="id" value="${data.id}">` : ''}
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" class="form-control" name="name" value="${data.name || ''}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control" name="email" value="${data.email || ''}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="tel" class="form-control" name="phone" value="${data.phone || ''}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Company</label>
                        <input type="text" class="form-control" name="company" value="${data.company || ''}">
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="3">${data.notes || ''}</textarea>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Lead</button>
            </div>
        </form>
    `;
}

function getProjectForm(data = {}) {
    return `
        <form id="projectForm" onsubmit="submitForm(event, 'projects')">
            ${data.id ? `<input type="hidden" name="id" value="${data.id}">` : ''}
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Project Title *</label>
                        <input type="text" class="form-control" name="title" value="${data.title || ''}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Client *</label>
                        <select class="form-control" name="clientId" required>
                            <option value="">Select Client</option>
                            <!-- Clients will be loaded dynamically -->
                        </select>
                        ${data.clientId ? `<script>setTimeout(() => document.querySelector('select[name="clientId"]').value = '${data.clientId}', 100);</script>` : ''}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="startDate" value="${data.startDate || ''}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" name="endDate" value="${data.endDate || ''}">
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3">${data.description || ''}</textarea>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Project</button>
            </div>
        </form>
    `;
}

function getTaskForm(data = {}) {
    return `
        <form id="taskForm" onsubmit="submitForm(event, 'tasks')">">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Task Title *</label>
                        <input type="text" class="form-control" name="task" value="${data.task || ''}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Project *</label>
                        <select class="form-control" name="projectId" required>
                            <option value="">Select Project</option>
                            <!-- Projects will be loaded dynamically -->
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Assign To *</label>
                        <select class="form-control" name="to" required>
                            <option value="">Select Employee</option>
                            <!-- Employees will be loaded dynamically -->
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Expected Date</label>
                        <input type="date" class="form-control" name="expected" value="${data.expected || ''}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Priority</label>
                        <select class="form-control" name="priority">
                            <option value="low" ${data.priority === 'low' ? 'selected' : ''}>Low</option>
                            <option value="medium" ${data.priority === 'medium' ? 'selected' : ''}>Medium</option>
                            <option value="high" ${data.priority === 'high' ? 'selected' : ''}>High</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-control" name="status">
                            <option value="PENDING" ${data.status === 'PENDING' ? 'selected' : ''}>Pending</option>
                            <option value="DOING" ${data.status === 'DOING' ? 'selected' : ''}>In Progress</option>
                            <option value="FINISHED" ${data.status === 'FINISHED' ? 'selected' : ''}>Completed</option>
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3">${data.description || ''}</textarea>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Task</button>
            </div>
        </form>
    `;
}

function getEmployeeForm(data = {}) {
    return `
        <form id="employeeForm" onsubmit="submitForm(event, 'employees')">
            ${data.id ? `<input type="hidden" name="id" value="${data.id}">` : ''}
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" class="form-control" name="name" value="${data.name || ''}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Username *</label>
                        <input type="text" class="form-control" name="username" value="${data.username || ''}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control" name="email" value="${data.email || ''}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="tel" class="form-control" name="phone" value="${data.phone || ''}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Password *</label>
                        <input type="password" class="form-control" name="password" ${data.id ? '' : 'required'}>
                        ${data.id ? '<small class="text-muted">Leave blank to keep current password</small>' : ''}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <input type="text" class="form-control" name="department" value="${data.department || ''}">
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Employee</button>
            </div>
        </form>
    `;
}

// Form Submission
async function submitForm(event, type) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    
    try {
        const result = await makeRequest(`requests/api${type.charAt(0).toUpperCase() + type.slice(1)}.php`, {
            method: 'POST',
            body: JSON.stringify(data)
        });
        
        showToast(result.data.message || `${type} saved successfully!`, 'success');
        
        // Close modal
        bootstrap.Modal.getInstance(form.closest('.modal')).hide();
        
        // Refresh current view
        location.reload();
        
    } catch (error) {
        // Error already handled in makeRequest
    }
}

// Delete Function
async function deleteItem(type, id, name) {
    if (!confirm(`Are you sure you want to delete this ${type}: ${name}?`)) {
        return;
    }
    
    try {
        const result = await makeRequest(`requests/api${type.charAt(0).toUpperCase() + type.slice(1)}.php`, {
            method: 'DELETE',
            body: JSON.stringify({ id: id })
        });
        
        showToast(result.data.message || `${type} deleted successfully!`, 'success');
        location.reload();
        
    } catch (error) {
        // Error already handled in makeRequest
    }
}

// Chat Functions
let chatInterval;

function openTaskChat(taskId) {
    const chatModal = createModal('chatModal', 'Task Chat', getChatHTML(taskId), 'lg');
    chatModal.show();
    
    // Load chat messages
    loadChatMessages(taskId);
    
    // Start auto-refresh
    chatInterval = setInterval(() => loadChatMessages(taskId), 5000);
    
    // Stop auto-refresh when modal is closed
    document.getElementById('chatModal').addEventListener('hidden.bs.modal', () => {
        clearInterval(chatInterval);
    });
}

function getChatHTML(taskId) {
    return `
        <div class="chat-container" id="chatContainer">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading messages...</span>
                </div>
            </div>
        </div>
        <div class="chat-input">
            <form onsubmit="sendMessage(event, ${taskId})">
                <div class="input-group">
                    <input type="text" class="form-control" name="message" placeholder="Type your message..." required>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i> Send
                    </button>
                </div>
            </form>
        </div>
    `;
}

async function loadChatMessages(taskId) {
    try {
        const response = await fetch(`requests/apiComments.php?taskId=${taskId}`);
        const data = await response.json();
        
        if (data.ok) {
            displayChatMessages(data.data);
        }
    } catch (error) {
        console.error('Error loading chat messages:', error);
    }
}

function displayChatMessages(messages) {
    const container = document.getElementById('chatContainer');
    
    if (!messages || messages.length === 0) {
        container.innerHTML = '<div class="text-center text-muted">No messages yet</div>';
        return;
    }
    
    let html = '';
    messages.forEach(message => {
        const isOwn = message.userId == currentUser.id;
        html += `
            <div class="chat-message ${isOwn ? 'own' : 'other'}">
                <div class="sender">${message.username}</div>
                <div class="content">${message.comment}</div>
                <div class="time">${formatDate(message.date)}</div>
            </div>
        `;
    });
    
    container.innerHTML = html;
    container.scrollTop = container.scrollHeight;
}

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
                comment: message
            })
        });
        
        input.value = '';
        loadChatMessages(taskId);
        
    } catch (error) {
        // Error already handled in makeRequest
    }
}

// Utility Functions
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString();
}

function showProfile() {
    // Implementation for user profile modal
    showToast('Profile functionality coming soon!', 'info');
}

// Load dropdown data
async function loadDropdownData() {
    try {
        // Load clients for project form
        const clients = await makeRequest('requests/apiLeads.php');
        if (clients.ok) {
            updateSelectOptions('clientId', clients.data, 'id', 'name');
        }
        
        // Load projects for task form
        const projects = await makeRequest('requests/apiProjects.php');
        if (projects.ok) {
            updateSelectOptions('projectId', projects.data);
        }
        
        // Load employees for task form
        const employees = await makeRequest('requests/apiEmployees.php');
        if (employees.ok) {
            updateSelectOptions('to', employees.data, 'id', 'name');
        }
    } catch (error) {
        console.error('Error loading dropdown data:', error);
    }
}

function updateSelectOptions(selectName, data, valueField = 'id', textField = 'title') {
    const selects = document.querySelectorAll(`select[name="${selectName}"]`);
    selects.forEach(select => {
        // Keep the first option (placeholder)
        const firstOption = select.querySelector('option');
        select.innerHTML = '';
        if (firstOption) {
            select.appendChild(firstOption);
        }
        
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item[valueField];
            option.textContent = item[textField];
            select.appendChild(option);
        });
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Load dropdown data when modals are opened
    document.addEventListener('shown.bs.modal', function(event) {
        if (event.target.id === 'addModal' || event.target.id === 'editModal') {
            loadDropdownData();
        }
    });
    
    // Clean up modals when closed
    document.addEventListener('hidden.bs.modal', function(event) {
        event.target.remove();
    });
    
    // Add click handlers for task cards
    setupTaskCardHandlers();
    
    // Add chat icons to task cards on tasks page
    if (window.location.search.includes('v=Tasks')) {
        setTimeout(addChatIconsToTasks, 100);
    }
});

// Task status update and chat functionality
function setupTaskCardHandlers() {
    // Handle task card clicks for status updates
    document.addEventListener('click', function(event) {
        const taskCard = event.target.closest('.task-card');
        if (taskCard && event.target.classList.contains('badge')) {
            const taskId = taskCard.dataset.taskId;
            if (taskId) {
                showTaskStatusModal(taskId);
            }
        }
        
        // Handle chat icon clicks
        if (event.target.classList.contains('chat-icon') || 
            event.target.closest('.chat-icon')) {
            const taskCard = event.target.closest('.task-card');
            const taskId = taskCard ? taskCard.dataset.taskId : null;
            if (taskId) {
                openTaskChat(taskId);
            }
        }
    });
}

// Show task status update modal
function showTaskStatusModal(taskId) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'taskStatusModal';
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Task Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-warning" onclick="updateTaskStatus(${taskId}, 0)">
                            <i class="bi bi-clock"></i> Set to Pending
                        </button>
                        <button class="btn btn-info" onclick="updateTaskStatus(${taskId}, 1)">
                            <i class="bi bi-arrow-clockwise"></i> Set to In Progress
                        </button>
                        <button class="btn btn-success" onclick="updateTaskStatus(${taskId}, 2)">
                            <i class="bi bi-check-circle"></i> Mark as Completed
                        </button>
                        <button class="btn btn-secondary" onclick="updateTaskStatus(${taskId}, 3)">
                            <i class="bi bi-pause-circle"></i> Put on Hold
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="openTaskChat(${taskId})">
                        <i class="bi bi-chat"></i> Open Chat
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
}

// Update task status
async function updateTaskStatus(taskId, status) {
    try {
        const response = await makeRequest('requests/apiTasks.php', {
            method: 'PUT',
            body: JSON.stringify({ id: taskId, status: status })
        });
        
        if (response.ok) {
            showToast('Task status updated successfully!', 'success');
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('taskStatusModal'));
            modal.hide();
            // Reload the page to show updated status
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            throw new Error(response.data || 'Failed to update task status');
        }
    } catch (error) {
        console.error('Error updating task status:', error);
        showToast('Error updating task status: ' + error.message, 'error');
    }
}

// Open task chat
function openTaskChat(taskId) {
    // Close any existing modals
    const existingModals = document.querySelectorAll('.modal.show');
    existingModals.forEach(modal => {
        const bootstrapModal = bootstrap.Modal.getInstance(modal);
        if (bootstrapModal) {
            bootstrapModal.hide();
        }
    });
    
    // Navigate to chat page
    window.location.href = `?p=ChatTask&task=${taskId}`;
}

// Add chat icons to task cards
function addChatIconsToTasks() {
    const taskCards = document.querySelectorAll('.task-card');
    taskCards.forEach(card => {
        const cardBody = card.querySelector('.card-body');
        if (cardBody && !card.querySelector('.chat-icon')) {
            const chatIcon = document.createElement('button');
            chatIcon.className = 'btn chat-icon position-absolute';
            chatIcon.style.cssText = 'top: 8px; right: 8px; z-index: 10;';
            chatIcon.innerHTML = '<i class="bi bi-chat-fill"></i>';
            chatIcon.title = 'Open Task Chat';
            cardBody.style.position = 'relative';
            cardBody.appendChild(chatIcon);
        }
    });
}
</script>
