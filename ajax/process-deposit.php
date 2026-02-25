<?php
// Use statements must be at the very top
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../session.php';

// Check if PHPMailer is available
$phpMailerAvailable = false;
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $phpMailerAvailable = true;
}


header('Content-Type: application/json');

try {
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method', 405);
    }

    // Check user authentication
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Unauthorized access - Please login', 401);
    }

    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        throw new Exception('Security error - Invalid CSRF token', 403);
    }

    // Validate and sanitize inputs
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
    $methodCode = isset($_POST['payment_method']) ? htmlspecialchars($_POST['payment_method'], ENT_QUOTES, 'UTF-8') : null;

    if (!$amount || $amount <= 0) {
        throw new Exception('Please enter a valid deposit amount (minimum $10)', 400);
    }

    if ($amount < 10) {
        throw new Exception('Minimum deposit amount is $10', 400);
    }

    if (empty($methodCode)) {
        throw new Exception('Please select a payment method', 400);
    }

    // Get user information - Fixed to use correct column names from your schema
    $userStmt = $pdo->prepare("SELECT id, email, first_name, last_name FROM users WHERE id = ?");
    $userStmt->execute([$_SESSION['user_id']]);
    $user = $userStmt->fetch();

    if (!$user) {
        throw new Exception('User not found', 404);
    }

    // Get payment method ID and name
    $stmt = $pdo->prepare("SELECT id, method_name FROM payment_methods WHERE method_code = ? AND is_active = 1");
    $stmt->execute([$methodCode]);
    $paymentMethod = $stmt->fetch();

    if (!$paymentMethod) {
        throw new Exception('Invalid payment method selected', 400);
    }
    $paymentMethodId = $paymentMethod['id'];

    // Handle file upload
    $proofPath = null;
    if (isset($_FILES['proof_of_payment'])) {
        $file = $_FILES['proof_of_payment'];
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('File upload error: ' . $file['error'], 400);
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        $fileType = mime_content_type($file['tmp_name']);
        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception('Only JPG, PNG, and PDF files are allowed', 400);
        }

        // Validate file size (max 2MB)
        $maxSize = 2 * 1024 * 1024; // 2MB
        if ($file['size'] > $maxSize) {
            throw new Exception('File size must be less than 2MB', 400);
        }

        // Create upload directory if it doesn't exist
        $uploadDir = '../uploads/proofs/';
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new Exception('Failed to create upload directory', 500);
            }
        }

        // Generate unique filename
        $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
        $proofPath = $uploadDir . uniqid('deposit_') . '.' . $fileExt;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $proofPath)) {
            throw new Exception('Failed to save proof of payment', 500);
        }
    } else {
        throw new Exception('Proof of payment is required', 400);
    }

    // Generate unique reference number
    $reference = 'DEP-' . time() . '-' . strtoupper(substr(uniqid(), -6));

    // Start database transaction
    $pdo->beginTransaction();

    try {
        // Insert deposit record
        $stmt = $pdo->prepare("INSERT INTO deposits 
                              (user_id, amount, method_code, reference, proof_path, status) 
                              VALUES (:user_id, :amount, :method, :reference, :proof_path, 'pending')");
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':amount' => $amount,
            ':method' => $methodCode,
            ':reference' => $reference,
            ':proof_path' => $proofPath
        ]);
        
        $depositId = $pdo->lastInsertId();

        // Insert transaction record
        $stmt = $pdo->prepare("INSERT INTO transactions 
                              (user_id, type, amount, payment_method_id, reference, status, proof_path) 
                              VALUES (:user_id, 'deposit', :amount, :method_id, :reference, 'pending', :proof_path)");
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':amount' => $amount,
            ':method_id' => $paymentMethodId,
            ':reference' => $reference,
            ':proof_path' => $proofPath
        ]);
        
        $transactionId = $pdo->lastInsertId();

        // Insert transaction attachment
        $stmt = $pdo->prepare("INSERT INTO transaction_attachments 
                              (transaction_id, file_path, file_type) 
                              VALUES (:transaction_id, :file_path, :file_type)");
        $stmt->execute([
            ':transaction_id' => $transactionId,
            ':file_path' => $proofPath,
            ':file_type' => mime_content_type($proofPath)
        ]);

        // Update user balance (commented out for pending status)
        // $stmt = $pdo->prepare("UPDATE users SET balance = balance + :amount WHERE id = :user_id");
        // $stmt->execute([
        //     ':amount' => $amount,
        //     ':user_id' => $_SESSION['user_id']
        // ]);

        // Get current balance
        $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = :user_id");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $currentBalance = $stmt->fetchColumn();

        // Send email notification
        try {
            sendDepositConfirmationEmail($pdo, $user, $amount, $reference, $paymentMethod['method_name']);
        } catch (Exception $emailError) {
            error_log("Email sending failed: " . $emailError->getMessage());
            // Continue processing even if email fails
        }

        // Commit transaction
        $pdo->commit();

        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Your deposit is pending. Please wait while we process your request. A confirmation email has been sent.',
            'reference' => $reference,
            'amount' => number_format($amount, 2),
            'current_balance' => $currentBalance,
            'next_steps' => 'Your deposit will be reviewed and processed within 1-2 business days. You will be notified once approved.'
        ]);

    } catch (PDOException $e) {
        $pdo->rollBack();
        // Delete uploaded file if transaction failed
        if ($proofPath && file_exists($proofPath)) {
            unlink($proofPath);
        }
        throw new Exception('Database error: ' . $e->getMessage(), 500);
    }

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_type' => get_class($e)
    ]);
}

