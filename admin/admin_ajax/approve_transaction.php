<?php
// =======================================================
// PHPMailer imports
// =======================================================
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// =======================================================
// Error reporting (disable in production)
// =======================================================
ini_set('display_errors', 0);
error_reporting(E_ALL);

// =======================================================
// PHPMailer availability check
// =======================================================
$phpMailerAvailable = false;
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $phpMailerAvailable = true;
}

// =======================================================
// Include admin session
// =======================================================
require_once '../admin_session.php';
header('Content-Type: application/json');

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid transaction ID']);
    exit();
}

$transactionId = (int)$_POST['id'];

try {
    $pdo->beginTransaction();

    // =======================================================
    // Fetch transaction
    // =======================================================
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = ?");
    $stmt->execute([$transactionId]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$transaction) {
        throw new Exception('Transaction not found');
    }

    if ($transaction['status'] !== 'pending') {
        throw new Exception('Transaction is not pending');
    }

    // =======================================================
    // Update transaction
    // =======================================================
    $stmt = $pdo->prepare("
        UPDATE transactions 
        SET status = 'completed', processed_by = ?, updated_at = NOW() 
        WHERE id = ?
    ");
    $stmt->execute([$_SESSION['admin_id'], $transactionId]);

    // =======================================================
    // If deposit → update user balance, deposits table, email & notifications
    // =======================================================
    if ($transaction['type'] === 'deposit') {
        // Update user balance
        $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$transaction['amount'], $transaction['user_id']]);

        // Update deposit record (if found)
        $stmt = $pdo->prepare("
            UPDATE deposits 
            SET status = 'completed', processed_by = ?, processed_at = NOW(), updated_at = NOW() 
            WHERE reference = ?
        ");
        $stmt->execute([$_SESSION['admin_id'], $transaction['reference']]);

        // Get user
        $stmt = $pdo->prepare("SELECT id, email, first_name, last_name FROM users WHERE id = ?");
        $stmt->execute([$transaction['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // === 1️⃣ Send deposit email
            sendDepositEmail($pdo, $user, 'deposit_received', $transaction);

            // === 2️⃣ Create user notification
            try {
                $notifUser = $pdo->prepare("
                    INSERT INTO user_notifications (user_id, title, message, type, related_entity, related_id, created_at)
                    VALUES (:user_id, :title, :message, :type, :entity, :rel_id, NOW())
                ");
                $notifUser->execute([
                    ':user_id' => (int)$transaction['user_id'],
                    ':title' => 'Einzahlung bestätigt',
                    ':message' => 'Ihre Einzahlung über <strong>' 
                        . number_format($transaction['amount'], 2) . ' €</strong> wurde erfolgreich bestätigt. '
                        . 'Referenz: <strong>' . htmlspecialchars($transaction['reference']) . '</strong>.',
                    ':type' => 'success',
                    ':entity' => 'transaction',
                    ':rel_id' => $transactionId
                ]);
            } catch (Exception $e) {
                error_log("User notification failed: " . $e->getMessage());
            }

            // === 3️⃣ Create admin notification
            try {
                $assignedAdmin = (int)$_SESSION['admin_id'];
                $notifAdmin = $pdo->prepare("
                    INSERT INTO admin_notifications (admin_id, title, message, type, is_read, created_at)
                    VALUES (:admin_id, :title, :message, :type, 0, NOW())
                ");
                $notifAdmin->execute([
                    ':admin_id' => $assignedAdmin,
                    ':title' => 'Einzahlung genehmigt',
                    ':message' => 'Sie haben eine Einzahlung von Benutzer-ID <strong>'
                        . (int)$transaction['user_id'] . '</strong> über <strong>'
                        . number_format($transaction['amount'], 2) . ' €</strong> bestätigt.',
                    ':type' => 'info'
                ]);
            } catch (Exception $e) {
                error_log("Admin notification failed: " . $e->getMessage());
            }
        }
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Transaction approved successfully and notifications sent.'
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Failed to approve transaction',
        'error' => $e->getMessage()
    ]);
}

/**
 * =======================================================
 * Send Deposit Email
 * =======================================================
 */
function sendDepositEmail($pdo, $user, $templateKey, $transaction)
{
    try {
        // Get template
        $templateStmt = $pdo->prepare("SELECT * FROM email_templates WHERE template_key = ? LIMIT 1");
        $templateStmt->execute([$templateKey]);
        $template = $templateStmt->fetch(PDO::FETCH_ASSOC);
        if (!$template) {
            throw new Exception("Email template not found: " . $templateKey);
        }

        // SMTP
        $smtpStmt = $pdo->prepare("SELECT * FROM smtp_settings WHERE is_active = 1 LIMIT 1");
        $smtpStmt->execute();
        $smtpSettings = $smtpStmt->fetch(PDO::FETCH_ASSOC);
        if (!$smtpSettings) {
            throw new Exception("No active SMTP configuration found");
        }

        // System settings
        $systemStmt = $pdo->prepare("SELECT * FROM system_settings WHERE id = 1");
        $systemStmt->execute();
        $systemSettings = $systemStmt->fetch(PDO::FETCH_ASSOC);

        // Variables
        $variables = [
            '{first_name}' => $user['first_name'] ?? '',
            '{last_name}' => $user['last_name'] ?? '',
            '{user_name}' => $user['first_name'] . ' ' . $user['last_name'],
            '{email}' => $user['email'],
            '{amount}' => number_format($transaction['amount'], 2),
            '{reference}' => $transaction['reference'] ?? '',
            '{status}' => ucfirst($transaction['status']),
            '{date}' => date('Y-m-d H:i:s'),
            '{current_year}' => date('Y'),
            '{site_name}' => $systemSettings['site_name'] ?? 'Fundtracer AI',
            '{site_url}' => $systemSettings['site_url'] ?? 'https://your-site.com',
            '{support_email}' => $systemSettings['contact_email'] ?? 'support@your-site.com',
            '{brand_name}' => $systemSettings['brand_name'] ?? 'Fundtracer AI',
            '{contact_phone}' => $systemSettings['contact_phone'] ?? '',
            '{contact_email}' => $systemSettings['contact_email'] ?? ''
        ];

        $subject = $template['subject'];
        $htmlBody = $template['content'];
        foreach ($variables as $key => $value) {
            $subject = str_replace($key, $value, $subject);
            $htmlBody = str_replace($key, $value, $htmlBody);
        }
        $textBody = strip_tags($htmlBody);

        global $phpMailerAvailable;

        if ($phpMailerAvailable) {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = $smtpSettings['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtpSettings['username'];
            $mail->Password   = $smtpSettings['password'];
            $mail->SMTPSecure = $smtpSettings['encryption'] === 'ssl'
                ? PHPMailer::ENCRYPTION_SMTPS
                : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $smtpSettings['port'];

            $mail->setFrom($smtpSettings['from_email'], $smtpSettings['from_name']);
            $mail->addAddress($user['email'], $user['first_name'] . ' ' . $user['last_name']);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = $textBody;
            $mail->send();
        } else {
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8\r\n";
            $headers .= 'From: ' . $smtpSettings['from_name'] . ' <' . $smtpSettings['from_email'] . '>' . "\r\n";
            mail($user['email'], $subject, $htmlBody, $headers);
        }

        // Log success
        $logStmt = $pdo->prepare("
            INSERT INTO email_logs (template_id, recipient, subject, content, sent_at, status)
            VALUES (?, ?, ?, ?, NOW(), 'sent')
        ");
        $logStmt->execute([$template['id'], $user['email'], $subject, $htmlBody]);

    } catch (Exception $e) {
        error_log("Deposit email failed: " . $e->getMessage());
    }
}
?>
