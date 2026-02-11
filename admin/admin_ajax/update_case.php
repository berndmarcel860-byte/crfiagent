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
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
    $phpMailerAvailable = true;
}

require_once '../admin_session.php';
require_once '../mail_functions.php'; // Include mail functions

header('Content-Type: application/json');

// Verify admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Admin not logged in',
        'error' => 'Session admin_id not set'
    ]);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['case_id'])) {
    echo json_encode(['success' => false, 'message' => 'Case ID required']);
    exit();
}

// Define complete status translations including all possible statuses
$statusTranslations = [
    'open' => 'Offen',
    'document_required' => 'Dokumente erforderlich',
    'documents_required' => 'Dokumente erforderlich', // Alternative spelling
    'under_review' => 'In Prüfung',
    'in_progress' => 'In Bearbeitung',
    'completed' => 'Abgeschlossen',
    'rejected' => 'Abgelehnt',
    'pending' => 'Ausstehend', // Add any other statuses you might use
];

try {
    $pdo->beginTransaction();
    
    // Get current case details including user info
    $stmt = $pdo->prepare("
        SELECT c.status, c.user_id, u.email, u.first_name, u.last_name, c.case_number
        FROM cases c
        JOIN users u ON c.user_id = u.id
        WHERE c.id = ?
    ");
    $stmt->execute([$data['case_id']]);
    $case = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$case) {
        throw new Exception('Case not found');
    }
    
    $currentStatus = $case['status'];
    $newStatus = $data['status'];
    
    // Update case
    $stmt = $pdo->prepare("
        UPDATE cases SET
            status = :status,
            admin_notes = :admin_notes,
            admin_id = :admin_id,
            updated_at = NOW()
        WHERE id = :case_id
    ");
    
    $stmt->execute([
        ':status' => $newStatus,
        ':admin_notes' => $data['admin_notes'] ?? null,
        ':admin_id' => $data['admin_id'] ?? $_SESSION['admin_id'],
        ':case_id' => $data['case_id']
    ]);
    
    // Record status change if different
    if ($currentStatus != $newStatus) {
        if (empty($_SESSION['admin_id'])) {
            throw new Exception('Cannot record status change - no admin ID in session');
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO case_status_history (
                case_id, old_status, new_status, changed_by, notes
            ) VALUES (
                :case_id, :old_status, :new_status, :admin_id, :notes
            )
        ");
        
        $stmt->execute([
            ':case_id' => $data['case_id'],
            ':old_status' => $currentStatus,
            ':new_status' => $newStatus,
            ':admin_id' => $_SESSION['admin_id'],
            ':notes' => $data['status_notes'] ?? null
        ]);
        
        // Send status update email
        sendCaseStatusUpdateEmail($pdo, $case, $data['case_id'], $currentStatus, $newStatus, $data);
        
        // Special handling for document_required status
        if (in_array(strtolower($newStatus), ['document_required', 'documents_required']) && 
            !empty($data['required_documents'])) {
            
            // Send documents required email
            sendDocumentsRequiredEmail($pdo, $case, $data['case_id'], $data);
        }
    }
    
    $pdo->commit();
    
    $response = [
        'success' => true,
        'message' => 'Case updated successfully',
        'data' => [
            'case_id' => $data['case_id'],
            'old_status' => $currentStatus,
            'new_status' => $newStatus,
            'status_translated' => $statusTranslations[strtolower($newStatus)] ?? $newStatus
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    $pdo->rollBack();
    error_log('Case update error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update case',
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}

/**
 * Send case status update email notification
 */
function sendCaseStatusUpdateEmail($pdo, $userData, $caseId, $oldStatus, $newStatus, $updateData) {
    try {
        // Generate a unique tracking token
        $trackingToken = bin2hex(random_bytes(16));

        // Get email template from database
        $templateStmt = $pdo->prepare("SELECT * FROM email_templates WHERE template_key = 'case_status_updated' LIMIT 1");
        $templateStmt->execute();
        $template = $templateStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$template) {
            throw new Exception("Email template not found: case_status_updated");
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
            '{first_name}' => $userData['first_name'] ?? '',
            '{last_name}' => $userData['last_name'] ?? '',
            '{user_name}' => $userData['first_name'] . ' ' . $userData['last_name'],
            '{email}' => $userData['email'],
            '{case_number}' => $userData['case_number'] ?? 'N/A',
            '{case_id}' => $caseId,
            '{old_status}' => $oldStatus,
            '{new_status}' => $newStatus,
            '{status_notes}' => $updateData['status_notes'] ?? '',
            '{update_date}' => date('Y-m-d H:i:s'),
            '{current_year}' => date('Y'),
            '{site_name}' => $systemSettings['site_name'] ?? 'ScamRecovery',
            '{site_url}' => $systemSettings['site_url'] ?? 'https://your-site.com',
            '{support_email}' => $systemSettings['contact_email'] ?? 'support@your-site.com',
            '{brand_name}' => $systemSettings['brand_name'] ?? 'ScamRecovery',
            '{contact_phone}' => $systemSettings['contact_phone'] ?? '',
            '{contact_email}' => $systemSettings['contact_email'] ?? ''
        ];
        
        // Replace variables in template
        $subject = $template['subject'];
        $htmlBody = $template['content'];
        
        foreach ($variables as $key => $value) {
            $subject = str_replace($key, $value, $subject);
            $htmlBody = str_replace($key, $value, $htmlBody);
        }
        
        // Add tracking pixel to the email body
        $trackingPixelUrl = $systemSettings['site_url'] . '/track.php?token=' . $trackingToken;
        $trackingPixel = '<img src="' . $trackingPixelUrl . '" width="1" height="1" alt="" style="display:none;" />';
        
        // Insert tracking pixel before the closing body tag
        if (strpos($htmlBody, '</body>') !== false) {
            $htmlBody = str_replace('</body>', $trackingPixel . '</body>', $htmlBody);
        } else {
            // If no body tag found, append it at the end
            $htmlBody .= $trackingPixel;
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
            $mail->addAddress($userData['email'], $userData['first_name'] . ' ' . $userData['last_name']);
            
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
            
            if (!mail($userData['email'], $subject, $htmlBody, $headers)) {
                throw new Exception("Failed to send email using mail() function");
            }
        }
        
        // Log successful email in database
        try {
            $logStmt = $pdo->prepare("
                INSERT INTO email_logs 
                (template_id, recipient, subject, content, sent_at, status, tracking_token) 
                VALUES (?, ?, ?, ?, NOW(), 'sent', ?)
            ");
            $logStmt->execute([
                $template['id'],
                $userData['email'],
                $subject,
                $htmlBody,
                $trackingToken
            ]);
        } catch (Exception $logError) {
            error_log("Failed to log email: " . $logError->getMessage());
        }
        
        error_log("Case status update email sent to: " . $userData['email'] . " for case ID: " . $caseId);
        
    } catch (Exception $e) {
        // Log failed email attempt
        try {
            $logStmt = $pdo->prepare("INSERT INTO email_logs (template_id, recipient, subject, content, sent_at, status, error_message) VALUES (?, ?, ?, ?, NOW(), 'failed', ?)");
            $logStmt->execute([
                isset($template) ? $template['id'] : null,
                $userData['email'] ?? 'unknown',
                $subject ?? 'Case Status Update',
                $htmlBody ?? '',
                $e->getMessage()
            ]);
        } catch (Exception $logError) {
            error_log("Failed to log email error: " . $logError->getMessage());
        }
        
        error_log("Case status update email sending failed: " . $e->getMessage());
        // Don't throw exception - email failure shouldn't break the case update
    }
}

/**
 * Send documents required email notification
 */
function sendDocumentsRequiredEmail($pdo, $userData, $caseId, $updateData) {
    try {
        // Generate a unique tracking token
        $trackingToken = bin2hex(random_bytes(16));

        // Get email template from database
        $templateStmt = $pdo->prepare("SELECT * FROM email_templates WHERE template_key = 'documents_required' LIMIT 1");
        $templateStmt->execute();
        $template = $templateStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$template) {
            throw new Exception("Email template not found: documents_required");
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
        
        // Prepare required documents list
        $requiredDocs = is_array($updateData['required_documents']) 
            ? $updateData['required_documents'] 
            : explode(',', $updateData['required_documents']);
        
        $documentsList = '<ul>';
        foreach ($requiredDocs as $doc) {
            $documentsList .= '<li>' . htmlspecialchars(trim($doc)) . '</li>';
        }
        $documentsList .= '</ul>';
        
        // Prepare template variables for replacement
        $variables = [
            '{first_name}' => $userData['first_name'] ?? '',
            '{last_name}' => $userData['last_name'] ?? '',
            '{user_name}' => $userData['first_name'] . ' ' . $userData['last_name'],
            '{email}' => $userData['email'],
            '{case_number}' => $userData['case_number'] ?? 'N/A',
            '{case_id}' => $caseId,
            '{required_documents}' => $documentsList,
            '{additional_notes}' => $updateData['status_notes'] ?? '',
            '{upload_link}' => $systemSettings['site_url'] . '/documents.php?case=' . $caseId ?? 'https://your-site.com/documents.php',
            '{deadline}' => $updateData['deadline'] ?? 'ASAP',
            '{current_year}' => date('Y'),
            '{site_name}' => $systemSettings['site_name'] ?? 'ScamRecovery',
            '{site_url}' => $systemSettings['site_url'] ?? 'https://your-site.com',
            '{support_email}' => $systemSettings['contact_email'] ?? 'support@your-site.com',
            '{brand_name}' => $systemSettings['brand_name'] ?? 'ScamRecovery',
            '{contact_phone}' => $systemSettings['contact_phone'] ?? '',
            '{contact_email}' => $systemSettings['contact_email'] ?? ''
        ];
        
        // Replace variables in template
        $subject = $template['subject'];
        $htmlBody = $template['content'];
        
        foreach ($variables as $key => $value) {
            $subject = str_replace($key, $value, $subject);
            $htmlBody = str_replace($key, $value, $htmlBody);
        }
        
        // Add tracking pixel to the email body
        $trackingPixelUrl = $systemSettings['site_url'] . '/track.php?token=' . $trackingToken;
        $trackingPixel = '<img src="' . $trackingPixelUrl . '" width="1" height="1" alt="" style="display:none;" />';
        
        // Insert tracking pixel before the closing body tag
        if (strpos($htmlBody, '</body>') !== false) {
            $htmlBody = str_replace('</body>', $trackingPixel . '</body>', $htmlBody);
        } else {
            // If no body tag found, append it at the end
            $htmlBody .= $trackingPixel;
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
            $mail->addAddress($userData['email'], $userData['first_name'] . ' ' . $userData['last_name']);
            
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
            
            if (!mail($userData['email'], $subject, $htmlBody, $headers)) {
                throw new Exception("Failed to send email using mail() function");
            }
        }
        
        // Log successful email in database
        try {
            $logStmt = $pdo->prepare("
                INSERT INTO email_logs 
                (template_id, recipient, subject, content, sent_at, status, tracking_token) 
                VALUES (?, ?, ?, ?, NOW(), 'sent', ?)
            ");
            $logStmt->execute([
                $template['id'],
                $userData['email'],
                $subject,
                $htmlBody,
                $trackingToken
            ]);
        } catch (Exception $logError) {
            error_log("Failed to log email: " . $logError->getMessage());
        }
        
        error_log("Documents required email sent to: " . $userData['email'] . " for case ID: " . $caseId);
        
    } catch (Exception $e) {
        // Log failed email attempt
        try {
            $logStmt = $pdo->prepare("INSERT INTO email_logs (template_id, recipient, subject, content, sent_at, status, error_message) VALUES (?, ?, ?, ?, NOW(), 'failed', ?)");
            $logStmt->execute([
                isset($template) ? $template['id'] : null,
                $userData['email'] ?? 'unknown',
                $subject ?? 'Documents Required',
                $htmlBody ?? '',
                $e->getMessage()
            ]);
        } catch (Exception $logError) {
            error_log("Failed to log email error: " . $logError->getMessage());
        }
        
        error_log("Documents required email sending failed: " . $e->getMessage());
        // Don't throw exception - email failure shouldn't break the case update
    }
}
?>