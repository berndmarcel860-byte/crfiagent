<?php
/**
 * Withdrawal Processing Script – Kryptox
 * ---------------------------------------------------------
 * Handles withdrawal requests securely with:
 *  - CSRF + OTP validation
 *  - Transactional DB writes
 *  - Balance update
 *  - Email confirmation
 *  - Notification (User + Admin)
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session.php';
require_once __DIR__ . '/../notify_user.php'; // ✅ Include notification helper

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

    // 3️⃣ CSRF token check
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        throw new Exception('Security error - Invalid CSRF token', 403);
    }

    // 4️⃣ OTP Verification
    if (empty($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
        throw new Exception('Please verify your email OTP before submitting a withdrawal request.', 400);
    }

    if (isset($_SESSION['otp_expire']) && time() > $_SESSION['otp_expire']) {
        unset($_SESSION['otp_verified'], $_SESSION['withdraw_otp'], $_SESSION['otp_expire']);
        throw new Exception('Your OTP has expired. Please request a new one.', 400);
    }

    // Invalidate OTP after use
    unset($_SESSION['otp_verified'], $_SESSION['withdraw_otp'], $_SESSION['otp_expire']);

    // 5️⃣ Validate inputs
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
    $methodCode = htmlspecialchars(trim($_POST['payment_method'] ?? ''), ENT_QUOTES, 'UTF-8');
    $details = htmlspecialchars(trim($_POST['payment_details'] ?? ''), ENT_QUOTES, 'UTF-8');

    if (!$amount || $amount <= 0) throw new Exception('Please enter a valid withdrawal amount', 400);
    if ($amount < 10) throw new Exception('Minimum withdrawal amount is $10', 400);
    if (empty($methodCode)) throw new Exception('Please select a payment method', 400);
    if (empty($details)) throw new Exception('Please provide payment details', 400);

    // Prevent duplicate submission
    if (!empty($_SESSION['withdraw_in_progress'])) {
        throw new Exception('A withdrawal is already in progress. Please wait a few seconds before retrying.', 429);
    }
    $_SESSION['withdraw_in_progress'] = true;

    // 6️⃣ Fetch user
    $userStmt = $pdo->prepare("SELECT id, email, first_name, last_name, balance FROM users WHERE id = ?");
    $userStmt->execute([$_SESSION['user_id']]);
    $user = $userStmt->fetch();
    if (!$user) throw new Exception('User not found', 404);

    // 7️⃣ Payment method validation
    $methodStmt = $pdo->prepare("SELECT id, method_name FROM payment_methods WHERE method_code = ? AND is_active = 1 AND allows_withdrawal = 1");
    $methodStmt->execute([$methodCode]);
    $paymentMethod = $methodStmt->fetch();
    if (!$paymentMethod) throw new Exception('Invalid or inactive payment method selected', 400);
    $paymentMethodId = $paymentMethod['id'];

    // 8️⃣ Check balance
    if ($user['balance'] < $amount) {
        throw new Exception('Insufficient balance. Available: $' . number_format($user['balance'], 2), 400);
    }

    // 9️⃣ Process withdrawal
    $reference = 'WDR-' . time() . '-' . strtoupper(substr(uniqid(), -6));

    $pdo->beginTransaction();

    try {
        // a) Insert withdrawal
        $stmt = $pdo->prepare("
            INSERT INTO withdrawals (user_id, amount, method_code, payment_details, reference, status)
            VALUES (?, ?, ?, ?, ?, 'pending')
        ");
        $stmt->execute([$user['id'], $amount, $methodCode, $details, $reference]);

        // b) Insert transaction
        $stmt = $pdo->prepare("
            INSERT INTO transactions (user_id, type, amount, payment_method_id, reference, status, payment_details)
            VALUES (?, 'withdrawal', ?, ?, ?, 'pending', ?)
        ");
        $stmt->execute([$user['id'], $amount, $paymentMethodId, $reference, $details]);

        // c) Deduct balance
        $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
        $stmt->execute([$amount, $user['id']]);

        // d) Get updated balance
        $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
        $stmt->execute([$user['id']]);
        $newBalance = $stmt->fetchColumn();

        // e) ✅ Add user notification
        addUserNotification(
            $pdo,
            $user['id'],
            'Withdrawal Request Submitted',
            "Your withdrawal of $" . number_format($amount, 2) . " via {$paymentMethod['method_name']} has been received and is now processing. Reference: {$reference}.",
            'success',
            'withdrawal',
            $reference
        );

        // f) ✅ Add admin notification (first admin)
        addAdminNotification(
            $pdo,
            1,
            'New Withdrawal Request',
            "User {$user['email']} requested a withdrawal of $" . number_format($amount, 2) . " ({$paymentMethod['method_name']}, Ref: {$reference}).",
            'info'
        );

        // g) Send confirmation email
        try {
            sendWithdrawalConfirmationEmail($pdo, $user, $amount, $reference, $paymentMethod['method_name'], $details);
        } catch (Exception $mailErr) {
            error_log('[Withdrawal Email Error] ' . $mailErr->getMessage());
        }

        $pdo->commit();
        unset($_SESSION['withdraw_in_progress']);

        echo json_encode([
            'success' => true,
            'message' => 'Withdrawal request submitted successfully! A confirmation email and notification have been sent.',
            'data' => [
                'reference' => $reference,
                'amount' => number_format($amount, 2),
                'new_balance' => number_format($newBalance, 2),
                'processing_time' => '1-3 business days'
            ]
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    } catch (Exception $inner) {
        $pdo->rollBack();
        unset($_SESSION['withdraw_in_progress']);
        throw $inner;
    }

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    unset($_SESSION['withdraw_in_progress']);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_type' => get_class($e)
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

/**
 * ---------------------------------------------------------
 * 📧 Send Withdrawal Confirmation Email
 * ---------------------------------------------------------
 */
