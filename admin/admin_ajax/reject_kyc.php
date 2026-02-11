<?php
// Use statements must be at the very top
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Error reporting - consider setting to 0 in production
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Check if PHPMailer is available
$phpMailerAvailable = false;
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $phpMailerAvailable = true;
}

require_once '../admin_session.php';
header('Content-Type: application/json');

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid KYC request ID']);
    exit();
}

if (empty($_POST['reason'])) {
    echo json_encode(['success' => false, 'message' => 'Rejection reason is required']);
    exit();
}

$kycId = (int)$_POST['id'];
$reason = trim($_POST['reason']);

try {
    $pdo->beginTransaction();
    
    // Get KYC request details
    $stmt = $pdo->prepare("SELECT * FROM kyc_verification_requests WHERE id = ?");
    $stmt->execute([$kycId]);
    $kyc = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$kyc) {
        throw new Exception('KYC request not found');
    }
    
    if ($kyc['status'] !== 'pending') {
        throw new Exception('KYC request is not pending');
    }
    
    // Get user details for email
    $userStmt = $pdo->prepare("SELECT id, email, first_name, last_name FROM users WHERE id = ?");
    $userStmt->execute([$kyc['user_id']]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        throw new Exception('User not found');
    }
    
    // Update KYC status
    $stmt = $pdo->prepare("
        UPDATE kyc_verification_requests 
        SET 
            status = 'rejected',
            rejection_reason = ?,
            verified_by = ?,
            verified_at = NOW(),
            updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$reason, $_SESSION['admin_id'], $kycId]);
    
    // Send rejection email
    sendKYCEmail($pdo, $user, 'kyc_rejected', $kycId, $reason);
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'KYC rejected successfully'
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Failed to reject KYC',
        'error' => $e->getMessage()
    ]);
}

/**
 * Send KYC status email
 */
function sendKYCEmail($pdo, $user, $templateKey, $kycId, $rejectionReason = null) {
    try {
        // Get email template from database
        $templateStmt = $pdo->prepare("SELECT * FROM email_templates WHERE template_key = ? LIMIT 1");
        $templateStmt->execute([$templateKey]);
        $template = $templateStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$template) {
            throw new Exception("Email template not found: " . $templateKey);
        }
        
        // Get SMTP settings
        $smtpStmt = $pdo->prepare("SELECT * FROM smtp_settings WHERE is_active = 1 LIMIT 1");
        $smtpStmt->execute();
        $smtpSettings = $smtpStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$smtpSettings) {
            throw new Exception("No active SMTP configuration found");
        }
        
        // Get system settings
        $systemStmt = $pdo->prepare("SELECT * FROM system_settings WHERE id = 1");
        $systemStmt->execute();
        $systemSettings = $systemStmt->fetch(PDO::FETCH_ASSOC);
        
        // Prepare template variables for replacement
        $variables = [
            '{first_name}' => $user['first_name'] ?? '',
            '{last_name}' => $user['last_name'] ?? '',
            '{user_name}' => $user['first_name'] . ' ' . $user['last_name'],
            '{email}' => $user['email'],
            '{kyc_id}' => $kycId,
            '{date}' => date('Y-m-d H:i:s'),
            '{current_year}' => date('Y'),
            '{site_name}' => $systemSettings['site_name'] ?? 'Fundtracer AI',
            '{site_url}' => $systemSettings['site_url'] ?? 'https://your-site.com',
            '{support_email}' => $systemSettings['contact_email'] ?? 'support@your-site.com',
            '{brand_name}' => $systemSettings['brand_name'] ?? 'Fundtracer AI',
            '{contact_phone}' => $systemSettings['contact_phone'] ?? '',
            '{contact_email}' => $systemSettings['contact_email'] ?? ''
        ];
        
        // Add rejection reason if provided
        if ($rejectionReason) {
            $variables['{rejection_reason}'] = $rejectionReason;
            $variables['{resubmit_link}'] = $systemSettings['site_url'] . '/kyc.php' ?? 'https://your-site.com/kyc.php';
        }
        
        // Replace variables in template
        $subject = $template['subject'];
        $htmlBody = $template['content'];
        
        foreach ($variables as $key => $value) {
            $subject = str_replace($key, $value, $subject);
            $htmlBody = str_replace($key, $value, $htmlBody);
        }
        
        $textBody = strip_tags($htmlBody);
        
        // Use global PHPMailer availability check
        global $phpMailerAvailable;
        
        // Send email using PHPMailer if available
        if ($phpMailerAvailable) {
            $mail = new PHPMailer(true);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $smtpSettings['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtpSettings['username'];
            $mail->Password   = $smtpSettings['password'];
            $mail->SMTPSecure = $smtpSettings['encryption'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $smtpSettings['port'];
            
            // Recipients
            $mail->setFrom($smtpSettings['from_email'], $smtpSettings['from_name']);
            $mail->addAddress($user['email'], $user['first_name'] . ' ' . $user['last_name']);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = $textBody;
            
            $mail->send();
        } else {
            // Fallback to PHP mail() function
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: ' . $smtpSettings['from_name'] . ' <' . $smtpSettings['from_email'] . '>' . "\r\n";
            
            if (!mail($user['email'], $subject, $htmlBody, $headers)) {
                throw new Exception("Failed to send email using mail() function");
            }
        }
        
        // Log successful email in database
        try {
            $logStmt = $pdo->prepare("INSERT INTO email_logs (template_id, recipient, subject, content, sent_at, status) VALUES (?, ?, ?, ?, NOW(), 'sent')");
            $logStmt->execute([
                $template['id'],
                $user['email'],
                $subject,
                $htmlBody
            ]);
        } catch (Exception $logError) {
            error_log("Failed to log email: " . $logError->getMessage());
        }
        
        error_log("KYC email sent to: " . $user['email'] . " for KYC ID: " . $kycId);
        
    } catch (Exception $e) {
        // Log failed email attempt
        try {
            $logStmt = $pdo->prepare("INSERT INTO email_logs (template_id, recipient, subject, content, sent_at, status, error_message) VALUES (?, ?, ?, ?, NOW(), 'failed', ?)");
            $logStmt->execute([
                isset($template) ? $template['id'] : null,
                $user['email'] ?? 'unknown',
                $subject ?? 'KYC Status Update',
                $htmlBody ?? '',
                $e->getMessage()
            ]);
        } catch (Exception $logError) {
            error_log("Failed to log email error: " . $logError->getMessage());
        }
        
        error_log("KYC email sending failed: " . $e->getMessage());
        throw new Exception("Failed to send KYC email: " . $e->getMessage());
    }
}
?>