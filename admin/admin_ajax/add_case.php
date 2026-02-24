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
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Admin not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$requiredFields = ['user_id', 'platform_id', 'reported_amount', 'description'];
$missing = array_filter($requiredFields, fn($f) => empty($data[$f]));

if ($missing) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields: ' . implode(', ', $missing)]);
    exit();
}

if (!is_numeric($data['user_id']) || !is_numeric($data['platform_id']) || !is_numeric($data['reported_amount'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input: user_id, platform_id, and reported_amount must be numeric']);
    exit();
}

try {
    $pdo->beginTransaction();

    $caseNumber = 'SCM-' . date('Y') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

    // === 1️⃣ Insert new case ===
    $stmt = $pdo->prepare("
        INSERT INTO cases (case_number, user_id, platform_id, reported_amount, status, description, admin_id, created_at, updated_at)
        VALUES (:case_number, :user_id, :platform_id, :reported_amount, 'open', :description, :admin_id, NOW(), NOW())
    ");
    $stmt->execute([
        ':case_number' => $caseNumber,
        ':user_id' => (int)$data['user_id'],
        ':platform_id' => (int)$data['platform_id'],
        ':reported_amount' => (float)$data['reported_amount'],
        ':description' => trim($data['description']),
        ':admin_id' => (int)$_SESSION['admin_id']
    ]);
    $caseId = $pdo->lastInsertId();

    // === 2️⃣ Record case status history ===
    $stmt = $pdo->prepare("
        INSERT INTO case_status_history (case_id, new_status, changed_by, notes)
        VALUES (:case_id, 'open', :admin_id, 'Case created')
    ");
    $stmt->execute([':case_id' => $caseId, ':admin_id' => (int)$_SESSION['admin_id']]);

    // === 3️⃣ Optional admin assignment ===
    if (!empty($data['admin_id']) && $data['admin_id'] != $_SESSION['admin_id']) {
        $stmt = $pdo->prepare("
            UPDATE cases SET admin_id = :assigned_admin_id, updated_at = NOW() WHERE id = :case_id
        ");
        $stmt->execute([
            ':assigned_admin_id' => (int)$data['admin_id'],
            ':case_id' => $caseId
        ]);

        $stmt = $pdo->prepare("
            INSERT INTO case_status_history (case_id, new_status, changed_by, notes)
            VALUES (:case_id, 'open', :admin_id, CONCAT('Case assigned to admin ID: ', :assigned_admin_id))
        ");
        $stmt->execute([
            ':case_id' => $caseId,
            ':admin_id' => (int)$_SESSION['admin_id'],
            ':assigned_admin_id' => (int)$data['admin_id']
        ]);
    }

    // === 4️⃣ Fetch user and platform ===
    $userStmt = $pdo->prepare("SELECT id, email, first_name, last_name FROM users WHERE id = ?");
    $userStmt->execute([$data['user_id']]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    $platformStmt = $pdo->prepare("SELECT name FROM scam_platforms WHERE id = ?");
    $platformStmt->execute([$data['platform_id']]);
    $platform = $platformStmt->fetch(PDO::FETCH_ASSOC);

    // === 5️⃣ Send case creation email ===
    if ($user) {
        sendCaseEmail($pdo, $user, 'case_created', $caseId, $caseNumber, [
            'platform_name' => $platform['name'] ?? 'Unknown Platform',
            'reported_amount' => $data['reported_amount'],
            'case_description' => $data['description'],
            'case_status' => 'Open'
        ]);
    }

    $pdo->commit();

    // === 6️⃣ Create user notification ===
    try {
        $notifUser = $pdo->prepare("
            INSERT INTO user_notifications (user_id, title, message, type, related_entity, related_id, created_at)
            VALUES (:user_id, :title, :message, :type, :entity, :rel_id, NOW())
        ");
        $notifUser->execute([
            ':user_id' => (int)$data['user_id'],
            ':title' => 'Neuer Fall eröffnet',
            ':message' => 'Unser KI-Algorithmus hat einen neuen Fall für Sie erstellt: <strong>' 
                . htmlspecialchars($caseNumber) . '</strong> über <strong>$' 
                . number_format($data['reported_amount'], 2) . '</strong>.',
            ':type' => 'info',
            ':entity' => 'case',
            ':rel_id' => $caseNumber
        ]);
    } catch (Exception $e) {
        error_log("User notification failed: " . $e->getMessage());
    }

    // === 7️⃣ Create admin notification ===
    try {
        $assignedAdmin = !empty($data['admin_id']) ? (int)$data['admin_id'] : (int)$_SESSION['admin_id'];
        $notifAdmin = $pdo->prepare("
            INSERT INTO admin_notifications (admin_id, title, message, type, is_read, created_at)
            VALUES (:admin_id, :title, :message, :type, 0, NOW())
        ");
        $notifAdmin->execute([
            ':admin_id' => $assignedAdmin,
            ':title' => 'Neuer Fall hinzugefügt',
            ':message' => 'Ein neuer Fall wurde erstellt: <strong>' 
                . htmlspecialchars($caseNumber) . '</strong> (Benutzer-ID: ' . (int)$data['user_id'] . ').',
            ':type' => 'info'
        ]);
    } catch (Exception $e) {
        error_log("Admin notification failed: " . $e->getMessage());
    }

    // === 8️⃣ Success response ===
    echo json_encode([
        'success' => true,
        'message' => 'Case created successfully',
        'case_id' => $caseId,
        'case_number' => $caseNumber
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("DB Error in add_case.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error', 'error' => $e->getMessage()]);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("General Error in add_case.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to create case', 'error' => $e->getMessage()]);
}

/**
 * 📧 Send case email
 */
function sendCaseEmail($pdo, $user, $templateKey, $caseId, $caseNumber, $vars = []) {
    global $phpMailerAvailable;
    try {
        $templateStmt = $pdo->prepare("SELECT * FROM email_templates WHERE template_key = ? LIMIT 1");
        $templateStmt->execute([$templateKey]);
        $template = $templateStmt->fetch(PDO::FETCH_ASSOC);
        if (!$template) throw new Exception("Email template not found: " . $templateKey);

        $smtpStmt = $pdo->prepare("SELECT * FROM smtp_settings WHERE is_active = 1 LIMIT 1");
        $smtpStmt->execute();
        $smtp = $smtpStmt->fetch(PDO::FETCH_ASSOC);
        if (!$smtp) throw new Exception("No active SMTP configuration found");

        $systemStmt = $pdo->prepare("SELECT * FROM system_settings WHERE id = 1");
        $systemStmt->execute();
        $system = $systemStmt->fetch(PDO::FETCH_ASSOC);

        $variables = [
            '{first_name}' => $user['first_name'],
            '{last_name}' => $user['last_name'],
            '{user_name}' => $user['first_name'].' '.$user['last_name'],
            '{email}' => $user['email'],
            '{case_number}' => $caseNumber,
            '{case_id}' => $caseId,
            '{date}' => date('Y-m-d H:i:s'),
            '{current_year}' => date('Y'),
            '{site_name}' => $system['site_name'] ?? 'ScamRecovery',
            '{support_email}' => $system['contact_email'] ?? 'support@your-site.com'
        ];
        foreach ($vars as $k => $v) $variables['{'.$k.'}'] = $v;

        $subject = str_replace(array_keys($variables), array_values($variables), $template['subject']);
        $body = str_replace(array_keys($variables), array_values($variables), $template['content']);

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
            $mail->addAddress($user['email'], $user['first_name'].' '.$user['last_name']);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body);
            $mail->send();
        }
    } catch (Exception $e) {
        error_log("Case email failed: " . $e->getMessage());
    }
}
?>