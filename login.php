<?php
require_once 'config.php';

// Initialize variables
$error = '';
$email = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Validate inputs
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password";
    } else {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Check if account is active
            if ($user['status'] !== 'active') {
                $error = "Your account is currently " . ucfirst($user['status']);
            } else {
                // Start session
                session_start();
                
                // Regenerate session ID for security
                session_regenerate_id(true);
                
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['last_activity'] = time();
                
                // Set remember me cookie if requested
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $expiry = time() + 60 * 60 * 24 * 30; // 30 days
                    
                    // Store token in database
                    $stmt = $pdo->prepare("UPDATE users SET remember_token = ?, token_expiry = ? WHERE id = ?");
                    $stmt->execute([$token, date('Y-m-d H:i:s', $expiry), $user['id']]);
                    
                    // Set cookie
                    setcookie('remember', $token, $expiry, '/', '', true, true);
                }
                
                // Update last login
                $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);
                
                // Redirect to dashboard
                header("Location: index.php");
                exit();
            }
        } else {
            $error = "Invalid email or password";
            
            // Log failed login attempt
            $ip = $_SERVER['REMOTE_ADDR'];
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $stmt = $pdo->prepare("INSERT INTO login_logs (email, ip_address, user_agent, success) VALUES (?, ?, ?, 0)");
            $stmt->execute([$email, $ip, $userAgent]);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Scam Recovery Dashboard</title>
    <link href="assets/css/app.min.css" rel="stylesheet">
    <style>
        .login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .login-card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border: none;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo img {
            height: 60px;
        }
        .form-control:focus {
            border-color: #5c6bc0;
            box-shadow: 0 0 0 0.2rem rgba(92, 107, 192, 0.25);
        }
        .btn-primary {
            background-color: #5c6bc0;
            border-color: #5c6bc0;
        }
        .btn-primary:hover {
            background-color: #3f51b5;
            border-color: #3f51b5;
        }
        .forgot-password {
            text-align: right;
            margin-top: -10px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="card login-card">
                        <div class="card-body p-4">
                            <div class="login-logo">
                                <img src="assets/images/logo/logo.png" alt="Scam Recovery">
                            </div>
                            <h4 class="text-center mb-4">Sign in to your account</h4>
                            
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            
                            <form method="POST" action="login.php">
                                <div class="form-group">
                                    <label for="email">Email address</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($email); ?>" required autofocus>
                                </div>
                                
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                
                                <div class="form-group form-check d-flex justify-content-between">
                                    <div>
                                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                        <label class="form-check-label" for="remember">Remember me</label>
                                    </div>
                                    <div class="forgot-password">
                                        <a href="forgot-password.php">Forgot password?</a>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-block">Sign in</button>
                                
                                <div class="text-center mt-3">
                                    <p>Don't have an account? <a href="register.php">Sign up</a></p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>