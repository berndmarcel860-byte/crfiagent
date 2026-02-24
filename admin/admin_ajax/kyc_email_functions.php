<?php
// File: admin_ajax/kyc_email_functions.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendKYCEmail($pdo, $user, $templateKey, $kycId, $rejectionReason = null) {
    try {
        // Get email template from database
        $templateStmt = $pdo->prepare("SELECT * FROM email_templates WHERE template_key = ? LIMIT 1");
        $templateStmt->execute([$templateKey]);
        $template = $templateStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$template) {
            error_log("Email template not found: " . $templateKey);
            return false;
        }
        
        // Get SMTP settings
        $smtpStmt = $pdo->prepare("SELECT * FROM smtp_settings WHERE is_active = 1 LIMIT 1");
        $smtpStmt->execute();
        $smtpSettings = $smtpStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$smtpSettings) {
            error_log("No active SMTP configuration found");
            return false;
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
            $variables['{resubmit_link}'] = ($systemSettings['site_url'] ?? 'https://your-site.com') . '/kyc.php';
        }
        
        // Replace variables in template
        $subject = $template['subject'];
        $htmlBody = $template['content'];
        
        foreach ($variables as $key => $value) {
            $subject = str_replace($key, $value, $subject);
            $htmlBody = str_replace($key, $value, $htmlBody);
        }
        
        $textBody = strip_tags($htmlBody);
        
        // Check if PHPMailer is available
        $phpMailerAvailable = false;
        $vendorPath = __DIR__ . '/../../vendor/autoload.php';
        if (file_exists($vendorPath)) {
            require_once $vendorPath;
            $phpMailerAvailable = true;
        }
        
        // Send email using PHPMailer if available
        if ($phpMailerAvailable) {
            $mail = new PHPMailer(true);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $smtpSettings['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtpSettings['username'];
            $mail->Password   = $smtpSettings['password'];
            
            // Determine encryption type
            if ($smtpSettings['encryption'] === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($smtpSettings['encryption'] === 'tls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
            
            $mail->Port       = $smtpSettings['port'];
            
            // Enable debugging if needed
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            
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
        return true;
        
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
        return false;
    }
}
?>