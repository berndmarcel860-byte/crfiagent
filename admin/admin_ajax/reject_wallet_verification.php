<?php
/**
 * Reject Wallet Verification - Admin Endpoint
 * Admin rejects a wallet verification (e.g., wrong amount, fake transaction)
 */

require_once '../../config.php';
require_once '../admin_session.php';
require_once '../AdminEmailHelper.php';

header('Content-Type: application/json');

try {
    $admin_id = $_SESSION['admin_id'];
    
    // Validate input
    if (!isset($_POST['wallet_id']) || !is_numeric($_POST['wallet_id'])) {
        throw new Exception('Invalid wallet ID');
    }
    
    $wallet_id = intval($_POST['wallet_id']);
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
    
    if (empty($reason)) {
        throw new Exception('Rejection reason is required');
    }
    
    // Get wallet details including verification_txid
    $stmt = $pdo->prepare("SELECT id, user_id, cryptocurrency, verification_status, verification_txid
                           FROM user_payment_methods 
                           WHERE id = ? AND type = 'crypto'");
    $stmt->execute([$wallet_id]);
    $wallet = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$wallet) {
        throw new Exception('Wallet not found');
    }
    
    // Check if wallet is in correct status
    if ($wallet['verification_status'] !== 'verifying') {
        throw new Exception('Wallet must be in verifying status to reject');
    }
    
    // Begin transaction
    $pdo->beginTransaction();
    
    try {
        // Update wallet status to failed and clear verification data
        $update_stmt = $pdo->prepare("UPDATE user_payment_methods 
                                       SET verification_status = 'failed',
                                           verification_txid = NULL,
                                           verification_notes = ?,
                                           updated_at = CURRENT_TIMESTAMP
                                       WHERE id = ?");
        $update_stmt->execute([$reason, $wallet_id]);
        
        // Log admin action
        $action = "reject_wallet_verification";
        $log_stmt = $pdo->prepare("INSERT INTO audit_logs (admin_id, action, entity_type, entity_id, ip_address) 
                                   VALUES (?, ?, 'payment_method', ?, ?)");
        $ip = $_SERVER['REMOTE_ADDR'];
        $log_stmt->execute([$admin_id, $action, $wallet_id, $ip]);
        
        // Commit transaction
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Wallet verification rejected',
            'wallet_id' => $wallet_id,
            'status' => 'failed',
            'reason' => $reason
        ]);
        
        // Send notification email to user
        try {
            $emailHelper = new AdminEmailHelper($pdo);
            
            $customVars = [
                'cryptocurrency' => $wallet['cryptocurrency'],
                'verification_txid' => $wallet['verification_txid'] ?? 'N/A',
                'wallet_id' => $wallet_id,
                'rejection_reason' => $reason,
                'rejection_date' => date('Y-m-d H:i:s')
            ];
            
            $emailHelper->sendTemplateEmail('wallet_rejected', $wallet['user_id'], $customVars);
        } catch (Exception $e) {
            error_log("Wallet rejection email failed: " . $e->getMessage());
        }
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
