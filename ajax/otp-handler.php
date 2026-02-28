<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session.php';
require_once __DIR__ . '/../EmailHelper.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method', 405);
    }

    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Unauthorized access', 401);
    }

    $action = $_POST['action'] ?? '';
    if (!in_array($action, ['send', 'verify'])) {
        throw new Exception('Invalid action', 400);
    }

    // Fetch user
    $stmt = $pdo->prepare("SELECT id, email, first_name FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if (!$user) throw new Exception('User not found');

    // ------------------------------------------------------------
    // 🔹 SEND OTP
    // ------------------------------------------------------------
    if ($action === 'send') {
        $otp = rand(100000, 999999);
        $expires = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        // Store in DB
        $pdo->prepare("INSERT INTO otp_logs (user_id, otp_code, purpose, expires_at) VALUES (?, ?, 'withdrawal', ?)")
            ->execute([$user['id'], $otp, $expires]);

        // Also store in session for fast verification
        $_SESSION['withdraw_otp'] = $otp;
        $_SESSION['otp_expire'] = $expires;
        $_SESSION['otp_verified'] = false;

        // Send OTP email using EmailHelper
        try {
            $emailHelper = new EmailHelper($pdo);
            $customVars = [
                'otp_code' => $otp,
                'expires_minutes' => '5'
            ];
            $emailHelper->sendEmail('otp_email', $user['id'], $customVars);
        } catch (Exception $e) {
            error_log("OTP email failed: " . $e->getMessage());
            // Continue even if email fails - OTP is still valid
        }

        echo json_encode([
            'success' => true,
            'message' => 'OTP sent successfully to your registered email.'
        ]);
        exit;
    }

    // ------------------------------------------------------------
    // 🔹 VERIFY OTP
    // ------------------------------------------------------------
    if ($action === 'verify') {
        $code = trim($_POST['otp_code'] ?? '');
        if (empty($code)) throw new Exception('Please enter the OTP code.');

        // Check in session first
        if (!isset($_SESSION['withdraw_otp']) || !isset($_SESSION['otp_expire'])) {
            throw new Exception('No OTP request found. Please resend.');
        }

        if (new DateTime() > new DateTime($_SESSION['otp_expire'])) {
            throw new Exception('OTP expired. Please request a new one.');
        }

        if ($code != $_SESSION['withdraw_otp']) {
            throw new Exception('Invalid OTP code.');
        }

        // Mark verified in DB
        $pdo->prepare("UPDATE otp_logs SET is_verified = 1 WHERE user_id = ? AND otp_code = ?")
            ->execute([$user['id'], $code]);

        $_SESSION['otp_verified'] = true;

        echo json_encode([
            'success' => true,
            'message' => 'OTP verified successfully. You can now submit your withdrawal.'
        ]);
        exit;
    }
} catch (Exception $e) {
    http_response_code((int)($e->getCode() ?: 500));
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
