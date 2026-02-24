<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

ini_set('display_errors', 0);
error_reporting(E_ALL);

$phpMailerAvailable = false;
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
    $phpMailerAvailable = true;
}

require_once '../admin_session.php';
require_once '../mail_functions.php';
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Admin not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
if (empty($data['case_id']) || !is_numeric($data['case_id']) || empty($data['amount']) || !is_numeric($data['amount'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid case ID or amount']);
    exit();
}

try {
    $pdo->beginTransaction();

    // === 1️⃣ Get case & user ===
    $stmt = $pdo->prepare("
        SELECT c.id, c.user_id, c.reported_amount, c.case_number, c.status,
               u.email, u.first_name, u.last_name
        FROM cases c
        JOIN users u ON c.user_id = u.id
        WHERE c.id = ?
    ");
    $stmt->execute([$data['case_id']]);
    $case = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$case) throw new Exception('Case not found');

    // === 2️⃣ Get admin info ===
    $stmt = $pdo->prepare("SELECT first_name, last_name FROM admins WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // === 3️⃣ Validation: not exceeding amount ===
    $stmt = $pdo->prepare("SELECT SUM(amount) FROM case_recovery_transactions WHERE case_id = ?");
    $stmt->execute([$data['case_id']]);
    $alreadyRecovered = (float)$stmt->fetchColumn();

    $newAmount = (float)$data['amount'];
    $totalAfter = $alreadyRecovered + $newAmount;

    if ($totalAfter > $case['reported_amount']) {
        throw new Exception('Total recovered cannot exceed reported amount');
    }

    // === 4️⃣ Record recovery transaction ===
    $stmt = $pdo->prepare("
        INSERT INTO case_recovery_transactions (case_id, amount, processed_by, notes)
        VALUES (:case_id, :amount, :admin_id, :notes)
    ");
    $stmt->execute([
        ':case_id' => $data['case_id'],
        ':amount' => $newAmount,
        ':admin_id' => $_SESSION['admin_id'],
        ':notes' => $data['notes'] ?? null
    ]);

    // === 5️⃣ Send recovery update email ===
    $emailSent = sendRecoveryUpdateEmail(
        $pdo, $case, $data['case_id'], $newAmount, $totalAfter,
        $case['reported_amount'], $data, $admin
    );

    // === 6️⃣ Audit log ===
    $stmt = $pdo->prepare("
        INSERT INTO audit_logs (admin_id, action, entity_type, entity_id, new_value, ip_address, user_agent)
        VALUES (:admin_id, :action, :entity_type, :entity_id, :new_value, :ip_address, :user_agent)
    ");
    $stmt->execute([
        ':admin_id' => $_SESSION['admin_id'],
        ':action' => 'recovery_added',
        ':entity_type' => 'case',
        ':entity_id' => $data['case_id'],
        ':new_value' => json_encode([
            'amount' => $newAmount,
            'email_sent' => $emailSent,
            'template_used' => 'recovery_amount_updated'
        ]),
        ':ip_address' => $_SERVER['REMOTE_ADDR'],
        ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);

    $pdo->commit();

    // === 7️⃣ 🔔 Create user notification ===
    try {
        $stmt = $pdo->prepare("
            INSERT INTO user_notifications (user_id, title, message, type, related_entity, related_id, created_at)
            VALUES (:user_id, :title, :message, :type, :entity, :rel_id, NOW())
        ");
        $stmt->execute([
            ':user_id' => (int)$case['user_id'],
            ':title' => 'Rückerstattungs-Update für Ihren Fall',
            ':message' => 'Ein Betrag von <strong>$' . number_format($newAmount, 2) .
                '</strong> wurde erfolgreich zu Ihrem Fall <strong>' . htmlspecialchars($case['case_number']) . '</strong> hinzugefügt.',
            ':type' => 'success',
            ':entity' => 'case',
            ':rel_id' => $case['case_number']
        ]);
    } catch (Exception $e) {
        error_log("User notification failed: " . $e->getMessage());
    }

    // === 8️⃣ 🧭 Create admin notification ===
    try {
        $stmt = $pdo->prepare("
            INSERT INTO admin_notifications (admin_id, title, message, type, is_read, created_at)
            VALUES (:admin_id, :title, :message, :type, 0, NOW())
        ");
        $stmt->execute([
            ':admin_id' => (int)$_SESSION['admin_id'],
            ':title' => 'Neue Rückerstattung registriert',
            ':message' => 'Eine Rückerstattung von <strong>$' . number_format($newAmount, 2) .
                '</strong> wurde dem Fall <strong>' . htmlspecialchars($case['case_number']) . '</strong> hinzugefügt.',
            ':type' => 'success'
        ]);
    } catch (Exception $e) {
        error_log("Admin notification failed: " . $e->getMessage());
    }

    // === 9️⃣ Response ===
    echo json_encode([
        'success' => true,
        'message' => 'Recovery amount updated successfully',
        'data' => [
            'case_id' => $data['case_id'],
            'case_number' => $case['case_number'],
            'new_amount' => $newAmount,
            'total_recovered' => $totalAfter,
            'remaining_amount' => $case['reported_amount'] - $totalAfter,
            'email_sent' => $emailSent
        ]
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    error_log('Recovery update error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to update recovery amount', 'error' => $e->getMessage()]);
}

/**
 * 📧 Send recovery update email notification
 */
function sendRecoveryUpdateEmail($pdo, $userData, $caseId, $newAmount, $totalAfter, $reportedAmount, $updateData, $adminData) {
    global $phpMailerAvailable;
    try {
        $trackingToken = bin2hex(random_bytes(16));
        $templateStmt = $pdo->prepare("SELECT * FROM email_templates WHERE template_key = 'recovery_amount_updated' LIMIT 1");
        $templateStmt->execute();
        $template = $templateStmt->fetch(PDO::FETCH_ASSOC);
        if (!$template) throw new Exception("Email template not found");

        $smtpStmt = $pdo->prepare("SELECT * FROM smtp_settings WHERE is_active = 1 LIMIT 1");
        $smtpStmt->execute();
        $smtp = $smtpStmt->fetch(PDO::FETCH_ASSOC);
        if (!$smtp) throw new Exception("No active SMTP config");

        $systemStmt = $pdo->prepare("SELECT * FROM system_settings WHERE id = 1");
        $systemStmt->execute();
        $system = $systemStmt->fetch(PDO::FETCH_ASSOC);

        $vars = [
            '{first_name}' => $userData['first_name'],
            '{last_name}' => $userData['last_name'],
            '{user_name}' => $userData['first_name'].' '.$userData['last_name'],
            '{case_number}' => $userData['case_number'],
            '{case_id}' => $caseId,
            '{reported_amount}' => number_format($reportedAmount, 2, ',', '.') . ' €',
            '{recovered_amount}' => number_format($newAmount, 2, ',', '.') . ' €',
            '{total_recovered}' => number_format($totalAfter, 2, ',', '.') . ' €',
            '{remaining_amount}' => number_format($reportedAmount - $totalAfter, 2, ',', '.') . ' €',
            '{recovery_notes}' => $updateData['notes'] ?? 'Keine zusätzlichen Anmerkungen',
            '{recovery_date}' => date('d.m.Y H:i:s'),
            '{processed_by}' => $adminData ? ($adminData['first_name'].' '.$adminData['last_name']) : 'System',
            '{current_year}' => date('Y'),
            '{site_name}' => $system['site_name'] ?? 'ScamRecovery',
            '{support_email}' => $system['contact_email'] ?? 'support@your-site.com'
        ];

        $subject = str_replace(array_keys($vars), array_values($vars), $template['subject']);
        $body = str_replace(array_keys($vars), array_values($vars), $template['content']);
        $pixel = '<img src="'.$system['site_url'].'/track.php?token='.$trackingToken.'" width="1" height="1" alt="" style="display:none;" />';
        $body = str_replace('</body>', $pixel.'</body>', $body);

        if ($phpMailerAvailable) {
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
            $mail->addAddress($userData['email'], $userData['first_name'].' '.$userData['last_name']);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body);
            $mail->send();
        }

        // Log success
        $log = $pdo->prepare("INSERT INTO email_logs (template_id, recipient, subject, content, sent_at, status, tracking_token) VALUES (?, ?, ?, ?, NOW(), 'sent', ?)");
        $log->execute([$template['id'], $userData['email'], $subject, $body, $trackingToken]);
        return true;

    } catch (Exception $e) {
        error_log("Recovery email failed: " . $e->getMessage());
        return false;
    }
}
?>
