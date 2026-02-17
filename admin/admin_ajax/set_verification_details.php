<?php
/**
 * Set Verification Details - Admin Endpoint
 * Admin sets the test amount and platform wallet address for verification
 */

require_once '../../config.php';
require_once '../admin_session.php';

header('Content-Type: application/json');

try {
    $admin_id = $_SESSION['admin_id'];
    
    // Validate input
    if (!isset($_POST['wallet_id']) || !is_numeric($_POST['wallet_id'])) {
        throw new Exception('Invalid wallet ID');
    }
    
    if (!isset($_POST['verification_amount']) || empty(trim($_POST['verification_amount']))) {
        throw new Exception('Verification amount is required');
    }
    
    if (!isset($_POST['verification_address']) || empty(trim($_POST['verification_address']))) {
        throw new Exception('Verification address is required');
    }
    
    $wallet_id = intval($_POST['wallet_id']);
    $verification_amount = trim($_POST['verification_amount']);
    $verification_address = trim($_POST['verification_address']);
    
    // Validate amount format (decimal)
    if (!is_numeric($verification_amount) || floatval($verification_amount) <= 0) {
        throw new Exception('Invalid verification amount. Must be a positive number.');
    }
    
    // Get wallet details
    $stmt = $pdo->prepare("SELECT id, user_id, cryptocurrency, verification_status 
                           FROM user_payment_methods 
                           WHERE id = ? AND type = 'crypto'");
    $stmt->execute([$wallet_id]);
    $wallet = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$wallet) {
        throw new Exception('Wallet not found');
    }
    
    // Update verification details
    $update_stmt = $pdo->prepare("UPDATE user_payment_methods 
                                   SET verification_amount = ?,
                                       verification_address = ?,
                                       updated_at = CURRENT_TIMESTAMP
                                   WHERE id = ?");
    
    if ($update_stmt->execute([$verification_amount, $verification_address, $wallet_id])) {
        // Log admin action
        $action = "set_verification_details";
        $log_stmt = $pdo->prepare("INSERT INTO audit_logs (admin_id, action, entity, entity_id, ip_address) 
                                   VALUES (?, ?, 'payment_method', ?, ?)");
        $ip = $_SERVER['REMOTE_ADDR'];
        $log_stmt->execute([$admin_id, $action, $wallet_id, $ip]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Verification details set successfully',
            'wallet_id' => $wallet_id,
            'verification_amount' => $verification_amount,
            'verification_address' => $verification_address
        ]);
    } else {
        throw new Exception('Failed to update verification details');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
