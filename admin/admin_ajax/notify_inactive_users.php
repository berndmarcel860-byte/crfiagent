<?php
/**
 * Notify Inactive Users
 * Sends email notifications to users who have been inactive for a specified period
 */
require_once '../admin_session.php';
require_once '../email_template_helper.php';

header('Content-Type: application/json');

try {
    // Get parameters
    $inactiveDays = isset($_POST['inactive_days']) ? (int)$_POST['inactive_days'] : 30;
    $emailTemplate = $_POST['email_template'] ?? 'inactive_user_reminder';
    
    // Select appropriate template based on inactive days if not specified
    if (!isset($_POST['email_template'])) {
        if ($inactiveDays >= 60) {
            $emailTemplate = 'inactive_user_60_days';
        } elseif ($inactiveDays >= 30) {
            $emailTemplate = 'inactive_user_30_days';
        } elseif ($inactiveDays >= 7) {
            $emailTemplate = 'inactive_user_7_days';
        } else {
            $emailTemplate = 'inactive_user_reminder';
        }
    }
    
    // Find inactive users
    $stmt = $pdo->prepare("
        SELECT 
            u.id,
            u.email,
            u.first_name,
            u.last_name,
            u.last_login,
            DATEDIFF(NOW(), u.last_login) as days_inactive
        FROM users u
        WHERE u.last_login < DATE_SUB(NOW(), INTERVAL ? DAY)
            AND u.status = 'active'
            AND u.is_verified = 1
        ORDER BY u.last_login ASC
        LIMIT 100
    ");
    $stmt->execute([$inactiveDays]);
    $inactiveUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($inactiveUsers)) {
        echo json_encode([
            'success' => true,
            'message' => 'No inactive users found',
            'count' => 0
        ]);
        exit;
    }
    
    // Initialize email template helper
    $emailHelper = new EmailTemplateHelper($pdo);
    
    $sentCount = 0;
    $failedCount = 0;
    $errors = [];
    
    // Send emails to inactive users
    foreach ($inactiveUsers as $user) {
        try {
            // Prepare template variables
            $variables = [
                'user_id' => $user['id'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email'],
                'days_inactive' => $user['days_inactive'],
                'login_url' => 'https://' . $_SERVER['HTTP_HOST'] . '/login.php',
                'analysis_count' => rand(50, 200), // AI analysis count (mock data)
                'case_number' => 'CASE-' . str_pad($user['id'], 6, '0', STR_PAD_LEFT),
                'support_email' => 'support@fundtracerai.com'
            ];
            
            // Send email using template
            $success = $emailHelper->sendTemplateEmail(
                $user['email'],
                $emailTemplate,
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
        'message' => "Sent {$sentCount} emails successfully. {$failedCount} failed.",
        'sent' => $sentCount,
        'failed' => $failedCount,
        'total_users' => count($inactiveUsers),
        'template_used' => $emailTemplate,
        'errors' => $errors
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

            $content = $template['content'];
            
            $replacements = [
                '{{first_name}}' => $user['first_name'],
                '{{last_name}}' => $user['last_name'],
                '{{email}}' => $user['email'],
                '{{days_inactive}}' => $user['days_inactive'],
                '{{login_url}}' => 'https://' . $_SERVER['HTTP_HOST'] . '/login.php'
            ];
            
            foreach ($replacements as $key => $value) {
                $content = str_replace($key, $value, $content);
            }
            
            // Prepare email HTML
            $emailHTML = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #2950a8, #2da9e3); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; }
        .footer { background: #333; color: #fff; padding: 20px; text-align: center; font-size: 12px; border-radius: 0 0 10px 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">FundTracer AI</h1>
            <p style="margin: 5px 0 0 0;">AI-Powered Fund Recovery Platform</p>
        </div>
        <div class="content">
            ' . $content . '
        </div>
        <div class="footer">
            <p>&copy; ' . date('Y') . ' FundTracer AI. All rights reserved.</p>
            <p>You received this email because you have an active account with us.</p>
        </div>
    </div>
</body>
</html>';
            
            // Send email using PHP mail() or external service
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: FundTracer AI <noreply@fundtracerai.com>" . "\r\n";
            
            if (mail($user['email'], $subject, $emailHTML, $headers)) {
                // Log email
                $logStmt = $pdo->prepare("
                    INSERT INTO email_logs 
                    (recipient, subject, template_key, status, sent_at, user_id) 
                    VALUES (?, ?, ?, 'sent', NOW(), ?)
                ");
                $logStmt->execute([
                    $user['email'],
                    $subject,
                    $emailTemplate,
                    $user['id']
                ]);
                
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
        'message' => "Sent {$sentCount} emails successfully. {$failedCount} failed.",
        'sent' => $sentCount,
        'failed' => $failedCount,
        'total_users' => count($inactiveUsers),
        'errors' => $errors
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
