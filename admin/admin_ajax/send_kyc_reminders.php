<?php
/**
 * Send KYC Reminder Emails
 * Sends reminder emails to all users who haven't completed KYC verification
 */
require_once '../admin_session.php';
require_once '../email_template_helper.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

try {
    // Find users without completed KYC
    $stmt = $pdo->prepare("
        SELECT 
            u.id,
            u.email,
            u.first_name,
            u.last_name
        FROM users u
        LEFT JOIN kyc_verification_requests k ON u.id = k.user_id
        WHERE u.status = 'active'
            AND u.is_verified = 1
            AND (k.id IS NULL OR k.status IN ('pending', 'rejected', 'none'))
        GROUP BY u.id
        LIMIT 100
    ");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo json_encode([
            'success' => true,
            'message' => 'No users need KYC reminders',
            'sent' => 0,
            'failed' => 0
        ]);
        exit;
    }
    
    // Initialize email template helper
    $emailHelper = new EmailTemplateHelper($pdo);
    
    $sentCount = 0;
    $failedCount = 0;
    $errors = [];
    
    foreach ($users as $user) {
        try {
            // Prepare template variables
            $variables = [
                'user_id' => $user['id'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email'],
                'kyc_url' => 'https://' . $_SERVER['HTTP_HOST'] . '/kyc.php',
                'support_email' => 'support@fundtracerai.com'
            ];
            
            // Send email using template
            $success = $emailHelper->sendTemplateEmail(
                $user['email'],
                'kyc_reminder',
                $variables
            );
            
            if ($success) {
                $sentCount++;
            } else {
                $failedCount++;
                $errors[] = "Failed to send to: {$user['email']}";
            }
            
        } catch (Exception $e) {
            $failedCount++;
            $errors[] = "Error sending to {$user['email']}: " . $e->getMessage();
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => "Sent {$sentCount} KYC reminder emails. {$failedCount} failed.",
        'sent' => $sentCount,
        'failed' => $failedCount,
        'total_users' => count($users),
        'errors' => $errors
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