/**
 * Send deposit confirmation email using templates from database
 */
function sendDepositConfirmationEmail($pdo, $user, $amount, $reference, $paymentMethod) {
    global $phpMailerAvailable;
    
    try {
        // Get SMTP settings
        $smtpStmt = $pdo->prepare("SELECT * FROM smtp_settings WHERE is_active = 1 LIMIT 1");
        $smtpStmt->execute();
        $smtpSettings = $smtpStmt->fetch();
        
        if (!$smtpSettings) {
            throw new Exception("No active SMTP configuration found");
        }

        // Get email template from database
        $templateStmt = $pdo->prepare("SELECT * FROM email_templates WHERE template_key = 'deposit_confirmation' LIMIT 1");
        $templateStmt->execute();
        $template = $templateStmt->fetch();

        // If no specific template found, try to get deposit_received template
        if (!$template) {
            $templateStmt = $pdo->prepare("SELECT * FROM email_templates WHERE template_key = 'deposit_received' LIMIT 1");
            $templateStmt->execute();
            $template = $templateStmt->fetch();
        }

        // Get system settings
        $systemStmt = $pdo->prepare("SELECT * FROM system_settings WHERE id = 1");
        $systemStmt->execute();
        $systemSettings = $systemStmt->fetch();

        // Create username from email (use part before @)
        $username = explode('@', $user['email'])[0];

        // Prepare template variables for replacement
        $variables = [
            '{first_name}' => $user['first_name'] ?? '',
            '{last_name}' => $user['last_name'] ?? '',
            '{username}' => $username,
            '{user_name}' => $user['first_name'] . ' ' . $user['last_name'],
            '{email}' => $user['email'],
            '{amount}' => '$' . number_format($amount, 2),
            '{reference}' => $reference,
            '{payment_method}' => $paymentMethod,
            '{date}' => date('Y-m-d H:i:s'),
            '{current_year}' => date('Y'),
            '{site_name}' => 'Fundtracer AI',
            '{site_url}' => $systemSettings['site_url'] ?? 'https://your-site.com',
            '{support_email}' => $systemSettings['contact_email'] ?? 'support@your-site.com',
            '{brand_name}' => $systemSettings['brand_name'] ?? 'Fundtracer AI',
            '{contact_phone}' => $systemSettings['contact_phone'] ?? '',
            '{contact_email}' => $systemSettings['contact_email'] ?? ''
        ];

        // Use template from database or fallback to default
        if ($template) {
            $subject = $template['subject'] ?? 'Deposit Confirmation - ' . $reference;
            $htmlBody = $template['content'] ?? getDefaultDepositTemplate();
            
            // Replace variables in template
            foreach ($variables as $key => $value) {
                $subject = str_replace($key, $value, $subject);
                $htmlBody = str_replace($key, $value, $htmlBody);
            }
        } else {
            // Use default template if no database template found
            $subject = 'Deposit Confirmation - ' . $reference;
            $htmlBody = getDefaultDepositTemplate();
            
            // Replace variables in default template
            foreach ($variables as $key => $value) {
                $subject = str_replace($key, $value, $subject);
                $htmlBody = str_replace($key, $value, $htmlBody);
            }
        }

        $textBody = strip_tags($htmlBody);

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
                $template['id'] ?? null,
                $user['email'],
                $subject,
                $htmlBody
            ]);
        } catch (Exception $logError) {
            // Continue even if logging fails
            error_log("Failed to log email: " . $logError->getMessage());
        }
        
        error_log("Deposit confirmation email sent to: " . $user['email'] . " for reference: " . $reference);
        
    } catch (Exception $e) {
        // Log failed email attempt
        try {
            $logStmt = $pdo->prepare("INSERT INTO email_logs (template_id, recipient, subject, content, sent_at, status, error_message) VALUES (?, ?, ?, ?, NOW(), 'failed', ?)");
            $logStmt->execute([
                isset($template) ? $template['id'] ?? null : null,
                $user['email'] ?? 'unknown',
                $subject ?? 'Deposit Confirmation',
                $htmlBody ?? '',
                $e->getMessage()
            ]);
        } catch (Exception $logError) {
            error_log("Failed to log email error: " . $logError->getMessage());
        }
        
        error_log("Email sending failed: " . $e->getMessage());
        throw new Exception("Failed to send confirmation email: " . $e->getMessage());
    }
}

