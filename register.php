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

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validation
    if (empty($name) || empty($username) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Check if username already exists
        $existingUser = selectDB("user", "username = '$username'");
        $existingEmployee = selectDB("employee", "username = '$username'");
        
        if (($existingUser && count($existingUser) > 0) || ($existingEmployee && count($existingEmployee) > 0)) {
            $error = 'Username already exists. Please choose a different username.';
        } else {
            // Check if email already exists
            $existingEmail = selectDB("user", "email = '$email'");
            if ($existingEmail && count($existingEmail) > 0) {
                $error = 'Email address already registered. Please use a different email.';
            } else {
                // Create new user
                $userData = [
                    'type' => 0, // Regular user type
                    'name' => $name,
                    'username' => $username,
                    'email' => $email,
                    'phone' => $phone,
                    'password' => sha1($password),
                    'hash' => md5(time() . $username),
                    'image' => '',
                    'status' => 0 // Active status
                ];
                
                $result = insertDB("user", $userData);
                
                if ($result) {
                    $success = 'Account created successfully! You can now sign in.';
                    
                    // Auto-login after registration
                    $newUser = selectDB("user", "username = '$username'");
                    if ($newUser && count($newUser) > 0) {
                        $_SESSION['user_id'] = $newUser[0]['id'];
                        $_SESSION['user_type'] = 0;
                        $_SESSION['username'] = $newUser[0]['username'];
                        $_SESSION['user_name'] = $newUser[0]['name'];
                        
                        // Set cookie
                        setcookie('cmsUser', $userData['hash'], time() + (3600*24*30), '/');
                        
                        header('Location: index.php?welcome=1');
                        exit;
                    }
                } else {
                    $error = 'Failed to create account. Please try again.';
                }
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
    <title>Register - Create CMS</title>
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
            max-width: 450px;
            width: 100%;
        }
        
        .auth-header {
            background: var(--success-color);
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
            border-color: var(--success-color);
            box-shadow: 0 0 0 0.2rem rgba(40,167,69,0.25);
        }
        
        .btn-register {
            background: var(--success-color);
            border: none;
            border-radius: 10px;
            padding: 12px 0;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-register:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40,167,69,0.3);
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
            color: var(--success-color);
        }
        
        .password-strength {
            margin-top: 5px;
            font-size: 0.8rem;
        }
        
        .strength-bar {
            height: 4px;
            border-radius: 2px;
            background: #e9ecef;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            width: 0%;
        }
        
        .strength-weak { background: #dc3545; width: 25%; }
        .strength-fair { background: #ffc107; width: 50%; }
        .strength-good { background: #17a2b8; width: 75%; }
        .strength-strong { background: #28a745; width: 100%; }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo">
                    <i class="bi bi-person-plus"></i>
                </div>
                <h2>Create Account</h2>
                <p>Join our platform today</p>
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
                
                <form method="POST" action="" id="registerForm">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                                <label for="name"><i class="bi bi-person"></i> Full Name *</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                                <label for="username"><i class="bi bi-at"></i> Username *</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                <label for="email"><i class="bi bi-envelope"></i> Email *</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-floating">
                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="Phone Number" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                        <label for="phone"><i class="bi bi-telephone"></i> Phone Number</label>
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <label for="password"><i class="bi bi-lock"></i> Password *</label>
                        <div class="password-strength">
                            <div class="strength-bar">
                                <div class="strength-fill" id="strengthBar"></div>
                            </div>
                            <small id="strengthText" class="text-muted">Password strength</small>
                        </div>
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                        <label for="confirm_password"><i class="bi bi-lock-fill"></i> Confirm Password *</label>
                        <div id="passwordMatch" class="mt-1"></div>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="#" target="_blank">Terms of Service</a> and <a href="#" target="_blank">Privacy Policy</a>
                        </label>
                    </div>
                    
                    <button type="submit" name="register" class="btn btn-success btn-register">
                        <i class="bi bi-person-plus"></i> Create Account
                    </button>
                </form>
            </div>
            
            <div class="auth-footer">
                <p class="mb-2">Already have an account?</p>
                <a href="login.php">Sign in here</a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');
            
            let strength = 0;
            let text = 'Too weak';
            
            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            strengthBar.className = 'strength-fill';
            
            switch(strength) {
                case 0:
                case 1:
                    strengthBar.classList.add('strength-weak');
                    text = 'Too weak';
                    break;
                case 2:
                    strengthBar.classList.add('strength-fair');
                    text = 'Fair';
                    break;
                case 3:
                case 4:
                    strengthBar.classList.add('strength-good');
                    text = 'Good';
                    break;
                case 5:
                    strengthBar.classList.add('strength-strong');
                    text = 'Strong';
                    break;
            }
            
            strengthText.textContent = text;
        });
        
        // Password confirmation checker
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            const matchDiv = document.getElementById('passwordMatch');
            
            if (confirmPassword.length > 0) {
                if (password === confirmPassword) {
                    matchDiv.innerHTML = '<small class="text-success"><i class="bi bi-check-circle"></i> Passwords match</small>';
                } else {
                    matchDiv.innerHTML = '<small class="text-danger"><i class="bi bi-x-circle"></i> Passwords do not match</small>';
                }
            } else {
                matchDiv.innerHTML = '';
            }
        });
        
        // Username availability checker (could be enhanced with AJAX)
        document.getElementById('username').addEventListener('blur', function() {
            const username = this.value.trim();
            if (username.length < 3) {
                this.setCustomValidity('Username must be at least 3 characters long');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
