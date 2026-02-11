<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

ini_set('display_errors', 0);
error_reporting(E_ALL);

$phpMailerAvailable = false;
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $phpMailerAvailable = true;
}

require_once '../admin_session.php';
header('Content-Type: application/json');

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid withdrawal ID']);
    exit();
}

$withdrawalId = (int)$_POST['id'];

try {
    $pdo->beginTransaction();

    // === FETCH WITHDRAWAL ===
    $stmt = $pdo->prepare("SELECT * FROM withdrawals WHERE id = ?");
    $stmt->execute([$withdrawalId]);
    $withdrawal = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$withdrawal) {
        throw new Exception('Withdrawal not found');
    }

    if (!in_array($withdrawal['status'], ['pending', 'processing'])) {
        throw new Exception('Withdrawal cannot be approved in its current state');
    }

    // === UPDATE WITHDRAWAL ===
    $stmt = $pdo->prepare("
        UPDATE withdrawals 
        SET 
            status = 'completed',
            processed_by = ?,
            processed_at = NOW(),
            updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$_SESSION['admin_id'], $withdrawalId]);

    // === UPDATE EXISTING TRANSACTION ===
    $stmt = $pdo->prepare("
        UPDATE transactions
        SET 
            status = 'completed',
            updated_at = NOW()
        WHERE reference = ?
          AND type = 'withdrawal'
          AND user_id = ?
        LIMIT 1
    ");
    $stmt->execute([
        $withdrawal['reference'],
        $withdrawal['user_id']
    ]);

    // === FETCH USER ===
    $stmt = $pdo->prepare("
        SELECT id, email, first_name, last_name, balance 
        FROM users 
        WHERE id = ?
    ");
    $stmt->execute([$withdrawal['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        // --- SEND EMAIL ---
        sendWithdrawalApprovalEmail($pdo, $user, 'withdrawal_completed', $withdrawal);

        // --- USER NOTIFICATION ---
        $notifUser = $pdo->prepare("
            INSERT INTO user_notifications
            (user_id, title, message, type, related_entity, related_id, created_at)
            VALUES (:user_id, :title, :message, 'success', 'withdrawal', :rel_id, NOW())
        ");

        $notifUser->execute([
            ':user_id' => (int)$withdrawal['user_id'],
            ':title' => 'Auszahlung abgeschlossen',
            ':message' => 'Ihre Auszahlung über <strong>'
                . number_format($withdrawal['amount'], 2) . ' €</strong> wurde erfolgreich ausgeführt.',
            ':rel_id' => $withdrawalId
        ]);

        // --- ADMIN NOTIFICATION ---
        $notifAdmin = $pdo->prepare("
            INSERT INTO admin_notifications 
            (admin_id, title, message, type, is_read, created_at)
            VALUES (:admin_id, :title, :message, 'success', 0, NOW())
        ");

        $notifAdmin->execute([
            ':admin_id' => (int)$_SESSION['admin_id'],
            ':title' => 'Auszahlung genehmigt',
            ':message' => 'Sie haben eine Auszahlung von Benutzer-ID <strong>'
                . (int)$withdrawal['user_id'] . '</strong> über <strong>'
                . number_format($withdrawal['amount'], 2) . ' €</strong> genehmigt.'
        ]);
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Withdrawal approved successfully'
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode([
        'success' => false,
        'message' => 'Failed to approve withdrawal',
        'error' => $e->getMessage()
    ]);
}

/**
 * =======================================================
 * SEND WITHDRAWAL APPROVAL EMAIL
 * =======================================================
 */
function sendWithdrawalApprovalEmail($pdo, $user, $templateKey, $withdrawal)
{
    global $phpMailerAvailable;

    try {
        // LOAD TEMPLATE
        $tpl = $pdo->prepare("SELECT * FROM email_templates WHERE template_key = ? LIMIT 1");
        $tpl->execute([$templateKey]);
        $template = $tpl->fetch(PDO::FETCH_ASSOC);
        if (!$template) return;

        // SMTP
        $smtp = $pdo->query("SELECT * FROM smtp_settings WHERE is_active = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        if (!$smtp) return;

        $sys = $pdo->query("SELECT * FROM system_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

        // PAYMENT METHOD
        $methodName = 'Banküberweisung';
        if (!empty($withdrawal['method_code'])) {
            $stmt = $pdo->prepare("SELECT method_name FROM payment_methods WHERE method_code = ? LIMIT 1");
            $stmt->execute([$withdrawal['method_code']]);
            $method = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($method && !empty($method['method_name'])) {
                $methodName = $method['method_name'];
            }
        }

        // VARIABLES
        $vars = [
            '{first_name}'       => htmlspecialchars($user['first_name'] ?? '', ENT_QUOTES, 'UTF-8'),
            '{last_name}'        => htmlspecialchars($user['last_name'] ?? '', ENT_QUOTES, 'UTF-8'),
            '{amount}'           => number_format($withdrawal['amount'], 2) . ' €',
            '{payment_method}'   => htmlspecialchars($methodName, ENT_QUOTES, 'UTF-8'),
            '{payment_details}'  => htmlspecialchars($withdrawal['payment_details'] ?? '', ENT_QUOTES, 'UTF-8'),
            '{reference}'        => htmlspecialchars($withdrawal['reference'] ?? 'WD-' . $withdrawal['id'], ENT_QUOTES, 'UTF-8'),
            '{transaction_id}'   => htmlspecialchars($withdrawal['reference'] ?? 'WD-' . $withdrawal['id'], ENT_QUOTES, 'UTF-8'),
            '{transaction_date}' => date('Y-m-d H:i:s'),
            '{balance}'          => number_format($user['balance'] ?? 0, 2) . ' €',
            '{site_url}'         => $sys['site_url'] ?? '',
            '{site_name}'        => htmlspecialchars($sys['site_name'] ?? '', ENT_QUOTES, 'UTF-8'),
            '{surl}'             => $sys['site_url'] ?? '',
            '{sbrand}'           => htmlspecialchars($sys['site_name'] ?? '', ENT_QUOTES, 'UTF-8'),
            '{semail}'           => htmlspecialchars($sys['contact_email'] ?? '', ENT_QUOTES, 'UTF-8'),
            '{sphone}'           => htmlspecialchars($sys['contact_phone'] ?? '', ENT_QUOTES, 'UTF-8')
        ];

        $subject  = strtr($template['subject'], $vars);
        $htmlBody = strtr($template['content'], $vars);
        $textBody = strip_tags($htmlBody);

        if ($phpMailerAvailable) {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = $smtp['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtp['username'];
            $mail->Password   = $smtp['password'];
            $mail->SMTPSecure = ($smtp['encryption'] === 'ssl')
                ? PHPMailer::ENCRYPTION_SMTPS
                : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int)$smtp['port'];
            $mail->CharSet    = 'UTF-8';
            $mail->Encoding   = 'base64';
            $mail->setFrom($smtp['from_email'], $smtp['from_name']);
            $mail->addAddress($user['email'], trim($user['first_name'] . ' ' . $user['last_name']));
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = $textBody;
            $mail->send();
        }

        // LOG EMAIL
        $log = $pdo->prepare("
            INSERT INTO email_logs (template_id, recipient, subject, content, sent_at, status)
            VALUES (?, ?, ?, ?, NOW(), 'sent')
        ");
        $log->execute([$template['id'], $user['email'], $subject, $htmlBody]);

    } catch (Exception $e) {
        error_log("Approval email failed: " . $e->getMessage());
    }
}