/**
 * Default email template if none found in database
 */
function getDefaultDepositTemplate() {
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Deposit Confirmation</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(90deg,#2950a8 0,#2da9e3 100%); color: white; padding: 20px; text-align: center; border-radius: 5px; }
            .content { padding: 20px; background: #f9f9f9; }
            .details { background: white; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #2950a8; }
            .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
            .success-badge { background: #28a745; color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>{site_name}</h1>
                <h2>🎯 Deposit Confirmation</h2>
                <span class="success-badge">✅ Successfully Received</span>
            </div>
            <div class="content">
                <h3>Dear {first_name} {last_name},</h3>
                <p>Thank you for your deposit submission to <strong>Fundtracer AI</strong> - Next-Generation Scam Recovery & Fund Tracing platform. We have received your deposit request and it is currently being processed by our financial team.</p>
                
                <div class="details">
                    <h4>💳 Transaction Details:</h4>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr><td style="padding: 5px; font-weight: bold;">Reference Number:</td><td style="padding: 5px;">{reference}</td></tr>
                        <tr><td style="padding: 5px; font-weight: bold;">Amount:</td><td style="padding: 5px; color: #28a745; font-weight: bold;">{amount}</td></tr>
                        <tr><td style="padding: 5px; font-weight: bold;">Payment Method:</td><td style="padding: 5px;">{payment_method}</td></tr>
                        <tr><td style="padding: 5px; font-weight: bold;">Date & Time:</td><td style="padding: 5px;">{date}</td></tr>
                        <tr><td style="padding: 5px; font-weight: bold;">Status:</td><td style="padding: 5px;"><span style="background: #ffc107; color: #000; padding: 2px 8px; border-radius: 10px;">⏳ Pending Processing</span></td></tr>
                    </table>
                </div>
                
                <div style="background: #e8f4f8; border: 1px solid #2950a8; border-radius: 8px; padding: 15px; margin: 15px 0;">
                    <h4 style="color: #2950a8; margin-top: 0;">🔄 What happens next?</h4>
                    <ul style="margin: 0; padding-left: 20px;">
                        <li><strong>Review Process:</strong> Our team will review your deposit within 1-2 business days</li>
                        <li><strong>Verification:</strong> We will verify your payment proof and process the transaction</li>
                        <li><strong>Credit to Account:</strong> Once approved, funds will be added to your account balance</li>
                        <li><strong>Notification:</strong> You will receive another email notification when processing is complete</li>
                        <li><strong>Dashboard Access:</strong> Monitor status anytime in your account dashboard</li>
                    </ul>
                </div>
                
                <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 5px; padding: 10px; margin: 10px 0;">
                    <p style="margin: 0;"><strong>⚠️ Important Security Notice:</strong> If you did not authorize this deposit, please contact our support team immediately at {support_email} with reference number <strong>{reference}</strong>.</p>
                </div>
                
                <p>Need assistance? Our support team is available 24/7 to help with any questions about your deposit or account.</p>
                
                <p style="margin-bottom: 0;">Thank you for choosing <strong>Fundtracer AI</strong> for your fund recovery needs!</p>
                
                <p style="margin-bottom: 0;"><strong>Best regards,</strong><br>The Fundtracer AI Team<br>Next-Generation Scam Recovery & Fund Tracing</p>
            </div>
            <div class="footer">
                <p>&copy; {current_year} {site_name}. All rights reserved.</p>
                <p>🔒 This is an automated secure message. Please do not reply directly to this email.</p>
                <p>Contact Support: {support_email} | Visit: {site_url}</p>
            </div>
        </div>
    </body>
    </html>';
}
?>