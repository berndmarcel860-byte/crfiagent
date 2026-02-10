<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session.php';

// Check PHPMailer
$phpMailerAvailable = false;
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $phpMailerAvailable = true;
}

header('Content-Type: application/json');

try {
    // 1️⃣ Request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method', 405);
    }

    // 2️⃣ Authentication
    if (empty($_SESSION['user_id'])) {
        throw new Exception('Unauthorized access - Please login', 401);
    }

    // 3️⃣ CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        throw new Exception('Security error - Invalid CSRF token', 403);
    }

    // 4️⃣ OTP Verification (from indexotp.php)
    if (empty($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
        echo json_encode([
            'success' => false,
            'message' => 'Please verify your email OTP before submitting a withdrawal request.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Check expiry
    if (isset($_SESSION['otp_expire']) && time() > $_SESSION['otp_expire']) {
        unset($_SESSION['otp_verified'], $_SESSION['withdraw_otp'], $_SESSION['otp_expire']);
        throw new Exception('Your OTP has expired. Please request a new one.', 400);
    }

    // Invalidate OTP after use
    unset($_SESSION['otp_verified']);
    unset($_SESSION['withdraw_otp']);
    unset($_SESSION['otp_expire']);

    // 5️⃣ Validate inputs
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
    $methodCode = isset($_POST['payment_method']) ? htmlspecialchars($_POST['payment_method'], ENT_QUOTES, 'UTF-8') : null;
    $details = isset($_POST['payment_details']) ? htmlspecialchars($_POST['payment_details'], ENT_QUOTES, 'UTF-8') : null;

    if (!$amount || $amount <= 0) throw new Exception('Please enter a valid withdrawal amount', 400);
    if ($amount < 10) throw new Exception('Minimum withdrawal amount is $10', 400);
    if (empty($methodCode)) throw new Exception('Please select a payment method', 400);
    if (empty($details)) throw new Exception('Please provide payment details', 400);

    // Prevent double submit
    if (isset($_SESSION['withdraw_in_progress']) && $_SESSION['withdraw_in_progress'] === true) {
        throw new Exception('Withdrawal already in progress. Please wait a moment before submitting again.', 429);
    }
    $_SESSION['withdraw_in_progress'] = true;

    // 6️⃣ Fetch user
    $userStmt = $pdo->prepare("SELECT id, email, first_name, last_name, balance FROM users WHERE id = ?");
    $userStmt->execute([$_SESSION['user_id']]);
    $user = $userStmt->fetch();

    if (!$user) throw new Exception('User not found', 404);

    // 7️⃣ Payment method
    $stmt = $pdo->prepare("SELECT id, method_name FROM payment_methods WHERE method_code = ? AND is_active = 1 AND allows_withdrawal = 1");
    $stmt->execute([$methodCode]);
    $paymentMethod = $stmt->fetch();
    if (!$paymentMethod) throw new Exception('Invalid or inactive payment method selected', 400);
    $paymentMethodId = $paymentMethod['id'];

    // 8️⃣ Balance check
    if ($user['balance'] < $amount) {
        throw new Exception('Insufficient balance. Available: $' . number_format($user['balance'], 2), 400);
    }

    // 9️⃣ Process withdrawal
    $reference = 'WDR-' . time() . '-' . strtoupper(substr(uniqid(), -6));
    $pdo->beginTransaction();

    try {
        // Insert withdrawal
        $stmt = $pdo->prepare("INSERT INTO withdrawals 
            (user_id, amount, method_code, payment_details, reference, status) 
            VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$_SESSION['user_id'], $amount, $methodCode, $details, $reference]);

        // Insert transaction
        $stmt = $pdo->prepare("INSERT INTO transactions 
            (user_id, type, amount, payment_method_id, reference, status, payment_details) 
            VALUES (?, 'withdrawal', ?, ?, ?, 'pending', ?)");
        $stmt->execute([$_SESSION['user_id'], $amount, $paymentMethodId, $reference, $details]);

        // Deduct balance
        $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
        $stmt->execute([$amount, $_SESSION['user_id']]);

        // Get new balance
        $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $newBalance = $stmt->fetchColumn();

        // Email confirmation
        try {
            sendWithdrawalConfirmationEmail($pdo, $user, $amount, $reference, $paymentMethod['method_name'], $details);
        } catch (Exception $mailError) {
            error_log("[Withdrawal Email Error] " . $mailError->getMessage());
        }

        $pdo->commit();
        unset($_SESSION['withdraw_in_progress']);

        echo json_encode([
            'success' => true,
            'message' => 'Withdrawal request submitted successfully! A confirmation email has been sent.',
            'data' => [
                'reference' => $reference,
                'amount' => number_format($amount, 2),
                'new_balance' => number_format($newBalance, 2),
                'processing_time' => '1-3 business days'
            ]
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    } catch (PDOException $e) {
        $pdo->rollBack();
        unset($_SESSION['withdraw_in_progress']);
        throw new Exception('Database error: ' . $e->getMessage(), 500);
    }

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_type' => get_class($e)
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

/**
 * 📧 Send withdrawal confirmation email
 */
function sendWithdrawalConfirmationEmail($pdo, $user, $amount, $reference, $paymentMethod, $paymentDetails) {
    global $phpMailerAvailable;

    $smtpStmt = $pdo->prepare("SELECT * FROM smtp_settings WHERE is_active = 1 LIMIT 1");
    $smtpStmt->execute();
    $smtp = $smtpStmt->fetch();
    if (!$smtp) throw new Exception('No active SMTP configuration found');

    $sysStmt = $pdo->prepare("SELECT * FROM system_settings WHERE id = 1");
    $sysStmt->execute();
    $system = $sysStmt->fetch();

    $subject = "Withdrawal Confirmation - {$reference}";
    $body = getDefaultWithdrawalTemplate();

    $replacements = [
        '{first_name}' => $user['first_name'],
        '{last_name}' => $user['last_name'],
        '{amount}' => '$' . number_format($amount, 2),
        '{reference}' => $reference,
        '{payment_method}' => $paymentMethod,
        '{payment_details}' => $paymentDetails,
        '{date}' => date('Y-m-d H:i:s'),
        '{site_name}' => $system['brand_name'] ?? 'TradeVest Crypto',
        '{support_email}' => $system['contact_email'] ?? 'support@tradevestcrypto.de',
        '{current_year}' => date('Y')
    ];

    foreach ($replacements as $k => $v) {
        $subject = str_replace($k, $v, $subject);
        $body = str_replace($k, $v, $body);
    }

    if ($phpMailerAvailable) {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $smtp['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $smtp['username'];
        $mail->Password = $smtp['password'];
        $mail->SMTPSecure = $smtp['encryption'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $smtp['port'];
        $mail->setFrom($smtp['from_email'], $smtp['from_name']);
        $mail->addAddress($user['email']);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->send();

        // Log email
        try {
            $logStmt = $pdo->prepare("INSERT INTO email_logs (template_id, recipient, subject, content, sent_at, status)
                                      VALUES (NULL, ?, ?, ?, NOW(), 'sent')");
            $logStmt->execute([$user['email'], $subject, $body]);
        } catch (Exception $logErr) {
            error_log("Email log failed: " . $logErr->getMessage());
        }
    } else {
        throw new Exception('Mailer not available');
    }
}

/**
 * 📄 Default email template
 */
function getDefaultWithdrawalTemplate() {
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Withdrawal Confirmation</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(90deg,#2950a8 0,#2da9e3 100%); color: white; padding: 20px; text-align: center; border-radius: 5px; }
            .content { padding: 20px; background: #f9f9f9; }
            .details { background: white; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #28a745; }
            .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
            .success-badge { background: #28a745; color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px; }
            .warning-box { background: #fff3cd; border: 1px solid #ffc107; border-radius: 5px; padding: 10px; margin: 10px 0; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>{site_name}</h1>
                <h2>💰 Withdrawal Request Received</h2>
                <span class="success-badge">✅ Request Submitted</span>
            </div>
            <div class="content">
                <h3>Dear {first_name} {last_name},</h3>
                <p>We have received your withdrawal request from your <strong>Fundtracer AI</strong> account. Your request is now being processed by our financial team.</p>
                
                <div class="details">
                    <h4>💳 Withdrawal Details:</h4>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr><td style="padding: 5px; font-weight: bold;">Reference Number:</td><td style="padding: 5px;">{reference}</td></tr>
                        <tr><td style="padding: 5px; font-weight: bold;">Amount:</td><td style="padding: 5px; color: #dc3545; font-weight: bold;">{amount}</td></tr>
                        <tr><td style="padding: 5px; font-weight: bold;">Payment Method:</td><td style="padding: 5px;">{payment_method}</td></tr>
                        <tr><td style="padding: 5px; font-weight: bold;">Payment Details:</td><td style="padding: 5px;">{payment_details}</td></tr>
                        <tr><td style="padding: 5px; font-weight: bold;">Date & Time:</td><td style="padding: 5px;">{date}</td></tr>
                        <tr><td style="padding: 5px; font-weight: bold;">Status:</td><td style="padding: 5px;"><span style="background: #17a2b8; color: white; padding: 2px 8px; border-radius: 10px;">🔄 Processing</span></td></tr>
                    </table>
                </div>
                
                <div style="background: #e1f5fe; border: 1px solid #0288d1; border-radius: 8px; padding: 15px; margin: 15px 0;">
                    <h4 style="color: #0288d1; margin-top: 0;">⏱️ Processing Timeline:</h4>
                    <ul style="margin: 0; padding-left: 20px;">
                        <li><strong>Verification:</strong> We will verify your withdrawal details within 2-4 hours</li>
                        <li><strong>Security Check:</strong> Standard security verification process</li>
                        <li><strong>Payment Processing:</strong> Funds will be transferred to your specified account</li>
                        <li><strong>Completion Time:</strong> Typically completes within 1-3 business days</li>
                        <li><strong>Final Notification:</strong> You will receive confirmation when payment is sent</li>
                    </ul>
                </div>
                
                <div class="warning-box">
                    <p style="margin: 0;"><strong>⚠️ Important Notice:</strong> The requested amount has been temporarily deducted from your account balance. If the withdrawal fails, the amount will be automatically refunded to your balance.</p>
                </div>
                
                <div style="background: #f8f9fa; border-left: 4px solid #6c757d; padding: 15px; margin: 15px 0;">
                    <h4 style="color: #495057; margin-top: 0;">📞 Need Assistance?</h4>
                    <p style="margin-bottom: 5px;">Contact our support team if you have any questions:</p>
                    <p style="margin: 0;"><strong>Email:</strong> {support_email}</p>
                    <p style="margin: 0;"><strong>Reference:</strong> {reference}</p>
                </div>
                
                <p>We appreciate your trust in <strong>Fundtracer AI</strong> for your financial transactions and fund recovery needs.</p>
                
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
