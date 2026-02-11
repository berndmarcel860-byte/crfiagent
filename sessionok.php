<?php
require_once 'config.php';

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', 1800); // 30 minutes

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auto-login from remember me cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember'])) {
    $token = $_COOKIE['remember'];
    
    $stmt = $pdo->prepare("SELECT id, email, first_name, last_name FROM users 
                          WHERE remember_token = ? AND token_expiry > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['last_activity'] = time();
        
        // Update last login
        $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);
        
        // Redirect to dashboard
        header("Location: " . BASE_URL . "/index.php");
        exit();
    } else {
        // Invalid token - clear cookie
        setcookie('remember', '', time() - 3600, '/');
    }
}
?>