# CreateCMS - Live Database Compatible

A modern Single Page Application (SPA) built with PHP, HTML5, CSS3, JavaScript, and Bootstrap 5. **Now fully compatible with your existing live database structure!**

## 🎯 Features
- ✅ **Single Page Application (SPA)** with dynamic routing
- ✅ **Progressive Web App (PWA)** with offline support
- ✅ **Compatible with your live database** (u409066344_createcmsDB)
- ✅ **Responsive Design** using Bootstrap 5
- ✅ **Authentication System** for users and employees
- ✅ **CRUD Operations** for Clients, Projects, Tasks, and Employees
- ✅ **Real-time Task Chat System**
- ✅ **Dashboard with Analytics**
- ✅ **Kanban Board** for task management
- ✅ **Export Functionality** (CSV format)
- ✅ **Custom Animations** and smooth UX
- ✅ **Mobile-first Design**
- ✅ **Service Worker** for caching and offline functionality

## 🗄️ Database Compatibility

This CreateCMS is designed to work with your existing live database structure:

### **Your Current Tables:**
- `user` - User accounts and authentication
- `employee` - Team members and staff
- `client` - Client/customer information (replaces "leads" concept)
- `project` - Project management with client relationships
- `task` - Task assignments and tracking
- `comments` - Task communication system
- `logs` - System activity logging
- `invoice` - Project invoicing
- `notifications` - System notifications
- `settings` - Application settings
- `upload` - File management
- `events` - System events

### **Key Mappings:**
- **Clients** (your `client` table) = "Leads" in the interface
- **Projects** (your `project` table) with client relationships
- **Tasks** (your `task` table) with numeric status system
- **Employees** (your `employee` table) with department tracking
- **Comments** (your `comments` table) with user/employee distinction

## 🚀 Quick Setup

### 1. Database Connection
The system automatically connects to your existing database. Update `/admin/includes/config.php` if needed:

```php
$host = 'localhost';
$username = 'your_db_username';
$password = 'your_db_password';
$database = 'u409066344_createcmsDB'; // Your existing database
```

### 2. Optional Sample Data
If you want to add sample data to your existing database:

```sql
-- Import database_live_compatible.sql for sample records
-- This will NOT create new tables, only add sample data
```

### 3. Access the Application
- **Main Application:** `http://yourdomain.com/CreateCMS/`
- **Login:** Use your existing admin panel credentials

## 📊 Status System Mapping

Your database uses numeric status codes. The SPA maps them as follows:

### **Task Status:**
- `0` = Pending
- `1` = In Progress (Doing)
- `2` = Completed/Finished
- `3` = Returned (if used)

### **User/Employee Status:**
- `0` = Active
- `1` = Inactive/Deleted

### **Comments Status:**
- `0` = Deleted
- `1` = Active

## 🔌 API Endpoints

All APIs are updated to work with your live database structure:

- **GET/POST** `/requests/apiLeads.php` - Client management (your `client` table)
- **GET/POST** `/requests/apiProjects.php` - Project management with client relationships
- **GET/POST** `/requests/apiTasks.php` - Task management with numeric status
- **GET/POST** `/requests/apiEmployees.php` - Employee management
- **GET/POST** `/requests/apiComments.php` - Task communication system
- **GET** `/requests/apiDashboard.php` - Dashboard statistics

## 💡 Key Differences from Standard CreateCMS

### **Database Structure:**
- Uses your existing table names (`client`, `employee`, `project`, `task`)
- Numeric status codes instead of string status
- Additional fields like `price`, `department`, `empId`
- `date` field (datetime) instead of `created_at` (timestamp)

### **Functionality Preserved:**
- All CRUD operations work with your existing data
- Task assignments between projects and employees
- Client-to-project relationships maintained
- Comment system supports both users and employees
- Invoice tracking (additional feature from your DB)

### **Enhanced Features:**
- PWA capabilities added to your existing system
- Modern Bootstrap 5 interface
- Real-time task chat
- Kanban board view
- Export functionality
- Mobile-responsive design

## 🛠️ Customization Options

### **Status Labels:**
Edit the status mapping in `/templates/js.php` to match your business logic:

```javascript
const statusLabels = {
    0: 'Pending',
    1: 'In Progress', 
    2: 'Completed',
    3: 'Returned'
};
```

### **User Types:**
Your `user.type` field determines access levels:
- `0` = Administrator (full access)
- `1` = Regular user (limited access)

### **Additional Tables:**
The system can easily be extended to use your other tables:
- `invoice` - For financial tracking
- `notifications` - For system alerts
- `settings` - For configuration
- `upload` - For file management

## 📱 PWA Features

Install as a native app on any device:
- **Desktop:** Click install icon in browser address bar
- **Mobile:** "Add to Home Screen" option
- **Offline:** Cached content available without internet

## 🔒 Security Features

- Uses your existing authentication system
- Password hashing with SHA1 (matches your current system)
- Session-based security
- SQL injection protection using existing helper functions
- XSS protection with proper data sanitization

## 🎨 Interface Updates

The interface uses modern terminology while maintaining database compatibility:
- "Clients" instead of "Leads" (but uses your `client` table)
- "Team" view for employees with department grouping
- Project portfolio with progress tracking
- Task board with drag-and-drop (coming soon)

## 📞 Support

This CreateCMS is specifically tailored for your existing database structure. All APIs and interfaces work seamlessly with your current data while providing a modern, mobile-friendly experience.

**Your existing admin panel continues to work alongside this new SPA interface!**

---

**🚀 Ready to experience modern project management with your existing data!**