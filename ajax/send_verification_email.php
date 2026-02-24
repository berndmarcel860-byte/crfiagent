<?php
/**
 * Send Email Verification
 * 
 * Sends a verification email to the user with a secure token
 */

session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../EmailHelper.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

try {
    $userId = $_SESSION['user_id'];
    
    // Get user data
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit();
    }
    
    // Check if already verified
    if ($user['is_verified']) {
        echo json_encode(['success' => false, 'message' => 'Email already verified']);
        exit();
    }
    
    // Check rate limiting - don't allow sending more than once per minute
    if (isset($_SESSION['last_verification_sent'])) {
        $timeSinceLastSend = time() - $_SESSION['last_verification_sent'];
        if ($timeSinceLastSend < 60) {
            $waitTime = 60 - $timeSinceLastSend;
            echo json_encode([
                'success' => false, 
                'message' => "Please wait {$waitTime} seconds before requesting another email"
            ]);
            exit();
        }
    }
    
    // Generate secure verification token
    $token = bin2hex(random_bytes(32)); // 64 character token
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Store token in database
    $stmt = $pdo->prepare("
        UPDATE users 
        SET verification_token = ?, 
            verification_token_expires = ? 
        WHERE id = ?
    ");
    $stmt->execute([$token, $expires, $userId]);
    
    // Send verification email using EmailHelper
    $emailHelper = new EmailHelper($pdo);
    
    // Create verification URL
    $siteUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") 
                . "://" . $_SERVER['HTTP_HOST'];
    $verificationUrl = $siteUrl . "/verify_email.php?token=" . $token;
    
    $customVars = [
        'verification_url' => $verificationUrl,
        'verification_token' => $token,
        'expires_in' => '1 hour'
    ];
    
    $emailSent = $emailHelper->sendEmail('email_verification', $userId, $customVars);
    
    if ($emailSent) {
        // Update session to track last send time
        $_SESSION['last_verification_sent'] = time();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Verification email sent successfully! Please check your inbox.'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to send verification email. Please try again later.'
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Email verification error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred. Please try again later.'
    ]);
} catch (Exception $e) {
    error_log("Email verification error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred. Please try again later.'
    ]);
}
