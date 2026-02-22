<?php 
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once '../admin_session.php';
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
    try {
        require_once '../AdminEmailHelper.php';
        $emailHelper = new AdminEmailHelper($pdo);
        
        $customVars = [
            'recovery_amount' => number_format($newAmount, 2) . ' €',
            'total_recovered' => number_format($totalAfter, 2) . ' €',
            'reported_amount' => number_format($case['reported_amount'], 2) . ' €',
            'recovery_id' => $data['case_id'],
            'update_date' => date('Y-m-d H:i:s'),
            'admin_notes' => $data['notes'] ?? ''
        ];
        
        $emailSent = $emailHelper->sendTemplateEmail('recovery_amount_updated', $case['user_id'], $customVars);
    } catch (Exception $e) {
        error_log("Recovery update email failed: " . $e->getMessage());
        $emailSent = false;
    }

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
