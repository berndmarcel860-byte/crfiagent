<?php
/**
 * admin_ajax/send_universal_email.php
 * Universal email sender using AdminEmailHelper
 * 
 * Now uses centralized AdminEmailHelper with 41+ variables
 * 
 * AVAILABLE VARIABLES (41+):
 * --------------------------
 * User: {user_id}, {first_name}, {last_name}, {full_name}, {email}, {balance}, {status}, etc.
 * Company: {brand_name}, {company_address}, {contact_email}, {contact_phone}, {fca_reference_number}, etc.
 * Bank: {has_bank_account}, {bank_name}, {account_holder}, {iban}, {bic}, {bank_country}
 * Crypto: {has_crypto_wallet}, {cryptocurrency}, {network}, {wallet_address}
 * Onboarding: {onboarding_completed}, {onboarding_step}
 * Cases: {case_number}, {case_status}, {case_title}, {case_amount}
 * System: {current_year}, {current_date}, {current_time}, {dashboard_url}, {login_url}
 * 
 * POST Parameters:
 * - user_id: User ID (required)
 * - subject: Email subject with {variables} (required)
 * - message: Email message with {variables} (required)
 */

require_once '../admin_session.php';
require_once '../AdminEmailHelper.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Validate required fields
if (empty($_POST['user_id']) || empty($_POST['subject']) || empty($_POST['message'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields: user_id, subject, message']);
    exit();
}

$userId = (int)$_POST['user_id'];
$subject = trim($_POST['subject']);
$message = trim($_POST['message']);

try {
    // Initialize email helper
    $emailHelper = new AdminEmailHelper($pdo);
    
    // Prepare custom variables if provided
    $customVars = [];
    if (isset($_POST['custom_variables']) && is_array($_POST['custom_variables'])) {
        $customVars = $_POST['custom_variables'];
    }
    
    // Send email using AdminEmailHelper
    // This automatically fetches all 41+ variables and replaces them
    $success = $emailHelper->sendDirectEmail($userId, $subject, $message, $customVars);
    
    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Email sent successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to send email'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Universal email error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}