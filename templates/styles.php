<style>
/* Custom CSS for CreateCMS */
:root {
    --primary-color: #0d6efd;
    --secondary-color: #6c757d;
    --success-color: #198754;
    --info-color: #0dcaf0;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --light-color: #f8f9fa;
    --dark-color: #212529;
}

/* Global Styles */
body {
    background-color: #f8f9fa;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Loading Spinner */
#loadingSpinner {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 9999;
}

#loadingSpinner.show {
    display: block !important;
}

/* Navigation */
.navbar-brand img {
    border-radius: 50%;
}

.nav-link {
    transition: all 0.3s ease;
}

.nav-link:hover {
    transform: translateY(-2px);
}

/* Sidebar */
.sidebar {
    position: sticky;
    top: 80px;
    height: calc(100vh - 100px);
    overflow-y: auto;
}

.sidebar-heading {
    color: var(--primary-color);
    font-weight: 600;
    margin-bottom: 1rem;
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 0.5rem;
}

/* Cards */
.card {
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

/* Buttons */
.btn {
    transition: all 0.3s ease;
    border-radius: 8px;
}

.btn:hover {
    transform: translateY(-2px);
}

/* Tables */
.table {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.table th {
    background-color: var(--primary-color);
    color: white;
    font-weight: 600;
    border: none;
}

.table td {
    vertical-align: middle;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

/* Forms */
.form-control {
    border-radius: 8px;
    border: 1px solid #ddd;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/* Modals */
.modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.modal-header {
    background-color: var(--primary-color);
    color: white;
    border-radius: 12px 12px 0 0;
}

.modal-header .btn-close {
    filter: invert(1);
}

/* Chat Styles */
.chat-container {
    height: 400px;
    overflow-y: auto;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 1rem;
    background: white;
}

.chat-message {
    margin-bottom: 1rem;
    padding: 0.5rem;
    border-radius: 8px;
    max-width: 80%;
}

.chat-message.own {
    background-color: var(--primary-color);
    color: white;
    margin-left: auto;
}

.chat-message.other {
    background-color: #e9ecef;
    color: var(--dark-color);
}

.chat-message .sender {
    font-weight: 600;
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
}

.chat-message .time {
    font-size: 0.75rem;
    opacity: 0.7;
    margin-top: 0.25rem;
}

.chat-input {
    border-top: 1px solid #ddd;
    padding-top: 1rem;
    margin-top: 1rem;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes slideIn {
    from { transform: translateX(-100%); }
    to { transform: translateX(0); }
}

.fade-in {
    animation: fadeIn 0.5s ease-out;
}

.slide-in {
    animation: slideIn 0.3s ease-out;
}

/* Stats Cards */
.stats-card {
    background: linear-gradient(135deg, var(--primary-color), #0056b3);
    color: white;
    border-radius: 12px;
    padding: 1.5rem;
}

.stats-card .stats-number {
    font-size: 2.5rem;
    font-weight: 700;
}

.stats-card .stats-label {
    opacity: 0.9;
    font-size: 0.9rem;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .sidebar {
        display: none !important;
    }
    
    .navbar-brand {
        font-size: 1rem;
    }
    
    .stats-card .stats-number {
        font-size: 1.8rem;
    }
    
    .chat-message {
        max-width: 95%;
    }
}

/* Task Status Colors */
.status-pending {
    background-color: var(--warning-color);
    color: var(--dark-color);
}

.status-doing {
    background-color: var(--info-color);
    color: var(--dark-color);
}

.status-finished {
    background-color: var(--success-color);
    color: white;
}

.status-returned {
    background-color: var(--danger-color);
    color: white;
}

/* Priority Colors */
.priority-low {
    border-left: 4px solid var(--info-color);
}

.priority-medium {
    border-left: 4px solid var(--warning-color);
}

.priority-high {
    border-left: 4px solid var(--danger-color);
}

/* Skeleton Loading */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

.skeleton-text {
    height: 1rem;
    border-radius: 4px;
    margin-bottom: 0.5rem;
}

.skeleton-title {
    height: 1.5rem;
    border-radius: 4px;
    margin-bottom: 1rem;
    width: 60%;
}

/* Toast Customization */
.toast {
    min-width: 300px;
}

/* Badge Styles */
.badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
}

/* Progress Bars */
.progress {
    height: 8px;
    border-radius: 4px;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: var(--primary-color);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #0056b3;
}
</style>
