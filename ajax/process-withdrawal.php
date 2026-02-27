<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../session.php';
require_once __DIR__ . '/../EmailHelper.php';

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
    $paymentMethodId = filter_input(INPUT_POST, 'payment_method_id', FILTER_VALIDATE_INT);
    $details = isset($_POST['payment_details']) ? htmlspecialchars($_POST['payment_details'], ENT_QUOTES, 'UTF-8') : null;

    if (!$amount || $amount <= 0) throw new Exception('Please enter a valid withdrawal amount', 400);
    if ($amount < 1000) throw new Exception('Minimum withdrawal amount is €1000', 400);
    if (!$paymentMethodId) throw new Exception('Please select a verified payment method', 400);
    if (empty($details)) throw new Exception('Please provide payment details', 400);

    // 5.5️⃣ Verify payment method belongs to user and is verified
    $stmt = $pdo->prepare("SELECT * FROM user_payment_methods 
                           WHERE id = ? AND user_id = ? AND verification_status = 'verified'");
    $stmt->execute([$paymentMethodId, $_SESSION['user_id']]);
    $userPaymentMethod = $stmt->fetch();
    
    if (!$userPaymentMethod) {
        throw new Exception('Invalid or unverified payment method. Please use a verified payment method for withdrawals.', 400);
    }
    
    // Set method code based on type
    if ($userPaymentMethod['type'] === 'crypto') {
        $methodCode = strtolower($userPaymentMethod['cryptocurrency']);
    } else {
        $methodCode = 'bank_transfer';
    }

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

    // 7️⃣ Payment method - already validated in user_payment_methods above
    // Use the payment method name from user's payment method
    $paymentMethodName = $userPaymentMethod['label'] ?: 
                        ($userPaymentMethod['type'] === 'crypto' ? $userPaymentMethod['cryptocurrency'] : $userPaymentMethod['bank_name']) ?: 
                        'Bank Transfer';

    // 8️⃣ Balance check
    if ($user['balance'] < $amount) {
        throw new Exception('Insufficient balance. Available: €' . number_format($user['balance'], 2), 400);
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
            VALUES (?, 'withdrawal', ?, NULL, ?, 'pending', ?)");
        $stmt->execute([$_SESSION['user_id'], $amount, $reference, $details]);

        // Deduct balance
        $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
        $stmt->execute([$amount, $_SESSION['user_id']]);

        // Get new balance
        $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $newBalance = $stmt->fetchColumn();

        // Email confirmation using EmailHelper
        try {
            $emailHelper = new EmailHelper($pdo);
            $customVars = [
                'amount' => '€' . number_format($amount, 2),
                'reference' => $reference,
                'payment_method' => $paymentMethodName,
                'payment_details' => $details,
                'transaction_id' => isset($withdrawalId) ? 'TXN-' . $withdrawalId : $reference,
                'transaction_date' => date('Y-m-d H:i:s'),
                'status' => 'pending'
            ];
            $emailHelper->sendEmail('withdrawal_pending', $user['id'], $customVars);
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
?>