function sendWithdrawalConfirmationEmail($pdo, $user, $amount, $reference, $paymentMethod, $paymentDetails) {
    global $phpMailerAvailable;
    if (!$phpMailerAvailable) throw new Exception('Mailer not available');

    // SMTP + System
    $smtp = $pdo->query("SELECT * FROM smtp_settings WHERE is_active = 1 LIMIT 1")->fetch();
    if (!$smtp) throw new Exception('No active SMTP configuration found');
    $system = $pdo->query("SELECT * FROM system_settings WHERE id = 1")->fetch();

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
        '{site_name}' => $system['brand_name'] ?? 'KryptoX',
        '{support_email}' => $system['contact_email'] ?? 'support@kryptox.co.uk',
        '{current_year}' => date('Y'),
        '{site_url}' => $system['domain'] ?? 'https://kryptox.co.uk'
    ];

    foreach ($replacements as $k => $v) {
        $subject = str_replace($k, $v, $subject);
        $body = str_replace($k, $v, $body);
    }

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $smtp['host'];
    $mail->SMTPAuth = true;
    $mail->Username = $smtp['username'];
    $mail->Password = $smtp['password'];
    $mail->SMTPSecure = $smtp['encryption'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $smtp['port'];

            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
    $mail->setFrom($smtp['from_email'], $smtp['from_name']);
    $mail->addAddress($user['email']);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();

    $logStmt = $pdo->prepare("
        INSERT INTO email_logs (template_id, recipient, subject, content, sent_at, status)
        VALUES (NULL, ?, ?, ?, NOW(), 'sent')
    ");
    $logStmt->execute([$user['email'], $subject, $body]);
}

/**
 * ---------------------------------------------------------
 * 📄 Default HTML Email Template
 * ---------------------------------------------------------
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
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f4f6f8; }
            .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 10px; overflow: hidden; }
            .header { background: linear-gradient(90deg,#2950a8 0,#2da9e3 100%); color: #fff; text-align: center; padding: 25px; }
            .header h1 { margin: 0; font-size: 24px; }
            .content { padding: 20px; }
            .details { background: #fafafa; border-left: 4px solid #28a745; padding: 15px; border-radius: 6px; margin-bottom: 20px; }
            .footer { background: #f8f9fa; text-align: center; font-size: 12px; color: #777; padding: 15px; }
            table { width: 100%; border-collapse: collapse; }
            td { padding: 6px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>{site_name}</h1>
                <p>💰 Withdrawal Request Received</p>
            </div>
            <div class="content">
                <p>Dear {first_name} {last_name},</p>
                <p>We have received your withdrawal request and it is now being processed by our financial team.</p>

                <div class="details">
                    <table>
                        <tr><td><strong>Reference:</strong></td><td>{reference}</td></tr>
                        <tr><td><strong>Amount:</strong></td><td>{amount}</td></tr>
                        <tr><td><strong>Method:</strong></td><td>{payment_method}</td></tr>
                        <tr><td><strong>Details:</strong></td><td>{payment_details}</td></tr>
                        <tr><td><strong>Date:</strong></td><td>{date}</td></tr>
                    </table>
                </div>

                <p><strong>Processing time:</strong> 1–3 business days.<br>
                You’ll be notified once your withdrawal is completed.</p>

                <p>Kind regards,<br><strong>{site_name} Team</strong></p>
            </div>
            <div class="footer">
                <p>&copy; {current_year} {site_name}. All rights reserved.<br>
                Contact: {support_email} – <a href="{site_url}">{site_url}</a></p>
            </div>
        </div>
    </body>
    </html>';
}
?>
