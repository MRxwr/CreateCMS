<?php
session_start();
require_once('admin/includes/config.php');
require_once('admin/includes/functions.php');

// Redirect if already logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['user_type'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

// Check for logout message
if (isset($_GET['logged_out']) && $_GET['logged_out'] == '1') {
    $success = 'You have been successfully logged out.';
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        // Check user table first
        $user = selectDB("user", "username = '$username' AND password = '" . sha1($password) . "' AND status = 0");
        
        if ($user && is_array($user) && count($user) > 0) {
            // User login successful
            $_SESSION['user_id'] = $user[0]['id'];
            $_SESSION['user_type'] = 0; // 0 for regular user
            $_SESSION['username'] = $user[0]['username'];
            $_SESSION['user_name'] = $user[0]['name'];
            
            // Update user hash for security
            $hash = md5(time() . $username);
            updateDB("user", ["hash" => $hash], "id = " . $user[0]['id']);
            
            // Set cookie
            setcookie('cmsUser', $hash, time() + (3600*24*30), '/');
            
            // Redirect to originally requested page or dashboard
            $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
            header('Location: ' . $redirect);
            exit;
        } else {
            // Check employee table
            $employee = selectDB("employee", "username = '$username' AND password = '" . sha1($password) . "' AND status = 0");
            
            if ($employee && is_array($employee) && count($employee) > 0) {
                // Employee login successful
                $_SESSION['user_id'] = $employee[0]['id'];
                $_SESSION['user_type'] = 1; // 1 for employee
                $_SESSION['username'] = $employee[0]['username'];
                $_SESSION['user_name'] = $employee[0]['name'];
                
                // Update employee hash for security
                $hash = md5(time() . $username);
                updateDB("employee", ["hash" => $hash], "id = " . $employee[0]['id']);
                
                // Set cookie
                setcookie('cmsUser', $hash, time() + (3600*24*30), '/');
                
                // Redirect to originally requested page or dashboard
                $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
                header('Location: ' . $redirect);
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Create CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .auth-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        
        .auth-header {
            background: var(--primary-color);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .auth-header h2 {
            margin: 0;
            font-weight: 600;
        }
        
        .auth-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        
        .auth-body {
            padding: 30px;
        }
        
        .form-floating {
            margin-bottom: 20px;
        }
        
        .form-floating input {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .form-floating input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }
        
        .btn-login {
            background: var(--primary-color);
            border: none;
            border-radius: 10px;
            padding: 12px 0;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-login:hover {
            background: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.3);
        }
        
        .auth-footer {
            text-align: center;
            padding: 20px 30px 30px;
            border-top: 1px solid #e9ecef;
        }
        
        .auth-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .auth-footer a:hover {
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 20px;
        }
        
        .logo {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 24px;
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo">
                    <i class="bi bi-building"></i>
                </div>
                <h2>Welcome Back</h2>
                <p>Sign in to your account</p>
            </div>
            
            <div class="auth-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i>
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                        <label for="username"><i class="bi bi-person"></i> Username</label>
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <label for="password"><i class="bi bi-lock"></i> Password</label>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>
                    
                    <button type="submit" name="login" class="btn btn-primary btn-login">
                        <i class="bi bi-box-arrow-in-right"></i> Sign In
                    </button>
                </form>
            </div>
            
            <div class="auth-footer">
                <p class="mb-2">Don't have an account?</p>
                <a href="register.php">Create an account</a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
